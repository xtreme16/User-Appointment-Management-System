<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AppointmentTableSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all()->values();
        if ($users->isEmpty()) {
            return;
        }

        for ($i = 1; $i <= 5; $i++) {
            $creator = $users->random();
            $tz = $creator->preferred_timezone ?? 'Asia/Jakarta';

            $dayOffset = rand(1, 10);
            $hour = rand(8, 17);
            $minute = rand(0, 1) ? 30 : 0;

            $startLocal = Carbon::now($tz)->addDays($dayOffset)->setTime($hour, $minute, 0);
            $endLocal = (clone $startLocal)->addHour();

            $startUtc = $startLocal->copy()->setTimezone('UTC')->toDateTimeString();
            $endUtc = $endLocal->copy()->setTimezone('UTC')->toDateTimeString();

            $appointment = Appointment::create([
                'id' => (string) Str::uuid(),
                'title' => "Dummy Meeting {$i}",
                'creator_id' => $creator->id,
                'start' => $startUtc,
                'end' => $endUtc,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            $participantIds = [$creator->id];
            $otherCandidates = $users->where('id', '<>', $creator->id);
            $otherCount = min(rand(1, 3), $otherCandidates->count());
            if ($otherCount > 0) {
                $others = $otherCandidates->random($otherCount)->pluck('id')->toArray();
                $participantIds = array_merge($participantIds, $others);
            }

            $appointment->users()->attach($participantIds);
        }
    }
}