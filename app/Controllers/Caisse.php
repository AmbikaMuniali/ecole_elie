<?php
namespace App\Controllers;
use App\Models\CaisseModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
class Caisse extends BaseController
{
    /**
     * Get all Caisses
     * @return Response
     */
    public function index()
    {
        $model = new CaisseModel();
        return $this->getResponse(
            [
                'message' => 'Caisses retrieved successfully',
                'caisses' => $model->selectAll()
            ]
        );
    }
    /**
     * Create a new Caisse
     */
    public function store()
    {
        $rules = [
            //'name' => 'required',   //INSERT VALIDATION RULES
            //
            //
        ];
        $input = $this->getRequestInput($this->request);
        if ($this->validateRequest($input, $rules)) {
            return $this
                ->getResponse(
                    $this->validator->getErrors(),
                    ResponseInterface::HTTP_BAD_REQUEST
                );
        }
 
        $model = new CaisseModel();
        $caisse = $model->insert($input);
        
      //  $caisse = // INSERTED ID
        return $this->getResponse(
            [
                'message' => 'Caisse added successfully',
                'caisse' => $caisse
            ]
        );
    }
    /**
     * Get a single caisse by ID
     */
    public function show($id)
    {
        try {
            $model = new CaisseModel();
            $caisse = $model->findCaisseById($id);
            return $this->getResponse(
                [
                    'message' => 'Caisse retrieved successfully',
                    'caisse' => $caisse
                ]
            );
        } catch (Exception $e) {
            return $this->getResponse(
                [
                    'message' => 'Could not find caisse for specified ID'
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
        public function update($id)
    {
        try {
            $model = new CaisseModel();
            $model->findCaisseById($id);
          $input = $this->getRequestInput($this->request);
          
            $model->update($id, $input);
            $caisse = $model->findCaisseById($id);
            return $this->getResponse(
                [
                    'message' => 'Caisse updated successfully',
                    'caisse' => $caisse
                ]
            );
        } catch (Exception $exception) {
            return $this->getResponse(
                [
                    'message' => $exception->getMessage()
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
public function delete($id) {

       return $this -> destroy($id);
    }

    public function create() {
        return $this -> store();
    }
    public function destroy($id)
    {
        try {
            $model = new CaisseModel();
            $caisse = $model->findCaisseById($id);
            $model->delete($caisse);
            return $this
                ->getResponse(
                    [
                        'message' => 'Caisse deleted successfully',
                    ]
                );
        } catch (Exception $exception) {
            return $this->getResponse(
                [
                    'message' => $exception->getMessage()
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
}