<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentClosureTable extends Migration
{
    public function up()
    {
        Schema::create('comment_closures', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            
            $table->unsignedBigInteger('ancestor');
            $table->unsignedBigInteger('comment_id');
    
            $table->foreign('ancestor')->references('id')->on('comments')->onDelete('cascade');
            $table->foreign('comment_id')->references('id')->on('comments')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('comment_closures');
    }
}
