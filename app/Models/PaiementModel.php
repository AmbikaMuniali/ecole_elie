<?php
namespace App\Models;
use App\Models\MyParentModel;
use Exception;

class PaiementModel extends MyParentModel
{
  protected $table = "paiement";
  protected $allowedFields = [
       "id", "montant", "devise", "fk_frais", "fk_eleve", "date_paiement", "fk_user"
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
