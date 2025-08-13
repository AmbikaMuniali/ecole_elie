<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\OtpModel;
use App\Models\AgentModel;
use App\Models\ClientModel;
use App\Models\MyParentModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Controller;
use Exception;
use ReflectionException;




class   ControlleurLivreur extends BaseController {

    public function getLivraisonsEnAttente($id_agent) {
        // get
        $model = new DeliveryModel();
        return $this -> getResponse(
            ['livraisons' => $model -> getDeliveriesByStatusAndAgent('ENCOURS', $id_agent)]
        );
    }

    // New function for deliveries in progress
    public function getLivraisonsEnCours($id_agent) {
        $model = new DeliveryModel();
        return $this -> getResponse(
            ['livraisons' => $model -> getDeliveriesByStatusAndAgent('ENCOURS', $id_agent)]
        );
    }


    private function hydrateConversation($id_user) {
        //private function to give internal response
        $modelMessage = model('MessageModel');

        $conversations = $modelMessage -> select('*') -> where (['fkuser'=> $id_user ]) -> orWhere(['fkuser_destinataire'=>$id_user])

                               -> get() ->getResultArray();
        return $conversations;

    }

    public function hydrateMessage($id_user) {
        //public function to give response json
        return $this -> getResponse(
        [
            // 'test' => 'hhhhhhhhhh',
            'messages' => $this -> hydrateConversation($id_user)
        ]);
    }


    // this function reads a converrsation where the sender is id_user
    public function readMessage($id_user) {
        //public function to give response json
        $a = model('MessageModel') -> builder()
             ->set('isread', 'TRUE')
             ->where('fkuser', $id_user)
             ->update();

        // Check if any rows were affected. This is a common way to determine success
        // for update operations in CodeIgniter 4.

        return $this -> getResponse(
        [
            'message' => 'success'
        ]);
    }


    // $this function set as read all messages sent to user id_user
    
    public function readMessageClient($id_user) {
        //public function to give response json
        $a = model('MessageModel') -> builder()
             ->set('isread', 'TRUE')
             ->where('fkuser_destinataire', $id_user)
             ->update();

        // Check if any rows were affected. This is a common way to determine success
        // for update operations in CodeIgniter 4.

        return $this -> getResponse(
        [
            'message' => 'success'
        ]);
    }

    




    public function getConversations($limit = 100) {
        $modelMessage = model('MessageModel');

        $users = $modelMessage ->select('fkuser, client.name_complet    as name_complet, user.phone as phone,') // Replace 'user_id' with the actual column name for the user's ID


                                ->join('user', 'message.fkuser = user.id', 'left')
                                ->join('client', 'user.fkclient  = client.id', 'left')

                               ->where(['isread' => 'FALSE', 'fkuser_destinataire' => null])

                               ->distinct('fkuser')
                               ->get()
                               ->getResultArray();

        $conversations = [];

        foreach ($users as $key => $user) {
            $conversations[$key] = [
                'user' => $user,
                'conversation' => $this -> hydrateConversation($user['fkuser'])];
        }
        return $this -> getResponse([
            'conversations' => $conversations
        ]);
    }

    public function getAllLivraisons($id_agent) {
        // $data = $this -> getRequestInput($this -> request);
        // $id_agent = isset ($data['id_agent'])?  $data['id_date'] : null;

        $modelLivraison = model('LivraisonModel');
        $modelCommande = model('CommandeModel');
        $modelLigneCommande = model('LigneCommandeModel');
        // $livraisons = $modelLivraison -> search (['where' => ['fkagent' => $id_agent]]); // This line is commented out or not used directly

        $model = new DeliveryModel();
        return $this -> getResponse([
            'livraisons' => $model -> getDeliveriesByAgent($id_agent) // Modified to fetch all deliveries for an agent
        ]);
    }

    

    function hydrateLivraison($id_livraison) {

        $model = new DeliveryModel();
        // // return $model -> getDeliveriesWithDetails();getOrderWithDetails
        // return $model -> getOrderWithDetails();
        return $model -> getDeliveryDetails($id_livraison);
    }

    public function commandeEnAttente () {


        $model = new DeliveryModel();
        return $this -> getResponse (
            ['commandes' => $model -> getOrderWithDetails()]
        );
    }

    public function getAllCommandes() {
        $this -> getRequestInput($this ->request);
        $model = new DeliveryModel();
        return $this -> getResponse (
            ['commandes' => $model -> getOrderWithDetails()]
        );

    }



    public function affecterLivraison() {
        // post
        $input = $this->getRequestInput($this->request);

        $id_commande = $input['id_commande'] ?? null;
        $id_agent = $input['id_agent'] ?? null;

        if (empty($id_commande) || empty($id_agent)) {
            return $this->getResponse(
                ['message' => 'Missing id_commande or id_agent in request body'],
                ResponseInterface::HTTP_BAD_REQUEST
            );
        }

        try {
            $deliveryModel = new DeliveryModel();
            $commandeModel = model('CommandeModel'); // Assuming you have a CommandeModel

            // Check if a livraison already exists for this commande
            $existingLivraison = $deliveryModel->where('fkcommande', $id_commande)->first();

            if ($existingLivraison) {
                // Update existing livraison
                $deliveryModel->update($existingLivraison['id'], [
                    'fkagent' => $id_agent,
                    'status' => 'ENCOURS',
                    'updated_at' => date('Y-m-d H:i:s') // Manually update updated_at if not handled by model
                ]);
            } else {
                // Create new livraison
                $deliveryModel->insert([
                    'fkcommande' => $id_commande,
                    'fkagent' => $id_agent,
                    'status' => 'ENCOURS',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }

            // Update the status of the commande
            $commandeModel->update($id_commande, [
                'status_cmd' => 'LIVRE',
                'updated_at' => date('Y-m-d H:i:s') // Manually update updated_at if not handled by model
            ]);

            return $this->getResponse(
                ['message' => 'Commande affected to agent successfully and status updated.'],
                ResponseInterface::HTTP_OK
            );

        } catch (Exception $e) {
            return $this->getResponse(
                ['message' => 'Error affecting delivery: ' . $e->getMessage()],
                ResponseInterface::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }


}



class DeliveryModel extends MyParentModel
{
    protected $table = 'livraison';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'fkcommande',
        'fkagent',
        'delivered_at',
        'status',
    ];
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function __construct() {
        parent::__construct($this -> table, $this -> allowedFields);
    }

    /**
     * Get deliveries with associated order, order lines, and product details.
     *
     * @return array An array of delivery data, or an empty array if no deliveries are found.
     */
    public function getDeliveryDetails($id_livraison): array
    {
        $builder = $this;
        $builder->select(
            'livraison.id AS livraison_id,
            livraison.created_at AS livraison_created_at,
            livraison.delivered_at AS livraison_delivered_at,
            livraison.status AS livraison_status,
            commande.id AS commande_id,
            commande.code AS commande_code,
            commande.libelle AS commande_libelle,
            commande.total_cmd AS commande_total,
            commande.frais_livraison AS commande_frais_livraison,
            commande.devise AS commande_devise,
            commande.status_cmd AS commande_status,
            commande.status_payement AS commande_status_payement,
            client.id AS client_id,
            client.name_complet AS client_name,
            client.primary_phone AS client_phone,
            agent.id AS agent_id,
            agent.name_complet AS agent_name,
            agent.phone AS agent_phone,
            adresse.id AS adresse_id,
            adresse.libelle_client AS adresse_libelle_client,
            adresse.avenue AS adresse_avenue,
            adresse.numero_rue AS adresse_numero_rue,
            adresse.description_batiment AS adresse_description_batiment'
        );
        $builder->join('commande', 'livraison.fkcommande = commande.id', 'left');
        $builder->join('client', 'commande.fkclient = client.id', 'left');
        $builder->join('agent', 'livraison.fkagent = agent.id', 'left');
        $builder->join('adresse', 'commande.fkadresse = adresse.id', 'left');
        $builder->where('livraison.id', $id_livraison);
        $deliveries = $builder->get()->getResultArray();

        // If no deliveries are found, return an empty array
        if (empty($deliveries)) {
            return [];
        }

        // Fetch order lines and product details for each delivery
        foreach ($deliveries as &$delivery) {
            $delivery['order_lines'] = $this->getOrderLinesWithProductDetails($delivery['commande_id']);
        }

        return $deliveries;
    }

    /**
     * Get order lines with associated product details for a given order ID.
     *
     * @param int $commande_id The ID of the order.
     * @return array An array of order line data, or an empty array if no order lines are found.
     */
    private function getOrderLinesWithProductDetails(int $commande_id): array
    {
        $builder = model('LigneCommandeModel');
        $builder->select(
            'ligne_commande.id AS ligne_commande_id,
            ligne_commande.quantite AS ligne_commande_quantite,
            ligne_commande.montant AS ligne_commande_montant,
            produit.id AS produit_id,
            produit.code AS produit_code,
            produit.designation AS produit_designation,
            produit.unite AS produit_unite,
            produit.prix_vente AS produit_prix_vente,
            produit.devise AS produit_devise'
        );
        $builder->join('produit', 'ligne_commande.fkproduit = produit.id', 'left');
        $builder->where('ligne_commande.fkcommande', $commande_id);
        $builder->orderBy('ligne_commande.created_at', 'DESC');
        $orderLines = $builder->get()->getResultArray();

        return $orderLines;
    }


    public function getOrderWithDetails($wheres = [], $limit = 100): array
    {
        $builder = model('CommandeModel');
        $builder->select(
            'commande.id AS commande_id,
            commande.code AS commande_code,
            commande.libelle AS commande_libelle,
            commande.total_cmd AS commande_total,
            commande.frais_livraison AS commande_frais_livraison,
            commande.devise AS commande_devise,
            commande.total_cmd AS commande_total,
            commande.status_cmd AS commande_status,
            commande.status_payement AS commande_status_payement,
            client.id AS client_id,
            client.name_complet AS client_name,
            client.primary_phone AS client_phone,
            adresse.id AS adresse_id,
            adresse.libelle_client AS adresse_libelle_client,
            adresse.avenue AS adresse_avenue,
            adresse.latitude AS adresse_latitude,
            adresse.longitude AS adresse_longitude,
            adresse.numero_rue AS adresse_numero_rue,
            adresse.description_batiment AS adresse_description_batiment'
        );
        $builder->join('client', 'commande.fkclient = client.id', 'left');
        $builder->join('adresse', 'commande.fkadresse = adresse.id', 'left');
        $builder->orderBy('commande.created_at', 'DESC');

        foreach($wheres as $where) {
            $builder->where($where[0], $where[1]);
        }
        $builder -> limit($limit);
        $deliveries = $builder->get()->getResultArray();

        // If no deliveries are found, return an empty array
        if (empty($deliveries)) {
            return [];
        }

        // Fetch order lines and product details for each delivery
        foreach ($deliveries as &$delivery) {
            $delivery['order_lines'] = $this->getOrderLinesWithProductDetails($delivery['commande_id']);
        }

        return $deliveries;
    }

    /**
     * Get deliveries filtered by status and agent.
     *
     * @param string $status The status of the delivery (e.g., 'ENCOURS', 'LIVRE', 'NON_LIVRE').
     * @param int $fkagent The ID of the agent.
     * @return array An array of delivery data.
     */
    public function getDeliveriesByStatusAndAgent(string $status, int $fkagent): array
    {
        $builder = $this;
        $builder->select(
            'livraison.id AS livraison_id,
            livraison.created_at AS livraison_created_at,
            livraison.delivered_at AS livraison_delivered_at,
            livraison.status AS livraison_status,
            commande.id AS commande_id,
            commande.code AS commande_code,
            commande.libelle AS commande_libelle,
            commande.total_cmd AS commande_total,
            commande.frais_livraison AS commande_frais_livraison,
            commande.devise AS commande_devise,
            commande.status_cmd AS commande_status_cmd,
            commande.status_payement AS commande_status_payement,
            client.id AS client_id,
            client.name_complet AS client_name,
            client.primary_phone AS client_phone,
            agent.id AS agent_id,
            agent.name_complet AS agent_name,
            agent.phone AS agent_phone,
            adresse.id AS adresse_id,
            adresse.libelle_client AS adresse_libelle_client,
            adresse.avenue AS adresse_avenue,
            adresse.numero_rue AS adresse_numero_rue,
            adresse.description_batiment AS adresse_description_batiment'
        );
        $builder->join('commande', 'livraison.fkcommande = commande.id', 'left');
        $builder->join('client', 'commande.fkclient = client.id', 'left');
        $builder->join('agent', 'livraison.fkagent = agent.id', 'left');
        $builder->join('adresse', 'commande.fkadresse = adresse.id', 'left');
        $builder->where('livraison.status', $status);
        $builder->where('livraison.fkagent', $fkagent);
        $deliveries = $builder->get()->getResultArray();

        foreach ($deliveries as &$delivery) {
            $delivery['order_lines'] = $this->getOrderLinesWithProductDetails($delivery['commande_id']);
        }

        return $deliveries;
    }

    /**
     * Get all deliveries for a specific agent.
     *
     * @param int $fkagent The ID of the agent.
     * @return array An array of delivery data.
     */
    public function getDeliveriesByAgent(int $fkagent): array
    {
        $builder = $this;
        $builder->select(
            'livraison.id AS livraison_id,
            livraison.created_at AS livraison_created_at,
            livraison.delivered_at AS livraison_delivered_at,
            livraison.status AS livraison_status,
            commande.id AS commande_id,
            commande.code AS commande_code,
            commande.libelle AS commande_libelle,
            commande.total_cmd AS commande_total,
            commande.frais_livraison AS commande_frais_livraison,
            commande.devise AS commande_devise,
            commande.status_cmd AS commande_status_cmd,
            commande.status_payement AS commande_status_payement,
            client.id AS client_id,
            client.name_complet AS client_name,
            client.primary_phone AS client_phone,
            agent.id AS agent_id,
            agent.name_complet AS agent_name,
            agent.phone AS agent_phone,
            adresse.id AS adresse_id,
            adresse.libelle_client AS adresse_libelle_client,
            adresse.avenue AS adresse_avenue,
            adresse.numero_rue AS adresse_numero_rue,
            adresse.description_batiment AS adresse_description_batiment'
        );
        $builder->join('commande', 'livraison.fkcommande = commande.id', 'left');
        $builder->join('client', 'commande.fkclient = client.id', 'left');
        $builder->join('agent', 'livraison.fkagent = agent.id', 'left');
        $builder->join('adresse', 'commande.fkadresse = adresse.id', 'left');
        $builder->where('livraison.fkagent', $fkagent);
        $deliveries = $builder->get()->getResultArray();

        foreach ($deliveries as &$delivery) {
            $delivery['order_lines'] = $this->getOrderLinesWithProductDetails($delivery['commande_id']);
        }

        return $deliveries;
    }


}