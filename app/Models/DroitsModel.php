<?php
namespace App\Models;
use App\Models\MyParentModel;
use Exception;

class DroitsModel extends MyParentModel
{
  protected $table = "droits";
  protected $allowedFields = [
       "id", "name", "code", "fkmodule", "created_at", "updated_at"
  ];
  public function __construct() {
    parent::__construct($this -> table, $this -> allowedFields);
  }

  public function findDroitsById($id)
  {
      $droits = $this
          ->where(["id" => $id])
          ->get () -> getFirstRow();
      // if (!$droits) throw new Exception("Could not find droits for specified ID");
      return $droits;
  }
}
