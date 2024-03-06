<?php

header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');
header('Strict-Transport-Security: max-age=63072000');
header('X-Robots-Tag: noindex, nofollow', true);

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="HandheldFriendly" content="True" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#c7ecee">

<title>REST API Tester - REST HTTP Client</title>
<meta name="description" content="REST API Tester - REST HTTP Client Testing Tool."/>
<?php $current_page = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; echo '<link rel="canonical" href="'.$current_page.'" />'; ?>


<link rel="shortcut icon" type="image/x-icon" href="/icons/favicon.ico" />
<link rel="icon" type="image/png" sizes="196x196" href="/icons/favicon-196.png" />
<link rel="apple-touch-icon" href="/icons/apple-icon-180.png" />
<meta name="mobile-web-app-capable" content="yes" />
<meta name="application-name" content="API Tester" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-title" content="API Tester" />

<link rel="preconnect" href="https://cdnjs.cloudflare.com">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.9.3/css/bulma.min.css" integrity="sha512-IgmDkwzs96t4SrChW29No3NXBIBv8baW490zk5aXvhCD8vuZM3yUSkbyTBcXohkySecyzIrUwiF/qV0cuPcL3Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" type="text/css" href="/css/app.css" />
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/vs2015.min.css" integrity="sha512-mtXspRdOWHCYp+f4c7CkWGYPPRAhq9X+xCvJMUBVAb6pqA4U8pxhT3RWT3LP3bKbiolYL2CkL1bSKZZO4eeTew==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js" integrity="sha512-D9gUyxqja7hBtkWpPWGt9wfbfaMGVt9gnyCvYa+jojwwPHLCzUm5i8rpk7vD7wNee9bA35eYIjobYPaQuKS1MQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<link rel="manifest" href="/manifest.json" />

</head>
<body>
<section class="section">
    <div class="container">
        <h1 class="title">API Tester</h1>
        <form id="apiForm">
            <div class="field">
                <label class="label">API Endpoint</label>
                <div class="control">
                    <input id="endpoint" class="input" type="url" placeholder="Enter API endpoint" required>
                    <p id="endpointError" class="help is-danger error-message"></p>
                </div>
            </div>
            <div class="field">
                <label class="label">HTTP Method</label>
                <div class="control">
                    <div class="select">
                        <select id="method" required onchange="toggleRequestBody()">
                            <option value="" disabled selected>Select method</option>
                            <option value="GET">GET</option>
                            <option value="POST">POST</option>
                            <option value="PUT">PUT</option>
                            <option value="DELETE">DELETE</option>
                        </select>
                    </div>
                    <p id="methodError" class="help is-danger error-message"></p>
                </div>
            </div>
            <div class="field" id="bodyField" style="display:none;">
                <label class="label">Request Body</label>
                <div class="control">
                    <textarea id="body" class="textarea" placeholder="Enter request body (JSON format)" rows="5"></textarea>
                </div>
                <div class="control">
                    <br>
                    <p>Check to Enter JSON Data</p>
                    <label class="checkbox">
                        <input id="jsonCheckbox" type="checkbox" onchange="toggleJsonMode()">
                    </label>
                </div>
            </div>
            <div class="field" id="headersField" style="display:none;">
                <label class="label">Request Headers</label>
                <div class="control">
                    <textarea id="headers" class="textarea" placeholder="Enter request headers (key: value)" rows="5"></textarea>
                </div>
            </div>
            <div class="field" id="queryParamsField">
                <label class="label">Query Parameters</label>
                <div class="control">
                    <textarea id="queryParams" class="textarea" placeholder="Enter query parameters (key1=value1&key2=value2)" rows="3"></textarea>
                    <p id="queryParamsError" class="help is-danger error-message"></p> <!-- Added error message for query params -->
                </div>
            </div>
            <div class="field">
                <label class="checkbox">
                    <input id="useProxy" type="checkbox" checked onchange="saveCheckboxState()">
                    Use Proxy
                </label>
            </div>
            <div class="field is-grouped">
                <div class="control">
                    <button id="submitBtn" class="button is-primary" type="button">Submit</button>
                    <button id="restoreDataBtn" class="button is-info" type="button">Restore Data</button>
                </div>
            </div>
        </form>
        <div id="response" class="response-container"></div>
    </div>
</section>

<script src="/js/app.js"></script>

</body>
</html>