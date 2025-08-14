<?php
namespace App\Models;
use App\Models\MyParentModel;
use Exception;

class UserPermissionModel extends MyParentModel
{
  protected $table = "user_permission";
  protected $allowedFields = [
       "id", "fk_user", "fk_permission"
  ];
  public function __construct() {
    parent::__construct($this -> table, $this -> allowedFields);
  }

  public function findById($id)
  {
      $userpermission = $this
          ->where(["id" => $id])
          ->get () -> getFirstRow();
      // if (!$userpermission) throw new Exception("Could not find userpermission for specified ID");
      return $userpermission;
  }
}
