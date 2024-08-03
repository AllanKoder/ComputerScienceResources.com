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
        DB::unprepared('
            CREATE TRIGGER update_vote_totals AFTER INSERT ON votes
            FOR EACH ROW
            BEGIN
                INSERT INTO vote_totals (voteable_id, voteable_type, total_votes, up_votes, down_votes)
                VALUES (NEW.voteable_id, NEW.voteable_type, NEW.vote_value, IF(NEW.vote_value > 0, NEW.vote_value, 0), IF(NEW.vote_value < 0, -NEW.vote_value, 0))
                ON DUPLICATE KEY UPDATE 
                    total_votes = total_votes + NEW.vote_value,
                    up_votes = up_votes + IF(NEW.vote_value > 0, NEW.vote_value, 0),
                    down_votes = down_votes + IF(NEW.vote_value < 0, -NEW.vote_value, 0);
            END
        ');

        // Trigger for DELETE
        DB::unprepared('
            CREATE TRIGGER update_vote_totals_after_delete AFTER DELETE ON votes
            FOR EACH ROW
            BEGIN
                UPDATE vote_totals
                SET 
                    total_votes = total_votes - OLD.vote_value,
                    up_votes = up_votes - IF(OLD.vote_value > 0, OLD.vote_value, 0),
                    down_votes = down_votes - IF(OLD.vote_value < 0, -OLD.vote_value, 0)
                WHERE voteable_id = OLD.voteable_id AND voteable_type = OLD.voteable_type;
            END
        ');

        // Trigger for UPDATE
        DB::unprepared('
            CREATE TRIGGER update_vote_totals_after_update AFTER UPDATE ON votes
            FOR EACH ROW
            BEGIN
                UPDATE vote_totals
                SET 
                    total_votes = total_votes - OLD.vote_value + NEW.vote_value,
                    up_votes = up_votes - IF(OLD.vote_value > 0, OLD.vote_value, 0) + IF(NEW.vote_value > 0, NEW.vote_value, 0),
                    down_votes = down_votes - IF(OLD.vote_value < 0, -OLD.vote_value, 0) + IF(NEW.vote_value < 0, -NEW.vote_value, 0)
                WHERE voteable_id = NEW.voteable_id AND voteable_type = NEW.voteable_type;
            END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS update_vote_totals');
        DB::unprepared('DROP TRIGGER IF EXISTS update_vote_totals_after_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS update_vote_totals_after_update');
    }
};