<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index');
$routes->get('/about', 'About::index');
$routes->get('/services', 'Services::index');
$routes->match(['get', 'post'], '/contact', 'Contact::index');
$routes->get('/register', 'Register::index');
$routes->post('/register', 'Register::create');

$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::attempt');

$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/dashboard', 'CustomerAccounts::index');
    $routes->get('/customers/new', 'CustomerAccounts::new');
    $routes->post('/customers', 'CustomerAccounts::create');
    $routes->get('/customers/(:num)', 'CustomerAccounts::show/$1');
    $routes->get('/customers/(:num)/edit', 'CustomerAccounts::edit/$1');
    $routes->post('/customers/(:num)/update', 'CustomerAccounts::update/$1');
    $routes->post('/customers/(:num)/delete', 'CustomerAccounts::delete/$1');
    $routes->post('/logout', 'Auth::logout');
});
