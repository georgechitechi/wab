<?php

namespace Database\Seeders;

use App\Models\CabinZone;
use App\Models\Hold;
use App\Models\User;
use App\Models\Cargo;
use App\Models\Flight;
use App\Models\Passenger;
use App\Models\FuelFigure;
use Faker\Factory as Faker;
use App\Models\AircraftType;
use App\Models\Registration;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class
        ]);
        AircraftType::factory(3)->create()->each(function ($value) {
            $faker = Faker::create();
            $previousFwd = 0;
            for ($i = 1; $i <= 5; $i++) {
                $currentAft = $previousFwd + 20;
                Hold::create([
                    'aircraft_type_id' => $value->id,
                    'hold_no' => $i,
                    'fwd' => $previousFwd,
                    'aft' => $currentAft,
                    'max' => $faker->numberBetween(1400, 2300),
                    'restrictions' => $faker->optional()->randomElement(['No Avi', 'No HUM']),
                ]);
                $previousFwd = $currentAft;
            }
            Registration::factory(5)->create([
                'aircraft_type_id' => $value
            ]);

            foreach (['A', 'B', 'C'] as $zone) {
                CabinZone::factory(1)->create([
                    'aircraft_type_id' => $value,
                    'zone_name' => $zone
                ]);
            }
        });

        $data = [
            ['envelope_type' => 'TOW', 'x' => 39.02, 'y' => 40.600],
            ['envelope_type' => 'TOW', 'x' => 36.66, 'y' => 45.279],
            ['envelope_type' => 'TOW', 'x' => 33.43, 'y' => 53.000],
            ['envelope_type' => 'TOW', 'x' => 34.52, 'y' => 63.000],
            ['envelope_type' => 'TOW', 'x' => 31.50, 'y' => 72.000],
            ['envelope_type' => 'TOW', 'x' => 37.16, 'y' => 73.500],
            ['envelope_type' => 'TOW', 'x' => 62.28, 'y' => 79.000],
            ['envelope_type' => 'TOW', 'x' => 80.83, 'y' => 79.000],
            ['envelope_type' => 'TOW', 'x' => 87.90, 'y' => 74.708],
            ['envelope_type' => 'TOW', 'x' => 90.18, 'y' => 73.326],
            ['envelope_type' => 'TOW', 'x' => 86.45, 'y' => 67.400],
            ['envelope_type' => 'TOW', 'x' => 70.21, 'y' => 51.000],
            ['envelope_type' => 'TOW', 'x' => 69.22, 'y' => 50.000],
            ['envelope_type' => 'TOW', 'x' => 65.36, 'y' => 47.038],
            ['envelope_type' => 'TOW', 'x' => 60.62, 'y' => 45.249],
            ['envelope_type' => 'TOW', 'x' => 59.56, 'y' => 42.735],
            ['envelope_type' => 'TOW', 'x' => 56.62, 'y' => 40.600],

            ['envelope_type' => 'ZFW', 'x' => 40.70, 'y' => 40.600],
            ['envelope_type' => 'ZFW', 'x' => 39.02, 'y' => 43.941],
            ['envelope_type' => 'ZFW', 'x' => 37.05, 'y' => 48.658],
            ['envelope_type' => 'ZFW', 'x' => 37.56, 'y' => 53.398],
            ['envelope_type' => 'ZFW', 'x' => 37.56, 'y' => 53.872],
            ['envelope_type' => 'ZFW', 'x' => 37.43, 'y' => 54.346],
            ['envelope_type' => 'ZFW', 'x' => 37.01, 'y' => 55.611],
            ['envelope_type' => 'ZFW', 'x' => 37.50, 'y' => 60.143],
            ['envelope_type' => 'ZFW', 'x' => 36.11, 'y' => 64.300],
            ['envelope_type' => 'ZFW', 'x' => 83.40, 'y' => 64.300],
            ['envelope_type' => 'ZFW', 'x' => 69.31, 'y' => 50.080],
            ['envelope_type' => 'ZFW', 'x' => 69.84, 'y' => 49.606],
            ['envelope_type' => 'ZFW', 'x' => 68.10, 'y' => 49.132],
            ['envelope_type' => 'ZFW', 'x' => 65.39, 'y' => 47.049],
            ['envelope_type' => 'ZFW', 'x' => 60.86, 'y' => 45.340],
            ['envelope_type' => 'ZFW', 'x' => 61.39, 'y' => 44.866],
            ['envelope_type' => 'ZFW', 'x' => 60.26, 'y' => 44.392],
            ['envelope_type' => 'ZFW', 'x' => 59.66, 'y' => 42.970],
            ['envelope_type' => 'ZFW', 'x' => 58.80, 'y' => 42.022],
            ['envelope_type' => 'ZFW', 'x' => 58.20, 'y' => 40.600],
        ];

        foreach ($data as $point) {
            AircraftType::all()->each(function ($aircraft) use ($point) {
                $aircraft->envelopes()->create($point);
            });
        }

        Flight::factory(50)->create()->each(function ($id_no) {
            FuelFigure::factory(1)->create([
                'flight_id' => $id_no
            ]);
            Passenger::factory(1)->create([
                'flight_id' => $id_no,
                'zone' => $id_no->registration->aircraftType->cabinZones->random()->zone_name
            ]);
            Cargo::factory(5)->create([
                'flight_id' => $id_no,
                'hold_id' => null
            ]);
        });

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Default App Setting
        foreach (config("admin.default") as $key => $value) {
            \App\Models\Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value],
            );
        }
    }
}
