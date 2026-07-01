<?php
class AuthController extends Controller
{
    public function showLogin(): void
    {
        if (Auth::isLoggedIn()) {
            $this->redirect('dashboard');
        }
        $this->renderPlain('auth/login', ['error' => $_SESSION['login_error'] ?? null]);
        unset($_SESSION['login_error']);
    }

    public function login(): void
    {
        $username = trim($this->input('username', ''));
        $password = $this->input('password', '');

        if ($username === '' || $password === '') {
            $_SESSION['login_error'] = 'Please enter both username and password.';
            $this->redirect('login');
        }

        if (Auth::attempt($username, $password)) {
            $this->redirect('dashboard');
        }

        $_SESSION['login_error'] = 'Invalid username or password, or account disabled.';
        $this->redirect('login');
    }

    public function logout(): void
    {
        Auth::logout();
        $this->redirect('login');
    }
}