<?php
namespace App\Models;
use App\Models\MyParentModel;
use Exception;

class FournisseurModel extends MyParentModel
{
  protected $table = "fournisseur";
  protected $allowedFields = [
       "id", "denomination", "adresse", "phone", "email", "fkadresse", "forme_juridique", "created_at", "updated_at"
  ];
  public function __construct() {
    parent::__construct($this -> table, $this -> allowedFields);
  }

  public function findFournisseurById($id)
  {
      $fournisseur = $this
          ->where(["id" => $id])
          ->get () -> getFirstRow();
      // if (!$fournisseur) throw new Exception("Could not find fournisseur for specified ID");
      return $fournisseur;
  }
}
