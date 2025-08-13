<?php
namespace App\Models;
use App\Models\MyParentModel;
use Exception;

class OtpModel extends MyParentModel
{
  protected $table = "otp";
  protected $allowedFields = [
       "id", "value", "created_at", "updated_at", "expire_at", "sent_to", "fkuser", "status"
  ];
  public function __construct() {
    parent::__construct($this -> table, $this -> allowedFields);
  }

  public function findOtpById($id)
  {
      $otp = $this
          ->where(["id" => $id])
          ->get () -> getFirstRow();
      // if (!$otp) throw new Exception("Could not find otp for specified ID");
      return $otp;
  }
}
