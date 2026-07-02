<div class="card" style="max-width:640px;">
    <div class="card-header">
        <div>
            <h2 class="code"><?= htmlspecialchars($request['request_code']) ?></h2>
            <div style="color:var(--text-muted); font-size:12.5px; margin-top:2px;"><?= htmlspecialchars($request['customer_name']) ?></div>
        </div>
        <span class="pill pill-warning">Awaiting Payment</span>
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

    <div style="display:flex; justify-content:flex-end; margin:14px 0 20px; padding-top:14px; border-top:1px solid var(--border);">
        <div style="text-align:right;">
            <div class="stat-label">Amount Due</div>
            <div class="data" style="font-size:22px; font-weight:600; color:var(--color-primary);">KES <?= number_format($request['total_amount'], 2) ?></div>
        </div>
    </div>

    <form method="POST" action="<?= BASE_URL ?>/index.php?action=payments_pay&id=<?= $request['id'] ?>">
        <div class="form-group">
            <label for="payment_method">Payment Method</label>
            <select id="payment_method" name="payment_method">
                <option value="cash">Cash</option>
                <option value="card">Card</option>
                <option value="mobile_money">Mobile Money</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Confirm Payment &amp; Issue Receipt</button>
        <a href="<?= BASE_URL ?>/index.php?action=payments_pending" class="btn btn-outline">Cancel</a>
    </form>
</div>
