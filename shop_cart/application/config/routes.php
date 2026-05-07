<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'Shop';
$route['404_override']       = '';
$route['translate_uri_dashes'] = FALSE;

// Shop routes
$route['cart']              = 'Shop/cart';
$route['cart/add']          = 'Shop/add_to_cart';
$route['cart/update']       = 'Shop/update_cart';
$route['cart/remove/(:num)'] = 'Shop/remove_from_cart/$1';
$route['checkout']          = 'Shop/checkout';
$route['place-order']       = 'Shop/place_order';
$route['order-success/(:any)'] = 'Shop/order_success/$1';