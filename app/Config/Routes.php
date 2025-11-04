<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Page::index');

$routes->get('/about', 'Page::about');

$routes->get('articles', 'Article::index');
$routes->get('articles/list_article', 'Article::index');
$routes->get('articles/show/(:num)', 'Article::show/$1');
$routes->get('articles/(:segment)', 'Article::show/$1');
$routes->resource('articles');
$routes->get('search', 'Search::index');

$routes->match(['GET', 'POST'], 'contact', 'Page::contact');
// Admin routes (protected by auth filter)
$routes->group('admin', ['namespace'=> 'App\Controllers\Admin', 'filter' => 'auth'], function($routes){
    $routes->get('dashboard','Dashboard::index');
    
    // Feedback routes
    $routes->get('feedback','Feedback::index');
    $routes->get('feedback/delete/(:num)','Feedback::delete/$1');
    
    // Post routes
    $routes->get('post','Post::index');
    $routes->match(['GET', 'POST'], 'post/new','Post::new');
    $routes->match(['GET', 'POST'], 'post/edit/(:segment)','Post::edit/$1');
    $routes->get('post/delete/(:segment)','Post::delete/$1');
    
    // Setting routes
    $routes->get('setting','Setting::index');
    $routes->get('setting/edit_profile','Setting::edit_profile');
    $routes->get('setting/edit_password','Setting::edit_password');
    $routes->get('setting/upload_avatar','Setting::upload_avatar');
    $routes->get('setting/remove_avatar','Setting::remove_avatar');
    $routes->post('setting/update','Setting::update');
    $routes->post('setting/uploadAvatar','Setting::uploadAvatar');
    $routes->post('setting/removeAvatar','Setting::removeAvatar');
    $routes->post('setting/updateProfile','Setting::updateProfile');
    $routes->post('setting/updatePassword','Setting::updatePassword');
});

// Authentication routes
$routes->match(['GET', 'POST'], 'login', 'Auth::login');
$routes->match(['GET', 'POST'], 'register', 'Auth::register');
$routes->get('logout', 'Auth::logout');
$routes->get('auth/check', 'Auth::checkAuth');
$routes->get('auth/check', 'Auth::checkAuth');




