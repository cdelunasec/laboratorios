<?php
require '/var/www/lab/lab.php';

$me = require_login_api();
json_response(['usuario' => $me['username'], 'correo' => $me['email'], 'rol' => $me['role']]);
