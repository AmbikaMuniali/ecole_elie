<?php
namespace App\Models;
use App\Models\MyParentModel;
use Exception;

class InformationPaiementModel extends MyParentModel
{
  protected $table = "information_paiement";
  protected $allowedFields = [
       "id", "operateur", "numero_compte", "intutile_compte", "banque", "fkclient", "fkcaisse", "fkfournisseur", "created_at", "updated_at"
  ];
  public function __construct() {
    parent::__construct($this -> table, $this -> allowedFields);
  }

  public function findInformationPaiementById($id)
  {
      $informationpaiement = $this
          ->where(["id" => $id])
          ->get () -> getFirstRow();
      // if (!$informationpaiement) throw new Exception("Could not find informationpaiement for specified ID");
      return $informationpaiement;
  }
}
