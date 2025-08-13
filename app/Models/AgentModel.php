<?php
namespace App\Models;
use App\Models\MyParentModel;
use Exception;

class AgentModel extends MyParentModel
{
  protected $table = "agent";
  protected $allowedFields = [
       "id", "name_complet", "fonction", "phone", "created_at", "updated_at", "solde_usd", "solde_cdf"
  ];
  public function __construct() {
    parent::__construct($this -> table, $this -> allowedFields);
  }

  public function findAgentById($id)
  {
      $agent = $this
          ->where(["id" => $id])
          ->get () -> getFirstRow();
      // if (!$agent) throw new Exception("Could not find agent for specified ID");
      return $agent;
  }
}
