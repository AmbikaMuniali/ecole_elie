<?php
namespace App\Models;
use App\Models\MyParentModel;
use Exception;

class LigneOperationModel extends MyParentModel
{
  protected $table = "ligne_operation";
  protected $allowedFields = [
       "id", "fkcompte", "fkoperation", "fkinfo_paiement", "montant", "motif", "transaction_id", "transaction_token", "fkuser_create", "operation", "created_at", "updated_at"
  ];
  public function __construct() {
    parent::__construct($this -> table, $this -> allowedFields);
  }

  public function findLigneOperationById($id)
  {
      $ligneoperation = $this
          ->where(["id" => $id])
          ->get () -> getFirstRow();
      // if (!$ligneoperation) throw new Exception("Could not find ligneoperation for specified ID");
      return $ligneoperation;
  }
}
