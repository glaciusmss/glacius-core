<?php

namespace App\Console\Commands;

use App\Customer;
use App\Order;
use App\Product;
use Illuminate\Console\Command;
use Laravel\Scout\Console\ImportCommand;

class ScoutImportAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scout:import:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'import all related model to scout';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->call(ImportCommand::class, ['model' => Customer::class]);
        $this->call(ImportCommand::class, ['model' => Order::class]);
        $this->call(ImportCommand::class, ['model' => Product::class]);

        return 0;
    }
}
