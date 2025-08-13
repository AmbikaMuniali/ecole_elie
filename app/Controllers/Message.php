<?php
namespace App\Controllers;
use App\Models\MessageModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
class Message extends BaseController
{
    /**
     * Get all Messages
     * @return Response
     */
    public function index()
    {
        $model = new MessageModel();
        return $this->getResponse(
            [
                'message' => 'Messages retrieved successfully',
                'messages' => $model->selectAll()
            ]
        );
    }
    /**
     * Create a new Message
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
 
        $model = new MessageModel();
        $message = $model->insert($input);
        
      //  $message = // INSERTED ID
        return $this->getResponse(
            [
                'message' => 'Message added successfully',
                'message' => $message
            ]
        );
    }
    /**
     * Get a single message by ID
     */
    public function show($id)
    {
        try {
            $model = new MessageModel();
            $message = $model->findMessageById($id);
            return $this->getResponse(
                [
                    'message' => 'Message retrieved successfully',
                    'message' => $message
                ]
            );
        } catch (Exception $e) {
            return $this->getResponse(
                [
                    'message' => 'Could not find message for specified ID'
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
        public function update($id)
    {
        try {
            $model = new MessageModel();
            $model->findMessageById($id);
          $input = $this->getRequestInput($this->request);
          
            $model->update($id, $input);
            $message = $model->findMessageById($id);
            return $this->getResponse(
                [
                    'message' => 'Message updated successfully',
                    'message' => $message
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
            $model = new MessageModel();
            $message = $model->findMessageById($id);
            $model->delete($message);
            return $this
                ->getResponse(
                    [
                        'message' => 'Message deleted successfully',
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