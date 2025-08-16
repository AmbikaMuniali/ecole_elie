<?php
namespace App\Models;
use App\Models\MyParentModel;
use Exception;

class CoursModel extends MyParentModel
{
  protected $table = "cours";
  protected $allowedFields = [
       "id", "nom", "description"
  ];
  public function __construct() {
    parent::__construct($this -> table, $this -> allowedFields);
  }

  public function findById($id)
  {
      $cours = $this
          ->where(["id" => $id])
          ->get () -> getFirstRow();
      // if (!$cours) throw new Exception("Could not find cours for specified ID");
      return $cours;
  }
}
