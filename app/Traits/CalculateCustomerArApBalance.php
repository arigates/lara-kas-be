<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait CalculateCustomerArApBalance
{
    public function calculateCustomerArApBalance(string $customerId): void
    {
        if (trim($customerId) === '') {
            return;
        }

        $updatedAt = Carbon::now()->toDateTimeString();

        DB::statement('UPDATE customers SET ar_balance = (SELECT SUM(ar) FROM ar_aps WHERE customer_id = ? AND deleted_at IS NULL), ap_balance = (SELECT SUM(ap) FROM ar_aps WHERE customer_id = ? AND deleted_at IS NULL), ar_ap_balance = (SELECT SUM(ap-ar) FROM ar_aps WHERE customer_id = ? AND deleted_at IS NULL), updated_at = ? WHERE id = ?', [$customerId, $customerId, $customerId, $updatedAt, $customerId]);
    }
}
