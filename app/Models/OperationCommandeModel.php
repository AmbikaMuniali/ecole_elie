<?php
namespace App\Models;
use App\Models\MyParentModel;
use Exception;

class OperationCommandeModel extends MyParentModel
{
  protected $table = "operation_commande";
  protected $allowedFields = [
       "id", "fkcommande", "fkoperation", "created_at", "updated_at"
  ];
  public function __construct() {
    parent::__construct($this -> table, $this -> allowedFields);
  }

  public function findOperationCommandeById($id)
  {
      $operationcommande = $this
          ->where(["id" => $id])
          ->get () -> getFirstRow();
      // if (!$operationcommande) throw new Exception("Could not find operationcommande for specified ID");
      return $operationcommande;
  }
}
