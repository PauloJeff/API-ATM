<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('accounts')->insert([
            'user_id'           => 1,
            'account_number'    => 123456,
            'password'          => Hash::make('sample123'),
            'balance'           => 0.00,
            'type'              => 1
        ]);
    }
}
