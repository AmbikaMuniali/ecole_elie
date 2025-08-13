<?php
namespace App\Models;
use App\Models\MyParentModel;
use Exception;

class LivraisonModel extends MyParentModel
{
  protected $table = "livraison";
  protected $allowedFields = [
       "id", "fkcommande", "fkagent", "created_at", "updated_at", "delivered_at", "status"
  ];
  public function __construct() {
    parent::__construct($this -> table, $this -> allowedFields);
  }

  public function findLivraisonById($id)
  {
      $livraison = $this
          ->where(["id" => $id])
          ->get () -> getFirstRow();
      // if (!$livraison) throw new Exception("Could not find livraison for specified ID");
      return $livraison;
  }
}
