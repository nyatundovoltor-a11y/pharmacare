<?php
class Drug extends Model
{
    protected string $table = 'drugs';

    public function all(string $orderBy = 'name ASC'): array
    {
        return parent::all($orderBy);
    }

    public function find(int $id): ?array
    {
        return parent::find($id);
    }

    public function search(string $term): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM drugs WHERE name LIKE :term ORDER BY name ASC LIMIT 20"
        );
        $stmt->execute(['term' => '%' . $term . '%']);
        return $stmt->fetchAll();
    }

    /** Add a brand-new drug line to inventory */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO drugs (name, description, category, unit, unit_price, quantity_available, reorder_level, added_by)
             VALUES (:name, :description, :category, :unit, :unit_price, :qty, :reorder_level, :added_by)"
        );
        $stmt->execute([
            'name'          => $data['name'],
            'description'   => $data['description'] ?? null,
            'category'      => $data['category'] ?? null,
            'unit'          => $data['unit'] ?? 'unit',
            'unit_price'    => $data['unit_price'],
            'qty'           => $data['quantity'],
            'reorder_level' => $data['reorder_level'] ?? 10,
            'added_by'      => $data['added_by'],
        ]);
        $drugId = (int) $this->db->lastInsertId();

        $this->logStock($drugId, (int) $data['quantity'], 'stock_in', $data['added_by'], 'Initial stock');

        return $drugId;
    }

    /** Top up quantity for an existing drug already in inventory */
    public function addStock(int $drugId, int $quantity, int $performedBy, string $note = ''): void
    {
        $stmt = $this->db->prepare(
            "UPDATE drugs SET quantity_available = quantity_available + :qty WHERE id = :id"
        );
        $stmt->execute(['qty' => $quantity, 'id' => $drugId]);
        $this->logStock($drugId, $quantity, 'stock_in', $performedBy, $note ?: 'Restock');
    }

    /** Deduct stock when drugs are checked out/sold */
    public function deductStock(int $drugId, int $quantity, int $performedBy, string $note = ''): void
    {
        $stmt = $this->db->prepare(
            "UPDATE drugs SET quantity_available = quantity_available - :qty WHERE id = :id"
        );
        $stmt->execute(['qty' => $quantity, 'id' => $drugId]);
        $this->logStock($drugId, -$quantity, 'sale', $performedBy, $note ?: 'Checked out to customer');
    }

    private function logStock(int $drugId, int $changeQty, string $action, int $performedBy, string $note): void
    {
        $stmt = $this->db->prepare(
            "INSERT INTO stock_logs (drug_id, change_qty, action, performed_by, note)
             VALUES (:drug_id, :change_qty, :action, :performed_by, :note)"
        );
        $stmt->execute([
            'drug_id'      => $drugId,
            'change_qty'   => $changeQty,
            'action'       => $action,
            'performed_by' => $performedBy,
            'note'         => $note,
        ]);
    }
}