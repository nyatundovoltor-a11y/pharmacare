<?php
class RequestController extends Controller
{
    public function index(): void
    {
        Auth::requireRole(['pharmacist']);
        $requestModel = new DrugRequest();
        $this->render('requests/list', ['requests' => $requestModel->byPharmacist(Auth::id())]);
    }

    public function create(): void
    {
        Auth::requireRole(['pharmacist']);
        $drugModel = new Drug();
        $this->render('requests/create', [
            'drugs' => $drugModel->all(),
            'error' => $_SESSION['form_error'] ?? null,
        ]);
        unset($_SESSION['form_error']);
    }

    /**
     * The pharmacist has checked the doctor's note against the drugs list
     * (see drugs/create.php + drugs_search) and is now submitting the
     * items the customer wants. If any item is out of stock, no request
     * is created and the pharmacist tells the customer it's unavailable.
     */
    public function store(): void
    {
        Auth::requireRole(['pharmacist']);

        $customerName  = trim($this->input('customer_name', ''));
        $customerPhone = trim($this->input('customer_phone', ''));
        $drugIds       = $this->input('drug_id', []);
        $quantities    = $this->input('quantity', []);

        if ($customerName === '') {
            $_SESSION['form_error'] = "Please enter the customer's name.";
            $this->redirect('requests_create');
        }

        if (empty($drugIds)) {
            $_SESSION['form_error'] = 'Add at least one drug from the note.';
            $this->redirect('requests_create');
        }

        $items = [];
        foreach ($drugIds as $i => $drugId) {
            $qty = (int) ($quantities[$i] ?? 0);
            if ($drugId && $qty > 0) {
                $items[] = ['drug_id' => (int) $drugId, 'quantity' => $qty];
            }
        }

        if (empty($items)) {
            $_SESSION['form_error'] = 'Enter valid quantities for the drugs requested.';
            $this->redirect('requests_create');
        }

        $requestModel = new DrugRequest();
        try {
            $requestId = $requestModel->createWithItems($customerName, $customerPhone, Auth::id(), $items);
        } catch (RuntimeException $e) {
            // e.g. "Insufficient stock for Paracetamol" - drug not available
            $_SESSION['form_error'] = $e->getMessage() . '. Please tell the customer this item is not available right now.';
            $this->redirect('requests_create');
        }

        $this->flash('success', 'Request sent to the cashier. Direct the customer to make payment.');
        $this->redirect('requests_view', ['id' => $requestId]);
    }

    public function view(): void
    {
        Auth::requireLogin();
        $id = (int) $this->input('id');
        $requestModel = new DrugRequest();
        $request = $requestModel->findWithItems($id);

        if (!$request) {
            http_response_code(404);
            echo "Request not found.";
            return;
        }

        $paymentModel = new Payment();
        $payment = $paymentModel->findByRequestId($id);

        $this->render('requests/view', ['request' => $request, 'payment' => $payment]);
    }

    public function cancel(): void
    {
        Auth::requireRole(['pharmacist']);
        $id = (int) $this->input('id');
        (new DrugRequest())->cancel($id);
        $this->flash('success', 'Request cancelled.');
        $this->redirect('requests');
    }
}