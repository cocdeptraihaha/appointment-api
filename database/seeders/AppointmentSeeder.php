<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insert appointment_types data
        DB::table('appointment_types')->insert([
            ['id' => '1', 'label' => 'Consultation', 'color' => '#2ecc71', 'deleted_at' => null],
            ['id' => '2', 'label' => 'Follow-up', 'color' => '#b09973', 'deleted_at' => null],
            ['id' => '3', 'label' => 'Procedure', 'color' => '#ff1a66', 'deleted_at' => null],
            ['id' => '4', 'label' => 'NEW Weight Loss Consultation', 'color' => '#1abc9c', 'deleted_at' => null],
            ['id' => '5', 'label' => 'Follow-Up Weight Loss Consultation', 'color' => '#173436', 'deleted_at' => null],
            ['id' => '43fd', 'label' => 'Date', 'color' => '#fec3f3', 'deleted_at' => '2025-08-28 09:33:14'],
        ]);

        // Insert contacts data
        DB::table('contacts')->insert([
            ['id' => '1', 'avatar' => 'https://www.w3schools.com/w3images/avatar2.png', 'name' => 'Julie at PilotPractice'],
            ['id' => '2', 'avatar' => 'https://www.w3schools.com/w3images/avatar2.png', 'name' => 'Yari Maldonado'],
            ['id' => '3', 'avatar' => 'https://www.w3schools.com/w3images/avatar2.png', 'name' => 'IL Rockford'],
            ['id' => '4', 'avatar' => 'https://www.w3schools.com/w3images/avatar2.png', 'name' => 'Unknown'],
            ['id' => '5', 'avatar' => 'https://www.w3schools.com/w3images/avatar2.png', 'name' => 'Reza Keshavarzi'],
            ['id' => '6', 'avatar' => 'https://www.w3schools.com/w3images/avatar2.png', 'name' => 'Unknown'],
        ]);

        // Insert services data
        DB::table('services')->insert([
            ['id' => '1', 'name' => 'Expedited Gastric Sleeve'],
            ['id' => '2', 'name' => 'Teen Gastric Sleeve'],
            ['id' => '3', 'name' => 'Gastric Balloon'],
            ['id' => '4', 'name' => 'Endoscopic Sleeve Gastroplasty (ESG)'],
            ['id' => '5', 'name' => 'Gastric Bypass Revision'],
            ['id' => '6', 'name' => 'Weight Loss Injections'],
            ['id' => '7', 'name' => 'Low BMI Gastric Sleeve'],
            ['id' => '8', 'name' => 'I am Not Sure'],
        ]);

        // Insert staff data
        DB::table('staff')->insert([
            ['id' => '1', 'avatar' => 'https://www.w3schools.com/w3images/avatar2.png', 'name' => 'Pilotpractice Support'],
            ['id' => '2', 'avatar' => 'https://www.w3schools.com/w3images/avatar2.png', 'name' => 'Dr. Reza Keshavarzi'],
            ['id' => '3', 'avatar' => 'https://www.w3schools.com/w3images/avatar2.png', 'name' => 'Lucia Teran'],
            ['id' => '4', 'avatar' => 'https://www.w3schools.com/w3images/avatar2.png', 'name' => 'Rose Huber'],
            ['id' => '5', 'avatar' => 'https://www.w3schools.com/w3images/avatar2.png', 'name' => 'Lily Colas'],
            ['id' => '6', 'avatar' => 'https://www.w3schools.com/w3images/avatar2.png', 'name' => 'Dr. Pat Pazmiño'],
            ['id' => '7', 'avatar' => 'https://www.w3schools.com/w3images/avatar2.png', 'name' => 'Dr. Sarah Johnson'],
            ['id' => '8', 'avatar' => 'https://www.w3schools.com/w3images/avatar2.png', 'name' => 'Dr. Michael Chen'],
            ['id' => '9', 'avatar' => 'https://www.w3schools.com/w3images/avatar2.png', 'name' => 'Dr. Lisa Anderson'],
        ]);

        // Insert appointments data
        DB::table('appointments')->insert([
            ['id' => '7', 'title' => 'Follow-up Appointment', 'type_id' => '2', 'contact_id' => '1', 'staff_id' => '8', 'start_time' => 1756641600000, 'end_time' => 1756643400000],
            ['id' => '10', 'title' => 'Follow-up Appointment', 'type_id' => '2', 'contact_id' => '5', 'staff_id' => '7', 'start_time' => 1756123800000, 'end_time' => 1756125600000],
            ['id' => '13', 'title' => 'Follow-up Appointment', 'type_id' => '2', 'contact_id' => '1', 'staff_id' => '2', 'start_time' => 1756296600000, 'end_time' => 1756298400000],
            ['id' => '14', 'title' => 'Follow-up Appointment', 'type_id' => '2', 'contact_id' => '2', 'staff_id' => '3', 'start_time' => 1756728900000, 'end_time' => 1756730700000],
            ['id' => '20', 'title' => 'Procedure Appointment', 'type_id' => '3', 'contact_id' => '2', 'staff_id' => '7', 'start_time' => 1755178500000, 'end_time' => 1755180300000],
            ['id' => '23', 'title' => 'Follow-up Appointment', 'type_id' => '2', 'contact_id' => '1', 'staff_id' => '2', 'start_time' => 1756278600000, 'end_time' => 1756280400000],
            ['id' => '25', 'title' => 'NEW Weight Loss Consultation Appointment', 'type_id' => '4', 'contact_id' => '3', 'staff_id' => '2', 'start_time' => 1756183800000, 'end_time' => 1756185600000],
            ['id' => '26', 'title' => 'Procedure Appointment', 'type_id' => '3', 'contact_id' => '2', 'staff_id' => '3', 'start_time' => 1755752400000, 'end_time' => 1755754200000],
            ['id' => '27', 'title' => 'Follow-up Appointment', 'type_id' => '2', 'contact_id' => '2', 'staff_id' => '3', 'start_time' => 1755147900000, 'end_time' => 1755149700000],
            ['id' => '28', 'title' => 'Follow-up Appointment', 'type_id' => '2', 'contact_id' => '2', 'staff_id' => '3', 'start_time' => 1755234300000, 'end_time' => 1755236100000],
            ['id' => '29', 'title' => 'Follow-up Appointment', 'type_id' => '2', 'contact_id' => '2', 'staff_id' => '2', 'start_time' => 1755324300000, 'end_time' => 1755326100000],
            ['id' => '30', 'title' => 'Follow-up Appointment', 'type_id' => '2', 'contact_id' => '2', 'staff_id' => '2', 'start_time' => 1756188900000, 'end_time' => 1756190700000],
            ['id' => '33', 'title' => 'Follow-up Appointment', 'type_id' => '2', 'contact_id' => '2', 'staff_id' => '8', 'start_time' => 1756457400000, 'end_time' => 1756459200000],
            ['id' => '34', 'title' => 'hihi', 'type_id' => '2', 'contact_id' => '2', 'staff_id' => '8', 'start_time' => 1756344900000, 'end_time' => 1756346700000],
            ['id' => '36', 'title' => 'Date Appointment', 'type_id' => '43fd', 'contact_id' => '1', 'staff_id' => '2', 'start_time' => 1754099100000, 'end_time' => 1754100900000],
            ['id' => '37', 'title' => 'Procedure Appointment', 'type_id' => '3', 'contact_id' => '1', 'staff_id' => '2', 'start_time' => 1755066000000, 'end_time' => 1755067800000],
            ['id' => '38', 'title' => 'Procedure Appointment', 'type_id' => '3', 'contact_id' => '2', 'staff_id' => '4', 'start_time' => 1754020800000, 'end_time' => 1754022600000],
            ['id' => '39', 'title' => 'Consultation Appointment', 'type_id' => '1', 'contact_id' => '4', 'staff_id' => '1', 'start_time' => 1753935000000, 'end_time' => 1753936800000],
            ['id' => '40', 'title' => 'Follow-up Appointment', 'type_id' => '2', 'contact_id' => '2', 'staff_id' => '5', 'start_time' => 1756440300000, 'end_time' => 1756442100000],
        ]);

        // Insert appointment_services relationships
        DB::table('appointment_services')->insert([
            ['appointment_id' => '7', 'service_id' => '7'],
            ['appointment_id' => '10', 'service_id' => '2'], ['appointment_id' => '10', 'service_id' => '5'],
            ['appointment_id' => '13', 'service_id' => '1'], ['appointment_id' => '13', 'service_id' => '2'],
            ['appointment_id' => '14', 'service_id' => '1'], ['appointment_id' => '14', 'service_id' => '2'], ['appointment_id' => '14', 'service_id' => '3'],
            ['appointment_id' => '20', 'service_id' => '1'], ['appointment_id' => '20', 'service_id' => '2'], ['appointment_id' => '20', 'service_id' => '5'],
            ['appointment_id' => '23', 'service_id' => '1'], ['appointment_id' => '23', 'service_id' => '2'], ['appointment_id' => '23', 'service_id' => '6'],
            ['appointment_id' => '25', 'service_id' => '1'], ['appointment_id' => '25', 'service_id' => '2'], ['appointment_id' => '25', 'service_id' => '3'],
            ['appointment_id' => '26', 'service_id' => '1'], ['appointment_id' => '26', 'service_id' => '2'],
            ['appointment_id' => '27', 'service_id' => '3'], ['appointment_id' => '27', 'service_id' => '2'],
            ['appointment_id' => '28', 'service_id' => '1'], ['appointment_id' => '28', 'service_id' => '2'],
            ['appointment_id' => '29', 'service_id' => '2'], ['appointment_id' => '29', 'service_id' => '1'],
            ['appointment_id' => '30', 'service_id' => '1'], ['appointment_id' => '30', 'service_id' => '3'],
            ['appointment_id' => '33', 'service_id' => '5'],
            ['appointment_id' => '34', 'service_id' => '1'], ['appointment_id' => '34', 'service_id' => '2'], ['appointment_id' => '34', 'service_id' => '5'],
            ['appointment_id' => '36', 'service_id' => '2'],
            ['appointment_id' => '37', 'service_id' => '1'], ['appointment_id' => '37', 'service_id' => '2'],
            ['appointment_id' => '38', 'service_id' => '1'],
            ['appointment_id' => '39', 'service_id' => '8'],
            ['appointment_id' => '40', 'service_id' => '2'],
        ]);

        // Insert staff_services relationships
        DB::table('staff_services')->insert([
            ['staff_id' => '1', 'service_id' => '8'],
            ['staff_id' => '2', 'service_id' => '1'], ['staff_id' => '2', 'service_id' => '2'], ['staff_id' => '2', 'service_id' => '3'], ['staff_id' => '2', 'service_id' => '8'],
            ['staff_id' => '3', 'service_id' => '1'], ['staff_id' => '3', 'service_id' => '2'], ['staff_id' => '3', 'service_id' => '3'], ['staff_id' => '3', 'service_id' => '6'],
            ['staff_id' => '4', 'service_id' => '1'], ['staff_id' => '4', 'service_id' => '6'],
            ['staff_id' => '5', 'service_id' => '2'], ['staff_id' => '5', 'service_id' => '3'],
            ['staff_id' => '6', 'service_id' => '4'], ['staff_id' => '6', 'service_id' => '5'], ['staff_id' => '6', 'service_id' => '1'],
            ['staff_id' => '7', 'service_id' => '2'], ['staff_id' => '7', 'service_id' => '5'], ['staff_id' => '7', 'service_id' => '7'], ['staff_id' => '7', 'service_id' => '1'],
            ['staff_id' => '8', 'service_id' => '7'], ['staff_id' => '8', 'service_id' => '5'],
            ['staff_id' => '9', 'service_id' => '6'], ['staff_id' => '9', 'service_id' => '4'],
        ]);

        // Insert settings data
        DB::table('settings')->insert([
            ['setting_key' => 'visibleContacts', 'setting_value' => '["1", "2", "3", "4", "5", "6"]'],
        ]);
    }
}
