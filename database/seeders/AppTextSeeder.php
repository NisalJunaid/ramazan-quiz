<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AppTextSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $viewPath = resource_path('views');
        $files = File::allFiles($viewPath);

        // Match text('key', 'fallback') or text("key", "fallback") with static strings only.
        $pattern = '/text\(\s*([\'"])([^\'"\\\\]+)\1\s*,\s*([\'"])((?:\\\\.|(?!\3).)*)\3\s*\)/s';

        $entries = [];

        foreach ($files as $file) {
            $contents = File::get($file->getPathname());

            if (! preg_match_all($pattern, $contents, $matches, PREG_SET_ORDER)) {
                continue;
            }

            foreach ($matches as $match) {
                $key = $match[2];
                $fallback = trim(stripcslashes($match[4]));

                if (! isset($entries[$key])) {
                    $entries[$key] = $fallback;
                }
            }
        }

        if ($entries === []) {
            return;
        }

        $existingKeys = DB::table('app_texts')
            ->whereIn('key', array_keys($entries))
            ->distinct()
            ->pluck('key')
            ->all();

        $existingLookup = array_flip($existingKeys);
        $now = now();
        $rows = [];

        foreach ($entries as $key => $value) {
            if (isset($existingLookup[$key])) {
                continue;
            }

            $rows[] = [
                'key' => $key,
                'value' => $value,
                'locale' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($rows !== []) {
            DB::table('app_texts')->insert($rows);
        }
    }
}
