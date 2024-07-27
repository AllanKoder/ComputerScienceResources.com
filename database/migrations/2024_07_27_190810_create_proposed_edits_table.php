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
        Schema::create('proposed_edits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_edit_id')->constrained('resource_edits')->onDelete('cascade');
            $table->string('field_name');
            $table->text('new_value');
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
        Schema::dropIfExists('proposed_edits');
    }
};
