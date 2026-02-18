<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Admin;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Public as PublicApi;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function (): void {

    // Auth
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class, 'register']);

    // Tours
    Route::get('/tours', [PublicApi\TourController::class, 'index'])->name('public.tours.index');
    Route::get('/tours/{tour}', [PublicApi\TourController::class, 'show'])->name('public.tours.show');

    // Events
    Route::get('/events', [PublicApi\EventController::class, 'index'])->name('public.events.index');
    Route::get('/events/featured', [PublicApi\EventController::class, 'featured'])->name('public.events.featured');
    Route::get('/events/{event}', [PublicApi\EventController::class, 'show'])->name('public.events.show');

    // Articles
    Route::get('/articles', [PublicApi\ArticleController::class, 'index'])->name('public.articles.index');
    Route::get('/articles/{article}', [PublicApi\ArticleController::class, 'show'])->name('public.articles.show');
    Route::get('/articles/{article}/related', [PublicApi\ArticleController::class, 'related'])->name('public.articles.related');

    // Banners
    Route::get('/banners', [PublicApi\BannerController::class, 'index'])->name('public.banners.index');

    // Gallery
    Route::get('/gallery', [PublicApi\GalleryController::class, 'index'])->name('public.gallery.index');

    // Videos
    Route::get('/videos', [PublicApi\VideoController::class, 'index'])->name('public.videos.index');
    Route::get('/videos/featured', [PublicApi\VideoController::class, 'featured'])->name('public.videos.featured');

    // Sponsors
    Route::get('/sponsors', [PublicApi\SponsorController::class, 'index'])->name('public.sponsors.index');

    // FAQs
    Route::get('/faqs', [PublicApi\FaqController::class, 'index'])->name('public.faqs.index');

    // Pages
    Route::get('/pages', [PublicApi\PageController::class, 'index'])->name('public.pages.index');

    // Contact
    Route::post('/contact', [PublicApi\ContactController::class, 'store'])->name('public.contact.store');

    // Newsletter
    Route::post('/newsletter/subscribe', [PublicApi\NewsletterController::class, 'subscribe'])->name('public.newsletter.subscribe');

    // Booking
    Route::post('/bookings', [PublicApi\BookingController::class, 'store'])->name('public.bookings.store');

    // Reviews (public submission)
    Route::post('/reviews', [PublicApi\ReviewController::class, 'store'])->name('public.reviews.store');

    // Settings (public)
    Route::get('/settings/contact', [PublicApi\SettingController::class, 'contact'])->name('public.settings.contact');

    /*
    |--------------------------------------------------------------------------
    | Admin API Routes (Auth Required)
    |--------------------------------------------------------------------------
    */

    Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->name('admin.')->group(function (): void {

        // Auth
        Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::get('/auth/me', [AuthController::class, 'me'])->name('auth.me');

        // Dashboard
        Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

        // Tours CRUD
        Route::apiResource('tours', Admin\TourController::class)->names('tours');
        Route::post('/tours/{tour}/translate', [Admin\TourController::class, 'translate'])->name('tours.translate');
        Route::post('/tours/translate-all', [Admin\TourController::class, 'translateAll'])->name('tours.translateAll');

        // Events CRUD
        Route::apiResource('events', Admin\EventController::class)->names('events');
        Route::post('/events/{event}/set-featured', [Admin\EventController::class, 'setFeatured'])->name('events.setFeatured');
        Route::post('/events/{event}/translate', [Admin\EventController::class, 'translate'])->name('events.translate');
        Route::post('/events/translate-all', [Admin\EventController::class, 'translateAll'])->name('events.translateAll');

        // Articles CRUD
        Route::apiResource('articles', Admin\ArticleController::class)->names('articles');

        // Banners CRUD
        Route::apiResource('banners', Admin\BannerController::class)->names('banners');

        // Gallery CRUD
        Route::apiResource('gallery', Admin\GalleryController::class)
            ->parameters(['gallery' => 'galleryImage'])
            ->names('gallery');

        // Videos CRUD
        Route::apiResource('videos', Admin\VideoController::class)->names('videos');
        Route::post('/videos/{video}/set-featured', [Admin\VideoController::class, 'setFeatured'])->name('videos.setFeatured');

        // Sponsors CRUD
        Route::apiResource('sponsors', Admin\SponsorController::class)->names('sponsors');

        // FAQs CRUD
        Route::apiResource('faqs', Admin\FaqController::class)->names('faqs');

        // Pages (list + toggle visibility)
        Route::get('/pages', [Admin\PageController::class, 'index'])->name('pages.index');
        Route::put('/pages/{page}', [Admin\PageController::class, 'update'])->name('pages.update');

        // Bookings
        Route::get('/bookings', [Admin\BookingController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/{booking}', [Admin\BookingController::class, 'show'])->name('bookings.show');
        Route::patch('/bookings/{booking}/status', [Admin\BookingController::class, 'updateStatus'])->name('bookings.updateStatus');
        Route::delete('/bookings/{booking}', [Admin\BookingController::class, 'destroy'])->name('bookings.destroy');

        // Reviews
        Route::get('/reviews', [Admin\ReviewController::class, 'index'])->name('reviews.index');
        Route::get('/reviews/{review}', [Admin\ReviewController::class, 'show'])->name('reviews.show');
        Route::patch('/reviews/{review}/approve', [Admin\ReviewController::class, 'approve'])->name('reviews.approve');
        Route::patch('/reviews/{review}/reject', [Admin\ReviewController::class, 'reject'])->name('reviews.reject');
        Route::delete('/reviews/{review}', [Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');

        // Contact Messages
        Route::get('/contact-messages', [Admin\ContactMessageController::class, 'index'])->name('contactMessages.index');
        Route::get('/contact-messages/{contactMessage}', [Admin\ContactMessageController::class, 'show'])->name('contactMessages.show');
        Route::delete('/contact-messages/{contactMessage}', [Admin\ContactMessageController::class, 'destroy'])->name('contactMessages.destroy');

        // Newsletter Subscribers
        Route::get('/newsletter-subscribers', [Admin\NewsletterController::class, 'index'])->name('newsletter.index');
        Route::delete('/newsletter-subscribers/{subscriber}', [Admin\NewsletterController::class, 'destroy'])->name('newsletter.destroy');

        // Translations
        Route::get('/translations', [Admin\TranslationController::class, 'index'])->name('translations.index');
        Route::get('/translations/groups', [Admin\TranslationController::class, 'groups'])->name('translations.groups');
        Route::put('/translations/bulk', [Admin\TranslationController::class, 'bulkUpdate'])->name('translations.bulkUpdate');
        Route::post('/translations/publish', [Admin\TranslationController::class, 'publishAll'])->name('translations.publishAll');

        // File Upload (Wasabi S3)
        Route::post('/upload', [Admin\UploadController::class, 'store'])->name('upload.store');
        Route::delete('/upload', [Admin\UploadController::class, 'destroy'])->name('upload.destroy');

        // Site Settings
        Route::get('/settings', [Admin\SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [Admin\SettingController::class, 'update'])->name('settings.update');

        // Notifications
        Route::get('/notifications', [Admin\NotificationController::class, 'index'])->name('notifications.index');
        Route::delete('/notifications/{notification}', [Admin\NotificationController::class, 'destroy'])->name('notifications.destroy');
        Route::delete('/notifications', [Admin\NotificationController::class, 'destroyAll'])->name('notifications.destroyAll');
    });
});
