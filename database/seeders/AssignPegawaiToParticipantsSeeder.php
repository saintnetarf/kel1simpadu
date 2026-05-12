<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClassParticipant;
use App\Models\Pegawai;

class AssignPegawaiToParticipantsSeeder extends Seeder
{
    public function run(): void
    {
        $pegawai = Pegawai::where('is_active', true)->get();
        if ($pegawai->isEmpty()) {
            return;
        }

        $participants = ClassParticipant::limit(50)->get();
        $i = 0;
        foreach ($participants as $p) {
            $p->pegawai_id = $pegawai[$i % $pegawai->count()]->id;
            $p->save();
            $i++;
        }
    }
}
