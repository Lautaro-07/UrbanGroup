<?php
require_once __DIR__ . '/../includes/PortalClientModel.php';

function computeDv($body) {
    $sum = 0;
    $multiplier = 2;
    for ($i = strlen($body) - 1; $i >= 0; $i--) {
        $sum += intval($body[$i]) * $multiplier;
        $multiplier = $multiplier === 7 ? 2 : $multiplier + 1;
    }
    $expectedDv = 11 - ($sum % 11);
    if ($expectedDv === 11) return '0';
    if ($expectedDv === 10) return 'K';
    return (string)$expectedDv;
}

$bodies = ['12345678','11111111','76086428','87654321','76262337'];

foreach ($bodies as $body) {
    $dv = computeDv($body);
    $rut = substr(chunk_split(strrev($body),3,'.'),0,-1);
    $rut = strrev($rut) . '-' . $dv;
    $ok = PortalClientModel::validateRut($rut) ? 'VALID' : 'INVALID';
    echo "Body: $body -> RUT: $rut => $ok\n";
}

// Also test raw formats
$tests = ['12.345.678-5','12345678-5','12.345.678-k','76086428-5','76262337-9'];
foreach ($tests as $t) {
    echo "$t => " . (PortalClientModel::validateRut($t) ? 'VALID' : 'INVALID') . "\n";
}

?>