<?php
namespace App\Models;
use App\Models\MyParentModel;
use Exception;

class FraisModel extends MyParentModel
{
  protected $table = "frais";
  protected $allowedFields = [
       "id", "nom", "fk_type_frais", "montant", "devise", "fk_classe", "fk_annee"
  ];
  public function __construct() {
    parent::__construct($this -> table, $this -> allowedFields);
  }

  public function findById($id)
  {
      $frais = $this
          ->where(["id" => $id])
          ->get () -> getFirstRow();
      // if (!$frais) throw new Exception("Could not find frais for specified ID");
      return $frais;
  }
}
