<?php
class DrugRequest extends Model
{
    protected string $table = 'drug_requests';

    /**
     * Create a request with its line items in one transaction.
     * $items = [['drug_id' => 1, 'quantity' => 2], ...]
     * Stock availability must already have been checked by the caller.
     */
    public function createWithItems(string $customerName, ?string $customerPhone, int $pharmacistId, array $items): int
    {
        $drugModel = new Drug();

        $this->db->beginTransaction();
        try {
            $requestCode = 'REQ-' . str_pad((string) $this->nextSequence(), 6, '0', STR_PAD_LEFT);

            $stmt = $this->db->prepare(
                "INSERT INTO drug_requests (request_code, customer_name, customer_phone, pharmacist_id, total_amount, status)
                 VALUES (:code, :name, :phone, :pharmacist_id, 0, 'awaiting_payment')"
            );
            $stmt->execute([
                'code'          => $requestCode,
                'name'          => $customerName,
                'phone'         => $customerPhone,
                'pharmacist_id' => $pharmacistId,
            ]);
            $requestId = (int) $this->db->lastInsertId();

            $total = 0;
            $itemStmt = $this->db->prepare(
                "INSERT INTO drug_request_items (request_id, drug_id, quantity, unit_price, subtotal)
                 VALUES (:request_id, :drug_id, :quantity, :unit_price, :subtotal)"
            );

            foreach ($items as $item) {
                $drug = $drugModel->find((int) $item['drug_id']);
                if (!$drug) {
                    throw new RuntimeException("Drug not found: " . $item['drug_id']);
                }
                if ($drug['quantity_available'] < $item['quantity']) {
                    throw new RuntimeException("Insufficient stock for {$drug['name']}");
                }
                $subtotal = $drug['unit_price'] * $item['quantity'];
                $itemStmt->execute([
                    'request_id' => $requestId,
                    'drug_id'    => $drug['id'],
                    'quantity'   => $item['quantity'],
                    'unit_price' => $drug['unit_price'],
                    'subtotal'   => $subtotal,
                ]);
                $total += $subtotal;
            }

            $update = $this->db->prepare("UPDATE drug_requests SET total_amount = :total WHERE id = :id");
            $update->execute(['total' => $total, 'id' => $requestId]);

            $this->db->commit();
            return $requestId;
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    private function nextSequence(): int
    {
        $row = $this->db->query("SELECT COALESCE(MAX(id), 0) + 1 AS next_id FROM drug_requests")->fetch();
        return (int) $row['next_id'];
    }

    public function findWithItems(int $id): ?array
    {
        $request = $this->find($id);
        if (!$request) {
            return null;
        }
        $stmt = $this->db->prepare(
            "SELECT dri.*, d.name AS drug_name
             FROM drug_request_items dri
             JOIN drugs d ON d.id = dri.drug_id
             WHERE dri.request_id = :id"
        );
        $stmt->execute(['id' => $id]);
        $request['items'] = $stmt->fetchAll();
        return $request;
    }

    public function byPharmacist(int $pharmacistId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM drug_requests WHERE pharmacist_id = :pid ORDER BY created_at DESC"
        );
        $stmt->execute(['pid' => $pharmacistId]);
        return $stmt->fetchAll();
    }

    public function awaitingPayment(): array
    {
        return $this->db->query(
            "SELECT dr.*, u.full_name AS pharmacist_name
             FROM drug_requests dr
             JOIN users u ON u.id = dr.pharmacist_id
             WHERE dr.status = 'awaiting_payment'
             ORDER BY dr.created_at ASC"
        )->fetchAll();
    }

    public function readyForCheckout(): array
    {
        return $this->db->query(
            "SELECT dr.*, p.receipt_no
             FROM drug_requests dr
             JOIN payments p ON p.request_id = dr.id
             WHERE dr.status = 'paid'
             ORDER BY p.paid_at ASC"
        )->fetchAll();
    }

    public function markPaid(int $id): void
    {
        $this->db->prepare("UPDATE drug_requests SET status = 'paid' WHERE id = :id")->execute(['id' => $id]);
    }

    public function markCompleted(int $id): void
    {
        $this->db->prepare("UPDATE drug_requests SET status = 'completed' WHERE id = :id")->execute(['id' => $id]);
    }

    public function cancel(int $id): void
    {
        $this->db->prepare("UPDATE drug_requests SET status = 'cancelled' WHERE id = :id")->execute(['id' => $id]);
    }
}