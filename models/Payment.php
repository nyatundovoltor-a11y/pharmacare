<?php
class Payment extends Model
{
    protected string $table = 'payments';

    public function create(int $requestId, int $cashierId, float $amountPaid, string $method = 'cash'): int
    {
        $receiptNo = 'RCT-' . str_pad((string) $this->nextSequence(), 6, '0', STR_PAD_LEFT);

        $stmt = $this->db->prepare(
            "INSERT INTO payments (request_id, receipt_no, cashier_id, amount_paid, payment_method)
             VALUES (:request_id, :receipt_no, :cashier_id, :amount_paid, :method)"
        );
        $stmt->execute([
            'request_id'  => $requestId,
            'receipt_no'  => $receiptNo,
            'cashier_id'  => $cashierId,
            'amount_paid' => $amountPaid,
            'method'      => $method,
        ]);
        return (int) $this->db->lastInsertId();
    }

    private function nextSequence(): int
    {
        $row = $this->db->query("SELECT COALESCE(MAX(id), 0) + 1 AS next_id FROM payments")->fetch();
        return (int) $row['next_id'];
    }

    public function findByRequestId(int $requestId): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT p.*, u.full_name AS cashier_name
             FROM payments p
             JOIN users u ON u.id = p.cashier_id
             WHERE p.request_id = :rid LIMIT 1"
        );
        $stmt->execute(['rid' => $requestId]);
        return $stmt->fetch() ?: null;
    }
}