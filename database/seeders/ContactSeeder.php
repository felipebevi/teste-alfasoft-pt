<?php

namespace Database\Seeders;

use App\Models\Contact;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    public function run(): void
    {
        Contact::create([
            'name'    => 'João Silva',
            'contact' => '912345678',
            'email'   => 'joao@example.com',
        ]);

        Contact::create([
            'name'    => 'Maria Santos',
            'contact' => '923456789',
            'email'   => 'maria@example.com',
        ]);

        Contact::create([
            'name'    => 'Pedro Oliveira',
            'contact' => '934567890',
            'email'   => 'pedro@example.com',
        ]);
    }
}