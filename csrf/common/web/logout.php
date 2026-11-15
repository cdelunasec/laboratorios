<?php
require '/var/www/lab/lab.php';

$_SESSION = [];
session_destroy();
header('Location: /');
