<!DOCTYPE html>
<html>
<head>
<title>Cross-Browser QRCode generator for Javascript</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no" />
<script type="text/javascript" src="qrcode.js"></script>
</head>
<body>
<div id="qrcode" style="margin-top:15px;"></div>

<script type="text/javascript">
var qrcode = new QRCode(document.getElementById("qrcode"), {
	width : 1000,
	height : 1000
});

qrcode.makeCode(`
`);

</script>
</body>