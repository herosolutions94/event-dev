<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\UserController;
use App\Http\Controllers\Frontend\PagesController;
use App\Http\Components\Services\CountryService;
use App\Http\Controllers\Frontend\TournamentController;
use App\Http\Controllers\Frontend\ProfileController;
use App\Http\Controllers\Frontend\BookingController;
use App\Http\Controllers\Frontend\TeamController;
use App\Http\Controllers\Frontend\TransactionController;
use App\Http\Controllers\Frontend\WishlistController;
use App\Http\Controllers\Frontend\PaymentController;
use App\Http\Controllers\Frontend\NotificationController;
use App\Http\Controllers\Frontend\SettingController;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::get('/run-migration', function () {
    Artisan::call('migrate');
    return 'Migration completed successfully.';
});
Route::post('/upload-image', [App\Http\Controllers\ProfileController::class, 'upload_image']);
Route::get('/get-site-settings', [SettingController::class, 'getAll']);
Route::get('/get-notifications', [NotificationController::class, 'getAll']);

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/verify-code', [UserController::class, 'verifyCode']);
Route::get('/countries', [CountryService::class, 'getAll']);
Route::get('/states/{country_id}', [PagesController::class, 'get_country_states']);

Route::post('/save-user-buy-credits', [UserController::class, 'saveUserBuyCredits']);

// wishlist
Route::post('/remove-from-wishlist', [WishlistController::class, 'removeFromWishlist']);

// transaction
Route::get('/transactions/{userId}', [TransactionController::class, 'getAll']);

// payment
Route::post('/create-indent-payment', [PaymentController::class, 'createStripeIntent']);
// team

// Route::group(['middleware' => ['auth:sanctum']], function () {
    
    Route::post('/wishlist', [WishlistController::class, 'getAll']);
    Route::post('/teamsByUser', [TeamController::class, 'teamsByUser']);
    Route::post('/add-to-wishlist', [WishlistController::class, 'addToWishlist']);
    Route::post('/tournaments-create', [TournamentController::class, 'create'])->name('tournaments.create');
    Route::post('/tournaments-update/{id}', [TournamentController::class, 'update_tournament'])->name('tournaments.update_tournament');
    Route::post('/tournamentsByUser', [TournamentController::class, 'tournamentsByUser']);
    Route::post('/create-team', [TeamController::class, 'create']);
// });
Route::get('/teams', [TeamController::class, 'getAll']);
Route::post('/accept_team/{id}', [TeamController::class, 'accept_team']);
Route::post('/start-tournament/{id}', [TournamentController::class, 'start_tournament']);
Route::post('/save-match-score/{id}', [TournamentController::class, 'save_match_score']);
Route::post('/update-match-score/{id}', [TournamentController::class, 'update_match_score']);
Route::post('/start-next-round/{id}', [TournamentController::class, 'start_next_round']);


Route::delete('/teams/{id}', [TeamController::class, 'delete']);
// category
Route::post('/get-categories', [TournamentController::class, 'getCategories']);

// booking
Route::get('/bookings', [BookingController::class, 'getAll']);
Route::post('/create-booking', [BookingController::class, 'bookingCreate']);
Route::post('/reset-password', [BookingController::class, 'reset_password']);

// public profile details
Route::get('/public-profile/{id}', [ProfileController::class, 'public_profile']);
// tournament details
Route::get('/tournament-details/{id}', [TournamentController::class, 'tournamentDetail']);
Route::get('/tournament-round-details/{id}/{round_id}', [TournamentController::class, 'tournamentRoundDetail']);
// apply middleware to check if user is logged in



// profile
Route::post('/get-user-profile', [ProfileController::class, 'getUserProfile']);
Route::post('/update-user-profile', [ProfileController::class, 'updateUserProfile']);
Route::post('/upload-image', [ProfileController::class, 'upload_image']);
Route::post('/upload-cover', [ProfileController::class, 'upload_cover']);
// about us 
Route::get('/privacy-policy', [PagesController::class, 'privacyPolicy']);
Route::get('/home', [PagesController::class, 'home']);

// terms and conditions
Route::get('/terms-and-conditions', [PagesController::class, 'termsAndConditions']);
Route::get('/disclaimer', [PagesController::class, 'disclaimer']);
Route::get('/about-us', [PagesController::class, 'aboutUs']);
Route::get('/contact-us', [PagesController::class, 'contactUs']);
Route::get('/faq', [PagesController::class, 'faq']);

// post the contact us form to this route.
Route::post('/contact-us', [PagesController::class, 'saveContactUs']);

// tournaments
Route::get('/tournament-details', [TournamentController::class, 'getDetails']);
Route::get('/tournaments', [TournamentController::class, 'getAll']);
Route::get('/tournaments/{id}', [TournamentController::class, 'getById']);

Route::post('/upload', [TournamentController::class, 'upload']);
Route::put('/tournaments/{id}', [TournamentController::class, 'update']);
Route::put('/update-tournament-payment-status/{id}', [TournamentController::class, 'updatePaymentStatus']);
Route::delete('/tournaments/{id}', [TournamentController::class, 'delete']);

// create-indent-payment
Route::post('/create-indent-payment', [TournamentController::class, 'create_stripe_intent']);