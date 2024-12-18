<?php


use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use MoonShine\Models\MoonshineUser;

Route::get('/', [ProductController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');


Route::middleware(['auth'])->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::delete('/cart/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');
});

Route::middleware(['auth'])->group(function () {
   Route::post('/order', [OrderController::class, 'store'])->name('order.store');
   Route::get('/orders', [OrderController::class, 'history'])->name('orders.history');
});

Route::get('/admin-panel', function () {
    $user = auth()->user();

    if ($user &&$user->role === 'admin') {
        $moonshineUser = MoonshineUser::where('email', $user->email)->first();

        if ($moonshineUser) {
            auth('moonshine')->login($moonshineUser);
            return redirect(route('moonshine.index'));
        }

        return redirect('/')->with('error', 'У вас нет доступа к админ-панели');
    }

    return redirect('/')->with('error', 'Доступ запрещён');
})->middleware('auth');


require __DIR__.'/auth.php';
