<?php
namespace App\Controllers;
use App\Models\DepenseModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
class Depense extends BaseController
{
    /**
     * Get all Depenses
     * @return Response
     */
    public function index()
    {
        $model = new DepenseModel();
        return $this->getResponse(
            [
                'message' => 'Depenses retrieved successfully',
                'result' => $model->selectAll()
            ]
        );
    }
    /**
     * Create a new Depense
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
 
        $model = new DepenseModel();
        $depense = $model->insert($input);
        
      //  $depense = // INSERTED ID
        return $this->getResponse(
            [
                'message' => 'Depense added successfully',
                'result' => $depense
            ]
        );
    }
    /**
     * Get a single depense by ID
     */
    public function show($id)
    {
        try {
            $model = new DepenseModel();
            $depense = $model->findDepenseById($id);
            return $this->getResponse(
                [
                    'message' => 'Depense retrieved successfully',
                    'result' => $depense
                ]
            );
        } catch (Exception $e) {
            return $this->getResponse(
                [
                    'message' => 'Could not find depense for specified ID'
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
        public function update($id)
    {
        try {
            $model = new DepenseModel();
            $model->findById($id);
          $input = $this->getRequestInput($this->request);
          
            $model->update($id, $input);
            $depense = $model->findById($id);
            return $this->getResponse(
                [
                    'message' => 'Depense updated successfully',
                    'result' => $depense
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
            $model = new DepenseModel();
            $depense = (array) $model->findById($id);
            $model->delete($depense);
            return $this
                ->getResponse(
                    [
                        'message' => 'Depense deleted successfully',
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