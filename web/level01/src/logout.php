<?php
require '/var/www/lib/app.php';

$_SESSION = [];
session_destroy();
lab_redirect('/');
