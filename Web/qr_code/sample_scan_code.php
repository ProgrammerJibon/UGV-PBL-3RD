<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DEMO</title>
</head>
<body>
<div id="qr-canvas" style="filter: invert(1) contrast(100) brightness(100) blur(5px) saturate(100) grayscale(0.5);"></div>
<script type="module">
    import QrScanner from "../qrscan.js";

    const video = document.createElement("video");

    function setResult(result) {
        console.log(result.data);
    }

    const scanner = new QrScanner(video, result => setResult(result), {
        onDecodeError: error => {
            console.log(error);
        },
        highlightScanRegion: true,
        highlightCodeOutline: true,
        maxScansPerSecond: 10,
        
    });
    document.getElementById("qr-canvas").appendChild(scanner.$canvas);
    video.remove();
    scanner.start()
</script>


</body>
</html>