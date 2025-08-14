<?php
namespace App\Models;
use App\Models\MyParentModel;
use Exception;

class ClasseLogiqueModel extends MyParentModel
{
  protected $table = "classe_logique";
  protected $allowedFields = [
       "id", "nom", "niveau_numerique", "ecole"
  ];
  public function __construct() {
    parent::__construct($this -> table, $this -> allowedFields);
  }

  public function findById($id)
  {
      $classelogique = $this
          ->where(["id" => $id])
          ->get () -> getFirstRow();
      // if (!$classelogique) throw new Exception("Could not find classelogique for specified ID");
      return $classelogique;
  }
}
