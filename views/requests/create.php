<div class="card" style="max-width: 760px;">
    <div class="card-header">
        <h2>New Request</h2>
    </div>
    <p style="color:var(--text-muted); margin-top:-8px;">
        Check the doctor's note against inventory below, add each drug the customer needs, then send to the cashier.
    </p>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/index.php?action=requests_store" id="requestForm">
        <div class="form-row">
            <div class="form-group">
                <label for="customer_name">Customer Name</label>
                <input type="text" id="customer_name" name="customer_name" required>
            </div>
            <div class="form-group">
                <label for="customer_phone">Customer Phone (optional)</label>
                <input type="tel" id="customer_phone" name="customer_phone">
            </div>
        </div>

        <div class="form-group">
            <label>Drugs from the note</label>
            <table class="chart-table" id="itemsTable">
                <thead>
                    <tr>
                        <th style="width:40%;">Drug</th>
                        <th style="width:15%;">In Stock</th>
                        <th style="width:15%;">Unit Price</th>
                        <th style="width:15%;">Quantity</th>
                        <th style="width:15%;">Subtotal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="itemsBody"></tbody>
            </table>
            <button type="button" class="btn btn-outline btn-sm" style="margin-top:10px;" onclick="addRow()">+ Add drug</button>
        </div>

        <div class="card" style="background:#F0F6F9; margin-top:18px;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <span style="color:var(--text-muted); font-size:12.5px;">Total to be paid at the cashier</span>
                <span class="data" style="font-size:20px; font-weight:600; color:var(--color-primary);">KES <span id="grandTotal">0.00</span></span>
            </div>
        </div>

        <div style="margin-top:18px;">
            <button type="submit" class="btn btn-primary">Send to Cashier</button>
            <a href="<?= BASE_URL ?>/index.php?action=requests" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>

<script>
const DRUGS = <?= json_encode(array_map(fn($d) => [
    'id' => $d['id'],
    'name' => $d['name'],
    'price' => (float) $d['unit_price'],
    'stock' => (int) $d['quantity_available'],
    'unit' => $d['unit'],
], $drugs)) ?>;

let rowCount = 0;

function drugOptions(selectedId = '') {
    let opts = '<option value="">Select a drug&hellip;</option>';
    DRUGS.forEach(d => {
        const disabled = d.stock <= 0 ? 'disabled' : '';
        const sel = String(d.id) === String(selectedId) ? 'selected' : '';
        opts += `<option value="${d.id}" ${sel} ${disabled}>${d.name}${d.stock <= 0 ? ' (out of stock)' : ''}</option>`;
    });
    return opts;
}

function addRow() {
    rowCount++;
    const tbody = document.getElementById('itemsBody');
    const tr = document.createElement('tr');
    tr.id = 'row-' + rowCount;
    tr.innerHTML = `
        <td><select name="drug_id[]" onchange="updateRow(${rowCount})" required>${drugOptions()}</select></td>
        <td class="num" id="stock-${rowCount}">&mdash;</td>
        <td class="num" id="price-${rowCount}">&mdash;</td>
        <td><input type="number" name="quantity[]" min="1" value="1" onchange="updateRow(${rowCount})" oninput="updateRow(${rowCount})"></td>
        <td class="num" id="subtotal-${rowCount}">0.00</td>
        <td><button type="button" class="btn btn-outline btn-sm" onclick="removeRow(${rowCount})">&times;</button></td>
    `;
    tbody.appendChild(tr);
}

function removeRow(id) {
    const row = document.getElementById('row-' + id);
    if (row) row.remove();
    calculateGrandTotal();
}

function updateRow(id) {
    const row = document.getElementById('row-' + id);
    const select = row.querySelector('select[name="drug_id[]"]');
    const qtyInput = row.querySelector('input[name="quantity[]"]');
    const drug = DRUGS.find(d => String(d.id) === select.value);

    if (drug) {
        document.getElementById('stock-' + id).textContent = drug.stock + ' ' + drug.unit;
        document.getElementById('price-' + id).textContent = 'KES ' + drug.price.toFixed(2);
        qtyInput.max = drug.stock;
        const qty = Math.min(parseInt(qtyInput.value || '1', 10), drug.stock || 1);
        const subtotal = drug.price * (qty > 0 ? qty : 0);
        document.getElementById('subtotal-' + id).textContent = subtotal.toFixed(2);
    } else {
        document.getElementById('stock-' + id).textContent = '\u2014';
        document.getElementById('price-' + id).textContent = '\u2014';
        document.getElementById('subtotal-' + id).textContent = '0.00';
    }
    calculateGrandTotal();
}

function calculateGrandTotal() {
    let total = 0;
    document.querySelectorAll('[id^="subtotal-"]').forEach(el => {
        total += parseFloat(el.textContent) || 0;
    });
    document.getElementById('grandTotal').textContent = total.toFixed(2);
}

// Start with one row
addRow();
</script>
