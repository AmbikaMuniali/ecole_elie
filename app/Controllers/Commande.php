<?php
namespace App\Controllers;
use App\Models\CommandeModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
class Commande extends BaseController
{
    /**
     * Get all Commandes
     * @return Response
     */
    public function index()
    {
        $model = new CommandeModel();
        return $this->getResponse(
            [
                'message' => 'Commandes retrieved successfully',
                'commandes' => $model->selectAll()
            ]
        );
    }
    /**
     * Create a new Commande
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
 
        $model = new CommandeModel();
        $commande = $model->insert($input);
        
      //  $commande = // INSERTED ID
        return $this->getResponse(
            [
                'message' => 'Commande added successfully',
                'commande' => $commande
            ]
        );
    }
    /**
     * Get a single commande by ID
     */
    public function show($id)
    {
        try {
            $model = new CommandeModel();
            $commande = $model->findCommandeById($id);
            return $this->getResponse(
                [
                    'message' => 'Commande retrieved successfully',
                    'commande' => $commande
                ]
            );
        } catch (Exception $e) {
            return $this->getResponse(
                [
                    'message' => 'Could not find commande for specified ID'
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
        public function update($id)
    {
        try {
            $model = new CommandeModel();
            $model->findCommandeById($id);
          $input = $this->getRequestInput($this->request);
          
            $model->update($id, $input);
            $commande = $model->findCommandeById($id);
            return $this->getResponse(
                [
                    'message' => 'Commande updated successfully',
                    'commande' => $commande
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
            $model = new CommandeModel();
            $commande = $model->findCommandeById($id);
            $model->delete($commande);
            return $this
                ->getResponse(
                    [
                        'message' => 'Commande deleted successfully',
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