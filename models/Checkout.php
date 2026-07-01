<?php
class Checkout extends Model
{
    protected string $table = 'checkouts';

    /**
     * Finalize dispensing: deduct stock for every item on the request,
     * mark the request completed, and log the checkout - all atomically.
     */
    public function process(int $requestId, int $pharmacistId): void
    {
        $requestModel = new DrugRequest();
        $drugModel = new Drug();

        $request = $requestModel->findWithItems($requestId);
        if (!$request) {
            throw new RuntimeException('Request not found.');
        }
        if ($request['status'] !== 'paid') {
            throw new RuntimeException('This request has not been paid for yet, or has already been checked out.');
        }

        $this->db->beginTransaction();
        try {
            foreach ($request['items'] as $item) {
                $drugModel->deductStock(
                    (int) $item['drug_id'],
                    (int) $item['quantity'],
                    $pharmacistId,
                    "Dispensed for {$request['request_code']}"
                );
            }

            $stmt = $this->db->prepare(
                "INSERT INTO checkouts (request_id, pharmacist_id) VALUES (:request_id, :pharmacist_id)"
            );
            $stmt->execute(['request_id' => $requestId, 'pharmacist_id' => $pharmacistId]);

            $requestModel->markCompleted($requestId);

            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}