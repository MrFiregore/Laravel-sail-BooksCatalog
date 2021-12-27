<?php

    use App\Http\Controllers\AuthorController;
    use App\Http\Controllers\BookController;
    use App\Http\Controllers\GenreController;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(
    [
        'middleware' => ['api'],
    ],
    function () {
        Route::prefix('book')->group(function () {
            Route::get('', [BookController::class, "all"])->name('books');
            Route::post('', [BookController::class, "create"]);
            Route::get('/{book}', [BookController::class, "single"])->name('book');
            Route::post('/{book}', [BookController::class, "update"]);
            Route::delete('/{book}', [BookController::class, "delete"]);
        });

        Route::prefix('author')->group(function () {
            Route::get('', [AuthorController::class, "all"]);
            Route::post('', [AuthorController::class, "create"]);
            Route::get('/{author}', [AuthorController::class, "single"]);

        });

        Route::prefix('genre')->group(function () {
            Route::get('', [GenreController::class, "all"]);
            Route::get('/{genre}', [GenreController::class, "single"]);

        });

    }
);
