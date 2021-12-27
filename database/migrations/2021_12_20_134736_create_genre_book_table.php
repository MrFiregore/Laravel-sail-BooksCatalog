<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGenreBookTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('genre_book', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("genre_id");
            $table->unsignedBigInteger("book_id");


            $table->foreign("genre_id")->references("id")->on('genre')->onDelete("cascade");
            $table->foreign("book_id")->references("id")->on('book')->onDelete("cascade");

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('genre_book');
    }
}
