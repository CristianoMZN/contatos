<?php

/**
 * Web Routes
 * Define all application routes here
 */

use App\Core\App;

$app = App::getInstance();
$router = $app->get('router');

// Public routes
$router->get('/', 'HomeController@index');
$router->get('/contatos', 'ContactController@index');
$router->get('/contato/{slug}', 'ContactController@show');

// Authentication routes
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@showRegister');
$router->post('/register', 'AuthController@register');
$router->post('/logout', 'AuthController@logout');
$router->get('/forgot-password', 'AuthController@showForgotPassword');
$router->post('/forgot-password', 'AuthController@forgotPassword');

// Dashboard (authenticated users)
$router->get('/dashboard', 'DashboardController@index', ['Auth']);

// Contact management (authenticated users)
$router->get('/contacts/create', 'ContactController@create', ['Auth']);
$router->post('/contacts', 'ContactController@store', ['Auth']);
$router->get('/contacts/{slug}/edit', 'ContactController@edit', ['Auth']);
$router->post('/contacts/{slug}', 'ContactController@update', ['Auth']);
$router->post('/contacts/{slug}/delete', 'ContactController@destroy', ['Auth']);

// API routes for AJAX
$router->get('/api/contacts', 'ApiController@contacts');
$router->get('/api/contacts/search', 'ApiController@searchContacts');

// SEO routes
$router->get('/sitemap.xml', 'SeoController@sitemap');

// Admin routes (if needed later)
// $router->get('/admin', 'AdminController@index', ['Auth', 'Admin']);