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
        Schema::create('resource_edits', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('resource_id')->constrained('resources')->onDelete('cascade')->index();
            $table->string('edit_title');
            $table->text('edit_description');
            $table->unsignedBigInteger('user_id')->index();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('resource_edits');
    }
};
