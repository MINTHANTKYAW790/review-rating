<?php

namespace App\Console\Commands;

use App\Services\BillingService;
use Illuminate\Console\Command;

class ExpireSubscriptionsCommand extends Command
{
    protected $signature = 'subscriptions:expire';

    protected $description = 'Expire ended store subscriptions and apply free fallback or store deactivation';

    public function handle(BillingService $billing): int
    {
        $count = $billing->expireDueSubscriptions();
        $this->info("Expired {$count} subscription(s).");

        return self::SUCCESS;
    }
}
