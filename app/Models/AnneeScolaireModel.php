<?php
namespace App\Models;
use App\Models\MyParentModel;
use Exception;

class AnneeScolaireModel extends MyParentModel
{
  protected $table = "annee_scolaire";
  protected $allowedFields = [
       "id", "nom"
  ];
  public function __construct() {
    parent::__construct($this -> table, $this -> allowedFields);
  }

  public function findById($id)
  {
      $anneescolaire = $this
          ->where(["id" => $id])
          ->get () -> getFirstRow();
      // if (!$anneescolaire) throw new Exception("Could not find anneescolaire for specified ID");
      return $anneescolaire;
  }
}
