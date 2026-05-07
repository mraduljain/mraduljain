<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| Update hostname, username, password, database as per your setup.
*/

$active_group = 'default';
$query_builder = TRUE;

$db['default'] = [
    'dsn'          => '',
    'hostname'     => 'localhost',
    'username'     => 'root',        // <-- Change this
    'password'     => '',            // <-- Change this
    'database'     => 'shop_db',
    'dbdriver'     => 'mysqli',
    'dbprefix'     => '',
    'pconnect'     => FALSE,
    'db_debug'     => (ENVIRONMENT !== 'production'),
    'cache_on'     => FALSE,
    'cachedir'     => '',
    'char_set'     => 'utf8mb4',
    'dbcollat'     => 'utf8mb4_unicode_ci',
    'swap_pre'     => '',
    'encrypt'      => FALSE,
    'compress'     => FALSE,
    'stricton'     => FALSE,
    'failover'     => [],
    'save_queries' => TRUE,
];