<?php
namespace App\Models;
use App\Models\MyParentModel;
use Exception;

class AchatModel extends MyParentModel
{
  protected $table = "achat";
  protected $allowedFields = [
       "id", "created_at", "updated_at", "delivered_at", "status_payement", "fkagent", "total_achat", "frais_logistique", "devise", "code_cmd", "code_achat", "libelle_cmd", "libelle_achat", "status_cmd", "fkfournisseur"
  ];
  public function __construct() {
    parent::__construct($this -> table, $this -> allowedFields);
  }

  public function findAchatById($id)
  {
      $achat = $this
          ->where(["id" => $id])
          ->get () -> getFirstRow();
      // if (!$achat) throw new Exception("Could not find achat for specified ID");
      return $achat;
  }
}
