<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE votes ADD CONSTRAINT check_vote_value CHECK (vote_value IN (-1, 1))');
    }

    public function down()
    {
        DB::statement('ALTER TABLE votes DROP CONSTRAINT check_vote_value');
    }
};
