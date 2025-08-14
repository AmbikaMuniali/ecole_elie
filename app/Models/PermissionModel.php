<?php
namespace App\Models;
use App\Models\MyParentModel;
use Exception;

class PermissionModel extends MyParentModel
{
  protected $table = "permission";
  protected $allowedFields = [
       "id", "fk_module", "nom", "code"
  ];
  public function __construct() {
    parent::__construct($this -> table, $this -> allowedFields);
  }

  public function findById($id)
  {
      $permission = $this
          ->where(["id" => $id])
          ->get () -> getFirstRow();
      // if (!$permission) throw new Exception("Could not find permission for specified ID");
      return $permission;
  }
}
