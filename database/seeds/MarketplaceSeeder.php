<?php

use App\Marketplace;
use Illuminate\Database\Seeder;

class MarketplaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Marketplace::create([
            'name' => 'shopee',
            'website' => 'https://shopee.com.my/',
        ]);

        Marketplace::create([
            'name' => 'shopify',
            'website' => 'https://www.shopify.com/',
        ]);

        Marketplace::create([
            'name' => 'woocommerce',
            'website' => 'https://woocommerce.com/',
        ]);

        Marketplace::create([
            'name' => 'easystore',
            'website' => 'https://www.easystore.co/',
        ]);
    }
}
