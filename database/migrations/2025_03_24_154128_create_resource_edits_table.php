<?php

use App\Models\ComputerScienceResource;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('resource_edits', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->timestamps();

            // The resource we are editting
            $table->foreignIdFor(ComputerScienceResource::class)->constrained()->cascadeOnDelete();
            // The user who created the edit
            $table->foreignIdFor(User::class);

            // Reasoning behind the edit
            $table->string('edit_title');
            $table->text('edit_description');

            $table->json('proposed_changes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_edits');
    }
};
