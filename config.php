<?php

// ENVIRONMENT SPECIFIC CONSTANTS
if ($_SERVER['SERVER_NAME'] == 'bill-generator-pss.herokuapp.com') {
    define("ENV", 'production');
    error_reporting(E_ERROR | E_WARNING | E_PARSE);
    define('BASE_URL', 'https://bill-generator-pss.herokuapp.com');
    define('BASE_DIR', "$_SERVER[DOCUMENT_ROOT]");
} else if ($_SERVER['SERVER_NAME'] != 'localhost') {
    define("ENV", 'test');
    error_reporting(E_ALL & ~E_NOTICE);
    define('BASE_URL', 'http://localhost/bill-generator');
    define('BASE_DIR', "$_SERVER[DOCUMENT_ROOT]");
} else {
    define("ENV", 'dev');
    error_reporting(E_ALL & ~E_NOTICE);
    define('BASE_URL', 'http://localhost/bill-generator');
    define('BASE_DIR', "$_SERVER[DOCUMENT_ROOT]/bill-generator");
}

define('UPLOAD_FILE_NAME', 'processed.csv');
define('DEFAULT_TITLE', 'Bill Generator');
define('DEFAULT_DESCRIPTION', '');
