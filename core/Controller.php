<?php
abstract class Controller
{
    /** Render a view file with variables extracted into scope */
    protected function render(string $view, array $data = []): void
    {
        extract($data);
        $viewPath = __DIR__ . '/../views/' . $view . '.php';
        if (!file_exists($viewPath)) {
            die("View not found: {$view}");
        }
        require __DIR__ . '/../views/layouts/header.php';
        require $viewPath;
        require __DIR__ . '/../views/layouts/footer.php';
    }

    /** Render without the shared header/footer (e.g. login page) */
    protected function renderPlain(string $view, array $data = []): void
    {
        extract($data);
        require __DIR__ . '/../views/' . $view . '.php';
    }

    protected function redirect(string $action, array $params = []): void
    {
        $query = array_merge(['action' => $action], $params);
        header('Location: ' . BASE_URL . '/index.php?' . http_build_query($query));
        exit;
    }

    protected function input(string $key, $default = null)
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    protected function flash(string $type, string $message): void
    {
        $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
    }
}