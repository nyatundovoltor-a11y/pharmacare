<?php
class CheckoutController extends Controller
{
    public function ready(): void
    {
        Auth::requireRole(['pharmacist']);
        $requestModel = new DrugRequest();
        $this->render('checkouts/ready', [
            'requests'  => $requestModel->readyForCheckout(),
            'pageTitle' => 'Ready for Checkout',
        ]);
    }

    public function checkout(): void
    {
        Auth::requireRole(['pharmacist']);
        $id = (int) $this->input('id');

        $checkoutModel = new Checkout();
        try {
            $checkoutModel->process($id, Auth::id());
            $this->flash('success', 'Drugs dispensed. Customer may now leave with their order.');
        } catch (RuntimeException $e) {
            $this->flash('error', $e->getMessage());
        }

        $this->redirect('checkouts_ready');
    }
}
