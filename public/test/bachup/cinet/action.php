<?php
/*Commenter ses deux lines si vous êtes en production
error_reporting(E_ALL);
ini_set('display_errors', 1);*/

// required libs
require_once __DIR__ . '/src/cinetpay.php';
include('marchand.php');
include('commande.php');


// $marchand = array(
//     "apikey" => "130463917365f879e9284415.37748505", // Enrer votre apikey
//     "site_id" => "5883560", //Entrer votre site_ID
//     "secret_key" => "aweerreretete45434123124!" //Entrer votre clé secret
// );

// La class gère la table "Commande"( A titre d'exemple)
$commande = new Commande();


 function generate_uuid() {
        return sprintf('%04x%04x%04x%04x%04x%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

try {
    if(isset($_POST['data'])) {
        $data = (array) json_decode( $_POST['data']);
    } else {
        $data = $_POST;
    }

    
    
    $customer_name = !isset($data['customer_name'])? 'USER' : $data['customer_name'];
            $customer_surname = !isset($data['customer_surname'])? 'TEST' : $data['customer_surname'];
            $description = !isset($data['description'])? '-' : $data['description'];
            $amount = !isset($data['amount'])? 10 : $data['amount'];
            $currency = !isset($data['currency'])? 'USD' : $data['currency'];
            
            //transaction id
            $id_transaction = generate_uuid(); // or $id_transaction = Cinetpay::generateTransId()

            //Veuillez entrer votre apiKey
            $apikey =  $marchand["apikey"];
            //Veuillez entrer votre siteId
            $site_id = $marchand["site_id"];

            //notify url
            $notify_url = $commande->getCurrentUrl().'cinetpay-sdk-php/notify/notify.php';
            //return url
            $return_url = $commande->getCurrentUrl().'cinetpay-sdk-php/return/return.php';
            $channels = "MOBILE_MONEY";
            
            /*information supplémentaire que vous voulez afficher
             sur la facture de CinetPay(Supporte trois variables 
             que vous nommez à votre convenance)*/
            $invoice_data = array();





            $customer_email = !isset($data['customer_email'])? 'user@example.com' : $data['customer_email'] ;// "customer_email" => "ambika@gmail.com", //l'email du client
            $customer_phone_number = !isset($data['customer_phone_number'])? '+2430858298122' : $data['customer_phone_number'] ;//     "customer_phone_number" => "+2430858298122", //Le numéro de téléphone du client
            $lock_phone_number = !isset($data['lock_phone_number'])? false : $data['lock_phone_number'] ;//     "lock_phone_number" => false , //Le numéro de téléphone du client
            
            $customer_address = !isset($data['customer_address'])? 'NO' : $data['customer_address'] ;//     "customer_address" => "HERE", //l'adresse du client
            
            $customer_city = !isset($data['customer_city'])? 'BK' : $data['customer_city'] ;//     "customer_city" => "HERE", // ville du client
            
            $customer_country = !isset($data['customer_country'])? 'CD' : $data['customer_country'] ;//     "customer_country" => "CD",//Le pays du client, la valeur à envoyer est le code ISO du pays (code à deux chiffre) ex : CI, BF, US, CA, FR
            
            $customer_state = !isset($data['customer_state'])? 'CD' : $data['customer_state'] ;//    "customer_state" => "CD", //L’état dans de la quel se trouve le client. Cette valeur est obligatoire si le client se trouve au États Unis d’Amérique (US) ou au Canada (CA)
            
            $customer_zip_code = !isset($data['customer_zip_code'])? 'ZIPCODE' : $data['customer_zip_code'] ;//     "customer_zip_code" => "TEXT" //Le code postal du client 

  
    
   
    //transaction id
    $id_transaction = date("YmdHis"); // or $id_transaction = Cinetpay::generateTransId()

    //Veuillez entrer votre apiKey
    $apikey = $marchand["apikey"];
    //Veuillez entrer votre siteId
    $site_id = $marchand["site_id"];

    //notify url
    $notify_url = $commande->getCurrentUrl().'cinetpay-sdk-php/notify/notify.php';
    //return url
    $return_url = $commande->getCurrentUrl().'cinetpay-sdk-php/return/return.php';
    $channels = "MOBILE_MONEY";
    
    /*information supplémentaire que vous voulez afficher
     sur la facture de CinetPay(Supporte trois variables 
     que vous nommez à votre convenance)*/
    $invoice_data = array(
        "Data 1" => "",
        "Data 2" => "",
        "Data 3" => ""
    );

    //
    $formData = array(
        "transaction_id"=> $id_transaction,
        "amount"=> $amount,
        "currency"=> "USD",
        "customer_surname"=> $customer_surname,
        "customer_name"=> $customer_name,
        "description"=> $description,
        "notify_url" => $notify_url,
        "return_url" => $return_url,
        "channels" => $channels,
        "invoice_data" => $invoice_data,
        //pour afficher le paiement par carte de credit
        "customer_email" => $customer_email, //l'email du client
        "customer_phone_number" => $customer_phone_number, //Le numéro de téléphone du client
        "lock_phone_number" => false , //Le numéro de téléphone du client
        "customer_address" => $customer_address, //l'adresse du client
        "customer_city" => $customer_city, // ville du client
        "customer_country" => $customer_country,//Le pays du client, la valeur à envoyer est le code ISO du pays (code à deux chiffre) ex : CI, BF, US, CA, FR
        "customer_state" => $customer_state, //L’état dans de la quel se trouve le client. Cette valeur est obligatoire si le client se trouve au États Unis d’Amérique (US) ou au Canada (CA)
        "customer_zip_code" => $customer_zip_code //Le code postal du client 
    );
    // enregistrer la transaction dans votre base de donnée
    /*  $commande->create(); */

    $CinetPay = new CinetPay($site_id, $apikey , $VerifySsl=false);//$VerifySsl=true <=> Pour activerr la verification ssl sur curl 
    $result = $CinetPay->generatePaymentLink($formData);

    if ($result["code"] == '201')
    {
        $url = $result["data"]["payment_url"];

     

        // ajouter le token à la transaction enregistré
        /* $commande->update(); */
        // //redirection vers l'url de paiement
        // $ch = curl_init($url);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // À utiliser aussi
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Important pour récupérer le résultat
        // $response = curl_exec($ch);
        
        header('Location:'. $url);
        echo    $url    ;

    }
} catch (Exception $e) {
    echo $e->getMessage();
}

