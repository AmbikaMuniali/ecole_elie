<?php
namespace App\Models;
use App\Models\MyParentModel;
use Exception;

class OperationAchatModel extends MyParentModel
{
  protected $table = "operation_achat";
  protected $allowedFields = [
       "id", "fkachat", "fkoperation", "created_at", "updated_at"
  ];
  public function __construct() {
    parent::__construct($this -> table, $this -> allowedFields);
  }

  public function findOperationAchatById($id)
  {
      $operationachat = $this
          ->where(["id" => $id])
          ->get () -> getFirstRow();
      // if (!$operationachat) throw new Exception("Could not find operationachat for specified ID");
      return $operationachat;
  }
}
