<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// set base url based on environment.
switch ($_SERVER["HTTP_HOST"]) {
case 'localhost':
    $base_url = 'http://localhost/ccta';
    break;
case 'www.qa.vuonsite.com':
    $base_url = 'http://www.qa.vuonsite.com';
    break;
case 'www.vuonsite.com':
    $base_url = 'http://www.vuonsite.com/invoices';

default:
    $base_url = 'http://localhost/ccta';
    break;
}

$config['base_url'] = $base_url;
$config['index_page'] = 'index.php';
$config['uri_protocol']    = 'REQUEST_URI';
$config['url_suffix'] = '';
$config['language']    = 'english';
$config['charset'] = 'UTF-8';
$config['enable_hooks'] = false;
$config['subclass_prefix'] = 'MY_';
$config['composer_autoload'] = false;
$config['permitted_uri_chars'] = 'a-z 0-9~%.:_\-';
$config['enable_query_strings'] = false;
$config['controller_trigger'] = 'c';
$config['function_trigger'] = 'm';
$config['directory_trigger'] = 'd';
$config['allow_get_array'] = true;
$config['log_threshold'] = 4;
$config['log_path'] = '';
$config['log_file_extension'] = '';
$config['log_file_permissions'] = 0644;
$config['log_date_format'] = 'Y-m-d H:i:s';
$config['error_views_path'] = '';
$config['cache_path'] = '';
$config['cache_query_string'] = false;
$config['encryption_key'] = '';
$config['sess_driver'] = 'files';
$config['sess_cookie_name'] = 'ci_session';
$config['sess_expiration'] = 7200;
$config['sess_save_path'] = null;
$config['sess_match_ip'] = false;
$config['sess_time_to_update'] = 300;
$config['sess_regenerate_destroy'] = false;
$config['cookie_prefix']    = '';
$config['cookie_domain']    = '';
$config['cookie_path']        = '/';
$config['cookie_secure']    = false;
$config['cookie_httponly']     = false;
$config['standardize_newlines'] = false;
$config['global_xss_filtering'] = false;
$config['csrf_protection'] = false;
$config['csrf_token_name'] = 'csrf_test_name';
$config['csrf_cookie_name'] = 'csrf_cookie_name';
$config['csrf_expire'] = 7200;
$config['csrf_regenerate'] = true;
$config['csrf_exclude_uris'] = array();
$config['compress_output'] = false;
$config['time_reference'] = 'local';
$config['rewrite_short_tags'] = false;
$config['proxy_ips'] = '';
