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

<title>Random Quote Generator</title>
<meta name="description" content="Free Random Random Quote Generator with image."/>
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
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">

<style>
     html, body {
        min-height: 100vh;
    }
    body {
        font-family: "Roboto Mono", monospace;
        background-color: #FDA7DF;
        padding-bottom: 20px;
    }
    #quote-container {
        margin: 10px auto;
        border-radius: 10px;
        padding: 20px;
        background-color: #fff;
        font-family: "Roboto Mono", monospace;
    }
    #quote {
        font-family: "Roboto Mono", monospace;
        font-size: 20px;
        margin-bottom: 20px;
        color: #333;
    }
    #author {
        font-family: "Roboto Mono", monospace;
        font-style: italic;
        color: #777;
    }
    #image-container {
        margin-top: 20px;
    }
    #quote-card {
        max-width: 800px;
        margin: 10px auto;
        font-family: "Roboto Mono", monospace;
    }
    .generate-button {
        font-family: "Roboto Mono", monospace;
        background-color: #4CAF50;
        border: none;
        color: white;
        padding: 10px 20px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 4px 2px;
        transition-duration: 0.4s;
        cursor: pointer;
        border-radius: 4px;
    }

    .generate-button:hover {
        background-color: #45a049;
    }
    button {
       display: flex;
       flex-grow: 0.3;
       font-weight: 600;
       font-size: 14px;
       text-transform: uppercase;
       box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
       border-radius: 32px;
       padding: 12px;
       -moz-osx-font-smoothing: grayscale;
       -webkit-font-smoothing: antialiased !important;
       -moz-font-smoothing: antialiased !important;
       text-rendering: optimizelegibility !important;
    }
    canvas {
        font-family: "Roboto Mono", monospace;
        image-rendering: optimizeSpeed;
        image-rendering: -moz-crisp-edges;
        image-rendering: -webkit-optimize-contrast;
        image-rendering: -o-crisp-edges;
        image-rendering: crisp-edges;
       -ms-interpolation-mode: nearest-neighbor;
        width: 100%;
        height: 100%;
    }
</style>

<link rel="manifest" href="/manifest.json" />

</head>
<body>
<section class="section">
    <div class="container">
        <div id="quote-card" class="card">
            <div class="card-content">
                <div id="quote-container">
                    <hr>
                    <p id="quote">Click the button 🔄 to generate a quote.<br><br>🔄 Random Quotes <br>📑 Copy to Clipboard<br>📥 Download Quotes<br>📢 Share Quotes<br></p>
                    <p id="author"></p>
                    <br>
                    <div class="field is-grouped">
                    <div class="control">
                    <button onclick="generateQuote()" class="button is-warning is-rounded">🔄</button>
                    <button onclick="copyQuote()" id="copy-button" class="button is-success is-rounded" style="display: none;">📑</button>
                    </div>
                    </div>
                    <br>
                    <div id="error-message" style="display: none;" class="notification is-danger"></div>
                    <hr>
                    <div class="field is-grouped">
                    <div class="control">
                    <button id="download-button" style="display: none;" onclick="downloadImage()" class="button is-danger is-rounded">📥</button>
                    <button id="share-button" style="display: none;" onclick="shareImage()" class="button is-link is-rounded">📢</button>
                    </div>
                    </div>
                </div>
                <div id="image-container"></div>
            </div>
        </div>
    </div>
</section>

<script>
    function generateImage(quote, container) {
        const canvas = document.createElement("canvas");
        const ctx = canvas.getContext("2d");
        canvas.width = 1080;
        canvas.height = 1080;
        ctx.fillStyle = "#002147";
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.font = "36px 'Roboto Mono', monospace"; 
        ctx.fillStyle = "#FFFF99";
        ctx.imageSmoothingEnabled = false;

        function wrapText(text, x, y, maxWidth, lineHeight) {
            var words = text.split(' ');
            var line = '';
            var lines = [];
            for(var n = 0; n < words.length; n++) {
                var testLine = line + words[n] + ' ';
                var metrics = ctx.measureText(testLine);
                var testWidth = metrics.width;
                if (testWidth > maxWidth && n > 0) {
                    lines.push(line.trim());
                    line = words[n] + ' ';
                }
                else {
                    line = testLine;
                }
            }
            lines.push(line.trim());

            var yPosition = y;
            lines.forEach(function(line) {
                ctx.fillText(line, x + (maxWidth - ctx.measureText(line).width) / 2, yPosition);
                yPosition += lineHeight;
            });
        }

        var maxTextWidth = 800;
        var lineHeight = 65;
        var maxLines = 4;
        var textHeight = lineHeight * maxLines;

        var textX = (canvas.width - maxTextWidth) / 2;
        var textY = (canvas.height - textHeight) / 2;

        wrapText(`${quote.quotes}`, textX, textY, maxTextWidth, lineHeight);

        ctx.font = "italic 32px 'Roboto Mono', monospace";
        ctx.fillStyle = "#FDF2E9";
        ctx.textAlign = "center"; 
        const authorTextY = (canvas.height + textHeight + 630) / 2;
        ctx.fillText(`${quote.author}`, canvas.width / 2, authorTextY);

        container.innerHTML = "";
        container.appendChild(canvas);

        const image = new Image();
        image.src = canvas.toDataURL();
        container.appendChild(canvas);

        document.getElementById("download-button").style.display = "inline-block";
        document.getElementById("share-button").style.display = "inline-block";
    }

    function showErrorNotification(message) {
       const errorMessageContainer = document.getElementById("error-message");
       errorMessageContainer.innerHTML = `<button class="delete"></button>${message}`;
       errorMessageContainer.style.display = "block";

      errorMessageContainer.querySelector(".delete").addEventListener("click", () => {
         errorMessageContainer.style.display = "none";
      });
   }

    function generateQuote() {
        const quoteElement = document.getElementById("quote");
        const authorElement = document.getElementById("author");
        const imageContainer = document.getElementById("image-container");
        const copyButton = document.getElementById("copy-button");

        fetch('/quotes/api.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch quotes');
            }
            return response.json();
        })
        .then(data => {
            const quote = data[0];

            quoteElement.textContent = `"${quote.quotes}"`;
            authorElement.textContent = `– ${quote.author}`;

            generateImage(quote, imageContainer);
            copyButton.style.display = "inline-block";
        })
        .catch(error => {
            console.error('Error fetching quotes:', error);
            showErrorNotification("Error fetching quotes. Please try again later.");
            quoteElement.textContent = "Failed to fetch quotes. Please try again later.";
            authorElement.textContent = "";
            imageContainer.innerHTML = "";
        });
    }

    function downloadImage() {
    const canvas = document.querySelector("#image-container canvas");
    if (!canvas) {
        console.error("Canvas element not found.");
        return;
    }
    const timestamp = new Date().getTime();
    const image = canvas.toDataURL("image/png").replace("image/png", "image/octet-stream");
    const link = document.createElement("a");
    link.href = image;
    link.download = `quote-image-${timestamp}.png`
    link.click();
}

function shareImage() {
    const canvas = document.querySelector("#image-container canvas");
    const timestamp = new Date().getTime();

    if (!canvas) {
        console.log("Canvas element not found.");
        return;
    }

    canvas.toBlob(function(blob) {
        const filesArray = [new File([blob], `quote-image-${timestamp}.png`, { type: "image/png" })];
        const shareData = {
            files: filesArray,
        };

        if (navigator.canShare && navigator.canShare(shareData)) {
            navigator.share(shareData)
                .then(() => console.log("Image shared successfully"))
                .catch((error) => console.error("Error sharing image:", error));
        } else {

            showErrorNotification("Sharing is not supported on this browser.");
        }
    }, "image/png");
}
function copyQuote() {
    const quoteText = document.getElementById("quote").textContent.trim();
    navigator.clipboard.writeText(quoteText)
        .then(() => {
            console.log("Quotes Copied to Clipboard.");
        })
        .catch(err => {
            console.error('Failed to copy quote');
        });
}

</script>

</body>
</html>