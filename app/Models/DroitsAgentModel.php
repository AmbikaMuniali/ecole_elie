<?php
namespace App\Models;
use App\Models\MyParentModel;
use Exception;

class DroitsAgentModel extends MyParentModel
{
  protected $table = "droits_agent";
  protected $allowedFields = [
       "id", "status", "fkagent", "fkdroit", "created_at", "updated_at"
  ];
  public function __construct() {
    parent::__construct($this -> table, $this -> allowedFields);
  }

  public function findDroitsAgentById($id)
  {
      $droitsagent = $this
          ->where(["id" => $id])
          ->get () -> getFirstRow();
      // if (!$droitsagent) throw new Exception("Could not find droitsagent for specified ID");
      return $droitsagent;
  }
}
