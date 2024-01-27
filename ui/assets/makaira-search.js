function debounce(func, wait, immediate) {
  var timeout;
  return function () {
    var context = this,
      args = arguments;
    var later = function () {
      timeout = null;
      if (!immediate) func.apply(context, args);
    };
    var callNow = immediate && !timeout;
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
    if (callNow) func.apply(context, args);
  };
}

// document.addEventListener("DOMContentLoaded", () => {
  const searchFormClass = "makaira-autosuggestion__form";
  const searchInputId = "searchParam";
  const searchSubmitBtnClass = "makaira-autosuggestion__submit";
  const flyoutContainerClass = "makaira-autosuggestion";

  const searchForm = document.querySelector(`.${searchFormClass}`);
  const searchInput = document.getElementById(searchInputId);
  const searchSubmitBtn = document.querySelector(`.${searchSubmitBtnClass}`);
  const flyoutContainer = document.querySelector(`.${flyoutContainerClass}`);

  const enableLoading = () => {
    searchForm.classList.add("search-loading");
  };

  const disableLoading = () => {
    searchForm.classList.remove("search-loading");
  };

  const renderAutosuggestion = (html) => {
    let container = flyoutContainer;
    if (!container) {
      const _container = document.createElement("div");
      _container.className = flyoutContainerClass;
      searchForm.appendChild(container);
      container = _container;
    }
    container.innerHTML = html;
    container.classList.add("open");
    disableLoading();
  };

  const fetchAutosuggestion = debounce((e) => {
    const searchTerm = e.target.value.trim();

    if (searchTerm.length > 2) {
      enableLoading();

      let shopUrl = searchForm.action;
      shopUrl += shopUrl.includes("?") ? "&" : "?";

      fetch(`${shopUrl}keyword=${encodeURIComponent(searchTerm)}`)
        .then((res) => res.text())
        .then((html) => renderAutosuggestion(html, searchForm))
        .catch((err) => console.error("Processing in Makaira failed", err))
        .finally(() => disableLoading());
    }
  }, 300);

  const closeAutosuggestion = (event) => {
    const target = event.target;

    // only act if search has been fired at least once
    if (flyoutContainer) {
      let targetNotSearchInput = target.id !== searchInputId;
      let targetNotSubmitButton =
        !target.classList.contains(searchSubmitBtnClass);
      // close for all targets except the searchInput and submitbutton
      if (targetNotSearchInput && targetNotSubmitButton) {
        flyoutContainer.classList.remove("open");
        flyoutContainer.innerHTML = "";
      }
    }
  };

  if (searchInput) {
    searchInput.addEventListener("input", fetchAutosuggestion);
    document.body.addEventListener("click", closeAutosuggestion);
  }
// });
