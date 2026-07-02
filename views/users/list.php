<div class="card">
    <div class="card-header">
        <h2>Staff Accounts</h2>
        <a href="<?= BASE_URL ?>/index.php?action=users_create" class="btn btn-primary btn-sm">+ Create Account</a>
    </div>

    <?php if (empty($users)): ?>
        <div class="empty-state"><div class="glyph">&#9737;</div>No staff accounts yet.</div>
    <?php else: ?>
        <table class="chart-table">
            <thead><tr><th>Name</th><th>Username</th><th>Role</th><th>Created By</th><th>Status</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u['full_name']) ?><div style="color:var(--text-muted); font-size:12px;"><?= htmlspecialchars($u['email']) ?></div></td>
                    <td class="code"><?= htmlspecialchars($u['username']) ?></td>
                    <td><span class="pill pill-neutral"><?= htmlspecialchars($u['role_name']) ?></span></td>
                    <td><?= htmlspecialchars($u['created_by_name'] ?: 'System') ?></td>
                    <td><?= $u['status'] === 'active' ? '<span class="pill pill-success">Active</span>' : '<span class="pill pill-danger">Disabled</span>' ?></td>
                    <td>
                        <?php if ($u['role_name'] !== 'super_admin'): ?>
                            <form method="POST" action="<?= BASE_URL ?>/index.php?action=users_toggle" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                <button type="submit" class="btn btn-outline btn-sm">
                                    <?= $u['status'] === 'active' ? 'Disable' : 'Enable' ?>
                                </button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
