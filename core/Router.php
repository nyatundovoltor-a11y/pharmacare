<?php
class Router
{
    /**
     * Route table: action => [ControllerClass, method]
     * Access control is enforced inside each controller method via Auth::requireRole(),
     * not here, so it stays visible right next to the logic it protects.
     */
    private array $routes = [
        'login'            => ['AuthController', 'showLogin'],
        'do_login'         => ['AuthController', 'login'],
        'logout'           => ['AuthController', 'logout'],

        'dashboard'        => ['DashboardController', 'index'],

        // user management (super_admin, admin)
        'users'            => ['UserController', 'index'],
        'users_create'     => ['UserController', 'create'],
        'users_store'      => ['UserController', 'store'],
        'users_toggle'     => ['UserController', 'toggleStatus'],

        // drug inventory (super_admin adds stock; everyone with access can view)
        'drugs'            => ['DrugController', 'index'],
        'drugs_create'     => ['DrugController', 'create'],
        'drugs_store'      => ['DrugController', 'store'],
        'drugs_search'     => ['DrugController', 'search'],

        // pharmacist: build a request from the doctor's note
        'requests'         => ['RequestController', 'index'],
        'requests_create'  => ['RequestController', 'create'],
        'requests_store'   => ['RequestController', 'store'],
        'requests_view'    => ['RequestController', 'view'],
        'requests_cancel'  => ['RequestController', 'cancel'],

        // cashier: take payment against a pending request
        'payments_pending' => ['PaymentController', 'pending'],
        'payments_pay'     => ['PaymentController', 'pay'],
        'payments_receipt' => ['PaymentController', 'receipt'],

        // pharmacist: dispense drugs once a receipt exists
        'checkouts_ready'  => ['CheckoutController', 'ready'],
        'checkouts_do'     => ['CheckoutController', 'checkout'],
    ];

    public function dispatch(string $action): void
    {
        if (!array_key_exists($action, $this->routes)) {
            http_response_code(404);
            echo "<h2>404 - Page not found</h2>";
            return;
        }

        [$controllerName, $method] = $this->routes[$action];
        $controller = new $controllerName();
        $controller->$method();
    }
}