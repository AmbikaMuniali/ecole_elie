<?php
namespace App\Controllers;
use App\Models\PaiementModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
class Paiement extends BaseController
{
    /**
     * Get all Paiements
     * @return Response
     */
    public function index()
    {
        $model = new PaiementModel();
        return $this->getResponse(
            [
                'message' => 'Paiements retrieved successfully',
                'result' => $model->selectAll()
            ]
        );
    }
    /**
     * Create a new Paiement
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
 
        $model = new PaiementModel();
        $paiement = $model->insert($input);
        
      //  $paiement = // INSERTED ID
        return $this->getResponse(
            [
                'message' => 'Paiement added successfully',
                'result' => $paiement
            ]
        );
    }
    /**
     * Get a single paiement by ID
     */
    public function show($id)
    {
        try {
            $model = new PaiementModel();
            $paiement = $model->findById($id);
            return $this->getResponse(
                [
                    'message' => 'Paiement retrieved successfully',
                    'result' => $paiement
                ]
            );
        } catch (Exception $e) {
            return $this->getResponse(
                [
                    'message' => 'Could not find paiement for specified ID'
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
        public function update($id)
    {
        try {
            $model = new PaiementModel();
            $model->findById($id);
          $input = $this->getRequestInput($this->request);
          
            $model->update($id, $input);
            $paiement = $model->findById($id);
            return $this->getResponse(
                [
                    'message' => 'Paiement updated successfully',
                    'result' => $paiement
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
            $model = new PaiementModel();
            $paiement = (array) $model->findById($id);
            $model->delete($paiement);
            return $this
                ->getResponse(
                    [
                        'message' => 'Paiement deleted successfully',
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