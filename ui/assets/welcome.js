
function calculateTotal(context) {
    let total = 0;
    const checkboxes = context.querySelectorAll('input[type="checkbox"].package-checkbox');
    const result = context.querySelector('.total-price');
    const sum = context.querySelector('.sum');


    if (!result) {
        return;
    }

    checkboxes.forEach(checkbox => {
        if (checkbox.checked) {
            total += parseFloat(checkbox.getAttribute('data-price') || 0);
        }
    });

    (sum && total == 0) ? sum.style.display = "none" : sum.style.display = "flex"

    result.innerHTML = total;

}

function attachEventListeners(context) {
    context.querySelectorAll('input[type="checkbox"].package-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', () => calculateTotal(context));
    });
}

const package = document.querySelector('.select-package');

attachEventListeners(package);
calculateTotal(package);
