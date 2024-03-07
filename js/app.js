function saveCheckboxState() {
const useProxyCheckbox = document.getElementById('useProxy');
localStorage.setItem('useProxy', useProxyCheckbox.checked);
}

function saveFormData() {
    const formData = {
        endpoint: document.getElementById('endpoint').value,
        method: document.getElementById('method').value,
        headers: document.getElementById('headers').value,
        body: document.getElementById('body').value,
        queryParams: document.getElementById('queryParams').value // New line to save query parameters
    };
    localStorage.setItem('apiFormData', JSON.stringify(formData));
}

function loadFormData() {
    const formData = JSON.parse(localStorage.getItem('apiFormData'));
    if (formData) {
        document.getElementById('endpoint').value = formData.endpoint;
        document.getElementById('method').value = formData.method;
        document.getElementById('headers').value = formData.headers;
        document.getElementById('body').value = formData.body;
        document.getElementById('queryParams').value = formData.queryParams;

        const useProxyCheckbox = document.getElementById('useProxy');
        useProxyCheckbox.checked = localStorage.getItem('useProxy') === 'true'

        const jsonCheckbox = document.getElementById('jsonCheckbox');
        jsonCheckbox.checked = localStorage.getItem('jsonCheckbox') === 'true';
        toggleJsonMode();
    }
}

document.addEventListener('DOMContentLoaded', loadFormData);

function toggleRequestBody() {
    const methodSelect = document.getElementById('method');
    const bodyField = document.getElementById('bodyField');
    const headersField = document.getElementById('headersField');
    const jsonCheckbox = document.getElementById('jsonCheckbox');
    const selectedMethod = methodSelect.value;

    // Show body field for POST and PUT, hide for others
    bodyField.style.display = selectedMethod === 'POST' || selectedMethod === 'PUT' || selectedMethod === 'PATCH' ? 'block' : 'none';

    // Show headers field for all methods
    headersField.style.display = 'block';

    // Toggle JSON checkbox visibility
    jsonCheckbox.style.display = selectedMethod === 'GET' || selectedMethod === 'DELETE' ? 'none' : 'block';

    // Clear body field if method is changed after entering data
    if (selectedMethod !== 'POST' && selectedMethod !== 'PUT' && selectedMethod !== 'PATCH') {
        document.getElementById('body').value = '';
    }

    // Toggle JSON mode based on method
    toggleJsonMode();
}

function toggleJsonMode() {
    const bodyTextarea = document.getElementById('body');
    const jsonCheckbox = document.getElementById('jsonCheckbox');
    localStorage.setItem('jsonCheckbox', jsonCheckbox.checked);

    if (jsonCheckbox.checked) {
        //bodyTextarea.value = ''; // Clear the textarea
        bodyTextarea.placeholder = 'Enter request body (JSON format)';
        bodyTextarea.rows = '5';
    } else {
        bodyTextarea.placeholder = 'Enter request body key-value pairs (e.g., key1: value1\nkey2: value2)';
        bodyTextarea.rows = '10';
    }
}

function validateForm() {
    const endpointInput = document.getElementById('endpoint');
    const methodSelect = document.getElementById('method');
    const headersInput = document.getElementById('headers');
    const bodyTextarea = document.getElementById('body');
    const queryParamsInput = document.getElementById('queryParams'); // New line to get query parameters

    // Reset error messages
    document.getElementById('endpointError').textContent = '';
    document.getElementById('methodError').textContent = '';
    document.getElementById('queryParamsError').textContent = ''; // New line to reset query params error message

    const endpoint = endpointInput.value.trim();
    const method = methodSelect.value;
    const headers = headersInput ? headersInput.value.trim() : '';
    const body = bodyTextarea.value.trim();
    const queryParams = queryParamsInput.value.trim(); // New line to get query parameters

    // Validation
    if (!endpoint) {
        document.getElementById('endpointError').textContent = 'API Endpoint is required';
        return false;
    }

    if (!method) {
        document.getElementById('methodError').textContent = 'Please select a method';
        return false;
    }

    if (['POST', 'PUT', 'PATCH'].includes(method) && !body) {
        document.getElementById('methodError').textContent = 'Request Body is required for non-GET methods';
        return false;
    }

    // Validate query parameters format
    if (queryParams && !validateQueryParams(queryParams)) {
        document.getElementById('queryParamsError').textContent = 'Invalid query parameters format. Please use key-value pairs separated by "&"';
        return false;
    }

    // Additional validation for headers
    if (headers && !validateHeaders(headers)) {
        document.getElementById('methodError').textContent = 'Invalid request headers format. Please use key-value pairs separated by colon (e.g., Content-Type: application/json)';
        return false;
    }

    return true;
}

function validateQueryParams(queryParams) {
    // Regular expression to validate query parameters format
    const queryParamsPattern = /^([\w-]+=[^&\s]+&?)*$/;
    return queryParamsPattern.test(queryParams);
}

function validateHeaders(headers) {
    //const headerPattern = /^[^:\s]+:\s*([^:\s]+|Bearer\s[^:\s]+)$/;
    const headerPattern = /^[^:\s]+:\s*(Basic\s[^:\s]+|[^:\s]+|Bearer\s[^:\s]+)$/;
    const headerPairs = headers.split('\n');
    for (const pair of headerPairs) {
        const trimmedPair = pair.trim();
        if (trimmedPair === '') continue; // Skip empty lines
        if (!headerPattern.test(trimmedPair)) {
            return false;
        }
        //const [key, value] = trimmedPair.split(':').map(entry => entry.trim());
        //if (key.toLowerCase() === 'authorization') {
        //    if (!value.startsWith('Bearer ')) {
        //        return false; // Ensure Authorization header starts with 'Bearer '
        //    }
        //}
    }
    return true;
}


document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('apiForm');

    document.getElementById('restoreDataBtn').addEventListener('click', function () {
       loadFormData();
    });

    document.getElementById('submitBtn').addEventListener('click', function (event) {
        event.preventDefault();

        if (!validateForm()) {
            return;
        }

        const endpoint = document.getElementById('endpoint').value.trim();
        const method = document.getElementById('method').value;
        const headersInput = document.getElementById('headers');
        const headers = headersInput ? headersInput.value.trim() : '';
        const body = document.getElementById('body').value.trim();
        const useProxy = document.getElementById('useProxy').checked;
        const queryParams = document.getElementById('queryParams').value.trim(); // New line to get query parameters

        saveFormData();

        // Append query parameters to endpoint URL
        let urlWithParams = endpoint;
        if (queryParams) {
            urlWithParams += '?' + queryParams;
        }

        const requestOptions = {
            method: method,
            headers: headers ? parseHeaders(headers) : {},
        };

        // Only include body in the request if method is not GET or HEAD
        if (method !== 'GET' && method !== 'HEAD') {
            requestOptions.body = body;
        }

        if (useProxy) {
            // Use proxy
            requestOptions.headers['Content-Type'] = 'application/json'; // Ensure content type header is set

            fetch('/proxy/proxy.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    url: urlWithParams, // Use endpoint with query parameters
                    method: method,
                    headers: headers ? parseHeaders(headers) : {},
                    body: body,
                    use_proxy: true
                }),
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Failed with status ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    const responseContainer = document.getElementById('response');
                    responseContainer.innerHTML = `<pre><code class="json">${hljs.highlight('json', JSON.stringify(data, null, 2)).value}</code></pre>`;
                })
                .catch(error => {
                    const responseContainer = document.getElementById('response');
                    responseContainer.innerHTML = `<div class="notification is-danger">${error.message}</div>`;
                });
        } else {
            // Do not use proxy
            fetch(urlWithParams, requestOptions) // Use endpoint with query parameters
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Failed with status ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    const responseContainer = document.getElementById('response');
                    responseContainer.innerHTML = `<pre><code class="json">${hljs.highlight('json', JSON.stringify(data, null, 2)).value}</code></pre>`;
                })
                .catch(error => {
                    const responseContainer = document.getElementById('response');
                    responseContainer.innerHTML = `<div class="notification is-danger">${error.message}</div>`;
                });
        }
    });
});


// Function to parse headers string into key-value pairs
function parseHeaders(headersString) {
    const headers = {};
    const headerLines = headersString.trim().split('\n');
    headerLines.forEach(line => {
        const [key, value] = line.split(':').map(entry => entry.trim());
        headers[key] = value;
    });
    return headers;
}