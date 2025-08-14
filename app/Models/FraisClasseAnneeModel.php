<?php
namespace App\Models;
use App\Models\MyParentModel;
use Exception;

class FraisClasseAnneeModel extends MyParentModel
{
  protected $table = "frais_classe_annee";
  protected $allowedFields = [
       "id", "fk_frais", "fk_classe", "fk_annee"
  ];
  public function __construct() {
    parent::__construct($this -> table, $this -> allowedFields);
  }

  public function findById($id)
  {
      $fraisclasseannee = $this
          ->where(["id" => $id])
          ->get () -> getFirstRow();
      // if (!$fraisclasseannee) throw new Exception("Could not find fraisclasseannee for specified ID");
      return $fraisclasseannee;
  }
}
