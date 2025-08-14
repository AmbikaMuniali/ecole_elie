<?php
namespace App\Models;
use App\Models\MyParentModel;
use Exception;

class DepenseModel extends MyParentModel
{
  protected $table = "depense";
  protected $allowedFields = [
       "id", "date_depense", "montant", "devise", "motif", "fk_user"
  ];
  public function __construct() {
    parent::__construct($this -> table, $this -> allowedFields);
  }

  public function findById($id)
  {
      $depense = $this
          ->where(["id" => $id])
          ->get () -> getFirstRow();
      // if (!$depense) throw new Exception("Could not find depense for specified ID");
      return $depense;
  }
}
