<?php
namespace App\Models;
use App\Models\MyParentModel;
use Exception;

class CompteModel extends MyParentModel
{
  protected $table = "compte";
  protected $allowedFields = [
       "id", "devise", "intutile", "type_compte", "fkagent","fkclient", "fkfournisseur", "fkcaisse", "created_at", "updated_at"
  ];
  public function __construct() {
    parent::__construct($this -> table, $this -> allowedFields);
  }

  public function findCompteById($id)
  {
      $compte = $this
          ->where(["id" => $id])
          ->get () -> getFirstRow();
      // if (!$compte) throw new Exception("Could not find compte for specified ID");
      return $compte;
  }
}
