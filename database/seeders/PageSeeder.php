<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            ['slug' => 'home', 'name' => 'Home', 'sort_order' => 1],
            ['slug' => 'trails', 'name' => 'Trails', 'sort_order' => 2],
            ['slug' => 'media', 'name' => 'Media', 'sort_order' => 3],
            ['slug' => 'sponsors', 'name' => 'Sponsors', 'sort_order' => 4],
            ['slug' => 'contact', 'name' => 'Contact', 'sort_order' => 5],
        ];

        foreach ($pages as $page) {
            Page::updateOrCreate(
                ['slug' => $page['slug']],
                $page
            );
        }
    }
}
