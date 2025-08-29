<?php
function getFilesystemData(): array {
    $output = [];
    $returnCode = 0;
    exec('df -h -T', $output, $returnCode);

    if ($returnCode !== 0 || count($output) < 2) {
        return ['error' => 'Failed to retrieve filesystem information using df -h -T'];
    }

    $filesystems = [];
    for ($i = 1; $i < count($output); $i++) {
        $line = preg_split('/\s+/', $output[$i], 7);
        if (count($line) < 7) continue;

        $filesystems[] = [
            'filesystem'  => $line[0],
            'type'        => $line[1],
            'size'        => $line[2],
            'used'        => $line[3],
            'available'   => $line[4],
            'use_percent' => $line[5],
            'mounted_on'  => $line[6]
        ];
    }

    return $filesystems;
}

function getUsageClass(string $percent): string {
    $num = (int) rtrim($percent, '%');
    if ($num >= 90) return 'high-usage';
    if ($num >= 70) return 'medium-usage';
    return 'low-usage';
}

$filesystemData = getFilesystemData();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Filesystem Monitor</title>
<style>
body {
    font-family: "Segoe UI", Roboto, sans-serif;
    margin: 0;
    padding: 2em;
    background: #121212;
    color: #e0e0e0;
}
h1 {
    color: #ffffff;
    margin-bottom: 0.5em;
    text-shadow: 0 0 8px rgba(255,255,255,0.2);
}
p {
    color: #bbb;
    margin-bottom: 2em;
}
.table-container {
    background: #1e1e1e;
    padding: 1em;
    border-radius: 12px;
    box-shadow: 0 0 15px rgba(0,0,0,0.6);
    overflow-x: auto;
}
table {
    border-collapse: collapse;
    width: 100%;
    border-radius: 8px;
    overflow: hidden;
}
th, td {
    padding: 12px 16px;
    border-bottom: 1px solid #2a2a2a;
}
th {
    background: #2a2a2a;
    color: #ddd;
    text-align: left;
    font-weight: 600;
}
tr:hover {
    background: rgba(255,255,255,0.05);
    transition: background 0.3s ease;
}
.usage-bar {
    height: 14px;
    border-radius: 6px;
    background: #333;
    overflow: hidden;
    margin-top: 4px;
    box-shadow: inset 0 1px 3px rgba(0,0,0,0.8);
}
.usage-fill {
    height: 100%;
    transition: width 0.6s ease;
}
.low-usage { background: linear-gradient(90deg, #4caf50, #81c784); }
.medium-usage { background: linear-gradient(90deg, #ffb300, #ffca28); }
.high-usage { background: linear-gradient(90deg, #e53935, #ef5350); }
.error {
    color: #ff6b6b;
    font-weight: bold;
    background: #2a0e0e;
    padding: 1em;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(255,0,0,0.2);
}
footer {
    margin-top: 2em;
    font-size: 0.9em;
    color: #666;
    text-align: center;
}
</style>
</head>
<body>
<h1>Filesystem Monitor</h1>
<p>Last updated: <?=date('Y-m-d H:i:s')?> (server time)</p>

<?php if (isset($filesystemData['error'])): ?>
    <div class="error"><?=htmlspecialchars($filesystemData['error'])?></div>
<?php else: ?>
<div class="table-container">
<table>
    <thead>
        <tr>
            <th>Filesystem</th>
            <th>Type</th>
            <th>Size</th>
            <th>Used</th>
            <th>Available</th>
            <th>Usage %</th>
            <th>Mounted on</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($filesystemData as $fs): 
            $usageClass = getUsageClass($fs['use_percent']);
            $percentNum = (int) rtrim($fs['use_percent'], '%');
        ?>
        <tr>
            <td><?=htmlspecialchars($fs['filesystem'])?></td>
            <td><?=htmlspecialchars($fs['type'])?></td>
            <td><?=htmlspecialchars($fs['size'])?></td>
            <td><?=htmlspecialchars($fs['used'])?></td>
            <td><?=htmlspecialchars($fs['available'])?></td>
            <td>
                <?=$fs['use_percent']?>
                <div class="usage-bar">
                    <div class="usage-fill <?=$usageClass?>" style="width: <?=$percentNum?>%"></div>
                </div>
            </td>
            <td><?=htmlspecialchars($fs['mounted_on'])?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php endif; ?>

<footer>
    Filesystem Monitor &copy; <?=date('Y')?> | Dark Mode Dashboard
</footer>
</body>
</html>
