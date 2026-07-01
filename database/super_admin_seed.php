<?php
/**
 * Run this ONCE after importing pharmacare.sql to create the first
 * super_admin account (there's no UI to create the very first user,
 * since every other account is created by someone already logged in).
 *
 * Usage:
 *   Browser:  http://localhost/pharmacare/database/seed_super_admin.php
 *   CLI:      php seed_super_admin.php
 *
 * Delete this file (or move it out of the web root) once you've run it.
 */

require_once __DIR__ . '/../config/database.php';

$fullName = 'System Super Admin';
$username = 'superadmin';
$email    = 'superadmin@pharmacare.local';
$password = 'SuperAdmin123!'; // change this before running in production

$db = Database::getConnection();

$stmt = $db->prepare("SELECT id FROM users WHERE username = :username");
$stmt->execute(['username' => $username]);
if ($stmt->fetch()) {
    die("A super admin with username '$username' already exists. Nothing to do.\n");
}

$roleStmt = $db->prepare("SELECT id FROM roles WHERE name = 'super_admin'");
$roleStmt->execute();
$role = $roleStmt->fetch();
if (!$role) {
    die("roles table has no 'super_admin' row. Did you import pharmacare.sql?\n");
}

$hash = password_hash($password, PASSWORD_BCRYPT);

$insert = $db->prepare(
    "INSERT INTO users (full_name, username, email, password_hash, role_id, created_by, status)
     VALUES (:full_name, :username, :email, :password_hash, :role_id, NULL, 'active')"
);
$insert->execute([
    'full_name'     => $fullName,
    'username'      => $username,
    'email'         => $email,
    'password_hash' => $hash,
    'role_id'       => $role['id'],
]);

echo "Super admin created.\n";
echo "Username: $username\n";
echo "Password: $password\n";
echo "Log in and change this password, then delete this file.\n";