<?php
namespace App\Controllers;
use App\Models\CompteModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
class Compte extends BaseController
{
    /**
     * Get all Comptes
     * @return Response
     */
    public function index()
    {
        $model = new CompteModel();
        return $this->getResponse(
            [
                'message' => 'Comptes retrieved successfully',
                'comptes' => $model->selectAll()
            ]
        );
    }
    /**
     * Create a new Compte
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
 
        $model = new CompteModel();
        $compte = $model->insert($input);
        
      //  $compte = // INSERTED ID
        return $this->getResponse(
            [
                'message' => 'Compte added successfully',
                'compte' => $compte
            ]
        );
    }
    /**
     * Get a single compte by ID
     */
    public function show($id)
    {
        try {
            $model = new CompteModel();
            $compte = $model->findCompteById($id);
            return $this->getResponse(
                [
                    'message' => 'Compte retrieved successfully',
                    'compte' => $compte
                ]
            );
        } catch (Exception $e) {
            return $this->getResponse(
                [
                    'message' => 'Could not find compte for specified ID'
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
        public function update($id)
    {
        try {
            $model = new CompteModel();
            $model->findCompteById($id);
          $input = $this->getRequestInput($this->request);
          
            $model->update($id, $input);
            $compte = $model->findCompteById($id);
            return $this->getResponse(
                [
                    'message' => 'Compte updated successfully',
                    'compte' => $compte
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
            $model = new CompteModel();
            $compte = $model->findCompteById($id);
            $model->delete($compte);
            return $this
                ->getResponse(
                    [
                        'message' => 'Compte deleted successfully',
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