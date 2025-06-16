<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Login::login');
$routes->post('auth', 'Login::auth');

$routes->get('register', 'Register::index');
$routes->post('proses', 'Register::proses');

$routes->get('dashboard', 'Dashboard::index');

$routes->get('ptc', 'Ptc::index');

$routes->get('logout', 'Logout::proses');