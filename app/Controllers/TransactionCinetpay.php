<?php
namespace App\Controllers;
use App\Models\TransactionCinetpayModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
class TransactionCinetpay extends BaseController
{
    /**
     * Get all TransactionCinetpays
     * @return Response
     */
    public function index()
    {
        $model = new TransactionCinetpayModel();
        return $this->getResponse(
            [
                'message' => 'TransactionCinetpays retrieved successfully',
                'transactioncinetpays' => $model->selectAll()
            ]
        );
    }
    /**
     * Create a new TransactionCinetpay
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
 
        $model = new TransactionCinetpayModel();
        $transactioncinetpay = $model->insert($input);
        
      //  $transactioncinetpay = // INSERTED ID
        return $this->getResponse(
            [
                'message' => 'TransactionCinetpay added successfully',
                'transactioncinetpay' => $transactioncinetpay
            ]
        );
    }
    /**
     * Get a single transactioncinetpay by ID
     */
    public function show($id)
    {
        try {
            $model = new TransactionCinetpayModel();
            $transactioncinetpay = $model->findTransactionCinetpayById($id);
            return $this->getResponse(
                [
                    'message' => 'TransactionCinetpay retrieved successfully',
                    'transactioncinetpay' => $transactioncinetpay
                ]
            );
        } catch (Exception $e) {
            return $this->getResponse(
                [
                    'message' => 'Could not find transactioncinetpay for specified ID'
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
        public function update($id)
    {
        try {
            $model = new TransactionCinetpayModel();
            $model->findTransactionCinetpayById($id);
          $input = $this->getRequestInput($this->request);
          
            $model->update($id, $input);
            $transactioncinetpay = $model->findTransactionCinetpayById($id);
            return $this->getResponse(
                [
                    'message' => 'TransactionCinetpay updated successfully',
                    'transactioncinetpay' => $transactioncinetpay
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
            $model = new TransactionCinetpayModel();
            $transactioncinetpay = $model->findTransactionCinetpayById($id);
            $model->delete($transactioncinetpay);
            return $this
                ->getResponse(
                    [
                        'message' => 'TransactionCinetpay deleted successfully',
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