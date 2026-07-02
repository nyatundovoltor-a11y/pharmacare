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
<div class="card">
    <div class="card-header">
        <h2>My Requests</h2>
        <a href="<?= BASE_URL ?>/index.php?action=requests_create" class="btn btn-primary btn-sm">+ New Request</a>
    </div>

    <?php if (empty($requests)): ?>
        <div class="empty-state"><div class="glyph">&#9776;</div>No requests submitted yet.</div>
    <?php else: ?>
        <table class="chart-table">
            <thead><tr><th>Request</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($requests as $r): ?>
                <tr>
                    <td class="code"><?= htmlspecialchars($r['request_code']) ?></td>
                    <td><?= htmlspecialchars($r['customer_name']) ?></td>
                    <td class="num">KES <?= number_format($r['total_amount'], 2) ?></td>
                    <td><?= $statusPill($r['status']) ?></td>
                    <td class="num"><?= htmlspecialchars(substr($r['created_at'], 0, 16)) ?></td>
                    <td>
                        <a class="btn btn-outline btn-sm" href="<?= BASE_URL ?>/index.php?action=requests_view&id=<?= $r['id'] ?>">View</a>
                        <?php if ($r['status'] === 'awaiting_payment'): ?>
                            <a class="btn btn-danger btn-sm" href="<?= BASE_URL ?>/index.php?action=requests_cancel&id=<?= $r['id'] ?>" onclick="return confirm('Cancel this request?');">Cancel</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
