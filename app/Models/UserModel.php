<?php
namespace App\Models;
use App\Models\MyParentModel;
use Exception;

class UserModel extends MyParentModel
{
  protected $table = "user";
  protected $allowedFields = [
       "id", "username", "email", "phone", "password", "access_token", "status", "fkclient", "fkagent", "pref_lang", "created_at", "updated_at"
  ];
  public function __construct() {
    parent::__construct($this -> table, $this -> allowedFields);
  }

  public function findUserById($id)
  {
      $user = $this
          ->where(["id" => $id])
          ->get () -> getFirstRow();
      // if (!$user) throw new Exception("Could not find user for specified ID");
      return $user;
  }
}
