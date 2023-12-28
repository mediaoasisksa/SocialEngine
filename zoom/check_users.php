<?php
require_once 'config.php';
$db = new DB();
echo $db->is_table_empty($_GET['user_id']).'-'.CLIENT_ID;
?>