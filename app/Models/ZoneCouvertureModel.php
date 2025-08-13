<?php
namespace App\Models;
use App\Models\MyParentModel;
use Exception;

class ZoneCouvertureModel extends MyParentModel
{
  protected $table = "zone_couverture";
  protected $allowedFields = [
       "id", "status", "designation", "frontieres", "created_at", "updated_at"
  ];
  public function __construct() {
    parent::__construct($this -> table, $this -> allowedFields);
  }

  public function findZoneCouvertureById($id)
  {
      $zonecouverture = $this
          ->where(["id" => $id])
          ->get () -> getFirstRow();
      // if (!$zonecouverture) throw new Exception("Could not find zonecouverture for specified ID");
      return $zonecouverture;
  }
}
