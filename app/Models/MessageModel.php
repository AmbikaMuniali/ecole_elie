<?php
namespace App\Models;
use App\Models\MyParentModel;
use Exception;

class MessageModel extends MyParentModel
{
  protected $table = "message";
  protected $allowedFields = [
       "id", "fkuser", "fkmessage_prec", "isread", "media", "media_type", "corps_message", "created_at", "updated_at", "fkuser_destinataire"
  ];
  public function __construct() {
    parent::__construct($this -> table, $this -> allowedFields);
  }

  public function findMessageById($id)
  {
      $message = $this
          ->where(["id" => $id])
          ->get () -> getFirstRow();
      // if (!$message) throw new Exception("Could not find message for specified ID");
      return $message;
  }
}
