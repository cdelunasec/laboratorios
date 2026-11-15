<?php
require '/var/www/lab/lab.php';

session_destroy();
header('Location: /login.php');
