<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Course;

class ScheduleFactory extends Factory
{
    public function definition(): array
    {
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $startTime = now()->addDays($this->faker->numberBetween(1, 14))->setTime($this->faker->numberBetween(8, 14), 0);
        $endTime = (clone $startTime)->addHours($this->faker->numberBetween(1, 3));

        return [
            'course_id' => Course::factory(),
            'user_id' => 3, // Pengajar default fallback
            'day' => $this->faker->randomElement($days),
            'title' => 'Pertemuan: ' . $this->faker->sentence(2),
            'description' => $this->faker->paragraph(),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'location' => 'Ruang ' . $this->faker->numberBetween(101, 505),
            'type' => $this->faker->randomElement(['online', 'offline', 'hybrid']),
        ];
    }
}
