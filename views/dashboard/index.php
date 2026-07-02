<?php
$statusPill = function (string $status): string {
    return match ($status) {
        'awaiting_payment' => '<span class="pill pill-warning">Awaiting Payment</span>',
        'paid'              => '<span class="pill pill-accent">Paid</span>',
        'completed'         => '<span class="pill pill-success">Completed</span>',
        'cancelled'         => '<span class="pill pill-danger">Cancelled</span>',
        default             => '<span class="pill pill-neutral">' . htmlspecialchars($status) . '</span>',
    };
};
?>

<?php if ($role === 'super_admin'): ?>
    <div class="card-grid" style="margin-bottom: 22px;">
        <div class="stat-card accent-success">
            <div class="stat-label">Drugs in Inventory</div>
            <div class="stat-value data"><?= $drugCount ?></div>
        </div>
        <div class="stat-card accent-primary">
            <div class="stat-label">Quick Action</div>
            <div style="margin-top:6px;"><a class="btn btn-primary btn-sm" href="<?= BASE_URL ?>/index.php?action=drugs_create">+ Receive Stock</a></div>
        </div>
        <div class="stat-card accent-danger">
            <div class="stat-label">Quick Action</div>
            <div style="margin-top:6px;"><a class="btn btn-outline btn-sm" href="<?= BASE_URL ?>/index.php?action=users_create">+ Create Staff Account</a></div>
        </div>
    </div>
<?php endif; ?>

<?php if (in_array($role, ['super_admin', 'admin'], true)): ?>
    <div class="card">
        <div class="card-header">
            <h3>Recently Added Staff</h3>
            <a href="<?= BASE_URL ?>/index.php?action=users" class="btn btn-outline btn-sm">View all</a>
        </div>
        <?php if (empty($recentUsers)): ?>
            <div class="empty-state"><div class="glyph">&#9737;</div>No staff accounts yet.</div>
        <?php else: ?>
            <table class="chart-table">
                <thead><tr><th>Name</th><th>Role</th><th>Status</th><th>Created</th></tr></thead>
                <tbody>
                <?php foreach ($recentUsers as $u): ?>
                    <tr>
                        <td><?= htmlspecialchars($u['full_name']) ?></td>
                        <td><span class="pill pill-neutral"><?= htmlspecialchars($u['role_name']) ?></span></td>
                        <td><?= $u['status'] === 'active' ? '<span class="pill pill-success">Active</span>' : '<span class="pill pill-danger">Disabled</span>' ?></td>
                        <td class="num"><?= htmlspecialchars(substr($u['created_at'], 0, 10)) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if ($role === 'pharmacist'): ?>
    <div class="card-grid" style="margin-bottom: 22px;">
        <div class="stat-card accent-success">
            <div class="stat-label">Ready for Checkout</div>
            <div class="stat-value data"><?= count($readyForCheckout) ?></div>
        </div>
        <div class="stat-card accent-warning">
            <div class="stat-label">Quick Action</div>
            <div style="margin-top:6px;"><a class="btn btn-primary btn-sm" href="<?= BASE_URL ?>/index.php?action=requests_create">+ New Request</a></div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Ready for Checkout</h3>
            <a href="<?= BASE_URL ?>/index.php?action=checkouts_ready" class="btn btn-outline btn-sm">View all</a>
        </div>
        <?php if (empty($readyForCheckout)): ?>
            <div class="empty-state"><div class="glyph">&#10003;</div>Nothing waiting to be dispensed right now.</div>
        <?php else: ?>
            <table class="chart-table">
                <thead><tr><th>Request</th><th>Customer</th><th>Receipt</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($readyForCheckout as $r): ?>
                    <tr>
                        <td class="code"><?= htmlspecialchars($r['request_code']) ?></td>
                        <td><?= htmlspecialchars($r['customer_name']) ?></td>
                        <td class="code"><?= htmlspecialchars($r['receipt_no']) ?></td>
                        <td><a class="btn btn-success btn-sm" href="<?= BASE_URL ?>/index.php?action=checkouts_ready">Dispense</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="card-header"><h3>My Recent Requests</h3></div>
        <?php if (empty($myRequests)): ?>
            <div class="empty-state">No requests submitted yet.</div>
        <?php else: ?>
            <table class="chart-table">
                <thead><tr><th>Request</th><th>Customer</th><th>Total</th><th>Status</th></tr></thead>
                <tbody>
                <?php foreach ($myRequests as $r): ?>
                    <tr>
                        <td class="code"><a href="<?= BASE_URL ?>/index.php?action=requests_view&id=<?= $r['id'] ?>"><?= htmlspecialchars($r['request_code']) ?></a></td>
                        <td><?= htmlspecialchars($r['customer_name']) ?></td>
                        <td class="num">KES <?= number_format($r['total_amount'], 2) ?></td>
                        <td><?= $statusPill($r['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if ($role === 'cashier'): ?>
    <div class="card-grid" style="margin-bottom: 22px;">
        <div class="stat-card accent-warning">
            <div class="stat-label">Awaiting Payment</div>
            <div class="stat-value data"><?= count($awaitingPayment) ?></div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Requests Awaiting Payment</h3>
            <a href="<?= BASE_URL ?>/index.php?action=payments_pending" class="btn btn-outline btn-sm">View all</a>
        </div>
        <?php if (empty($awaitingPayment)): ?>
            <div class="empty-state"><div class="glyph">&#8353;</div>No pending payments right now.</div>
        <?php else: ?>
            <table class="chart-table">
                <thead><tr><th>Request</th><th>Customer</th><th>Pharmacist</th><th>Total</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($awaitingPayment as $r): ?>
                    <tr>
                        <td class="code"><?= htmlspecialchars($r['request_code']) ?></td>
                        <td><?= htmlspecialchars($r['customer_name']) ?></td>
                        <td><?= htmlspecialchars($r['pharmacist_name']) ?></td>
                        <td class="num">KES <?= number_format($r['total_amount'], 2) ?></td>
                        <td><a class="btn btn-primary btn-sm" href="<?= BASE_URL ?>/index.php?action=payments_pay&id=<?= $r['id'] ?>">Take Payment</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
<?php endif; ?>
