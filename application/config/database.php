<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$active_group = 'default';
$query_builder = true;


// set base url based on environment.
switch ($_SERVER["HTTP_HOST"]) {
case 'localhost':
  $db['default'] = array(
    'dsn'    => '',
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'ccta',
    'dbdriver' => 'mysqli',
    'dbprefix' => '',
    'pconnect' => false,
    'db_debug' => (ENVIRONMENT !== 'production'),
    'cache_on' => false,
    'cachedir' => '',
    'char_set' => 'utf8',
    'dbcollat' => 'utf8_general_ci',
    'swap_pre' => '',
    'encrypt' => false,
    'compress' => false,
    'stricton' => false,
    'failover' => array(),
    'save_queries' => true
  );
    break;
case 'www.qa.vuonsite.com':
  $db['default'] = array(
    'dsn'    => '',
    'hostname' => 'localhost',
    'username' => 'ypwkce7g_qaccta',
    'password' => '9CH?F8K78+%O',
    'database' => 'ypwkce7g_qaccta',
    'dbdriver' => 'mysqli',
    'dbprefix' => '',
    'pconnect' => FALSE,
    'db_debug' => (ENVIRONMENT !== 'production'),
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8',
    'dbcollat' => 'utf8_general_ci',
    'swap_pre' => '',
    'encrypt' => FALSE,
    'compress' => FALSE,
    'stricton' => FALSE,
    'failover' => array(),
    'save_queries' => TRUE
  );
    break;
case 'www.vuonsite.com':
    $db['default'] = array(
    'dsn'    => '',
    'hostname' => 'localhost',
    'username' => 'ypwkce7g_bluedom',
    'password' => 'ohnrf]lOxY{D',
    'database' => 'ypwkce7g_bluedometech',
    'dbdriver' => 'mysqli',
    'dbprefix' => '',
    'pconnect' => FALSE,
    'db_debug' => (ENVIRONMENT !== 'production'),
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8',
    'dbcollat' => 'utf8_general_ci',
    'swap_pre' => '',
    'encrypt' => FALSE,
    'compress' => FALSE,
    'stricton' => FALSE,
    'failover' => array(),
    'save_queries' => TRUE
    );
    break;
}
