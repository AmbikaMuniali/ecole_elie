<?php
namespace App\Libraries;
use App\Libraries\Cinet\CinetPay;
use Exception;

// Assuming the CinetPay library is in the 'src' directory relative to this file

require_once __DIR__ . '/cinet/src/cinetpay.php';
class Action {

    private $marchand = array(
    "apikey" => "130463917365f879e9284415.37748505", // Enrer votre apikey
    "site_id" => "5883560", //Entrer votre site_ID
    "secret_key" => "205785473767424c9561bae2.69980730" //Entrer votre clé secret
);
    private $cinetPay;
    private $data;

    public function __construct($data) {

        $this -> data = $data;
        

        try {
            $this->cinetPay = new CinetPay($this->marchand['site_id'], $this->marchand['apikey'], true); // Enable SSL
        } catch (Exception $e) {
            error_log("CinetPay Initialization Error: " . $e->getMessage());
            throw new Exception("CinetPay Initialization failed.");
        }
    }

    public function generatePaymentLink() {
        
        $postData = $this -> data;

         $base_url = base_url();
         $notify_url    = $base_url . '/payer/notify';
         $return_url    = $base_url;

         $idClient = isset($postData['id_client']) ? $postData['id_client'] : 1;


         $transaction_id = "KSK" . date('YmdHis') . random_int(1000,9999);

        $formData = [

            'transaction_id' => $transaction_id,
            "amount" => isset($postData['amount']) ? $postData['amount'] : 1,
            "currency" => isset($postData['currency']) ? $postData['currency'] : 'USD',
            "customer_surname" => isset($postData['customer_surname']) ? $postData['customer_surname'] : 'Doe',
            "customer_name" => isset($postData['customer_name']) ? $postData['customer_name'] : 'John',
            "description" => isset($postData['description']) ? $postData['description'] : 'Achat Marchandise chez Kasoko\'o',


            'notify_url' => $notify_url,
            'return_url' => $return_url,
            'channels' => 'MOBILE_MONEY',

            "customer_email" => isset($postData['customer_email']) ? $postData['customer_email'] : 'client.kasokoo@gmail.com',
            "customer_phone_number" => isset($postData['customer_phone_number']) ? $postData['customer_phone_number'] : '+2430850000000',
            "lock_phone_number" => true,//isset($postData['lock_phone_number'])? $postData['lock_phone_number'] : false, 

            "customer_address" => isset($postData['customer_address']) ? $postData['customer_address'] : 'Bukavu',
            "customer_city" => isset($postData['customer_city']) ? $postData['customer_city'] : 'Bukavu',
            "customer_country" => isset($postData['customer_country']) ? $postData['customer_country'] : 'CD',
            "customer_state" => isset($postData['customer_state']) ? $postData['customer_state'] : 'SK',
            "customer_zip_code" => isset($postData['customer_zip_code']) ? $postData['customer_zip_code'] : '0',
            "invoice_data" => isset($postData['invoice_data']) ? $postData['invoice_data'] : ["communication" => "RAS"],
        ];




        $result = null;

        try {
            $result = $this->cinetPay->generatePaymentLink($formData);


            if ($result["code"] == '201') {
                $url = $result["data"]["payment_url"];



                // Add transaction management here (e.g., save to database)
                $model = model('TransactionCinetpayModel');
                $id = $model -> insert([
                    'fkclient' => $idClient,
                    'status' => 'CREATED',
                    'montant' => isset($postData['amount']) ? $postData['amount'] : 1,
                    'devise' => isset($postData['currency']) ? $postData['currency'] : 'CDF',
                    'transaction_id' => $transaction_id,
                    'transaction_token' => $result["data"]["payment_token"],
                    'data_json' => json_encode(["url" => $url, "notify" => $notify_url])
                ]);


                $token = $result["data"]["payment_token"];


                $result = [
                    'payment_url' => $url,
                    'payment_token' => $token,
                    'transaction_id' => $transaction_id,

                ];//
                
                // $result = $model -> findTransactionCinetpayById($result);
            
            } else {
                error_log("CinetPay Payment Error: " . json_encode($result));
                echo "Payment error. Please try again.";
            }
        } catch (Exception $e) {
            print_r($e);
            echo "An unexpected error occurred.";
        }

        return $result;
    }

   
}

// john akre
// +2250749468227