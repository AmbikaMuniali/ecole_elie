<?php

    $curl = curl_init();

    $token = isset($_POST['token'])? $_POST['token'] : "";

    if(empty($token)) exit();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api-checkout.cinetpay.com/v2/payment/check',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>'{
         "token":"e56e910ba75c7d1d768dcf2ebf13d3878e04338466525ab1de5a10944db1eec5fcd72622df8d9fe1cb33bdd7274d150a5d7438be816908", 
         "site_id": "58835605883560",
         "apikey": "130463917365f879e9284415.37748505" 
    }',
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json'
      ),
    ));

    $response = curl_exec($curl);

    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
      echo $err;
      //throw new Exception("Error :" . $err);
    } 
    else{
     $res = json_decode($response,true);
       print_r($res);
    } 