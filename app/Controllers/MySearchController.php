<?php
namespace App\Controllers;
use App\Models\AchatModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
class MySearchController  extends BaseController
{
    /**
     * Get all Achats
     * @return Response
     */
    public function index()
    {


        $input = $this->getRequestInput($this->request);



        $where = isset($input['where']) ? $input['where']: '';
        $like = isset($input['like']) ? $input['like'] : '';
        $min = isset($input['min']) ? $input['min']:'';
        $max = isset($input['max']) ? $input['max'] :'';
        $limit = isset($input['limit']) ? $input['limit']: '';
        $offset = isset($input['offset']) ? $input['offset']: '';
        $group_by = isset($input['group_by']) ? $input['group_by']: '';
        $order_by = isset($input['order_by']) ? $input['order_by']: '';


        $data = [];
        if(!empty($where)) $data["where"] =$where;
        if(!empty($like)) $data["like"] =$like; 
        if(!empty($min)) $data["min"] =$min;
        if(!empty($max)) $data["max"] =$max;
        if(!empty($limit)) $data["limit"] =$limit;
        if(!empty($offset)) $data["offset"] =$offset;
        if(!empty($group_by)) $data["group_by"] =$group_by;
        if(!empty($order_by)) $data["order_by"] =$order_by;









        
        if(isset($input['table']) ) {

            $table = $input['table'];

            $model  = $this -> getModelName($table);

           $model =  model($model);
            
      }
        return $this->getResponse(
            [
                'message' => 'Data retrieved successfully',
                'data' => $data,
                'result' =>$model -> search($data)
            ]
        );
    }

    function getModelName($tableName) {
    // Split the table name by the underscore character
    $parts = explode('_', $tableName);
    
    // Capitalize the first letter of each part
    $camelCaseParts = array_map('ucfirst', $parts);
    
    // Join the parts and append 'Model'
    $modelName = implode('', $camelCaseParts) . 'Model';
    
    return $modelName;
}
    
}
