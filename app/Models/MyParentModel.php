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
        $this -> table = $table;
        $this -> allowedFields = $allowedFields;
    }

    public function search($data)
    {
        //SEARCH DATA WHERE ALLOWED FIELDS IN LIKE ARE LIKE, 
        // OR ALLOWED FIELDS IN min are less than
        // OR ALLOWED FIELDS IN MAX are great than
        // OR ALLOWED FIELDS IN before are BEFORE THE GIVEN VALUE
        // LIMIT AND PAGE OFFSET are given ORDER

        $where = isset($data['where']) ? $data['where']: '';
        $like = isset($data['like']) ? $data['like'] : '';
        $min = isset($data['min']) ? $data['min']:'';
        $max = isset($data['max']) ? $data['max'] :'';
        $limit = isset($data['limit']) ? $data['limit']: '';
        $offset = isset($data['offset']) ? $data['offset']: 1;
        $group_by = isset($data['group_by']) ? $data['group_by']: '';
        $order_by = isset($data['order_by']) ? $data['order_by']: '';
        
    
    
        $requete = $this;
        
        if(!empty($where) ){
            $requete = $requete->where($where);
        }


        

        if(!empty($like) ){
            $requete = $requete->like($like);
        }

        if(!empty($min) ) {
            $min1 = [];
            foreach ($min as $key => $value) {
                $min1[$key.' >= '] = $value;
            }
            $requete = $requete->where($min1);
        }

        if(!empty($max) ){
            $max1 = [];
            foreach ($max as $key => $value) {
                $max1[$key.' <= '] = $value;
            }
            $requete = $requete->where($max1);
        }

        if(is_numeric($limit) && is_numeric($offset)) {
            $requete = $requete->limit($limit, $offset);   
        }

        if(!empty($group_by) ){
            $requete = $requete->groupBy($group_by);
        }

        if(!empty($order_by)) {
            $requete = $requete->orderBy($order_by);
        }

        return  $requete -> get() -> getResultArray();


        
    }

    public function selectAll() {
        return $this -> select() -> get() -> getResultArray();
    }

   
}