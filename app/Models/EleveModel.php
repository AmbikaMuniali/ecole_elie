<?php
namespace App\Models;
use App\Models\MyParentModel;
use Exception;

class EleveModel extends MyParentModel
{
  protected $table = "eleve";
  protected $allowedFields = [
       "id", "nom", "postnom", "prenom", "date_naissance", "genre", "adresse", "telephone_parent", "date_inscription"
  ];
  public function __construct() {
    parent::__construct($this -> table, $this -> allowedFields);
  }

  public function findById($id)
  {
      $eleve = $this
          ->where(["id" => $id])
          ->get () -> getFirstRow();
      // if (!$eleve) throw new Exception("Could not find eleve for specified ID");
      return $eleve;
  }
}
