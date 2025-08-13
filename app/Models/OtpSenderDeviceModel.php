<?php
namespace App\Models;
use App\Models\MyParentModel;
use Exception;

class OtpSenderDeviceModel extends MyParentModel
{
  protected $table = "otp_sender_device";
  protected $allowedFields = [
       "id", "token", "numero_sim", "status", "reseau", "sms_sent_at", "created_at", "updated_at"
  ];
  public function __construct() {
    parent::__construct($this -> table, $this -> allowedFields);
  }

  public function findOtpSenderDeviceById($id)
  {
      $otpsenderdevice = $this
          ->where(["id" => $id])
          ->get () -> getFirstRow();
      // if (!$otpsenderdevice) throw new Exception("Could not find otpsenderdevice for specified ID");
      return $otpsenderdevice;
  }
}
