<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('comment_hierarchies', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            
            $table->unsignedBigInteger('ancestor')->nullable(false);
            $table->unsignedBigInteger('comment_id')->nullable(false);
            $table->smallInteger('depth')->default(0)->nullable(false);
    
            $table->foreign('ancestor')->references('id')->on('comments')->onDelete('cascade');
            $table->foreign('comment_id')->references('id')->on('comments')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('comment_hierarchies');
    }
};