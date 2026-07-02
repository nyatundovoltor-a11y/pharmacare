<div class="no-print" style="max-width:380px; margin:0 auto 16px; display:flex; justify-content:space-between;">
    <a href="<?= BASE_URL ?>/index.php?action=requests_view&id=<?= $request['id'] ?>" class="btn btn-outline btn-sm">&larr; Back to request</a>
    <button onclick="window.print()" class="btn btn-primary btn-sm">Print Receipt</button>
</div>

<div class="receipt">
    <div class="r-head">
        <div class="brand">&#8478; PharmaCare</div>
        <div class="sub">Official Payment Receipt</div>
    </div>

    <div class="r-divider"></div>

    <div class="r-row"><span>Receipt No.</span><span><?= htmlspecialchars($payment['receipt_no']) ?></span></div>
    <div class="r-row"><span>Request</span><span><?= htmlspecialchars($request['request_code']) ?></span></div>
    <div class="r-row"><span>Date</span><span><?= htmlspecialchars(substr($payment['paid_at'], 0, 16)) ?></span></div>
    <div class="r-row"><span>Cashier</span><span><?= htmlspecialchars($payment['cashier_name']) ?></span></div>
    <div class="r-row"><span>Customer</span><span><?= htmlspecialchars($request['customer_name']) ?></span></div>

    <div class="r-divider"></div>

    <?php foreach ($request['items'] as $item): ?>
        <div class="r-row">
            <span><?= htmlspecialchars($item['drug_name']) ?> &times;<?= (int) $item['quantity'] ?></span>
            <span><?= number_format($item['subtotal'], 2) ?></span>
        </div>
    <?php endforeach; ?>

    <div class="r-divider"></div>

    <div class="r-row"><span>Payment Method</span><span><?= htmlspecialchars(strtoupper($payment['payment_method'])) ?></span></div>
    <div class="r-row total"><span>TOTAL PAID</span><span>KES <?= number_format($payment['amount_paid'], 2) ?></span></div>

    <div class="r-divider"></div>

    <div class="r-foot">
        PRESENT THIS RECEIPT TO THE PHARMACIST<br>
        TO COLLECT YOUR DRUGS
    </div>
</div>
