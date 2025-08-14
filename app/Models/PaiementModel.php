<?php
namespace App\Models;
use App\Models\MyParentModel;
use Exception;

class PaiementModel extends MyParentModel
{
  protected $table = "paiement";
  protected $allowedFields = [
       "id", "date_paiement", "montant", "devise", "fk_eleve", "fk_annee", "fk_classe", "fk_user", "fk_frais"
  ];
  public function __construct() {
    parent::__construct($this -> table, $this -> allowedFields);
  }

  public function findById($id)
  {
      $paiement = $this
          ->where(["id" => $id])
          ->get () -> getFirstRow();
      // if (!$paiement) throw new Exception("Could not find paiement for specified ID");
      return $paiement;
  }
}
