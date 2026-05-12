<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pegawai;

class PegawaiSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['employee_number' => 'P001', 'name' => 'Andi Setiawan', 'email' => 'andi@poliban.ac.id', 'phone' => '081234567890', 'position' => 'Dosen', 'is_active' => true],
            ['employee_number' => 'P002', 'name' => 'Siti Nurjanah', 'email' => 'siti@poliban.ac.id', 'phone' => '081298765432', 'position' => 'Admin Akademik', 'is_active' => true],
            ['employee_number' => 'P003', 'name' => 'Budi Santoso', 'email' => 'budi@poliban.ac.id', 'phone' => '08135551234', 'position' => 'Staff', 'is_active' => true],
            ['employee_number' => 'P004', 'name' => 'Rina Widya', 'email' => 'rina@poliban.ac.id', 'phone' => '08136662345', 'position' => 'Dosen', 'is_active' => true],
            ['employee_number' => 'P005', 'name' => 'Anton Pratama', 'email' => 'anton@poliban.ac.id', 'phone' => '08137773456', 'position' => 'Koordinator', 'is_active' => true],
        ];

        foreach ($items as $data) {
            Pegawai::firstOrCreate(['employee_number' => $data['employee_number']], $data);
        }
    }
}
