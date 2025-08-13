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
$routes->get('/', 'Home::index');
$routes->get('/test', 'Home::test');
$routes->get('/admin', 'Home::admin');
$routes->get('/gestion-agent', 'Home::gestionAgent');
$routes->get('/require-login', 'Home::requireLogin');
$routes->get('/require-permission', 'Home::requirePermission');

// override get produit to use custom filters to not allow hidden products to apprear
$routes->get('/produit', 'MyProduitController::index');
$routes->post('/adresse', 'MyAdresseController::save');


$routes->post('auth/register', 'MyAuthController::register');
$routes->post('auth/activate', 'MyAuthController::activateUser');
$routes->post('auth/login', 'MyAuthController::login');
$routes->get('auth/logout', 'MyAuthController::logout');
$routes->post('upload/image', 'AjaxFileUpload::upload');
$routes->get('initialinfoclient/(:any)', 'MyAuthController::getAllClientInitialData/$1');

// Client device
$routes->post('saveclientdevice', 'MyAuthController::saveClientDevice');
$routes->get('saveclientdevice', 'MyAuthController::saveClientDevice');

//Money Transfert
$routes->post('transaction/rechargerclient', 'MyTransationController::transfertAgentToClient');

// Agent
$routes->post('initialinfoagent/(:any)', 'MyAuthController::getAllAgentInitialData/$1');
$routes->get('initialinfoagent/(:any)', 'MyAuthController::getAllAgentInitialData/$1');



$routes->post('auth/verify-otp', 'MyAuthController::validateRegistrationOTP');

//HELPER ROUTES
$routes->post('search', 'MySearchController::index');
$routes->get('search', 'MySearchController::index');

//PAYER cinet ROUTES
$routes->post('payer', 'MyPayController::index');
$routes->get('payer', 'MyPayController::index');
$routes->post('payer/notify', 'MyPayController::notifyCinet');
$routes->get('payer/notify', 'MyPayController::notifyCinet');
$routes->get('payer/check/(:any)', 'MyPayController::checkCommande/$1');


$routes->post('payer/savesmstransid', 'MyTransationController::saveSMSTransactionId');
$routes->post('payer/reclamertransid', 'MyPayController::reclamerTransactionId');

//routes kdelivery
$routes->get('livraisonencours', 'ControlleurLivreur::getLivraisonsEnAttente/$1');
$routes->post('affecterLivraison', 'ControlleurLivreur::affecterLivraison/$1');
$routes->get('livreur/toutes/(:any)', 'ControlleurLivreur::getAllLivraisons/$1');
$routes->post('livreur/toutes/(:any)', 'ControlleurLivreur::getAllLivraisons/$1');


//commandes
$routes->get('livraison/toutes', 'ControlleurLivreur::getAllLivraisons');
$routes->post('livraison/toutes', 'ControlleurLivreur::getAllLivraisons');

$routes->get('livraison/affecter', 'ControlleurLivreur::affecterLivraison');
$routes->post('livraison/affecter', 'ControlleurLivreur::affecterLivraison');

$routes->get('commandes/toutes', 'ControlleurLivreur::getAllCommandes');
$routes->post('commandes/toutes', 'ControlleurLivreur::getAllCommandes');



//conversations

$routes->get('livreur/conversations', 'ControlleurLivreur::getConversations');
$routes->post('livreur/conversations', 'ControlleurLivreur::getConversations');


$routes->get('livreur/conversations/(:any)', 'ControlleurLivreur::hydrateMessage/$1');
$routes->post('livreur/conversations/(:any)', 'ControlleurLivreur::hydrateMessage/$1');

$routes->get('livreur/readmessage/(:any)', 'ControlleurLivreur::readMessage/$1');
$routes->post('livreur/readmessage/(:any)', 'ControlleurLivreur::readMessage/$1');

$routes->get('client/readmessage/(:any)', 'ControlleurLivreur::readMessageClient/$1');
$routes->post('client/readmessage/(:any)', 'ControlleurLivreur::readMessageClient/$1');



// ADD FILTER FOR NOTIFICATION
$routes -> post('message','Message::create', ['filter' => 'notify-message']);




// COMMANDER SAVE ROUTES 
$routes -> post('save/commande', 'MyTransationController::saveCommande');
$routes -> post('payercashpower', 'MyTransationController::saveCashPower');




// ADDING ROUTES TO RESSOURCE CONTROLLER FOR TABLE COMPTE
  $routes->resource('compte', [
    'controller' => 'Compte', 
    'placeholder' => '(:num)', 
    'websafe' => 1,
    'except' => 'new,edit'
  ]);

  // ADDING ROUTES TO RESSOURCE CONTROLLER FOR TABLE CAISSE
  $routes->resource('caisse', [
    'controller' => 'Caisse', 
    'placeholder' => '(:num)', 
    'websafe' => 1,
    'except' => 'new,edit'
  ]);

  // ADDING ROUTES TO RESSOURCE CONTROLLER FOR TABLE INFORMATIONPAIEMENT
  $routes->resource('informationpaiement', [
    'controller' => 'InformationPaiement', 
    'placeholder' => '(:num)', 
    'websafe' => 1,
    'except' => 'new,edit'
  ]);

  // ADDING ROUTES TO RESSOURCE CONTROLLER FOR TABLE OPERATION
  $routes->resource('operation', [
    'controller' => 'Operation', 
    'placeholder' => '(:num)', 
    'websafe' => 1,
    'except' => 'new,edit'
  ]);

  // ADDING ROUTES TO RESSOURCE CONTROLLER FOR TABLE LIGNEOPERATION
  $routes->resource('ligneoperation', [
    'controller' => 'LigneOperation', 
    'placeholder' => '(:num)', 
    'websafe' => 1,
    'except' => 'new,edit'
  ]);

  // ADDING ROUTES TO RESSOURCE CONTROLLER FOR TABLE TRANSACTIONCINETPAY
  $routes->resource('transactioncinetpay', [
    'controller' => 'TransactionCinetpay', 
    'placeholder' => '(:num)', 
    'websafe' => 1,
    'except' => 'new,edit'
  ]);

  // ADDING ROUTES TO RESSOURCE CONTROLLER FOR TABLE OTPSENDERDEVICE
  $routes->resource('otpsenderdevice', [
    'controller' => 'OtpSenderDevice', 
    'placeholder' => '(:num)', 
    'websafe' => 1,
    'except' => 'new,edit'
  ]);

  // ADDING ROUTES TO RESSOURCE CONTROLLER FOR TABLE OPERATIONCOMMANDE
  $routes->resource('operationcommande', [
    'controller' => 'OperationCommande', 
    'placeholder' => '(:num)', 
    'websafe' => 1,
    'except' => 'new,edit'
  ]);

  // ADDING ROUTES TO RESSOURCE CONTROLLER FOR TABLE OPERATIONACHAT
  $routes->resource('operationachat', [
    'controller' => 'OperationAchat', 
    'placeholder' => '(:num)', 
    'websafe' => 1,
    'except' => 'new,edit'
  ]);

  // ADDING ROUTES TO RESSOURCE CONTROLLER FOR TABLE PRODUIT
  $routes->resource('produit', [
    'controller' => 'Produit', 
    'placeholder' => '(:num)', 
    'websafe' => 1,
    'except' => 'new,edit'
  ]);

  // ADDING ROUTES TO RESSOURCE CONTROLLER FOR TABLE CATEGORIEPROD
  $routes->resource('categorieprod', [
    'controller' => 'CategorieProd', 
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

  // ADDING ROUTES TO RESSOURCE CONTROLLER FOR TABLE PARAMETRE
  $routes->resource('parametre', [
    'controller' => 'Parametre', 
    'placeholder' => '(:num)', 
    'websafe' => 1,
    'except' => 'new,edit'
  ]);

  // ADDING ROUTES TO RESSOURCE CONTROLLER FOR TABLE ADRESSE
  $routes->resource('adresse', [
    'controller' => 'Adresse', 
    'placeholder' => '(:num)', 
    'websafe' => 1,
    'except' => 'new,edit'
  ]);

  // ADDING ROUTES TO RESSOURCE CONTROLLER FOR TABLE COMMANDE
  $routes->resource('commande', [
    'controller' => 'Commande', 
    'placeholder' => '(:num)', 
    'websafe' => 1,
    'except' => 'new,edit'
  ]);

  // ADDING ROUTES TO RESSOURCE CONTROLLER FOR TABLE LIVRAISON
  $routes->resource('livraison', [
    'controller' => 'Livraison', 
    'placeholder' => '(:num)', 
    'websafe' => 1,
    'except' => 'new,edit'
  ]);

  // ADDING ROUTES TO RESSOURCE CONTROLLER FOR TABLE LIGNECOMMANDE
  $routes->resource('lignecommande', [
    'controller' => 'LigneCommande', 
    'placeholder' => '(:num)', 
    'websafe' => 1,
    'except' => 'new,edit'
  ]);

  // ADDING ROUTES TO RESSOURCE CONTROLLER FOR TABLE CLIENT
  $routes->resource('client', [
    'controller' => 'Client', 
    'placeholder' => '(:num)', 
    'websafe' => 1,
    'except' => 'new,edit'
  ]);

  // ADDING ROUTES TO RESSOURCE CONTROLLER FOR TABLE AGENT
  $routes->resource('agent', [
    'controller' => 'Agent', 
    'placeholder' => '(:num)', 
    'websafe' => 1,
    'except' => 'new,edit'
  ]);

  // ADDING ROUTES TO RESSOURCE CONTROLLER FOR TABLE ACHAT
  $routes->resource('achat', [
    'controller' => 'Achat', 
    'placeholder' => '(:num)', 
    'websafe' => 1,
    'except' => 'new,edit'
  ]);

  // ADDING ROUTES TO RESSOURCE CONTROLLER FOR TABLE LIGNEACHAT
  $routes->resource('ligneachat', [
    'controller' => 'LigneAchat', 
    'placeholder' => '(:num)', 
    'websafe' => 1,
    'except' => 'new,edit'
  ]);

  // ADDING ROUTES TO RESSOURCE CONTROLLER FOR TABLE FOURNISSEUR
  $routes->resource('fournisseur', [
    'controller' => 'Fournisseur', 
    'placeholder' => '(:num)', 
    'websafe' => 1,
    'except' => 'new,edit'
  ]);

  // ADDING ROUTES TO RESSOURCE CONTROLLER FOR TABLE MESSAGE
  $routes->resource('message', [
    'controller' => 'Message', 
    'placeholder' => '(:num)', 
    'websafe' => 1,
    'except' => 'new,edit'
  ]);

  // ADDING ROUTES TO RESSOURCE CONTROLLER FOR TABLE OTP
  $routes->resource('otp', [
    'controller' => 'Otp', 
    'placeholder' => '(:num)', 
    'websafe' => 1,
    'except' => 'new,edit'
  ]);

  // ADDING ROUTES TO RESSOURCE CONTROLLER FOR TABLE PUBLICITE
  $routes->resource('publicite', [
    'controller' => 'Publicite', 
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

  // ADDING ROUTES TO RESSOURCE CONTROLLER FOR TABLE DROITS
  $routes->resource('droits', [
    'controller' => 'Droits', 
    'placeholder' => '(:num)', 
    'websafe' => 1,
    'except' => 'new,edit'
  ]);

  // ADDING ROUTES TO RESSOURCE CONTROLLER FOR TABLE DROITSAGENT
  $routes->resource('droitsagent', [
    'controller' => 'DroitsAgent', 
    'placeholder' => '(:num)', 
    'websafe' => 1,
    'except' => 'new,edit'
  ]);

  // ADDING ROUTES TO RESSOURCE CONTROLLER FOR TABLE ZONECOUVERTURE
  $routes->resource('zonecouverture', [
    'controller' => 'ZoneCouverture', 
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


