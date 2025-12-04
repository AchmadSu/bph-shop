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
        $count = $orderService->cancelExpiredOrders();
        $this->info("Cancelled {$count} orders.");
    }
}
