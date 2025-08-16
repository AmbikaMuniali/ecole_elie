<?php
namespace App\Models;
use App\Models\MyParentModel;
use Exception;

class ClasseModel extends MyParentModel
{
  protected $table = "classe";
  protected $allowedFields = [
       "id", "nom", "niveau_numerique"
  ];
  public function __construct() {
    parent::__construct($this -> table, $this -> allowedFields);
  }

  public function findById($id)
  {
      $classe = $this
          ->where(["id" => $id])
          ->get () -> getFirstRow();
      // if (!$classe) throw new Exception("Could not find classe for specified ID");
      return $classe;
  }
}
