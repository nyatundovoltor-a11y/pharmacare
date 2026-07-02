<div class="card">
    <div class="card-header">
        <h2>Ready for Checkout</h2>
    </div>
    <p style="color:var(--text-muted); margin-top:-8px;">
        These requests have been paid for. Verify the receipt number with the customer, then dispense.
    </p>

    <?php if (empty($requests)): ?>
        <div class="empty-state"><div class="glyph">&#10003;</div>Nothing waiting to be dispensed right now.</div>
    <?php else: ?>
        <table class="chart-table">
            <thead><tr><th>Request</th><th>Customer</th><th>Receipt No.</th><th>Total</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($requests as $r): ?>
                <tr>
                    <td class="code"><a href="<?= BASE_URL ?>/index.php?action=requests_view&id=<?= $r['id'] ?>"><?= htmlspecialchars($r['request_code']) ?></a></td>
                    <td><?= htmlspecialchars($r['customer_name']) ?></td>
                    <td class="code"><?= htmlspecialchars($r['receipt_no']) ?></td>
                    <td class="num">KES <?= number_format($r['total_amount'], 2) ?></td>
                    <td>
                        <form method="POST" action="<?= BASE_URL ?>/index.php?action=checkouts_do" style="display:inline;" onsubmit="return confirm('Confirm drugs handed over to the customer?');">
                            <input type="hidden" name="id" value="<?= $r['id'] ?>">
                            <button type="submit" class="btn btn-success btn-sm">Dispense</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
