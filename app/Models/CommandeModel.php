<?php
namespace App\Models;
use App\Models\MyParentModel;
use Exception;

class CommandeModel extends MyParentModel
{
  protected $table = "commande";
  protected $allowedFields = [
       "id", "type_commande", "delivered_at", "status_cmd", "status_payement", "fkclient", "fkadresse", "total_cmd", "frais_livraison", "devise", "code", "libelle", "created_at", "updated_at"
  ];
  public function __construct() {
    parent::__construct($this -> table, $this -> allowedFields);
  }

  public function findCommandeById($id)
  {
      $commande = $this
          ->where(["id" => $id])
          ->get () -> getFirstRow();
      // if (!$commande) throw new Exception("Could not find commande for specified ID");
      return $commande;
  }
}
