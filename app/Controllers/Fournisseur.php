<?php
namespace App\Controllers;
use App\Models\FournisseurModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
class Fournisseur extends BaseController
{
    /**
     * Get all Fournisseurs
     * @return Response
     */
    public function index()
    {
        $model = new FournisseurModel();
        return $this->getResponse(
            [
                'message' => 'Fournisseurs retrieved successfully',
                'fournisseurs' => $model->selectAll()
            ]
        );
    }
    /**
     * Create a new Fournisseur
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
 
        $model = new FournisseurModel();
        $fournisseur = $model->insert($input);
        
      //  $fournisseur = // INSERTED ID
        return $this->getResponse(
            [
                'message' => 'Fournisseur added successfully',
                'fournisseur' => $fournisseur
            ]
        );
    }
    /**
     * Get a single fournisseur by ID
     */
    public function show($id)
    {
        try {
            $model = new FournisseurModel();
            $fournisseur = $model->findFournisseurById($id);
            return $this->getResponse(
                [
                    'message' => 'Fournisseur retrieved successfully',
                    'fournisseur' => $fournisseur
                ]
            );
        } catch (Exception $e) {
            return $this->getResponse(
                [
                    'message' => 'Could not find fournisseur for specified ID'
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
        public function update($id)
    {
        try {
            $model = new FournisseurModel();
            $model->findFournisseurById($id);
          $input = $this->getRequestInput($this->request);
          
            $model->update($id, $input);
            $fournisseur = $model->findFournisseurById($id);
            return $this->getResponse(
                [
                    'message' => 'Fournisseur updated successfully',
                    'fournisseur' => $fournisseur
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
            $model = new FournisseurModel();
            $fournisseur = $model->findFournisseurById($id);
            $model->delete($fournisseur);
            return $this
                ->getResponse(
                    [
                        'message' => 'Fournisseur deleted successfully',
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