<?php
class User extends Model
{
    protected string $table = 'users';

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :u LIMIT 1");
        $stmt->execute(['u' => $username]);
        return $stmt->fetch() ?: null;
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = :e LIMIT 1");
        $stmt->execute(['e' => $email]);
        return (bool) $stmt->fetch();
    }

    public function usernameExists(string $username): bool
    {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE username = :u LIMIT 1");
        $stmt->execute(['u' => $username]);
        return (bool) $stmt->fetch();
    }

    public function roleIdByName(string $role): ?int
    {
        $stmt = $this->db->prepare("SELECT id FROM roles WHERE name = :n LIMIT 1");
        $stmt->execute(['n' => $role]);
        $row = $stmt->fetch();
        return $row ? (int) $row['id'] : null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO users (full_name, username, email, password_hash, role_id, created_by, status)
             VALUES (:full_name, :username, :email, :password_hash, :role_id, :created_by, 'active')"
        );
        $stmt->execute([
            'full_name'     => $data['full_name'],
            'username'      => $data['username'],
            'email'         => $data['email'],
            'password_hash' => password_hash($data['password'], PASSWORD_BCRYPT),
            'role_id'       => $data['role_id'],
            'created_by'    => $data['created_by'],
        ]);
        return (int) $this->db->lastInsertId();
    }

    /** All users, with role name and creator's name, newest first */
    public function allWithRole(): array
    {
        return $this->db->query(
            "SELECT u.id, u.full_name, u.username, u.email, u.status, u.created_at,
                    r.name AS role_name, creator.full_name AS created_by_name
             FROM users u
             JOIN roles r ON r.id = u.role_id
             LEFT JOIN users creator ON creator.id = u.created_by
             ORDER BY u.created_at DESC"
        )->fetchAll();
    }

    public function toggleStatus(int $id): void
    {
        $stmt = $this->db->prepare(
            "UPDATE users SET status = IF(status = 'active', 'disabled', 'active') WHERE id = :id"
        );
        $stmt->execute(['id' => $id]);
    }
}