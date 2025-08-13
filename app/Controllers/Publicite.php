<?php
namespace App\Controllers;
use App\Models\PubliciteModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
class Publicite extends BaseController
{
    /**
     * Get all Publicites
     * @return Response
     */
    public function index()
    {
        $model = new PubliciteModel();
        return $this->getResponse(
            [
                'message' => 'Publicites retrieved successfully',
                'publicites' => $model->selectAll()
            ]
        );
    }
    /**
     * Create a new Publicite
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
 
        $model = new PubliciteModel();
        $publicite = $model->insert($input);
        
      //  $publicite = // INSERTED ID
        return $this->getResponse(
            [
                'message' => 'Publicite added successfully',
                'publicite' => $publicite
            ]
        );
    }
    /**
     * Get a single publicite by ID
     */
    public function show($id)
    {
        try {
            $model = new PubliciteModel();
            $publicite = $model->findPubliciteById($id);
            return $this->getResponse(
                [
                    'message' => 'Publicite retrieved successfully',
                    'publicite' => $publicite
                ]
            );
        } catch (Exception $e) {
            return $this->getResponse(
                [
                    'message' => 'Could not find publicite for specified ID'
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
        public function update($id)
    {
        try {
            $model = new PubliciteModel();
            $model->findPubliciteById($id);
          $input = $this->getRequestInput($this->request);
          
            $model->update($id, $input);
            $publicite = $model->findPubliciteById($id);
            return $this->getResponse(
                [
                    'message' => 'Publicite updated successfully',
                    'publicite' => $publicite
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
            $model = new PubliciteModel();
            $publicite = $model->findPubliciteById($id);
            $model->delete($publicite);
            return $this
                ->getResponse(
                    [
                        'message' => 'Publicite deleted successfully',
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