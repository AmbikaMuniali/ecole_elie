<?php
namespace App\Controllers;
use App\Models\CategorieProdModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
class CategorieProd extends BaseController
{
    /**
     * Get all CategorieProds
     * @return Response
     */
    public function index()
    {
        $model = new CategorieProdModel();
        return $this->getResponse(
            [
                'message' => 'CategorieProds retrieved successfully',
                'categorieprods' => $model->selectAll()
            ]
        );
    }
    /**
     * Create a new CategorieProd
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
 
        $model = new CategorieProdModel();
        $categorieprod = $model->insert($input);
        
      //  $categorieprod = // INSERTED ID
        return $this->getResponse(
            [
                'message' => 'CategorieProd added successfully',
                'categorieprod' => $categorieprod
            ]
        );
    }
    /**
     * Get a single categorieprod by ID
     */
    public function show($id)
    {
        try {
            $model = new CategorieProdModel();
            $categorieprod = $model->findCategorieProdById($id);
            return $this->getResponse(
                [
                    'message' => 'CategorieProd retrieved successfully',
                    'categorieprod' => $categorieprod
                ]
            );
        } catch (Exception $e) {
            return $this->getResponse(
                [
                    'message' => 'Could not find categorieprod for specified ID'
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
        public function update($id)
    {
        try {
            $model = new CategorieProdModel();
            $model->findCategorieProdById($id);
          $input = $this->getRequestInput($this->request);
          
            $model->update($id, $input);
            $categorieprod = $model->findCategorieProdById($id);
            return $this->getResponse(
                [
                    'message' => 'CategorieProd updated successfully',
                    'categorieprod' => $categorieprod
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
            $model = new CategorieProdModel();
            $categorieprod = $model->findCategorieProdById($id);
            $model->delete($categorieprod);
            return $this
                ->getResponse(
                    [
                        'message' => 'CategorieProd deleted successfully',
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