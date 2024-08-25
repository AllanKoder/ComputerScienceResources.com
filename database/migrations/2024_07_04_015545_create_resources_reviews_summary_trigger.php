<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Trigger for inserting a new review
        DB::unprepared('
            CREATE TRIGGER after_review_insert
            AFTER INSERT ON resource_reviews
            FOR EACH ROW
            BEGIN
                IF (SELECT COUNT(*) FROM resources WHERE id = NEW.resource_id) = 0 THEN
                    INSERT INTO resources (
                        id, 
                        community_size_total, 
                        teaching_explanation_clarity_total, 
                        technical_depth_total, 
                        practicality_to_industry_total, 
                        user_friendliness_total, 
                        updates_and_maintenance_total, 
                        total_reviews
                    ) VALUES (
                        NEW.resource_id, 
                        NEW.community_size, 
                        NEW.teaching_explanation_clarity, 
                        NEW.technical_depth, 
                        NEW.practicality_to_industry, 
                        NEW.user_friendliness, 
                        NEW.updates_and_maintenance, 
                        1
                    );
                ELSE
                    UPDATE resources
                    SET 
                        community_size_total = community_size_total + NEW.community_size,
                        teaching_explanation_clarity_total = teaching_explanation_clarity_total + NEW.teaching_explanation_clarity,
                        technical_depth_total = technical_depth_total + NEW.technical_depth,
                        practicality_to_industry_total = practicality_to_industry_total + NEW.practicality_to_industry,
                        user_friendliness_total = user_friendliness_total + NEW.user_friendliness,
                        updates_and_maintenance_total = updates_and_maintenance_total + NEW.updates_and_maintenance,
                        total_reviews = total_reviews + 1
                    WHERE id = NEW.resource_id;
                END IF;
            END
        ');

        // Trigger for updating a review
        DB::unprepared('
            CREATE TRIGGER after_review_update
            AFTER UPDATE ON resource_reviews
            FOR EACH ROW
            BEGIN
                UPDATE resources
                SET 
                    community_size_total = community_size_total - OLD.community_size + NEW.community_size,
                    teaching_explanation_clarity_total = teaching_explanation_clarity_total - OLD.teaching_explanation_clarity + NEW.teaching_explanation_clarity,
                    technical_depth_total = technical_depth_total - OLD.technical_depth + NEW.technical_depth,
                    practicality_to_industry_total = practicality_to_industry_total - OLD.practicality_to_industry + NEW.practicality_to_industry,
                    user_friendliness_total = user_friendliness_total - OLD.user_friendliness + NEW.user_friendliness,
                    updates_and_maintenance_total = updates_and_maintenance_total - OLD.updates_and_maintenance + NEW.updates_and_maintenance
                WHERE id = NEW.resource_id;
            END
        ');

        // Trigger for deleting a review
        DB::unprepared('
            CREATE TRIGGER after_review_delete
            AFTER DELETE ON resource_reviews
            FOR EACH ROW
            BEGIN
                UPDATE resources
                SET 
                    community_size_total = community_size_total - OLD.community_size,
                    teaching_explanation_clarity_total = teaching_explanation_clarity_total - OLD.teaching_explanation_clarity,
                    technical_depth_total = technical_depth_total - OLD.technical_depth,
                    practicality_to_industry_total = practicality_to_industry_total - OLD.practicality_to_industry,
                    user_friendliness_total = user_friendliness_total - OLD.user_friendliness,
                    updates_and_maintenance_total = updates_and_maintenance_total - OLD.updates_and_maintenance,
                    total_reviews = total_reviews - 1
                WHERE id = OLD.resource_id;
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
        // Drop the triggers
        DB::unprepared('DROP TRIGGER IF EXISTS after_review_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS after_review_update');
        DB::unprepared('DROP TRIGGER IF EXISTS after_review_delete');
    }
};
