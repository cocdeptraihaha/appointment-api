<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Generate ~100 contacts with fake-like data
        $domains = ['example.com', 'mail.com', 'test.com', 'demo.org'];
        $firstNames = ['John','Jane','Alex','Maria','David','Sarah','Michael','Emily','Daniel','Sophia','Liam','Olivia','Noah','Ava','William','Isabella','James','Mia','Benjamin','Charlotte'];
        $lastNames = ['Smith','Johnson','Williams','Brown','Jones','Garcia','Miller','Davis','Rodriguez','Martinez','Hernandez','Lopez','Gonzalez','Wilson','Anderson','Thomas','Taylor','Moore','Jackson','Martin'];

        $rows = [];
        for ($i = 0; $i < 100; $i++) {
            $first = $firstNames[array_rand($firstNames)];
            $last = $lastNames[array_rand($lastNames)];
            $id = Str::random(10);
            $email = strtolower($first . '.' . $last . $i . '@' . $domains[array_rand($domains)]);
            $phone = '+1 ' . random_int(200, 999) . '-' . random_int(200, 999) . '-' . str_pad((string)random_int(0, 9999), 4, '0', STR_PAD_LEFT);

            $rows[] = [
                'id' => (string) $id,
                'first_name' => $first,
                'last_name' => $last,
                'email' => $email,
                'phone_number' => $phone,
                'avatar' => 'https://www.w3schools.com/w3images/avatar2.png',
            ];
        }

        // Upsert to avoid FK issues and keep existing references
        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('contacts')->upsert($chunk, ['id'], ['first_name','last_name','email','phone_number','avatar']);
        }
    }
}
