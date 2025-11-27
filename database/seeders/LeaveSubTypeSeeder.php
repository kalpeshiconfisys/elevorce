<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LeaveSubTypeSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now()->toDateTimeString();

        DB::table('leave_sub_types')->insert([
            [
                'name' => 'Full Day',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Half Day (Morning)',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Half Day (Evening)',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Early Leave',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
