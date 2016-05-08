<?php

use Illuminate\Database\Schema\Blueprint;

/**
 * Class OrdersTableSeeder
 */
class CurrencyTableSeeder extends Seeder
{
    /**
     * The seeder to run
     */
    public function run()
    {
        if (Schema::hasTable('currency'))
        {
            DB::table('currency')->insert([
                    0 => [
                        'display_name'  => 'US Dollars',
                        'currency'      => 'USD',
                        'rate'          => 0.0808279,
                        'surcharge'     => 7.5,
                        'discount'      => 0.0,
                        'created_at'    => DB::raw('NOW()'),
                    ],
                    1 => [
                        'display_name'  => 'British Pound',
                        'currency'      => 'GBP',
                        'rate'          => 0.0527032,
                        'surcharge'     => 5,
                        'discount'      => 0.0,
                        'created_at'    => DB::raw('NOW()'),
                    ],
                    2 => [
                        'display_name'  => 'Euro',
                        'currency'      => 'EUR',
                        'rate'          => 0.0718710,
                        'surcharge'     => 5,
                        'discount'      => 0.0,
                        'created_at'    => DB::raw('NOW()'),
                    ],
                    3 => [
                        'display_name'  => 'Kenyan Shilling',
                        'currency'      => 'KES',
                        'rate'          => 7.81498,
                        'surcharge'     => 2.5,
                        'discount'      => 2.0,
                        'created_at'    => DB::raw('NOW()'),
                    ]
                ]
            );

            $this->command->info('Currency table seeded');
        }
    }
}
