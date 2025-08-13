<?php
namespace App\Models;
use App\Models\MyParentModel;
use Exception;

class LigneCommandeModel extends MyParentModel
{
  protected $table = "ligne_commande";
  protected $allowedFields = [
       "id", "fkproduit", "fkcommande", "quantite", "montant", "created_at", "updated_at"
  ];
  public function __construct() {
    parent::__construct($this -> table, $this -> allowedFields);
  }

  public function findLigneCommandeById($id)
  {
      $lignecommande = $this
          ->where(["id" => $id])
          ->get () -> getFirstRow();
      // if (!$lignecommande) throw new Exception("Could not find lignecommande for specified ID");
      return $lignecommande;
  }
}
