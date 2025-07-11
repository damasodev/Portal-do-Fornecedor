<?php
require_once 'includes/barcode.php';
$code = $_GET['code'] ?? 'EXEMPLO123';
barcode(300, 80, $code, 12, 'horizontal', 'code128', true);
?>
