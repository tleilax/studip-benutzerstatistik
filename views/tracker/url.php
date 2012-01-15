<!DOCTYPE html>
<html>
<head>
    <title>Weiterleitung...</title>
    <script>
    if (navigator.userAgent.indexOf('Chrome') === -1) {
        location.href='<?= $url ?>';
    }
    </script>
</head>
<body style="text-align:center;padding-top:1em;">
    Sie werden nun zu <a href="<?= $url ?>"><?= htmlReady($url) ?></a> weitergeleitet.<br>
    Sollten Sie nicht innerhalb von 10 Sekunden weitergeleitet werden, so klicken Sie bitte <a href="<?= $url ?>">hier</a>.
    <meta http-equiv="refresh" content="0;url=<?= $url ?>">
</body>
</html>
