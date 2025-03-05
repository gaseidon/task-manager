<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Создание экземпляра Faker
        $faker = Faker::create();

        // Удаление всех записей из таблицы tasks перед заполнением
        DB::table('tasks')->delete();

        // Генерация и вставка 30 задач
        for ($i = 0; $i < 30; $i++) {
            DB::table('tasks')->insert([
                'title' => $faker->sentence(3), // Случайное предложение из 3 слов
                'description' => $faker->paragraph, // Случайный абзац
                'is_completed' => $faker->boolean, // Случайное булево значение
                'user_id' => 2, // Случайное число от 1 до 10 для user_id
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}