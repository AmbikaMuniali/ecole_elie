<?php
namespace App\Models;
use App\Models\MyParentModel;
use Exception;

class ModuleModel extends MyParentModel
{
  protected $table = "module";
  protected $allowedFields = [
       "id", "name", "created_at", "updated_at"
  ];
  public function __construct() {
    parent::__construct($this -> table, $this -> allowedFields);
  }

  public function findModuleById($id)
  {
      $module = $this
          ->where(["id" => $id])
          ->get () -> getFirstRow();
      // if (!$module) throw new Exception("Could not find module for specified ID");
      return $module;
  }
}
