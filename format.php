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

<title>Text Multi-line Converter</title>
<meta name="description" content="Simple Tool for Text Multi-line Converter."/>
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

  <style>
    body {
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Ubuntu, Cantarell, "Helvetica Neue", Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
    }
    .rounded {
      border-radius: 10px;
    }
    .textarea {
      width: 100%;
      height: 300px;
      margin-bottom: 20px;
    }
    .center-heading {
      text-align: center;
    }
    .button, .notification {
      box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
    }
    @media (max-width: 768px) {
      .button {
        width: 100%;
        margin-bottom: 5px;
      }
    }
    button {
      display: flex;
      flex-grow: 0.2;
      font-weight: 600;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
      border-radius: 32px;
      padding: 12px;
      -moz-osx-font-smoothing: grayscale;
     -webkit-font-smoothing: antialiased !important;
     -moz-font-smoothing: antialiased !important;
     text-rendering: optimizelegibility !important;
  }
  .textarea {
     box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    -moz-osx-font-smoothing: grayscale;
    -webkit-font-smoothing: antialiased !important;
    -moz-font-smoothing: antialiased !important;
    text-rendering: optimizelegibility !important;
  }
  </style>

<link rel="manifest" href="/manifest.json" />

</head>
<body>

  <section class="section">
    <div class="container">
      <h1 class="title center-heading">Text Multi-line Converter</h1> <!-- Centered heading -->
      <textarea id="inputText" class="textarea rounded" placeholder="Enter your multi-line text here..." oninput="toggleButtons()"></textarea>
      <div class="field is-grouped is-grouped-centered">
        <div class="control">
          <button id="convertButton" class="button is-primary rounded" onclick="convert()" disabled>Convert 🔄</button>
        </div>
      </div>
      <div class="field is-grouped is-grouped-centered">
        <div class="control">
          <button class="button is-info rounded" onclick="resetForm()">Reset 🔁</button>
        </div>
        <div class="control">
          <button class="button is-warning rounded" onclick="pasteFromClipboard()">Paste from Clipboard 📋</button>
        </div>
      </div>
      <div id="errorMessage" class="notification rounded" style="display: none;"></div>
      <div id="outputText" class="content"></div>
      <div class="field is-grouped is-grouped-centered">
        <div class="control">
          <button id="copyButton" class="button is-success rounded" onclick="copyToClipboard()" disabled>Copy to Clipboard 📝</button>
        </div>
      </div>
    </div>
  </section>

  <script>
    function toggleButtons() {
      var inputText = document.getElementById("inputText").value.trim();
      var convertButton = document.getElementById("convertButton");
      var wordCount = countWords(inputText);
      convertButton.disabled = wordCount < 3 || wordCount > 1000;
    }

    function countWords(text) {
      return text.split(/\s+/).filter(function(word) {
        return word.length > 0;
      }).length;
    }

    function convert() {
      var inputText = document.getElementById("inputText").value.trim();
      if (!inputText) {
        showError("Please enter some text to convert.");
        return;
      }
      var outputText = convertToDesiredFormat(inputText);
      document.getElementById("outputText").innerHTML = "<pre>" + outputText + "</pre>";
      document.getElementById("copyButton").disabled = false;
    }

    function convertToDesiredFormat(text) {
      return text.replace(/\n/g, '\\n');
    }

    function copyToClipboard() {
      var outputText = document.getElementById("outputText").textContent;
      if (!outputText) {
        showError("Nothing to copy.");
        return;
      }
      navigator.clipboard.writeText(outputText).then(function() {
        showNotification("Text copied to clipboard successfully!", "is-success");
      }, function() {
        showError("Failed to copy text to clipboard.");
      });
    }

    function pasteFromClipboard() {
      navigator.clipboard.readText().then(function(text) {
        document.getElementById("inputText").value = text;
        toggleButtons();
        showNotification("Text pasted from clipboard successfully!", "is-info");
      }, function() {
        showError("Failed to read text from clipboard.");
      });
    }

    function resetForm() {
      document.getElementById("inputText").value = "";
      document.getElementById("outputText").innerHTML = "";
      document.getElementById("convertButton").disabled = true;
      document.getElementById("copyButton").disabled = true;
      hideError();
    }

    function showError(message) {
      var errorMessage = document.getElementById("errorMessage");
      errorMessage.textContent = message;
      errorMessage.classList.add("is-danger");
      errorMessage.style.display = "block";
    }

    function hideError() {
      var errorMessage = document.getElementById("errorMessage");
      errorMessage.textContent = "";
      errorMessage.style.display = "none";
    }

    function showNotification(message, type) {
      var notification = document.getElementById("errorMessage");
      notification.textContent = message;
      notification.classList.add(type);
      notification.style.display = "block";
      setTimeout(function() {
        notification.style.display = "none";
        notification.classList.remove(type);
      }, 3000);
    }
  </script>

</body>
</html>