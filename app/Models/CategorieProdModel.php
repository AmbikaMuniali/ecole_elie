<?php
namespace App\Models;
use App\Models\MyParentModel;
use Exception;

class CategorieProdModel extends MyParentModel
{
  protected $table = "categorie_prod";
  protected $allowedFields = [
       "id", "designation", "description", "created_at", "updated_at"
  ];
  public function __construct() {
    parent::__construct($this -> table, $this -> allowedFields);
  }

  public function findCategorieProdById($id)
  {
      $categorieprod = $this
          ->where(["id" => $id])
          ->get () -> getFirstRow();
      // if (!$categorieprod) throw new Exception("Could not find categorieprod for specified ID");
      return $categorieprod;
  }
}
