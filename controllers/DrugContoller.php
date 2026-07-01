<?php
class DrugController extends Controller
{
    public function index(): void
    {
        Auth::requireLogin(); // any logged-in role can view inventory
        $drugModel = new Drug();
        $this->render('drugs/list', ['drugs' => $drugModel->all()]);
    }

    public function create(): void
    {
        Auth::requireRole(['super_admin']);
        $this->render('drugs/create', ['error' => $_SESSION['form_error'] ?? null]);
        unset($_SESSION['form_error']);
    }

    public function store(): void
    {
        Auth::requireRole(['super_admin']);

        $name     = trim($this->input('name', ''));
        $unit     = trim($this->input('unit', 'unit'));
        $price    = (float) $this->input('unit_price', 0);
        $quantity = (int) $this->input('quantity', 0);
        $existingDrugId = $this->input('existing_drug_id');

        if ($name === '' && !$existingDrugId) {
            $_SESSION['form_error'] = 'Please provide a drug name.';
            $this->redirect('drugs_create');
        }
        if ($quantity <= 0) {
            $_SESSION['form_error'] = 'Quantity must be greater than zero.';
            $this->redirect('drugs_create');
        }

        $drugModel = new Drug();

        if ($existingDrugId) {
            // Restocking a drug that's already registered
            $drugModel->addStock((int) $existingDrugId, $quantity, Auth::id());
            $this->flash('success', 'Stock updated.');
        } else {
            $drugModel->create([
                'name'          => $name,
                'description'   => trim($this->input('description', '')),
                'category'      => trim($this->input('category', '')),
                'unit'          => $unit,
                'unit_price'    => $price,
                'quantity'      => $quantity,
                'reorder_level' => (int) $this->input('reorder_level', 10),
                'added_by'      => Auth::id(),
            ]);
            $this->flash('success', "{$name} added to inventory.");
        }

        $this->redirect('drugs');
    }

    /** AJAX-style search used by the pharmacist when building a request */
    public function search(): void
    {
        Auth::requireRole(['pharmacist', 'super_admin', 'admin']);
        $term = trim($this->input('term', ''));
        $drugModel = new Drug();
        $results = $term !== '' ? $drugModel->search($term) : [];

        header('Content-Type: application/json');
        echo json_encode($results);
        exit;
    }
}