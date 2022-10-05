<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $param = [
            [
                'name' => '野菜',
            ],
            [
                'name' => 'タンパク質',
            ],
            [
                'name' => '炭水化物',
            ]
        ];
        DB::table('categories')->insert($param);
    }
}
