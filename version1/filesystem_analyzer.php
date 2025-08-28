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
body { font-family: sans-serif; margin: 2em; background: #121212; color: #eee; }
h1 { color: #f0f0f0; margin-bottom: 1em; }
table { border-collapse: collapse; width: 100%; }
th, td { padding: 8px 12px; border: 1px solid #333; text-align: left; }
th { background: #1e1e1e; }
.usage-bar { height: 12px; border-radius: 4px; background: #333; overflow: hidden; }
.usage-fill { height: 100%; transition: width 0.5s ease; }
.low-usage { background: #4caf50; }
.medium-usage { background: #ffc107; }
.high-usage { background: #f44336; }
.error { color: #ff5555; font-weight: bold; }
</style>
</head>
<body>
<h1>Filesystem Monitor</h1>
<p>Last updated: <?=date('Y-m-d H:i:s')?> (server time)</p>

<?php if (isset($filesystemData['error'])): ?>
    <p class="error"><?=htmlspecialchars($filesystemData['error'])?></p>
<?php else: ?>
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
<?php endif; ?>

</body>
</html>
