<?php
namespace App\Models;
use App\Models\MyParentModel;
use Exception;

class ProduitModel extends MyParentModel
{
  protected $table = "produit";
  protected $allowedFields = [
       "id", "code", "designation", "description", "unite", "status", "devise", "prix_vente", "poids", "volume", "photo", "created_at", "updated_at", "fkcategorie_prod"
  ];
  public function __construct() {
    parent::__construct($this -> table, $this -> allowedFields);
  }

  public function findProduitById($id)
  {
      $produit = $this
          ->where(["id" => $id])
          ->get () -> getFirstRow();
      // if (!$produit) throw new Exception("Could not find produit for specified ID");
      return $produit;
  }
}
