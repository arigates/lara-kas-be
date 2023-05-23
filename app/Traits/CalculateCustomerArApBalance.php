<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait CalculateCustomerArApBalance
{
    public function calculateCustomerArApBalance(string $customerId): void
    {
        if (trim($customerId) === '') {
            return;
        }

        DB::statement('UPDATE customers SET ar_balance = (SELECT SUM(ar) FROM ar_aps WHERE customer_id = ? AND deleted_at IS NULL), ap_balance = (SELECT SUM(ap) FROM ar_aps WHERE customer_id = ? AND deleted_at IS NULL), ar_ap_balance = (SELECT SUM(ap-ar) FROM ar_aps WHERE customer_id = ? AND deleted_at IS NULL) WHERE id = ?', [$customerId, $customerId, $customerId, $customerId]);
    }
}
