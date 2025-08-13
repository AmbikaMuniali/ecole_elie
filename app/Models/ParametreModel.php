<?php
namespace App\Models;
use App\Models\MyParentModel;
use Exception;

class ParametreModel extends MyParentModel
{
  protected $table = "parametre";
  protected $allowedFields = [
       "id", "devise", "taux_change", "created_at", "updated_at", "adresse", "phone", "email", "logo", "app_version"
  ];
  public function __construct() {
    parent::__construct($this -> table, $this -> allowedFields);
  }

  public function findParametreById($id)
  {
      $parametre = $this
          ->where(["id" => $id])
          ->get () -> getFirstRow();
      // if (!$parametre) throw new Exception("Could not find parametre for specified ID");
      return $parametre;
  }
}
