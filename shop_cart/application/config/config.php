<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Base Site URL
|--------------------------------------------------------------------------
| Set this to your full URL including a trailing slash.
| e.g. http://localhost/shop_cart/
*/
/*
 * Auto-detect base URL so it works whether browser hits localhost or 127.0.0.1.
 * Falls back to localhost if running from CLI.
 */
$config['base_url'] = (isset($_SERVER['HTTP_HOST'])
    ? 'http://' . $_SERVER['HTTP_HOST'] . '/shop_cart/'
    : 'http://localhost/shop_cart/');

/*
|--------------------------------------------------------------------------
| Index File
|--------------------------------------------------------------------------
| Set to empty string if you use mod_rewrite / .htaccess to remove index.php
*/
$config['index_page'] = '';

/*
|--------------------------------------------------------------------------
| URI PROTOCOL
|--------------------------------------------------------------------------
*/
$config['uri_protocol'] = 'REQUEST_URI';

/*
|--------------------------------------------------------------------------
| URL suffix
|--------------------------------------------------------------------------
*/
$config['url_suffix'] = '';

/*
|--------------------------------------------------------------------------
| Default Language
|--------------------------------------------------------------------------
*/
$config['language'] = 'english';

/*
|--------------------------------------------------------------------------
| Default Character Set
|--------------------------------------------------------------------------
*/
$config['charset'] = 'UTF-8';

/*
|--------------------------------------------------------------------------
| Enable/Disable System Hooks
|--------------------------------------------------------------------------
*/
$config['enable_hooks'] = FALSE;

/*
|--------------------------------------------------------------------------
| Class Extension Prefix
|--------------------------------------------------------------------------
*/
$config['subclass_prefix'] = 'MY_';

/*
|--------------------------------------------------------------------------
| Composer auto-loading
|--------------------------------------------------------------------------
*/
$config['composer_autoload'] = FALSE;

/*
|--------------------------------------------------------------------------
| Allowed URL Characters
|--------------------------------------------------------------------------
*/
$config['permitted_uri_chars'] = 'a-z 0-9~%.:_\-';

/*
|--------------------------------------------------------------------------
| Enable Query Strings
|--------------------------------------------------------------------------
*/
$config['enable_query_strings'] = FALSE;
$config['controller_trigger']   = 'c';
$config['function_trigger']     = 'm';
$config['directory_trigger']    = 'd';

/*
|--------------------------------------------------------------------------
| Error Logging
|--------------------------------------------------------------------------
*/
$config['log_threshold'] = 1;
$config['log_path']      = '';
$config['log_file_extension'] = '';
$config['log_file_permissions'] = 0644;
$config['log_date_format'] = 'Y-m-d H:i:s';

/*
|--------------------------------------------------------------------------
| Date Format for Logs
|--------------------------------------------------------------------------
*/
$config['date_format'] = 'Y-m-d';

/*
|--------------------------------------------------------------------------
| Cache
|--------------------------------------------------------------------------
*/
$config['cache_path']            = '';
$config['cache_query_string']    = FALSE;

/*
|--------------------------------------------------------------------------
| Encryption Key — REQUIRED for sessions
| Generate a 32-char random string
|--------------------------------------------------------------------------
*/
$config['encryption_key'] = 'sH3k9pQmR7tLwXvN2cYjBdZuAoFgEiKl';

/*
|--------------------------------------------------------------------------
| Session Variables
|--------------------------------------------------------------------------
*/
$config['sess_driver']            = 'files';     // Use 'files' — safest for XAMPP
$config['sess_cookie_name']       = 'shopcart_session';
$config['sess_expiration']        = 7200;
$config['sess_save_path']         = NULL;        // NULL = system temp dir
$config['sess_match_ip']          = FALSE;
$config['sess_time_to_update']    = 300;
$config['sess_regenerate_destroy'] = FALSE;

/*
|--------------------------------------------------------------------------
| Cookie Related Variables
|--------------------------------------------------------------------------
*/
$config['cookie_prefix']   = '';
$config['cookie_domain']   = '';
$config['cookie_path']     = '/';
$config['cookie_secure']   = FALSE;
$config['cookie_httponly']  = FALSE;

/*
|--------------------------------------------------------------------------
| Standardize newlines
|--------------------------------------------------------------------------
*/
$config['standardize_newlines'] = FALSE;

/*
|--------------------------------------------------------------------------
| Global XSS Filtering
|--------------------------------------------------------------------------
*/
$config['global_xss_filtering'] = FALSE;

/*
|--------------------------------------------------------------------------
| Cross Site Request Forgery
|--------------------------------------------------------------------------
*/
$config['csrf_protection']   = FALSE;
$config['csrf_token_name']   = 'csrf_test_name';
$config['csrf_cookie_name']  = 'csrf_cookie_name';
$config['csrf_expire']       = 7200;
$config['csrf_regenerate']   = TRUE;
$config['csrf_exclude_uris'] = [];

/*
|--------------------------------------------------------------------------
| Output Compression
|--------------------------------------------------------------------------
*/
$config['compress_output']         = FALSE;
$config['time_reference']          = 'local';
$config['rewrite_short_tags']      = FALSE;
$config['error_views_path']        = '';
$config['reverse_proxy_ips']       = '';
$config['proxy_ips']               = '';