<?php
namespace App\Controllers;
use App\Models\DroitsModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
class Droits extends BaseController
{
    /**
     * Get all Droitss
     * @return Response
     */
    public function index()
    {
        $model = new DroitsModel();
        return $this->getResponse(
            [
                'message' => 'Droitss retrieved successfully',
                'droitss' => $model->selectAll()
            ]
        );
    }
    /**
     * Create a new Droits
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
 
        $model = new DroitsModel();
        $droits = $model->insert($input);
        
      //  $droits = // INSERTED ID
        return $this->getResponse(
            [
                'message' => 'Droits added successfully',
                'droits' => $droits
            ]
        );
    }
    /**
     * Get a single droits by ID
     */
    public function show($id)
    {
        try {
            $model = new DroitsModel();
            $droits = $model->findDroitsById($id);
            return $this->getResponse(
                [
                    'message' => 'Droits retrieved successfully',
                    'droits' => $droits
                ]
            );
        } catch (Exception $e) {
            return $this->getResponse(
                [
                    'message' => 'Could not find droits for specified ID'
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
        public function update($id)
    {
        try {
            $model = new DroitsModel();
            $model->findDroitsById($id);
          $input = $this->getRequestInput($this->request);
          
            $model->update($id, $input);
            $droits = $model->findDroitsById($id);
            return $this->getResponse(
                [
                    'message' => 'Droits updated successfully',
                    'droits' => $droits
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
            $model = new DroitsModel();
            $droits = $model->findDroitsById($id);
            $model->delete($droits);
            return $this
                ->getResponse(
                    [
                        'message' => 'Droits deleted successfully',
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