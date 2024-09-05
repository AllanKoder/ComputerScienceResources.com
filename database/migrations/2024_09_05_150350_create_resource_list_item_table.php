<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resource_list_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('resource_list_id')->index();
            $table->unsignedBigInteger('resource_id')->index();
            $table->text('description')->default('');

            $table->foreign('resource_list_id')->references('id')->on('resource_lists')->onDelete('cascade');
            $table->foreign('resource_id')->references('id')->on('resources')->onDelete('cascade');
            
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
        Schema::dropIfExists('resource_list_items');
    }
};
