<?php
namespace App\Models;
use App\Models\MyParentModel;
use Exception;

class EleveClasseAnneeModel extends MyParentModel
{
  protected $table = "eleve_classe_annee";
  protected $allowedFields = [
       "id", "fk_eleve", "fk_classe", "fk_annee"
  ];
  public function __construct() {
    parent::__construct($this -> table, $this -> allowedFields);
  }

  public function findById($id)
  {
      $eleveclasseannee = $this
          ->where(["id" => $id])
          ->get () -> getFirstRow();
      // if (!$eleveclasseannee) throw new Exception("Could not find eleveclasseannee for specified ID");
      return $eleveclasseannee;
  }
}
