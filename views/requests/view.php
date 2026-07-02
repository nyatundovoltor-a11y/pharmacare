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
<div class="card" style="max-width:700px;">
    <div class="card-header">
        <div>
            <h2 class="code"><?= htmlspecialchars($request['request_code']) ?></h2>
            <div style="color:var(--text-muted); font-size:12.5px; margin-top:2px;">
                Submitted <?= htmlspecialchars(substr($request['created_at'], 0, 16)) ?>
            </div>
        </div>
        <?= $statusPill($request['status']) ?>
    </div>

    <div class="form-row" style="margin-bottom: 18px;">
        <div>
            <div class="stat-label">Customer</div>
            <div><?= htmlspecialchars($request['customer_name']) ?></div>
        </div>
        <div>
            <div class="stat-label">Phone</div>
            <div><?= htmlspecialchars($request['customer_phone'] ?: '&mdash;') ?></div>
        </div>
    </div>

    <table class="chart-table">
        <thead><tr><th>Drug</th><th>Qty</th><th>Unit Price</th><th>Subtotal</th></tr></thead>
        <tbody>
        <?php foreach ($request['items'] as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['drug_name']) ?></td>
                <td class="num"><?= (int) $item['quantity'] ?></td>
                <td class="num">KES <?= number_format($item['unit_price'], 2) ?></td>
                <td class="num">KES <?= number_format($item['subtotal'], 2) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div style="display:flex; justify-content:flex-end; margin-top:14px; padding-top:14px; border-top:1px solid var(--border);">
        <div style="text-align:right;">
            <div class="stat-label">Total Due</div>
            <div class="data" style="font-size:20px; font-weight:600; color:var(--color-primary);">KES <?= number_format($request['total_amount'], 2) ?></div>
        </div>
    </div>

    <?php if ($payment): ?>
        <div class="alert alert-success" style="margin-top:18px;">
            Paid via <?= htmlspecialchars($payment['payment_method']) ?> &middot; Receipt <span class="code"><?= htmlspecialchars($payment['receipt_no']) ?></span>
            &middot; <a href="<?= BASE_URL ?>/index.php?action=payments_receipt&id=<?= $request['id'] ?>">View receipt</a>
        </div>
    <?php elseif ($request['status'] === 'awaiting_payment'): ?>
        <div class="alert" style="margin-top:18px;">Direct the customer to the cashier to complete payment.</div>
    <?php endif; ?>
</div>
