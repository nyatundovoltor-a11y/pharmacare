<div class="card" style="max-width:520px;">
    <div class="card-header">
        <h2>Create Staff Account</h2>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (empty($creatableRoles)): ?>
        <div class="empty-state">Your role cannot create new accounts.</div>
    <?php else: ?>
        <form method="POST" action="<?= BASE_URL ?>/index.php?action=users_store">
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
            </div>
            <div class="form-group">
                <label for="password">Temporary Password</label>
                <input type="password" id="password" name="password" minlength="8" required>
                <div class="hint">At least 8 characters. Share this with the staff member securely; they should change it after first login.</div>
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="">Select a role&hellip;</option>
                    <?php foreach ($creatableRoles as $r): ?>
                        <option value="<?= $r ?>"><?= ucfirst(str_replace('_', ' ', $r)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Create Account</button>
            <a href="<?= BASE_URL ?>/index.php?action=users" class="btn btn-outline">Cancel</a>
        </form>
    <?php endif; ?>
</div>
