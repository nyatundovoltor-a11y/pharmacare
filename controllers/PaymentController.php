<?php
class PaymentController extends Controller
{
    /** Requests the pharmacist has sent over, awaiting payment */
    public function pending(): void
    {
        Auth::requireRole(['cashier']);
        $requestModel = new DrugRequest();
        $this->render('payments/pending', ['requests' => $requestModel->awaitingPayment()]);
    }

    public function pay(): void
    {
        Auth::requireRole(['cashier']);
        $id = (int) $this->input('id');

        $requestModel = new DrugRequest();
        $request = $requestModel->findWithItems($id);

        if (!$request || $request['status'] !== 'awaiting_payment') {
            $this->flash('error', 'This request is no longer awaiting payment.');
            $this->redirect('payments_pending');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $method = $this->input('payment_method', 'cash');

            $paymentModel = new Payment();
            $paymentModel->create($id, Auth::id(), (float) $request['total_amount'], $method);
            $requestModel->markPaid($id);

            $this->flash('success', 'Payment recorded. Hand the customer their receipt - the pharmacist will dispense the drugs.');
            $this->redirect('payments_receipt', ['id' => $id]);
        }

        $this->render('payments/pay', ['request' => $request]);
    }

    public function receipt(): void
    {
        Auth::requireLogin(); // cashier prints it, pharmacist/customer may also view it
        $id = (int) $this->input('id');

        $requestModel = new DrugRequest();
        $paymentModel = new Payment();

        $request = $requestModel->findWithItems($id);
        $payment = $paymentModel->findByRequestId($id);

        if (!$request || !$payment) {
            http_response_code(404);
            echo "Receipt not found.";
            return;
        }

        $this->render('payments/receipt', ['request' => $request, 'payment' => $payment]);
    }
}