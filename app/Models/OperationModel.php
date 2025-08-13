<?php
namespace App\Models;
use App\Models\MyParentModel;
use Exception;

class OperationModel extends MyParentModel
{
  protected $table = "operation";
  protected $allowedFields = [
       "id", "fkuser_create", "libelle", "type_operation", "created_at", "updated_at"
  ];
  public function __construct() {
    parent::__construct($this -> table, $this -> allowedFields);
  }

  public function findOperationById($id)
  {
      $operation = $this
          ->where(["id" => $id])
          ->get () -> getFirstRow();
      // if (!$operation) throw new Exception("Could not find operation for specified ID");
      return $operation;
  }
}
