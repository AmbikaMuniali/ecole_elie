<?php
namespace App\Models;
use App\Models\MyParentModel;
use Exception;

class AdresseModel extends MyParentModel
{
  protected $table = "adresse";
  protected $allowedFields = [
       "id", "fkuser_create", "fkuser_validate", "longitude", "latitude", "is_registred", "code_OLC", "numero_rue", "description_batiment", "libelle_client", "libelle_kasokoo", "avenue", "created_at", "updated_at"
  ];
  public function __construct() {
    parent::__construct($this -> table, $this -> allowedFields);
  }

  public function findAdresseById($id)
  {
      $adresse = $this
          ->where(["id" => $id])
          ->get () -> getFirstRow();
      // if (!$adresse) throw new Exception("Could not find adresse for specified ID");
      return $adresse;
  }
}
