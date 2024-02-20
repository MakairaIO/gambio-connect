const btnExport = document.querySelector('.export button');
const btnReplace = document.querySelector('.replace button');
const btnSwitch = document.querySelector('.switch button');
const successKey = 'gx-admin:send-info-box-success-message';
const warningKey = 'gx-admin:send-info-box-warning-message';

function handleClick(event, callbackUrl)
{
    const btn = event.target;
    const request = fetch(callbackUrl, {
        method: 'post'
    });
    btn.setAttribute('disabled', true);

    request.then(response => {
        if (response.ok) {
            window.dispatchEvent(new Event(successKey));
        } else {
            window.dispatchEvent(new CustomEvent(warningKey, {
                detail: {
                    title: 'Request failed',
                    message: 'Could not perform requested action.'
                }
            }))
        }
    }).finally(() => {
        btn.removeAttribute('disabled');
    });
}

function addCallbackListener(button, callbackUrl)
{
    button.addEventListener('click', event => {
        handleClick(event, callbackUrl);
    });
}

addCallbackListener(btnExport, `${window.jsEnvironment.baseUrl} / admin / makaira / gambio - connect / sync / export`);
addCallbackListener(btnReplace, `${window.jsEnvironment.baseUrl} / admin / makaira / gambio - connect / sync / replace`);
addCallbackListener(btnSwitch, `${window.jsEnvironment.baseUrl} / admin / makaira / gambio - connect / sync / switch`);
