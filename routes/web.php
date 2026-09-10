<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

// Catalogue
Route::get('/catalogue', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/promotions', [CatalogController::class, 'promotions'])->name('catalog.promotions');
Route::get('/categorie/{category:slug}', [CatalogController::class, 'category'])->name('catalog.category');
Route::get('/collections', [CollectionController::class, 'index'])->name('collections.index');
Route::get('/collections/{collection:slug}', [CollectionController::class, 'show'])->name('collections.show');
Route::get('/produit/{product:slug}', [ProductController::class, 'show'])->name('products.show');
Route::post('/produit/{product}/avis', [ReviewController::class, 'store'])
    ->middleware('auth')
    ->name('reviews.store');

// Contenu / blog
Route::get('/blog', [PageController::class, 'blogIndex'])->name('blog.index');
Route::get('/blog/{page:slug}', [PageController::class, 'blogShow'])->name('blog.show');
Route::get('/p/{page:slug}', [PageController::class, 'show'])->name('pages.show');

// Contact & newsletter
Route::get('/contact', [ContactController::class, 'create'])->name('contact.create');
Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contact.store');
Route::post('/newsletter', [NewsletterController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('newsletter.store');

// Panier
Route::prefix('panier')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/ajouter', [CartController::class, 'store'])
        ->middleware('throttle:30,1')
        ->name('store');
    Route::patch('/{item}', [CartController::class, 'update'])->name('update');
    Route::delete('/{item}', [CartController::class, 'destroy'])->name('destroy');
    Route::post('/coupon', [CartController::class, 'applyCoupon'])
        ->middleware('throttle:10,1')
        ->name('coupon.apply');
    Route::delete('/coupon', [CartController::class, 'removeCoupon'])->name('coupon.remove');
});

// Favoris
Route::middleware('auth')->group(function () {
    Route::get('/favoris', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/produit/{product}/favoris', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
});

// Checkout
Route::prefix('commande')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/', [CheckoutController::class, 'storeInformation'])->name('information.store');
    Route::get('/paiement', [CheckoutController::class, 'payment'])->name('payment');
    Route::post('/paiement', [CheckoutController::class, 'placeOrder'])
        ->middleware('throttle:10,1')
        ->name('payment.store');
    Route::get('/confirmation/{order:order_number}', [CheckoutController::class, 'confirmation'])
        ->name('confirmation');
});

// SEO
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

// Compte client
Route::middleware(['auth', 'verified'])->prefix('compte')->name('account.')->group(function () {
    Route::get('/', [AccountController::class, 'index'])->name('index');
    Route::get('/commandes', [AccountController::class, 'orders'])->name('orders');
    Route::get('/commandes/{order:order_number}', [AccountController::class, 'orderShow'])->name('orders.show');

    Route::resource('adresses', AddressController::class)
        ->parameters(['adresses' => 'address'])
        ->except(['show']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
