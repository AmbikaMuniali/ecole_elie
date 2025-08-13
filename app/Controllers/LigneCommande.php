<?php
namespace App\Controllers;
use App\Models\LigneCommandeModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
class LigneCommande extends BaseController
{
    /**
     * Get all LigneCommandes
     * @return Response
     */
    public function index()
    {
        $model = new LigneCommandeModel();
        return $this->getResponse(
            [
                'message' => 'LigneCommandes retrieved successfully',
                'lignecommandes' => $model->selectAll()
            ]
        );
    }
    /**
     * Create a new LigneCommande
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
 
        $model = new LigneCommandeModel();
        $lignecommande = $model->insert($input);
        
      //  $lignecommande = // INSERTED ID
        return $this->getResponse(
            [
                'message' => 'LigneCommande added successfully',
                'lignecommande' => $lignecommande
            ]
        );
    }
    /**
     * Get a single lignecommande by ID
     */
    public function show($id)
    {
        try {
            $model = new LigneCommandeModel();
            $lignecommande = $model->findLigneCommandeById($id);
            return $this->getResponse(
                [
                    'message' => 'LigneCommande retrieved successfully',
                    'lignecommande' => $lignecommande
                ]
            );
        } catch (Exception $e) {
            return $this->getResponse(
                [
                    'message' => 'Could not find lignecommande for specified ID'
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
        public function update($id)
    {
        try {
            $model = new LigneCommandeModel();
            $model->findLigneCommandeById($id);
          $input = $this->getRequestInput($this->request);
          
            $model->update($id, $input);
            $lignecommande = $model->findLigneCommandeById($id);
            return $this->getResponse(
                [
                    'message' => 'LigneCommande updated successfully',
                    'lignecommande' => $lignecommande
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
            $model = new LigneCommandeModel();
            $lignecommande = $model->findLigneCommandeById($id);
            $model->delete($lignecommande);
            return $this
                ->getResponse(
                    [
                        'message' => 'LigneCommande deleted successfully',
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