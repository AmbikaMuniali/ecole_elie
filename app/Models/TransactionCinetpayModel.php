<?php
namespace App\Models;
use App\Models\MyParentModel;
use Exception;

class TransactionCinetpayModel extends MyParentModel
{
  protected $table = "transaction_cinetpay";
  protected $allowedFields = [
       "id", "fkclient", "montant","devise","data_json", "status", "numero", "transaction_id", "transaction_token", "created_at", "updated_at"
  ];
  public function __construct() {
    parent::__construct($this -> table, $this -> allowedFields);
  }

  public function findTransactionCinetpayById($id)
  {
      $transactioncinetpay = $this
          ->where(["id" => $id])
          ->get () -> getFirstRow();
      // if (!$transactioncinetpay) throw new Exception("Could not find transactioncinetpay for specified ID");
      return $transactioncinetpay;
  }
}
