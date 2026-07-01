<?php
class Auth
{
    public static function attempt(string $username, string $password): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare(
            "SELECT u.*, r.name AS role_name
             FROM users u
             JOIN roles r ON r.id = u.role_id
             WHERE u.username = :username AND u.status = 'active'
             LIMIT 1"
        );
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return false;
        }

        // Prevent session fixation on privilege change
        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id'        => $user['id'],
            'full_name' => $user['full_name'],
            'username'  => $user['username'],
            'role'      => $user['role_name'],
        ];

        return true;
    }

    public static function logout(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user']);
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function role(): ?string
    {
        return $_SESSION['user']['role'] ?? null;
    }

    public static function id(): ?int
    {
        return $_SESSION['user']['id'] ?? null;
    }

    /** Redirect to login if not authenticated */
    public static function requireLogin(): void
    {
        if (!self::isLoggedIn()) {
            header('Location: ' . BASE_URL . '/index.php?action=login');
            exit;
        }
    }

    /** Allow only the given roles; otherwise show 403 */
    public static function requireRole(array $allowedRoles): void
    {
        self::requireLogin();
        if (!in_array(self::role(), $allowedRoles, true)) {
            http_response_code(403);
            echo "<h2>403 Forbidden</h2><p>You don't have permission to view this page.</p>";
            exit;
        }
    }

    /** Which roles the current user is allowed to create */
    public static function creatableRoles(): array
    {
        return match (self::role()) {
            'super_admin' => ['admin', 'cashier', 'pharmacist'],
            'admin'       => ['cashier', 'pharmacist'],
            default       => [],
        };
    }
}