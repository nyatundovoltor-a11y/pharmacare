<div class="card">
    <div class="card-header">
        <h2>Requests Awaiting Payment</h2>
    </div>

    <?php if (empty($requests)): ?>
        <div class="empty-state"><div class="glyph">&#8353;</div>No pending payments right now.</div>
    <?php else: ?>
        <table class="chart-table">
            <thead><tr><th>Request</th><th>Customer</th><th>Pharmacist</th><th>Total</th><th>Sent</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($requests as $r): ?>
                <tr>
                    <td class="code"><?= htmlspecialchars($r['request_code']) ?></td>
                    <td><?= htmlspecialchars($r['customer_name']) ?></td>
                    <td><?= htmlspecialchars($r['pharmacist_name']) ?></td>
                    <td class="num">KES <?= number_format($r['total_amount'], 2) ?></td>
                    <td class="num"><?= htmlspecialchars(substr($r['created_at'], 0, 16)) ?></td>
                    <td><a class="btn btn-primary btn-sm" href="<?= BASE_URL ?>/index.php?action=payments_pay&id=<?= $r['id'] ?>">Take Payment</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
