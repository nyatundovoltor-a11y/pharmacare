# PharmaCare

A role-based pharmacy management system: inventory in, drug requests, cashier
payment, pharmacist checkout. Plain PHP (OOP/MVC, no framework), MySQL, built
for XAMPP.

## 1. Install

1. Copy the `pharmacare` folder into your XAMPP `htdocs` directory, e.g.
   `C:\xampp\htdocs\pharmacare` or `/Applications/XAMPP/htdocs/pharmacare`.
2. Start **Apache** and **MySQL** in the XAMPP control panel.

## 2. Database

1. Open phpMyAdmin (`http://localhost/phpmyadmin`).
2. Import `database/pharmacare.sql` (this creates the `pharmacare` database,
   all tables, and the four roles).
3. Check `config/database.php` matches your MySQL credentials (defaults to
   XAMPP's standard `root` with no password).

## 3. Create the first Super Admin

There's no UI to create the very first account, since every other account is
created by someone already logged in. Run the seed script once:

- Browser: `http://localhost/pharmacare/database/seed_super_admin.php`
- or CLI: `php database/seed_super_admin.php`

It prints the generated username/password. **Log in, then delete or move
this file out of the web root** — leaving a seeding script reachable in
production is a bad idea.

## 4. Log in

Visit: `http://localhost/pharmacare/public/index.php?action=login`

(If you cloned the project under a different folder name, update `BASE_URL`
in `config/config.php` to match.)

## How the workflow maps to the system

- **Super Admin** creates admins, cashiers, pharmacists, and receives/registers
  drug stock (`Receive Stock`). Stock becomes visible system-wide immediately.
- **Admin** creates cashiers and pharmacists only.
- **Pharmacist** checks the doctor's note against inventory (`New Request`),
  builds a line-item request; if any drug is short on stock the request is
  blocked and the pharmacist tells the customer it's unavailable. Otherwise
  the request goes to the cashier as `awaiting_payment`.
- **Cashier** sees requests `Awaiting Payment`, takes payment, and the system
  generates a receipt (`payments_receipt`) — this is the customer's
  authorization to collect drugs.
- **Pharmacist** sees paid requests under `Ready for Checkout`, verifies the
  receipt, and dispenses — this deducts stock and marks the request
  `completed`.

## Project structure

```
config/       DB connection + app bootstrap/autoload
core/         Base Controller/Model, Router, Auth (session + role guards)
models/       User, Drug, DrugRequest, Payment, Checkout
controllers/  One per resource, each route enforces its own Auth::requireRole()
views/        layouts/ (sidebar+topbar shell) + one folder per resource
public/       Web root - index.php is the single front controller
database/     Schema + one-time super admin seeder
```

Routing is a simple `?action=xxx` query-string dispatch (see
`core/Router.php`) rather than URL rewriting, so it works out of the box on
any Apache config without needing `mod_rewrite`.

## Notes / things to harden before real use

- Passwords are hashed with `password_hash()`/bcrypt — never stored in plain text.
- `config/database.php` and everything outside `public/` is blocked from
  direct browser access via `.htaccess` — but only if `mod_rewrite`/`mod_authz_core`
  is enabled in your Apache config (it is by default in XAMPP).
- There's no password-reset flow yet — an admin would need to add one, or a
  super admin can currently only disable/enable accounts, not reset passwords.
  Add a "reset password" action to `UserController` if you need it.
- Consider adding CSRF tokens to the POST forms before deploying beyond local use.
