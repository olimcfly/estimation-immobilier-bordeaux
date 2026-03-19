<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
echo "PHP Version: " . phpversion() . "\n";
echo "SAPI: " . php_sapi_name() . "\n";
var_dump($_SERVER);
?>
