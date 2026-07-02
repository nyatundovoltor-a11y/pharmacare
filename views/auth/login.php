<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PharmaCare - Log in</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<div class="auth-screen">
    <div class="auth-card">
        <div class="auth-brand">
            <span class="rx" style="color:var(--color-accent);">&#8478;</span>
            <span class="name">PharmaCare</span>
        </div>
        <div class="auth-sub">Dispensing &amp; inventory system &middot; sign in to continue</div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/index.php?action=do_login">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" autofocus required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">Log in</button>
        </form>
    </div>
</div>
</body>
</html>
