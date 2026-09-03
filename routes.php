<?php

use App\Support\Router;

/** @var Router $router */

// ---------------------------------------------------------------- public site
$router->get('/', 'Site\HomeController@index');
$router->get('/work', 'Site\WorkController@index');
$router->get('/work/{slug}', 'Site\WorkController@show');
$router->get('/services', 'Site\ServicesController@index');
$router->get('/about', 'Site\AboutController@index');
$router->get('/contact', 'Site\ContactController@index');
$router->post('/contact', 'Site\ContactController@submit');
$router->post('/api/video-view/{id}', 'Site\HomeController@trackVideo');

// --------------------------------------------------------------- admin: auth
$router->get('/admin/login', 'Admin\AuthController@showLogin');
$router->post('/admin/login', 'Admin\AuthController@login');
$router->get('/admin/logout', 'Admin\AuthController@logout');

// ---------------------------------------------------------- admin: dashboard
$router->get('/admin', 'Admin\DashboardController@index');
$router->get('/admin/dashboard', 'Admin\DashboardController@index');

// ----------------------------------------------------------- admin: projects
$router->get('/admin/projects', 'Admin\ProjectController@index');
$router->get('/admin/projects/create', 'Admin\ProjectController@create');
$router->post('/admin/projects', 'Admin\ProjectController@store');
$router->get('/admin/projects/{id}/edit', 'Admin\ProjectController@edit');
$router->post('/admin/projects/{id}', 'Admin\ProjectController@update');
$router->post('/admin/projects/{id}/delete', 'Admin\ProjectController@destroy');

// ------------------------------------------------------------- admin: videos
$router->get('/admin/videos', 'Admin\VideoController@index');
$router->get('/admin/videos/upload', 'Admin\VideoController@uploadForm');
$router->post('/admin/videos/chunk', 'Admin\VideoController@chunk');
$router->post('/admin/videos', 'Admin\VideoController@store');
$router->get('/admin/videos/{id}/edit', 'Admin\VideoController@edit');
$router->post('/admin/videos/{id}', 'Admin\VideoController@update');
$router->post('/admin/videos/{id}/delete', 'Admin\VideoController@destroy');

// -------------------------------------------------------------- admin: media
$router->get('/admin/media', 'Admin\MediaController@index');
$router->post('/admin/media', 'Admin\MediaController@store');
$router->post('/admin/media/{id}/delete', 'Admin\MediaController@destroy');

// ----------------------------------------------------------- admin: bookings
$router->get('/admin/bookings', 'Admin\BookingController@index');
$router->get('/admin/bookings/create', 'Admin\BookingController@create');
$router->post('/admin/bookings', 'Admin\BookingController@store');
$router->get('/admin/bookings/{id}', 'Admin\BookingController@show');
$router->post('/admin/bookings/{id}', 'Admin\BookingController@update');
$router->post('/admin/bookings/{id}/status', 'Admin\BookingController@setStatus');
$router->post('/admin/bookings/{id}/delete', 'Admin\BookingController@destroy');

// ----------------------------------------------------------- admin: messages
$router->get('/admin/messages', 'Admin\MessageController@index');
$router->post('/admin/messages/{id}/reply', 'Admin\MessageController@reply');
$router->post('/admin/messages/{id}/status', 'Admin\MessageController@setStatus');
$router->post('/admin/messages/{id}/booking', 'Admin\MessageController@convert');
$router->post('/admin/messages/{id}/delete', 'Admin\MessageController@destroy');

// ------------------------------------------------------------ admin: clients
$router->get('/admin/clients', 'Admin\ClientController@index');
$router->post('/admin/clients', 'Admin\ClientController@store');
$router->post('/admin/clients/{id}', 'Admin\ClientController@update');
$router->post('/admin/clients/{id}/delete', 'Admin\ClientController@destroy');

// ------------------------------------------------------------- admin: brands
$router->get('/admin/brands', 'Admin\BrandController@index');
$router->post('/admin/brands', 'Admin\BrandController@store');
$router->post('/admin/brands/{id}', 'Admin\BrandController@update');
$router->post('/admin/brands/{id}/delete', 'Admin\BrandController@destroy');

// ----------------------------------------------------------- admin: packages
$router->get('/admin/packages', 'Admin\PackageController@index');
$router->post('/admin/packages', 'Admin\PackageController@store');
$router->post('/admin/packages/{id}', 'Admin\PackageController@update');
$router->post('/admin/packages/{id}/delete', 'Admin\PackageController@destroy');

// ------------------------------------------------------------ admin: content
$router->get('/admin/content', 'Admin\ContentController@index');
$router->get('/admin/content/{id}/edit', 'Admin\ContentController@edit');
$router->post('/admin/content/{id}', 'Admin\ContentController@update');
$router->post('/admin/content/{id}/restore', 'Admin\ContentController@restore');

// ------------------------------------------- admin: analytics, activity, etc.
$router->get('/admin/analytics', 'Admin\AnalyticsController@index');
$router->get('/admin/activity', 'Admin\ActivityController@index');
$router->get('/admin/settings', 'Admin\SettingController@index');
$router->post('/admin/settings', 'Admin\SettingController@update');
$router->post('/admin/settings/password', 'Admin\SettingController@password');
$router->post('/admin/settings/users', 'Admin\SettingController@storeUser');
