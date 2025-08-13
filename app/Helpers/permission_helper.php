<?php
use App\Models\MyParentModel;
use App\Models\DroitsAgentModel;
use App\Models\DroitsModel;
// use Exception;

    /**
     * Get a list of all right codes for a given agent.
     *
     * @param int $agentId
     * @return array
     */
    function getAgentPermissions($user): array
    {
        // Find all rights assigned to the agent from droits_agent table
        $droitsAgentModel = new DroitsAgentModel();
        $userModel = model ('UserModel');



        $user = (array) $user;

        $agentId = $user['fkagent'];  
        $agentRights = $droitsAgentModel->search   ([ 'where' =>['fkagent'=> $agentId] ]);

        if (empty($agentRights)) {
            return ['produit.view', 'categorie_prod.view', 'parametre.view'];
        }

        // Extract the droit codes
        $codes = [];
        $droitsModel = model('DroitsModel');
        foreach ($agentRights as $droitsAgent) {
            // code
            $droit = $droitsModel -> findDroitsById($droitsAgent['fkdroit']);
            if($droit) $codes [] = $droit -> code;
        }
        

        if (empty($codes)) {
            return ['produit.view', 'categorie_prod.view', 'parametre.view'];
        }

        // Extract the codes from the droits

        return  $codes;
    }

    function getUserRights(int $userId) : array {
        $model = model('UserModel');
        $user = $model -> findUserById($userId);

        return array_merge(['produit.view', 'categorie_prod.view', 'parametre.view' ]);
    }



if (!function_exists('isClientRecord')) {
    /**
     * Checks if a database record is linked to the currently logged-in client.
     *
     * @param \CodeIgniter\Model $model The model instance for the table to check.
     * @param int $recordId The ID of the record to verify.
     * @return bool Returns true if the record belongs to the client, false otherwise.
     */
    function isClientRecord($model, int $recordId): bool
    {
        $session = session();
        $user = $session->get('user');

        // Check if a user is logged in and if the user is a client.
        if (empty($user) || $user['role'] !== 'client') {
            return false;
        }

        // Retrieve the record from the model.
        $record = $model->find($recordId);

        // If the record doesn't exist, or if the user ID doesn't match the foreign key, return false.
        if (empty($record) || (isset($record->fkuser) && $record->fkuser != $user['id'])) {
            return false;
        }

        return true;
    }
}