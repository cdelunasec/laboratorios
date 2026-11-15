<?php
require '/var/www/lab/lab.php';

$me = require_login();
header('Location: /perfil.php?id=' . $me['id']);
