
function calculateTotal(context)
{
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

function syncDependencies(checkbox, context)
{

    const status = checkbox.checked;
    const name = checkbox.getAttribute('name');
    const dependsOn = checkbox.getAttribute('data-depends-on') ? .split(',');
    const checkboxes = context.querySelectorAll('input[type="checkbox"].package-checkbox');


    if (status) {
        checkboxes.forEach(checkbox => {
            if (dependsOn.includes(checkbox.getAttribute('name'))) {
                checkbox.checked = status;
            }
        });
    } else {
        checkboxes.forEach(checkbox => {
            if (checkbox.getAttribute('data-depends-on') ? .split(',').includes(name)) {
                checkbox.checked = status;
            }
        });
    }
}

function attachEventListeners(context)
{
    context.querySelectorAll('input[type="checkbox"].package-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', () => syncDependencies(checkbox, context));
        checkbox.addEventListener('change', () => calculateTotal(context));
    });
}

const package = document.querySelector('.select-package');

attachEventListeners(package);
calculateTotal(package);
