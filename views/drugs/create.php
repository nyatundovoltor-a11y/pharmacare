<div class="card" style="max-width: 640px;">
    <div class="card-header">
        <h2>Receive Stock</h2>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (!empty($existingDrugs)): ?>
        <div class="form-group">
            <label for="existing_drug_id">Restock an existing drug</label>
            <select id="existing_drug_id" onchange="toggleNewDrugFields(this.value)">
                <option value="">&mdash; Or register a brand-new drug below &mdash;</option>
                <?php foreach ($existingDrugs as $d): ?>
                    <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?> (currently <?= (int) $d['quantity_available'] ?> <?= htmlspecialchars($d['unit']) ?>)</option>
                <?php endforeach; ?>
            </select>
            <div class="hint">Pick a drug here to just add quantity, or fill the form below to register a new one.</div>
        </div>
        <div class="card-header" style="margin-top: 4px;"></div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/index.php?action=drugs_store" id="stockForm">
        <input type="hidden" name="existing_drug_id" id="existing_drug_id_hidden" value="">

        <div id="newDrugFields">
            <div class="form-group">
                <label for="name">Drug Name</label>
                <input type="text" id="name" name="name" placeholder="e.g. Amoxicillin 500mg">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="category">Category</label>
                    <input type="text" id="category" name="category" placeholder="e.g. Antibiotic">
                </div>
                <div class="form-group">
                    <label for="unit">Unit</label>
                    <input type="text" id="unit" name="unit" placeholder="e.g. tablet, bottle, box" value="tablet">
                </div>
            </div>
            <div class="form-group">
                <label for="description">Description (optional)</label>
                <textarea id="description" name="description" rows="2"></textarea>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="quantity">Quantity Received</label>
                <input type="number" id="quantity" name="quantity" min="1" required>
            </div>
            <div class="form-group" id="priceField">
                <label for="unit_price">Unit Price (KES)</label>
                <input type="number" id="unit_price" name="unit_price" min="0" step="0.01">
            </div>
        </div>
        <div class="form-group" id="reorderField">
            <label for="reorder_level">Reorder Level</label>
            <input type="number" id="reorder_level" name="reorder_level" min="0" value="10">
            <div class="hint">Inventory shows "Low Stock" once quantity drops to this level.</div>
        </div>

        <button type="submit" class="btn btn-primary">Save to Inventory</button>
        <a href="<?= BASE_URL ?>/index.php?action=drugs" class="btn btn-outline">Cancel</a>
    </form>
</div>

<script>
function toggleNewDrugFields(existingId) {
    const newFields = document.getElementById('newDrugFields');
    const priceField = document.getElementById('priceField');
    const reorderField = document.getElementById('reorderField');
    const hidden = document.getElementById('existing_drug_id_hidden');
    const nameInput = document.getElementById('name');

    hidden.value = existingId;
    const isRestock = existingId !== '';
    newFields.style.display = isRestock ? 'none' : 'block';
    priceField.style.display = isRestock ? 'none' : 'block';
    reorderField.style.display = isRestock ? 'none' : 'block';
    nameInput.required = !isRestock;
}
</script>
