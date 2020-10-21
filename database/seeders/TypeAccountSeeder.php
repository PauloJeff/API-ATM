<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('type_accounts')->insert([
            'name' => 'Conta Corrente'
        ]);

        DB::table('type_accounts')->insert([
            'name' => 'Poupança'
        ]);
    }
}
