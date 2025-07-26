<?php
echo "=== DEBUG INFO ===\n";
echo "Current Directory: " . __DIR__ . "\n";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Server Name: " . $_SERVER['SERVER_NAME'] . "\n";
echo "HTTP Host: " . $_SERVER['HTTP_HOST'] . "\n";
echo "Request URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "Script Name: " . $_SERVER['SCRIPT_NAME'] . "\n";
echo "PHP Version: " . phpversion() . "\n";
echo "=== FILES IN CURRENT DIR ===\n";
$files = glob('*');
foreach ($files as $file) {
    echo $file . " (" . (is_file($file) ? 'file' : 'dir') . ")\n";
}
echo "=== SERVER VARS ===\n";
foreach ($_SERVER as $key => $value) {
    if (strpos($key, 'HTTP_') === 0 || strpos($key, 'SERVER_') === 0) {
        echo $key . ": " . $value . "\n";
    }
}
?> 