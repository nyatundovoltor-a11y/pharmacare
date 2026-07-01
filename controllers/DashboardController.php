<?php
class DashboardController extends Controller
{
    public function index(): void
    {
        Auth::requireLogin();

        $role = Auth::role();
        $data = ['role' => $role];

        if (in_array($role, ['super_admin', 'admin'], true)) {
            $userModel = new User();
            $data['recentUsers'] = array_slice($userModel->allWithRole(), 0, 5);
        }

        if ($role === 'pharmacist') {
            $requestModel = new DrugRequest();
            $data['myRequests'] = array_slice($requestModel->byPharmacist(Auth::id()), 0, 5);
            $data['readyForCheckout'] = $requestModel->readyForCheckout();
        }

        if ($role === 'cashier') {
            $requestModel = new DrugRequest();
            $data['awaitingPayment'] = $requestModel->awaitingPayment();
        }

        if ($role === 'super_admin') {
            $drugModel = new Drug();
            $data['drugCount'] = count($drugModel->all());
        }

        $this->render('dashboard/index', $data);
    }
}