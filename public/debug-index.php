<?php
// Test if we can manually call index.php
$_SERVER['REQUEST_URI'] = '/super-admin/health';
$_SERVER['SCRIPT_NAME'] = '/super-admin/index.php';
$_SERVER['SCRIPT_FILENAME'] = '/var/www/superadmin/superadmin-campusway/public/index.php';

require_once 'index.php';
