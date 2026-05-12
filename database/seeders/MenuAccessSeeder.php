<?php

namespace Database\Seeders;

use App\Models\MenuAccess;
use Illuminate\Database\Seeder;

class MenuAccessSeeder extends Seeder
{
    public function run(): void
    {
        $masterAkademik = MenuAccess::firstOrCreate([
            'code' => 'master-akademik',
        ], [
            'name' => 'Master Akademik',
            'path' => '/master-data/academic-years',
            'icon' => 'academic-cap',
            'sort_order' => 20,
            'is_active' => true,
            'description' => 'Menu untuk mengelola data akademik dasar.',
        ]);

        $menus = [
            ['code' => 'academic-years', 'name' => 'Tahun Akademik', 'path' => '/master-data/academic-years', 'sort_order' => 1],
            ['code' => 'study-programs', 'name' => 'Program Studi', 'path' => '/master-data/study-programs', 'sort_order' => 2],
            ['code' => 'classes', 'name' => 'Kelas', 'path' => '/master-data/classes', 'sort_order' => 3],
            ['code' => 'courses', 'name' => 'Mata Kuliah', 'path' => '/master-data/courses', 'sort_order' => 4],
            ['code' => 'students', 'name' => 'Mahasiswa', 'path' => '/master-data/students', 'sort_order' => 5],
            ['code' => 'pegawai', 'name' => 'Pegawai', 'path' => '/master-data/pegawai', 'sort_order' => 6],
            ['code' => 'class-participants', 'name' => 'Peserta Kelas', 'path' => '/master-data/class-participants', 'sort_order' => 7],
        ];

        foreach ($menus as $menu) {
            MenuAccess::firstOrCreate(
                ['code' => $menu['code']],
                [
                    'name' => $menu['name'],
                    'path' => $menu['path'],
                    'icon' => null,
                    'parent_id' => $masterAkademik->id,
                    'sort_order' => $menu['sort_order'],
                    'is_active' => true,
                    'description' => null,
                ]
            );
        }
    }
}
