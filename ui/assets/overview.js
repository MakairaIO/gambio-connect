const btnAll = document.querySelector('.clear-all button');
const btnCore = document.querySelector('.clear-core button');
const successKey = 'gx-admin:send-info-box-success-message';
const warningKey = 'gx-admin:send-info-box-warning-message';

function handleClick(event, callbackUrl) {
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

function addCallbackListener(button, callbackUrl) {
	button.addEventListener('click', event => {
		handleClick(event, callbackUrl);
	});
}

addCallbackListener(btnAll, `${window.jsEnvironment.baseUrl}/admin/gambio-samples/cache-cleaner/clear-all`);
addCallbackListener(btnCore, `${window.jsEnvironment.baseUrl}/admin/gambio-samples/cache-cleaner/clear-core`);
