<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $companies = collect([
            ['name' => 'โรงเรียนดิจิทัลเพื่อครูไทย', 'description' => 'หน่วยงานหลักด้านระบบงานวิชาการ'],
            ['name' => 'กลุ่มงานวิชาการ', 'description' => 'ดูแลงานสอนและแผนการสอน'],
            ['name' => 'งานบริการเอกสาร', 'description' => 'ระบบเอกสารและงานประสานงาน'],
            ['name' => 'กิจกรรมพัฒนาผู้เรียน', 'description' => 'ระบบกิจกรรมและชุมนุม'],
        ])->map(fn (array $company) => Company::updateOrCreate(
            ['name' => $company['name']],
            ['description' => $company['description']],
        ));

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => 'password',
                'email_verified_at' => now(),
            ],
        );

        if (User::count() < 4) {
            User::factory()->count(3)->create();
        }

        $this->call(ProjectSeeder::class);
    }
}
