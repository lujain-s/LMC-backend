<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LMCInfo;

class LMCInfoSeeder extends Seeder
{
    public function run()
    {
        $descriptions = [
            ['Title' => 'Mission', 'Explanation' => 'Our mission is to...'],
            ['Title' => 'Location', 'Explanation' => 'Jaramana-Rawda'],
        ];

        LMCInfo::create([
            'Title' => 'About LMC',
            'Descriptions' => json_encode($descriptions),
            'Photo' => url('images/sample-photo.jpg'),
        ]);
    }
}
