<?php
class UserController extends Controller
{
    public function index(): void
    {
        Auth::requireRole(['super_admin', 'admin']);
        $userModel = new User();
        $this->render('users/list', ['users' => $userModel->allWithRole()]);
    }

    public function create(): void
    {
        Auth::requireRole(['super_admin', 'admin']);
        $this->render('users/create', [
            'creatableRoles' => Auth::creatableRoles(),
            'error'          => $_SESSION['form_error'] ?? null,
        ]);
        unset($_SESSION['form_error']);
    }

    public function store(): void
    {
        Auth::requireRole(['super_admin', 'admin']);

        $fullName = trim($this->input('full_name', ''));
        $username = trim($this->input('username', ''));
        $email    = trim($this->input('email', ''));
        $password = $this->input('password', '');
        $role     = $this->input('role', '');

        $userModel = new User();
        $allowedRoles = Auth::creatableRoles();

        // Server-side guard: never trust the role coming from the form alone
        if (!in_array($role, $allowedRoles, true)) {
            $_SESSION['form_error'] = 'You are not allowed to create that role.';
            $this->redirect('users_create');
        }

        if ($fullName === '' || $username === '' || $email === '' || $password === '') {
            $_SESSION['form_error'] = 'All fields are required.';
            $this->redirect('users_create');
        }

        if (strlen($password) < 8) {
            $_SESSION['form_error'] = 'Password must be at least 8 characters.';
            $this->redirect('users_create');
        }

        if ($userModel->usernameExists($username)) {
            $_SESSION['form_error'] = 'That username is already taken.';
            $this->redirect('users_create');
        }

        if ($userModel->emailExists($email)) {
            $_SESSION['form_error'] = 'That email is already registered.';
            $this->redirect('users_create');
        }

        $roleId = $userModel->roleIdByName($role);

        $userModel->create([
            'full_name'  => $fullName,
            'username'   => $username,
            'email'      => $email,
            'password'   => $password,
            'role_id'    => $roleId,
            'created_by' => Auth::id(),
        ]);

        $this->flash('success', "Account for {$fullName} ({$role}) created successfully.");
        $this->redirect('users');
    }

    public function toggleStatus(): void
    {
        Auth::requireRole(['super_admin', 'admin']);
        $id = (int) $this->input('id');
        (new User())->toggleStatus($id);
        $this->redirect('users');
    }
}