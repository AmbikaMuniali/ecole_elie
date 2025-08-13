<?php
namespace App\Controllers;

use App\Models\AchatModel;
use App\Models\PaiementModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\Action;

use CodeIgniter\Controller;
use Exception;

class MyPayController extends BaseController
{
    /**
     * Get all Achats
     * @return Response
     */
    public function index()
    {

        try
        {

            $action = new Action($this->getRequestInput($this->request));

            $res = $action->generatePaymentLink();

            if (empty($res)) $res = throw new Exception("Error Processing Request", 1);;

            return $this->getResponse(['result' => $res]);

        }
        catch(Exception $e)
        {
            return $this->getResponse(['error' => "Failled to get paymentLink", ResponseInterface::HTTP_BAD_REQUEST]);
        }

    }

    public function checkCommande($token)

    {
        $curl = curl_init();

        if (empty($token)) exit();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api-checkout.cinetpay.com/v2/payment/check',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
           "token": "' . $token . '", 
           "site_id": "58835605883560",
           "apikey": "130463917365f879e9284415.37748505" 
      }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ) ,
        ));

        $response = curl_exec($curl);

        $err = curl_error($curl);
        curl_close($curl);
        if ($err)
        {
            echo $err;
            //throw new Exception("Error :" . $err);
            
        }
        else
        {
            // print_r($response);
            $res = json_decode($response, true);
            // print_r($res);
            
        }

        return $this->getResponse(['message' => $response]);

    }

    public function notifyCinet()
    {

        $data = $this->getRequestInput($this->request);

        $client = '';
        $caisseCinet = '';
        $compteClient = '';
        $solde = '';
        $devise = '';
        $cinetTransaction = '';
        $error1 = '';

        

        $transID = isset($data['cpm_trans_id']) ? $data['cpm_trans_id'] : null;
        $montant = isset($data['cpm_amount']) ? $data['cpm_amount'] : null;
        $devise = isset($data['cpm_currency']) ? $data['cpm_currency'] : null;


        $transData = json_encode(
          [

        
            "used_phone_number" => $transStatus = isset($data['cel_phone_num']) ? $data['cel_phone_num'] : null,
            "transaction_id" => $transStatus = isset($data['cpm_trans_id']) ? $data['cpm_trans_id'] : null,
            "date" => $transStatus = isset($data['cpm_trans_date']) ? $data['cpm_trans_date'] : null,

          ]

        );


        $model = model('TransactionCinetpayModel');
        

        // $model -> insert(['data_json' => json_encode($data), "status" => $error1]);
        $transStatus = isset($data['cpm_error_message']) ? $data['cpm_error_message'] : null;



        if (!empty($transID) && !empty($montant) && !empty($montant) && $transStatus == 'SUCCES')
        {
            // CHERCHER LE CLIENT
            

            // Add transaction management here (e.g., save to database)
            

            $model = model('TransactionCinetpayModel');

            //FIND IN DB FIRST
            $cinetTransaction = $model->search(["where" => ["transaction_id" => $transID]]);

            //FIND IF IT WAS IN DB
            if (!empty($cinetTransaction))
            {

                $idClient = $cinetTransaction[0]['fkclient'];

                //update the cinetransatcion
                $model->update($cinetTransaction[0]['id'], ['status' => $transStatus, 'transaction_id' => "DELETED - $transID"]);

                //TROUVER LE Compte client  ET la caisse a utiliser
                $modelClient = model('ClientModel');
                $client = $modelClient->findClientById($idClient);

                // chercher les comptes du client
                $modelCompte = model('CompteModel');

                $compteClient = $modelCompte->search(['where' => ['fkclient' => $idClient, 'devise' => $devise]]);

                // CAISSE
                $caisseCinet = $modelCompte->search(['where' => ['fkcaisse' => CAISSE_CINETPAY, 'devise' => $devise]]);

                //Caisse
                

                $modelLigneOperation = model('LigneOperationModel');

                if (!empty($client) && !empty($caisseCinet) && !empty($compteClient))
                {

                    $idCpteClient = $compteClient[0]['id'];
                    $idCaisse = $caisseCinet[0]['id'];

                    // CREER L'OPERATION
                    $modelOperation = model('OperationModel');
                    $idOperation = $modelOperation->insert(['type_operation' => 'DEPOT_CLIENT', 'fkuser_create' => ID_USER_SYSTEM, 'libelle' => 'Transaction cinetpay']);

                    // inserer les lignes
                    $modelLigneOperation->insert(['fkcompte' => $idCpteClient, 'transaction_id' => $transID,'transaction_token' => $transData, 'fkoperation' => $idOperation, 'montant' => $montant, 'operation' => 'CREDIT']);

                    $modelLigneOperation->insert(['fkcompte' => $idCaisse, 'fkoperation' => $idOperation, 'montant' => $montant, 'operation' => 'DEBIT']);

                    // mettre jour le client
                    if ($devise == "CDF")
                    {
                        $client->solde_cdf = $client->solde_cdf + $montant;
                    }
                    else
                    {

                        $client->solde_usd = (double)$client->solde_usd + (double)$montant;

                    }


                    $modelClient ->update($client->id, (array)$client);

                } else{
                     $error1 = "client account not found";
                }

            } else{
                 $error1 = "Transaction NOT FOUND $transID";
            }

        } else{
            $error1 = "NO TRANS ID, AMOUNT OR, SUCCES";
        }

        if(!empty($error1)) {
            // code...
            // $model -> insert(['data_json' => json_encode($data), "Error" => $error1]);
        }

        return $this->getResponse(['result' => "ok",

          

        ]);

    }


    public function reclamerTransactionId()
    {



        $client = null;
        $caisseMobileMoney = null;
        $compteClient = null ;
        $solde = null; 
        $devise = null;
        $cinetTransaction = null;

        $data = $this->getRequestInput($this->request);


        

        $transID = isset($data['transaction_id']) ? $data['transaction_id'] : null;
        $idClient = isset($data['client']) ? $data['client'] : null;

        // client
        $modelClient = model('ClientModel');
        $client = null;
        if (!empty($idClient)) $client = $modelClient -> findClientById($idClient);


        


        




        if (!empty($transID) && !empty($client))
        {
            

            $model = model('TransactionCinetpayModel');
            

            //FIND THE TEMPORARY TRANSACTION IN DB FIRST
            $transaction = $model->search(["where" => ["transaction_id" => $transID, "status" => "CREATED", ]]);
            if (!empty($transaction))
            {

                $montant = $transaction[0]['montant'];
                $devise = $transaction[0]['devise'];



                

                // chercher les comptes du client
                $modelCompte = model('CompteModel');

                $compteClient = $modelCompte->search(['where' => ['fkclient' => $client -> id, 'devise' => $devise]]);

                // CAISSE
                $caisseMobileMoney = $modelCompte->search(['where' => ['fkcaisse' => CAISSE_MOBILE_MONEY, 'devise' => $devise]]);

                //Caisse
                

                $modelLigneOperation = model('LigneOperationModel');

                if (!empty($client) && !empty($caisseMobileMoney) && !empty($compteClient))
                {


                    //update the transatcion
                    $model->update($transaction[0]['id'], ['status' => 'SUCCESS', 'transaction_id' => "DELETED - $transID"]);

                    $idCpteClient = $compteClient[0] ['id'];
                    $idCaisse = $caisseMobileMoney[0]  ['id'];

                    // CREER L'OPERATION
                    $modelOperation = model('OperationModel');
                    $idOperation = $modelOperation->insert(['type_operation' => 'DEPOT_CLIENT', 'fkuser_create' => ID_USER_SYSTEM, 'libelle' => 'Transaction cinetpay']);

                    // inserer les lignes
                    $modelLigneOperation->insert(['fkcompte' => $idCpteClient, 'transaction_id' => $transID,'transaction_token' => json_encode(["id_in_transaction_cinetpay" => $transaction[0]['id']]), 'fkoperation' => $idOperation, 'montant' => $montant, 'operation' => 'CREDIT']);

                    $modelLigneOperation->insert(['fkcompte' => $idCaisse, 'fkoperation' => $idOperation, 'montant' => $montant, 'operation' => 'DEBIT']);

                    // mettre jour le client
                    if ($devise == "CDF")
                    {

                        $client->solde_cdf = $client->solde_cdf + $montant;
                    }
                    else
                    {

                        $client->solde_usd = (double)$client->solde_usd + (double)$montant;

                    }


                    $modelClient ->update($client->id, (array)$client);


                }


            }
            else {
               return $this -> getResponse([
                    "message" => "Le numéro de la transaction n'est pas reconnu"]);
            }

        } else {
            return $this -> getResponse([
                    "error" => "No data sent "]);
        }

        return $this->getResponse([

          'message' => "Reussi La transaction a été traitée.",

          "client" => $client,
          "caisseCinet" => $caisseMobileMoney,
          "compteClient" => $compteClient,
          "solde" => $solde,
          "devise" => $devise,
          "cinetTransaction" => $cinetTransaction,

        ]);

    }


}

