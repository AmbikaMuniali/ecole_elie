<?php
namespace App\Models;
use App\Models\MyParentModel;
use Exception;

class CaisseModel extends MyParentModel
{
  protected $table = "caisse";
  protected $allowedFields = [
       "id", "designation", "created_at", "updated_at"
  ];
  public function __construct() {
    parent::__construct($this -> table, $this -> allowedFields);
  }

  public function findCaisseById($id)
  {
      $caisse = $this
          ->where(["id" => $id])
          ->get () -> getFirstRow();
      // if (!$caisse) throw new Exception("Could not find caisse for specified ID");
      return $caisse;
  }
}
