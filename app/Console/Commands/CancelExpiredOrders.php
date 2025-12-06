<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Order\OrderService;

class CancelExpiredOrders extends Command
{
    protected $signature = 'orders:cancel-expired';
    protected $description = 'Cancel orders that expired (pending payment > 24h)';

    public function handle(OrderService $orderService)
    {
        \Log::info("Running cancel-expired-order at: " . now());

        $count = $orderService->cancelExpiredOrders();

        \Log::info("Expired orders cancelled: {$count}");

        $this->info("Cancelled {$count} orders.");
    }
}
