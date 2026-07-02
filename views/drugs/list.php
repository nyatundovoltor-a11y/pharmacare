<?php
$statusOf = function (array $d): array {
    if ($d['quantity_available'] <= 0) {
        return ['pill-danger', 'Out of Stock'];
    }
    if ($d['quantity_available'] <= $d['reorder_level']) {
        return ['pill-warning', 'Low Stock'];
    }
    return ['pill-success', 'In Stock'];
};
?>
<div class="card">
    <div class="card-header">
        <h2>Drug Inventory</h2>
        <?php if (Auth::role() === 'super_admin'): ?>
            <a href="<?= BASE_URL ?>/index.php?action=drugs_create" class="btn btn-primary btn-sm">+ Receive Stock</a>
        <?php endif; ?>
    </div>

    <?php if (empty($drugs)): ?>
        <div class="empty-state">
            <div class="glyph">&#9636;</div>
            No drugs registered yet.
        </div>
    <?php else: ?>
        <table class="chart-table">
            <thead>
                <tr>
                    <th>Drug</th>
                    <th>Category</th>
                    <th>Unit Price</th>
                    <th>Qty Available</th>
                    <th>Reorder Level</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($drugs as $d): [$cls, $label] = $statusOf($d); ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($d['name']) ?></strong>
                            <?php if (!empty($d['description'])): ?>
                                <div style="color:var(--text-muted); font-size:12px;"><?= htmlspecialchars($d['description']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($d['category'] ?: '&mdash;') ?></td>
                        <td class="num">KES <?= number_format($d['unit_price'], 2) ?></td>
                        <td class="num"><?= (int) $d['quantity_available'] ?> <?= htmlspecialchars($d['unit']) ?></td>
                        <td class="num"><?= (int) $d['reorder_level'] ?></td>
                        <td><span class="pill <?= $cls ?>"><?= $label ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
