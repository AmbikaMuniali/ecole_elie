<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::admin');
$routes->get('/test', 'Home::test');
$routes->get('/admin', 'Home::admin');
$routes->get('/gestion-agent', 'Home::gestionAgent');
$routes->get('/require-login', 'Home::requireLogin');
$routes->get('/require-permission', 'Home::requirePermission');

$routes->post('auth/register', 'MyAuthController::register');
$routes->post('auth/activate', 'MyAuthController::activateUser');
$routes->post('auth/login', 'MyAuthController::login');
$routes->get('auth/logout', 'MyAuthController::logout');
$routes->post('upload/image', 'AjaxFileUpload::upload');
$routes->get('initialinfoclient/(:any)', 'MyAuthController::getAllClientInitialData/$1');

//HELPER ROUTES
$routes->post('search', 'MySearchController::index');
$routes->get('search', 'MySearchController::index');














  // ADDING ROUTES TO RESSOURCE CONTROLLER FOR TABLE CLASSELOGIQUE
  $routes->resource('classelogique', [
    'controller' => 'ClasseLogique', 
    'placeholder' => '(:num)', 
    'websafe' => 1,
    'except' => 'new,edit'
  ]);

  // ADDING ROUTES TO RESSOURCE CONTROLLER FOR TABLE CLASSE
  $routes->resource('classe', [
    'controller' => 'Classe', 
    'placeholder' => '(:num)', 
    'websafe' => 1,
    'except' => 'new,edit'
  ]);

  // ADDING ROUTES TO RESSOURCE CONTROLLER FOR TABLE ANNEESCOLAIRE
  $routes->resource('anneescolaire', [
    'controller' => 'AnneeScolaire', 
    'placeholder' => '(:num)', 
    'websafe' => 1,
    'except' => 'new,edit'
  ]);

  // ADDING ROUTES TO RESSOURCE CONTROLLER FOR TABLE ELEVE
  $routes->resource('eleve', [
    'controller' => 'Eleve', 
    'placeholder' => '(:num)', 
    'websafe' => 1,
    'except' => 'new,edit'
  ]);

  // ADDING ROUTES TO RESSOURCE CONTROLLER FOR TABLE USER
  $routes->resource('user', [
    'controller' => 'User', 
    'placeholder' => '(:num)', 
    'websafe' => 1,
    'except' => 'new,edit'
  ]);

  // ADDING ROUTES TO RESSOURCE CONTROLLER FOR TABLE MODULE
  $routes->resource('module', [
    'controller' => 'Module', 
    'placeholder' => '(:num)', 
    'websafe' => 1,
    'except' => 'new,edit'
  ]);

  // ADDING ROUTES TO RESSOURCE CONTROLLER FOR TABLE PERMISSION
  $routes->resource('permission', [
    'controller' => 'Permission', 
    'placeholder' => '(:num)', 
    'websafe' => 1,
    'except' => 'new,edit'
  ]);

  // ADDING ROUTES TO RESSOURCE CONTROLLER FOR TABLE USERPERMISSION
  $routes->resource('userpermission', [
    'controller' => 'UserPermission', 
    'placeholder' => '(:num)', 
    'websafe' => 1,
    'except' => 'new,edit'
  ]);

  // ADDING ROUTES TO RESSOURCE CONTROLLER FOR TABLE ELEVECLASSEANNEE
  $routes->resource('eleveclasseannee', [
    'controller' => 'EleveClasseAnnee', 
    'placeholder' => '(:num)', 
    'websafe' => 1,
    'except' => 'new,edit'
  ]);

  // ADDING ROUTES TO RESSOURCE CONTROLLER FOR TABLE COURS
  $routes->resource('cours', [
    'controller' => 'Cours', 
    'placeholder' => '(:num)', 
    'websafe' => 1,
    'except' => 'new,edit'
  ]);

  // ADDING ROUTES TO RESSOURCE CONTROLLER FOR TABLE DEPENSE
  $routes->resource('depense', [
    'controller' => 'Depense', 
    'placeholder' => '(:num)', 
    'websafe' => 1,
    'except' => 'new,edit'
  ]);

  // ADDING ROUTES TO RESSOURCE CONTROLLER FOR TABLE FRAIS
  $routes->resource('frais', [
    'controller' => 'Frais', 
    'placeholder' => '(:num)', 
    'websafe' => 1,
    'except' => 'new,edit'
  ]);

  // ADDING ROUTES TO RESSOURCE CONTROLLER FOR TABLE FRAISCLASSEANNEE
  $routes->resource('fraisclasseannee', [
    'controller' => 'FraisClasseAnnee', 
    'placeholder' => '(:num)', 
    'websafe' => 1,
    'except' => 'new,edit'
  ]);

  // ADDING ROUTES TO RESSOURCE CONTROLLER FOR TABLE PAIEMENT
  $routes->resource('paiement', [
    'controller' => 'Paiement', 
    'placeholder' => '(:num)', 
    'websafe' => 1,
    'except' => 'new,edit'
  ]);










/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}


