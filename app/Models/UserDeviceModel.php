<?php
namespace App\Models;
use App\Models\MyParentModel;
use Exception;

class UserDeviceModel extends MyParentModel
{
  protected $table = "user_device";
  protected $allowedFields = [
       "id", "uuid", "fcm_token", "device_info", "fkuser", "created_at", "updated_at"
  ];
  public function __construct() {
    parent::__construct($this -> table, $this -> allowedFields);
  }

  public function findUserDeviceById($id)
  {
      $userdevice = $this
          ->where(["id" => $id])
          ->get () -> getFirstRow();
      // if (!$userdevice) throw new Exception("Could not find userdevice for specified ID");
      return $userdevice;
  }
}
