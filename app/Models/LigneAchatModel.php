<?php
namespace App\Models;
use App\Models\MyParentModel;
use Exception;

class LigneAchatModel extends MyParentModel
{
  protected $table = "ligne_achat";
  protected $allowedFields = [
       "id", "fkproduit", "fkfournisseur", "fkachat", "quantite", "montant", "created_at", "updated_at"
  ];
  public function __construct() {
    parent::__construct($this -> table, $this -> allowedFields);
  }

  public function findLigneAchatById($id)
  {
      $ligneachat = $this
          ->where(["id" => $id])
          ->get () -> getFirstRow();
      // if (!$ligneachat) throw new Exception("Could not find ligneachat for specified ID");
      return $ligneachat;
  }
}
