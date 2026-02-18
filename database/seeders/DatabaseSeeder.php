<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Banner;
use App\Models\Event;
use App\Models\Faq;
use App\Models\GalleryImage;
use App\Models\Page;
use App\Models\SiteSetting;
use App\Models\Sponsor;
use App\Models\Tour;
use App\Models\User;
use App\Models\Video;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::firstOrCreate(
            ['email' => 'admin@enduroam.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        // Pages
        $pages = [
            ['slug' => 'home', 'name' => 'Home', 'is_visible' => true, 'sort_order' => 1],
            ['slug' => 'trails', 'name' => 'Trails', 'is_visible' => true, 'sort_order' => 2],
            ['slug' => 'media', 'name' => 'Media', 'is_visible' => true, 'sort_order' => 3],
            ['slug' => 'sponsors', 'name' => 'Sponsors', 'is_visible' => true, 'sort_order' => 4],
            ['slug' => 'contact', 'name' => 'Contact', 'is_visible' => true, 'sort_order' => 5],
        ];

        foreach ($pages as $page) {
            Page::firstOrCreate(['slug' => $page['slug']], $page);
        }

        // Tours
        $tour1 = Tour::create([
            'name' => 'Mountain Explorer',
            'description' => 'Discover breathtaking mountain trails with experienced guides.',
            'full_description' => 'Experience the ultimate mountain adventure with our Mountain Explorer tour. This full-day guided enduro ride takes you through some of the most spectacular mountain trails in the region.',
            'duration' => 8,
            'difficulty' => 'intermediate',
            'price' => 149.99,
            'currency' => '€',
            'location' => 'Alpine Mountains',
            'max_participants' => 12,
            'featured_image' => 'https://placehold.co/800x600/2d5016/white?text=Mountain+Explorer',
            'sort_order' => 1,
        ]);

        $tour1->includes()->createMany([
            ['icon' => 'shield', 'text' => 'Professional guide', 'sort_order' => 1],
            ['icon' => 'utensils', 'text' => 'Lunch included', 'sort_order' => 2],
            ['icon' => 'camera', 'text' => 'Photo package', 'sort_order' => 3],
        ]);

        $tour1->images()->createMany([
            ['path' => 'https://placehold.co/800x600/2d5016/white?text=Trail+1', 'alt' => 'Mountain trail view', 'sort_order' => 1],
            ['path' => 'https://placehold.co/800x600/2d5016/white?text=Trail+2', 'alt' => 'Forest section', 'sort_order' => 2],
        ]);

        $tour1->update(['availability_type' => 'weekdays', 'available_weekdays' => [1, 3, 5]]);


        $tour1->reviews()->createMany([
            ['author' => 'John D.', 'rating' => 5, 'text' => 'Absolutely amazing experience! The trails were incredible.', 'date' => '2026-01-15', 'is_approved' => true],
            ['author' => 'Sarah M.', 'rating' => 4, 'text' => 'Great tour, well organized. Would do again!', 'date' => '2026-01-20', 'is_approved' => true],
        ]);

        $tour2 = Tour::create([
            'name' => 'Forest Trail Adventure',
            'description' => 'Navigate through lush forest trails with technical challenges.',
            'full_description' => 'Immerse yourself in nature with our Forest Trail Adventure. Wind through ancient forests on perfectly maintained enduro trails.',
            'duration' => 6,
            'difficulty' => 'easy',
            'price' => 99.99,
            'currency' => '€',
            'location' => 'Black Forest',
            'max_participants' => 8,
            'featured_image' => 'https://placehold.co/800x600/1a3a0a/white?text=Forest+Trail',
            'sort_order' => 2,
        ]);

        $tour2->includes()->createMany([
            ['icon' => 'shield', 'text' => 'Professional guide', 'sort_order' => 1],
            ['icon' => 'droplet', 'text' => 'Water provided', 'sort_order' => 2],
        ]);

        $tour2->images()->createMany([
            ['path' => 'https://placehold.co/800x600/1a3a0a/white?text=Forest+1', 'alt' => 'Forest trail entry', 'sort_order' => 1],
        ]);

        $tour2->reviews()->createMany([
            ['author' => 'Mike R.', 'rating' => 5, 'text' => 'Perfect beginner-friendly tour. Beautiful scenery!', 'date' => '2026-01-10', 'is_approved' => true],
        ]);

        // Events
        $event1 = Event::create([
            'name' => 'Enduro Championship 2026',
            'description' => 'The biggest enduro competition of the year.',
            'full_description' => 'Join hundreds of riders in the annual Enduro Championship. Categories for all skill levels with amazing prizes.',
            'date' => '2026-06-15',
            'duration' => 2,
            'difficulty' => 'advanced',
            'price' => 75.00,
            'currency' => '€',
            'location' => 'Alpine Arena',
            'max_participants' => 200,
            'spots_left' => 150,
            'featured_image' => 'https://placehold.co/800x600/8b4513/white?text=Championship',
            'sort_order' => 1,
        ]);

        $event1->availableDates()->create(['date' => '2026-06-15']);

        $event1->includes()->createMany([
            ['icon' => 'trophy', 'text' => 'Prizes for top 3', 'sort_order' => 1],
            ['icon' => 'utensils', 'text' => 'BBQ lunch', 'sort_order' => 2],
            ['icon' => 'shirt', 'text' => 'Event t-shirt', 'sort_order' => 3],
        ]);

        $event1->images()->createMany([
            ['path' => 'https://placehold.co/800x600/8b4513/white?text=Event+1', 'alt' => 'Starting line', 'sort_order' => 1],
        ]);

        $event1->reviews()->createMany([
            ['author' => 'Alex K.', 'rating' => 5, 'text' => 'Best event of the year! Can\'t wait for the next one.', 'date' => '2025-06-20', 'is_approved' => true],
        ]);

        // Banners
        Banner::create([
            'type' => 'image',
            'image' => 'https://placehold.co/1920x800/2d5016/white?text=Welcome+to+Enduroam',
            'title' => 'Welcome to Enduroam',
            'text' => 'Experience the thrill of enduro riding in the most beautiful locations.',
            'text_position' => 'center',
            'cta_text' => 'View Tours',
            'cta_href' => '/trails',
            'sort_order' => 1,
        ]);

        Banner::create([
            'type' => 'image',
            'image' => 'https://placehold.co/1920x800/8b4513/white?text=Join+Our+Events',
            'title' => 'Join Our Events',
            'text' => 'Compete with the best riders in our upcoming championship.',
            'text_position' => 'left',
            'cta_text' => 'See Events',
            'cta_href' => '/trails',
            'sort_order' => 2,
        ]);

        // Gallery Images
        $galleryImages = [
            ['src' => 'https://placehold.co/800x600/2d5016/white?text=Gallery+1', 'alt' => 'Mountain ride', 'aspect_ratio' => 'landscape', 'sort_order' => 1],
            ['src' => 'https://placehold.co/600x800/1a3a0a/white?text=Gallery+2', 'alt' => 'Forest trail', 'aspect_ratio' => 'portrait', 'sort_order' => 2],
            ['src' => 'https://placehold.co/800x800/8b4513/white?text=Gallery+3', 'alt' => 'Rider action', 'aspect_ratio' => 'square', 'sort_order' => 3],
            ['src' => 'https://placehold.co/800x600/2d5016/white?text=Gallery+4', 'alt' => 'Sunset ride', 'aspect_ratio' => 'landscape', 'sort_order' => 4],
        ];

        foreach ($galleryImages as $image) {
            GalleryImage::create($image);
        }

        // Videos
        Video::create([
            'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'title' => 'Enduroam Season Highlights',
            'sort_order' => 1,
        ]);

        Video::create([
            'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'title' => 'Mountain Explorer Tour Preview',
            'sort_order' => 2,
        ]);

        // Sponsors
        Sponsor::create([
            'name' => 'KTM',
            'logo' => 'https://placehold.co/200x100/ff6600/white?text=KTM',
            'url' => 'https://www.ktm.com',
            'sort_order' => 1,
        ]);

        Sponsor::create([
            'name' => 'Red Bull',
            'logo' => 'https://placehold.co/200x100/cc0000/white?text=Red+Bull',
            'url' => 'https://www.redbull.com',
            'sort_order' => 2,
        ]);

        Sponsor::create([
            'name' => 'Fox Racing',
            'logo' => 'https://placehold.co/200x100/000000/white?text=Fox',
            'url' => 'https://www.foxracing.com',
            'sort_order' => 3,
        ]);

        // FAQs
        Faq::create([
            'question' => 'What experience level do I need?',
            'answer' => 'We offer tours for all experience levels, from beginners to experts. Each tour listing indicates the difficulty level so you can choose the right one for you.',
            'sort_order' => 1,
        ]);

        Faq::create([
            'question' => 'What should I bring?',
            'answer' => 'Please bring appropriate riding gear including helmet, goggles, gloves, boots, and body armor. We provide water and lunch on full-day tours.',
            'sort_order' => 2,
        ]);

        Faq::create([
            'question' => 'Can I cancel my booking?',
            'answer' => 'Yes, cancellations are free up to 48 hours before the tour start time. After that, a 50% cancellation fee applies.',
            'sort_order' => 3,
        ]);

        Faq::create([
            'question' => 'Do you provide bikes?',
            'answer' => 'Yes, we can provide enduro bikes for an additional fee. Please mention this when booking so we can ensure availability.',
            'sort_order' => 4,
        ]);

        // Site Settings (contact defaults)
        $contactSettings = [
            'contact_email' => 'info@enduroam.com',
            'contact_phone' => '+382 69 123 456',
            'whatsapp_number' => '+382691234567',
            'address' => 'Montenegro',
            'social_instagram' => 'https://instagram.com/enduroam',
            'social_facebook' => 'https://facebook.com/enduroam',
            'social_youtube' => 'https://youtube.com/@enduroam',
        ];

        foreach ($contactSettings as $key => $value) {
            SiteSetting::firstOrCreate(['key' => $key], ['value' => $value]);
        }

        // Translations
        $this->call(TranslationSeeder::class);

        // Articles
        Article::create([
            'title' => 'Top 10 Enduro Trails for 2026',
            'excerpt' => 'Discover the most thrilling enduro trails to ride this season.',
            'content' => '<p>As we enter the 2026 riding season, we\'ve compiled a list of the top 10 enduro trails that every rider should experience. From technical single tracks to flowing forest paths, these trails offer something for everyone.</p><p>Whether you\'re a seasoned pro or just starting out, these trails will challenge and excite you.</p>',
            'image' => 'https://placehold.co/800x400/2d5016/white?text=Top+10+Trails',
            'date' => '2026-02-01',
            'author' => 'Enduroam Team',
            'is_published' => true,
        ]);

        Article::create([
            'title' => 'Preparing for Your First Enduro Tour',
            'excerpt' => 'Everything you need to know before your first guided enduro experience.',
            'content' => '<p>Booking your first enduro tour can be exciting and a little nerve-wracking. Here\'s everything you need to know to prepare for an unforgettable experience.</p><p>From choosing the right gear to understanding trail etiquette, we\'ve got you covered.</p>',
            'image' => 'https://placehold.co/800x400/1a3a0a/white?text=First+Tour',
            'date' => '2026-01-25',
            'author' => 'Enduroam Team',
            'is_published' => true,
        ]);
    }
}
