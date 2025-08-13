<?php
namespace App\Controllers;
use App\Models\ProduitModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
class MyProduitController extends BaseController
{
    /**
     * Get all Produits
     * @return Response
     */
    public function index()
    {
        $model = new ProduitModel();
        return $this->getResponse(
            [
                'message' => 'Produits retrieved successfully',
                'produits' => $model->where    (["status" => "ACTIF"])->orderBy ('fkcategorie_prod')->get()->getResultArray()
            ]
        );
    }
    /**
     * Create a new Produit
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
 
        $model = new ProduitModel();
        $produit = $model->insert($input);
        
      //  $produit = // INSERTED ID
        return $this->getResponse(
            [
                'message' => 'Produit added successfully',
                'produit' => $produit
            ]
        );
    }
    /**
     * Get a single produit by ID
     */
    public function show($id)
    {
        try {
            $model = new ProduitModel();
            $produit = $model->findProduitById($id);
            return $this->getResponse(
                [
                    'message' => 'Produit retrieved successfully',
                    'produit' => $produit
                ]
            );
        } catch (Exception $e) {
            return $this->getResponse(
                [
                    'message' => 'Could not find produit for specified ID'
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
        public function update($id)
    {
        try {
            $model = new ProduitModel();
            $model->findProduitById($id);
          $input = $this->getRequestInput($this->request);
          
            $model->update($id, $input);
            $produit = $model->findProduitById($id);
            return $this->getResponse(
                [
                    'message' => 'Produit updated successfully',
                    'produit' => $produit
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
            $model = new ProduitModel();
            $produit = $model->findProduitById($id);
            $model->delete($produit);
            return $this
                ->getResponse(
                    [
                        'message' => 'Produit deleted successfully',
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