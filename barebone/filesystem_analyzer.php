#!/usr/bin/env php
<?php
$rawOutput = [];
exec('df -h -T', $rawOutput, $returnCode);
if ($returnCode !== 0) {
    $rawOutput = ["Error: failed to run df -h -T"];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Disk Usage Monitor</title>
    <style>
        body { font-family: sans-serif; margin: 2em; background: #121212; color: #eee; }
        h1 { color: #f0f0f0; }
        pre { background: #1e1e1e; color: #bbb; padding: 1em; overflow-x: auto; border-radius: 8px; }
    </style>
</head>
<body>
    <h1>Filesystem Disk Usage</h1>
    <p>Last updated: <?=date('Y-m-d H:i:s')?> (server time)</p>
    <pre><?=htmlspecialchars(implode("\n", $rawOutput))?></pre>
</body>
</html>
