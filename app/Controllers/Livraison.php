<?php
namespace App\Controllers;
use App\Models\LivraisonModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
class Livraison extends BaseController
{
    /**
     * Get all Livraisons
     * @return Response
     */
    public function index()
    {
        $model = new LivraisonModel();
        return $this->getResponse(
            [
                'message' => 'Livraisons retrieved successfully',
                'livraisons' => $model->selectAll()
            ]
        );
    }
    /**
     * Create a new Livraison
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
 
        $model = new LivraisonModel();
        $livraison = $model->insert($input);
        
      //  $livraison = // INSERTED ID
        return $this->getResponse(
            [
                'message' => 'Livraison added successfully',
                'livraison' => $livraison
            ]
        );
    }
    /**
     * Get a single livraison by ID
     */
    public function show($id)
    {
        try {
            $model = new LivraisonModel();
            $livraison = $model->findLivraisonById($id);
            return $this->getResponse(
                [
                    'message' => 'Livraison retrieved successfully',
                    'livraison' => $livraison
                ]
            );
        } catch (Exception $e) {
            return $this->getResponse(
                [
                    'message' => 'Could not find livraison for specified ID'
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
        public function update($id)
    {
        try {
            $model = new LivraisonModel();
            $model->findLivraisonById($id);
          $input = $this->getRequestInput($this->request);
          
            $model->update($id, $input);
            $livraison = $model->findLivraisonById($id);
            return $this->getResponse(
                [
                    'message' => 'Livraison updated successfully',
                    'livraison' => $livraison
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
            $model = new LivraisonModel();
            $livraison = $model->findLivraisonById($id);
            $model->delete($livraison);
            return $this
                ->getResponse(
                    [
                        'message' => 'Livraison deleted successfully',
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