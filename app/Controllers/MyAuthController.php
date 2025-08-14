<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\OtpModel;
use App\Models\AgentModel;
use App\Models\ClientModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Controller;
use Exception;
use ReflectionException;




class   MyAuthController extends BaseController {






    private function createToken($user) {

        helper('jwt');
        $jwt = getSignedJWTForUser($user['id']);

        return $jwt;
        
    }

    public function logout () {
        session() -> destroy();
        return redirect()->to(site_url('/'));
    }

    public function login() {

        $input = $this->getRequestInput($this->request);
        // getResponse(['message' =>'NO USER found', 'user' =>  $user]);

        // redirect('0990');

        $password = isset(   $input ['password']) ?  $this -> hash_password($input ['password']) : '';
        $email = isset(   $input ['email']) ? $input ['email'] : '';
        $username = isset(   $input ['username']) ? $input ['username'] : '';
        $phone = isset(   $input ['phone']) ? $input ['phone'] : '';


        $input = [];
        $input['password'] = $password;
        $input['email'] = $email;
        $input['username'] = $username;
        $input['phone'] = $phone;

        if(empty($email) && empty($username) & empty($phone)) {
            return $this -> getResponse( ['message' => 'Provide credential', 'error' => 'Provide credential'],ResponseInterface::HTTP_BAD_REQUEST);
        }

        if(!empty($username)) {
            $userModel = model('UserModel');
            $user = $userModel -> search(['where' => ['username' => $username, 'password' => $password]]);

            if ($user) { 
                session() -> set('user', $user[0]);
                return $this -> getResponse(['message' => 'user found', 'user' => $user[0]]);
            }
            return $this -> getResponse(['message' => 'NO USER found', 'user' =>  $user]);

        }

        return $this -> getResponse(['message' =>'NO USER found', 'user' =>  $user]);

    }


    private function noDuplicate($input) {


        $user = model('UserModel');

        
        foreach ($input as $key => $value) {
            if ($key != 'password' && !empty($value)) {

                // FIND IF USER THERE IS RECORD OF CREDENTIAL IN DB
                $find = $user -> search(['where' => [$key => $value]]);


                if(!empty($find)) { 
                    return false;
                }
            }
        }

        return true;
    }


    private function hash_password($password) {
        return hash('sha256', $password . PASSWORD_SALT);

    }

    function register() {



        // ENREGISTRER USER PAR username, email ou phone
        // CREER otp pour email et phone
        // CREER agent pour username
        $input = $this->getRequestInput($this->request);


        $password = isset(   $input ['password']) ? $this -> hash_password($input ['password']) : '';

        $email = isset(   $input ['email']) ? $input ['email'] : '';
        $username = isset(   $input ['username']) ? $input ['username'] : '';
        $phone = isset(   $input ['phone']) ? $input ['phone'] : '';



        $input = [];
        $input['password'] = $password;
        $input['email'] = $email;
        $input['username'] = $username;
        $input['phone'] = $phone;


        // TRAITER LES USER DEMO DIRECT

        // return   $this ->getResponse    (['credentials' => $input  ]);
        
        if($phone == PHONE_USER_DEMO) {
            return  $this -> getResponse   (['message' => "WELCOME TO TEST MODE OF KASOKOO", 'id_user' => ID_USER_DEMO]);
        }


        // VERIFIER QU IL Y A AU MOINS UN CREDENTIAL LE CREDENTIAL 

        if(empty($email) && empty($username) && empty($phone)) {
            return $this -> getResponse( ['message' => 'Provide credential', 'error' => 'Provide credential'],ResponseInterface::HTTP_BAD_REQUEST);
        }

        // VERIFIER QUE LE CREDIT N'EXISTE PAS


        if(! $this -> noDuplicate($input)) {


            // tenter de retrouver le client 
            $userModel = new UserModel();
            $userFound = $userModel -> search(['where' => ['phone' => $phone,]]); 
            if(!empty($userFound)) {

                $user = $userModel -> findById($userFound[0]['id']);
                $otp = model('OtpModel');
                
                // CREER OTP pour user 
                $otp_sent_to = $phone ;
                $otp = null;

                if(!empty($otp_sent_to)) {
                    $otp = $this -> createOTPForClient($otp_sent_to, $user -> id);
                }

                return  $this -> getResponse   (['message' => "Please validate your number or email", 'id_user' => $userFound[0]['id']]);
        
            }

            // si aucun est trouve
            return $this -> getResponse( ['message' => 'Credential refused', 'error' => 'Credential refused'],ResponseInterface::HTTP_BAD_REQUEST);
        }






       
        // ENREGISTRER L USER
        $userModel = new UserModel();
        $id_user = $userModel->insert($input);

        //CREER UN ACCESS TOKEN POUR l'UTILSATEUR
        helper('jwt');
        $jwt = getSignedJWTForUser($id_user);
        $user = $userModel -> findById($id_user);
        $user -> access_token = $jwt;

        $userArray = (array) $user;
        $userModel -> replace($userArray);






        // USERNAME DONC C EST AGENT

        if(!empty($username)) {

            // CREER ET INITIALISER AGENT
            $id_agent = $this -> createAgent ($id_user);
            $user -> fkagent = $id_agent;

            $userArray = (array) $user;
            $userModel -> replace($userArray);

            return  $this -> getResponse   ([
                'message' => "user created succcesfully", 
                'id_user' => $id_user, 
                "id_agent" => $id_agent, 
                'access_token' =>$jwt]);

        
        } else {
        
            // MAIL et PHONE DONC C'est CLIENT : ATTENDRE VALIDATION

            $otp = model('OtpModel');
            // CREER OTP pour user : CREER OTP SEULEMENT POUR LE CLIENT QUI S'ENREGISTRE PAR NOM ET NUMERO DE TELEPHONE
            $otp_sent_to = !empty($email)? $email :(!empty($phone) ? $phone : '');
            $otp = null;

            if(!empty($otp_sent_to)) {
                $otp = $this -> createOTPForClient($otp_sent_to, $user -> id);
            }
        
            return  $this -> getResponse   (['message' => "Please validate your number or email", 'id_user' => $id_user]);
        
        }

    }

    function createOTPForClient ($otp_sent_to, $id_user) {

        helper('text');

        $value = intval( random_string('nozero', 6));
        
        $data = ['value' => $value, 'sent_to' => $otp_sent_to, "fkuser" => $id_user];
        
        $otp = new OtpModel();

        $result = $otp -> insert($data);

        helper('OtpSenderDevice');

        $res = sendSMSHelper($value, $otp_sent_to);


        $this -> getResponse(
            [
                'message' => $res
            ]);
        return $result;
    
    }

    public function resendOTPForClient ($otp_sent_to) {


        // IS TO DO

        helper('text');

        $value = intval( random_string('nozero',6));

        
        // $data = ['value' => $value, 'sent_to' => $otp_sent_to];
        
        // $otp = new OtpModel();

        // return $otp -> insert($data);
    
    }

    private function createAgent ($id_user) {
        $data = ['created_at' => date('Y-m-d H:i:s')];


        $agent = new AgentModel();

        return $agent -> insert($data);

    }

    private function createClient ($id_user) {
        $data = ['created_at' => date('Y-m-d H:i:s')];


        $client = new ClientModel();
        $userModel = new UserModel();

        $phone = $userModel -> findById($id_user) -> phone;
        $data['primary_phone'] = $phone;

        return $client -> insert($data);

    }

    public function validateRegistrationOTP () {

        // TO DO

        // VALIDER OTP, creer client
        
        $input = $this->getRequestInput($this->request);

        $OtpModel = new OtpModel();
        $userModel = new UserModel();

        $otp_sent_to = isset($input['otp_sent_to']) ? $input['otp_sent_to'] : '';
        $otp_value = isset($input['value']) ? $input['value'] : '';

        $otp_search = $OtpModel -> search(['where' => ['sent_to' => $otp_sent_to,  'value' => $otp_value]]);





        if(!empty($otp_search)) {
            $otp =  $otp_search [0];


            $otp ['status'] = 'CHECKED';

            $otpArray = (array) $otp;
            $OtpModel -> replace($otpArray);




            $id_user = $otp ['fkuser'];
            $user = $userModel -> findById($id_user);






            // creer un nouve
            if(empty($user -> fkclient )) 
            {
                $id_client = $this -> createClient($user -> id);

                $this -> createCompteClient ($id_client, 'CDF');
                $this -> createCompteClient ($id_client, 'USD');
                $user -> fkclient = $id_client;

                $userArray = (array) $user;
                $userModel -> replace($userArray);


                $user = $userModel -> findById($id_user);
            }

            return $this -> getResponse(['message' => 'Acount created', 'user' => $user, ]);

            
        }

        // user de tester demo 

        if ($otp_sent_to == PHONE_USER_DEMO && $otp_value == "123456") {
            return $this -> getResponse(['message' => 'Acount created', 'user' => $userModel -> findById(ID_USER_DEMO), ]);
        }

        // $OtpModel


        return $this -> getResponse(['message' =>'Not Found OTP'], ResponseInterface::HTTP_BAD_REQUEST);
    }

    public function createCompteClient($idClient, $devise) {
        $modelCompte = model('CompteModel');
        return $modelCompte -> insert( [
                'fkclient' => $idClient,
                'devise' => $devise,
                'type_compte' => 'COMPTE_CLIENT',
                // 'intutile' =>
            ]);
    }


    public function getAllClientInitialData($id_user) {

        try {
            $userModel = model('UserModel');
            $parametreModel = model('ParametreModel');
            $clientModel = model('ClientModel');
            $adresseModel = model('AdresseModel');
            $messageModel = model('MessageModel');
            $informationpaiementModel = model('InformationPaiementModel');

            $user = $userModel -> findById($id_user);

            $client = $clientModel -> findById($user -> fkclient);
            $parametre = $parametreModel -> findById(1);
            $adresses = $adresseModel -> search([
                "where" => ["fkuser_create" => $user -> id]
            ]);

            $messages = $messageModel -> search([
                "where" => "fkuser ='" .$user -> id . "' OR fkuser_destinataire = '" . $user -> id . "'" 
            ]);

            $informationpaiement = $informationpaiementModel ->  search([
                "where" => ["fkcaisse" => CAISSE_MOBILE_MONEY]
            ]); 
            return $this -> getResponse(


                [
                    
                    "adresses" => $adresses,
                    "user" => $user,
                    "client" => $client,
                    "parametre" => $parametre,
                    "messages" => $messages,
                    "informationsPaiement" => $informationpaiement,
                                    ]
            );
        } catch (Exception $e) {
            return $this ->getResponse(
                ['erreur' => "some thing went wrong", 
                "user" => $user,
                    "client" => $client,
                    "parametre" => $parametre,
                    "messages" => $messages,
                    "adresses" => $adresses,
                    "commandes" => $commandes,
                    "informationsPaiement" => $informationpaiement,

            ], ResponseInterface::HTTP_BAD_REQUEST);
        }

    }



    public function getAllAgentInitialData($id_user) {

        $erreur = "Some thing went wrong";

        $data = $this -> getRequestInput($this -> request);

        $fcm = isset($data['fcm_token'])? $data['fcm_token'] : '' ;
        $device_id = isset($data['device_id'])? $data['device_id'] : '' ;

        if(!empty($fcm) && !empty($device_id)) {
            $userDeviceModel = model('UserDeviceModel');

            $device = $userDeviceModel -> search([
                "where" => ['uuid' => $device_id] 
            ]);

            if(empty($device)) {
                $userDeviceModel -> insert( ['uuid' => $device_id, 'fcm_token' => $fcm, 'fkuser' => $id_user]);
            } else if($device[0]['fcm_token'] != $fcm){
                
                $userDeviceModel -> update( $device[0]['id'], ['uuid' => $device_id, 'fcm_token' => $fcm]);
            }
        }

        try {
            $userModel = model('UserModel');
            $parametreModel = model('ParametreModel');
            $clientModel = model('ClientModel');
            $adresseModel = model('AdresseModel');
            $messageModel = model('MessageModel');
            $informationpaiementModel = model('InformationPaiementModel');


            $moduleModel = model('ModuleModel');
            $agentModel = model('AgentModel');
            $droitModel = model('DroitsModel');
            $droitAgentModel = model('DroitsAgentModel');




            $user = $userModel -> findById($id_user);

            $parametre = $parametreModel -> findById(1);
            


            $messages = $messageModel -> search([
                "where" => "fkuser ='" . $user -> id . "' OR fkuser_destinataire = '" . $user -> id . "'" 
            ]);





            $informationpaiement = $informationpaiementModel ->  search([
                "where" => ["fkcaisse" => CAISSE_MOBILE_MONEY]
            ]); 


            
            


            $modules = $moduleModel -> selectAll();

            
            $agent = $agentModel -> findById($user -> fkagent);


            $erreur = $agent;
            $droits = $droitModel -> selectAll();


            $droitsAgent = $droitAgentModel ->  search([
                "where" => ["fkagent" => $agent -> id]
            ]); 


            return $this -> getResponse(


                [
                    
                    "modules" => $modules,
                    "agent" => $agent,
                    "droits" => $droits,
                    "droitsAgent" => $droitsAgent,
                    "user" => $user,
                    // "client" => $client, 
                    "parametre" => $parametre,
                    // "messages" => $messages,
                    // "informationsPaiement" => $informationpaiement,
                                    ]
            );
        } catch (Exception $e) {
            return $this ->getResponse(
                ['erreur' => $erreur

            ], ResponseInterface::HTTP_BAD_REQUEST);
        }

    }
    


    public function saveClientDevice() {

        $erreur = "Some thing went wrong";

        $data = $this -> getRequestInput($this -> request);

        $fcm = isset($data['fcm_token'])? $data['fcm_token'] : '' ;
        $device_id = isset($data['device_id'])? $data['device_id'] : '' ;
        $id_user = isset($data['id_user'])? $data['id_user'] : '' ;

        if(!empty($fcm) && !empty($device_id) && !empty ($id_user)) {
            $userDeviceModel = model('UserDeviceModel');

            $device = $userDeviceModel -> search([
                "where" => ['uuid' => $device_id] 
            ]);

            if(empty($device)) {
                $userDeviceModel -> insert( ['uuid' => $device_id, 'fcm_token' => $fcm, 'fkuser' => $id_user]);
            } else {

                // if($device[0]['fcm_token'] != $fcm) 
                    $userDeviceModel -> update( $device[0]['id'], ['uuid' => $device_id,'fkuser' => $id_user, 'fcm_token' => $fcm]);
            }
            return $this -> getResponse(
            [
                'message' => 'Saved User Device',
                'result' => $device
            ]);

        }

        return $this ->getResponse(
                ['erreur' => $erreur,
                'data' => $data

            ], ResponseInterface::HTTP_BAD_REQUEST);


    }

}




























