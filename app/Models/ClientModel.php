<?php
namespace App\Models;
use App\Models\MyParentModel;
use Exception;

class ClientModel extends MyParentModel
{
  protected $table = "client";
  protected $allowedFields = [
           "id", "solde_cdf", "solde_usd", "name_complet", "email", "primary_phone", "phone_is_verified", "pincode", "devise_pref", "status", "created_at", "updated_at", "profession", "adresse", "photo", "statut_juridique", "avoir_credit"
      ];
  public function __construct() {
    parent::__construct($this -> table, $this -> allowedFields);
  }

  public function findClientById($id)
  {
      $client = $this
          ->where(["id" => $id])
          ->get () -> getFirstRow();
      // if (!$client) throw new Exception("Could not find client for specified ID");
      return $client;
  }
}
