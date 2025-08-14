<?php
namespace App\Models;
use App\Models\MyParentModel;
use Exception;

class UserModel extends MyParentModel
{
  protected $table = "user";
  protected $allowedFields = [
       "id", "username", "password", "email", "nom_complet", "est_actif", "date_creation"
  ];
  public function __construct() {
    parent::__construct($this -> table, $this -> allowedFields);
  }

  public function findById($id)
  {
      $user = $this
          ->where(["id" => $id])
          ->get () -> getFirstRow();
      // if (!$user) throw new Exception("Could not find user for specified ID");
      return $user;
  }
}
