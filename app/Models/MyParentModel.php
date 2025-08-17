<?php

namespace App\Models;

use CodeIgniter\Model;
use Exception;

class MyParentModel extends Model
{
    protected $table;
    protected $allowedFields;
    protected $updatedField = "updated_at";

    public function __construct($table, $allowedFields) {
        parent::__construct(); // Initialize the parent Model first
        $this->table = $table;
        $this->allowedFields = $allowedFields;
    }

    public function search($data)
    {
        $where = $data['where'] ?? [];
        $like = $data['like'] ?? [];
        $min = $data['min'] ?? [];
        $max = $data['max'] ?? [];
        $limit = $data['limit'] ?? null;
        $offset = $data['offset'] ?? 0;
        $group_by = $data['group_by'] ?? '';
        $order_by = $data['order_by'] ?? '';
        
        $builder = $this->db->table($this->table);
        
        if(!empty($where)) {
            $builder->where($where);
        }

        if(!empty($like)) {
            $builder->like($like);
        }

        if(!empty($min)) {
            foreach ($min as $key => $value) {
                $builder->where("$key >=", $value);
            }
        }

        if(!empty($max)) {
            foreach ($max as $key => $value) {
                $builder->where("$key <=", $value);
            }
        }

        if(!empty($group_by)) {
            $builder->groupBy($group_by);
        }

        if(!empty($order_by)) {
            $builder->orderBy($order_by);
        }

        if($limit !== null && is_numeric($limit)) {
            $builder->limit($limit, is_numeric($offset) ? $offset : 0);
            // return $builder -> getCompiledSelect();
        }

        return $builder->get()->getResultArray();
    }

    public function selectAll() {
        return $this->db->table($this->table)
                      ->select()
                      ->get()
                      ->getResultArray();
    }
}