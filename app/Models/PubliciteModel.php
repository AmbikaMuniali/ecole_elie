<?php
namespace App\Models;
use App\Models\MyParentModel;
use Exception;

class PubliciteModel extends MyParentModel
{
  protected $table = "publicite";
  protected $allowedFields = [
       "id", "fkproduit", "image", "corps", "created_at", "updated_at"
  ];
  public function __construct() {
    parent::__construct($this -> table, $this -> allowedFields);
  }

  public function findPubliciteById($id)
  {
      $publicite = $this
          ->where(["id" => $id])
          ->get () -> getFirstRow();
      // if (!$publicite) throw new Exception("Could not find publicite for specified ID");
      return $publicite;
  }
}
