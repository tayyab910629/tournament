<?php
require_once '../config/config.php';

session_destroy();
redirect('/admin/login.php');
?>
