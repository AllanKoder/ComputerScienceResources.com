<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Define the mapping from full class names to morph aliases
        $morphMap = [
            'App\\Models\\ComputerScienceResource' => 'resource',
            'App\\Models\\ResourceReview' => 'review',
            'App\\Models\\Comment' => 'comment',
            'App\\Models\\ResourceEdits' => 'edit',
            'App\\Models\\User' => 'user',
        ];

        // Update comments table
        foreach ($morphMap as $className => $alias) {
            DB::table('comments')
                ->where('commentable_type', $className)
                ->update(['commentable_type' => $alias]);
        }

        // Update comments_counts table
        foreach ($morphMap as $className => $alias) {
            DB::table('comments_counts')
                ->where('commentable_type', $className)
                ->update(['commentable_type' => $alias]);
        }

        // Update upvotes table
        foreach ($morphMap as $className => $alias) {
            DB::table('upvotes')
                ->where('upvotable_type', $className)
                ->update(['upvotable_type' => $alias]);
        }

        // Update upvote_summaries table
        foreach ($morphMap as $className => $alias) {
            DB::table('upvote_summaries')
                ->where('upvotable_type', $className)
                ->update(['upvotable_type' => $alias]);
        }

        // Update notifications table
        foreach ($morphMap as $className => $alias) {
            DB::table('notifications')
                ->where('notifiable_type', $className)
                ->update(['notifiable_type' => $alias]);
        }

        // Update personal_access_tokens table
        foreach ($morphMap as $className => $alias) {
            DB::table('personal_access_tokens')
                ->where('tokenable_type', $className)
                ->update(['tokenable_type' => $alias]);
        }

        // Update activity_log table (subject and causer)
        foreach ($morphMap as $className => $alias) {
            DB::table('activity_log')
                ->where('subject_type', $className)
                ->update(['subject_type' => $alias]);

            DB::table('activity_log')
                ->where('causer_type', $className)
                ->update(['causer_type' => $alias]);
        }

        // Update taggables table
        foreach ($morphMap as $className => $alias) {
            DB::table('taggables')
                ->where('taggable_type', $className)
                ->update(['taggable_type' => $alias]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Define the reverse mapping from morph aliases to full class names
        $reverseMorphMap = [
            'resource' => 'App\\Models\\ComputerScienceResource',
            'review' => 'App\\Models\\ResourceReview',
            'comment' => 'App\\Models\\Comment',
            'edit' => 'App\\Models\\ResourceEdits',
            'user' => 'App\\Models\\User',
        ];

        // Reverse update comments table
        foreach ($reverseMorphMap as $alias => $className) {
            DB::table('comments')
                ->where('commentable_type', $alias)
                ->update(['commentable_type' => $className]);
        }

        // Reverse update comments_counts table
        foreach ($reverseMorphMap as $alias => $className) {
            DB::table('comments_counts')
                ->where('commentable_type', $alias)
                ->update(['commentable_type' => $className]);
        }

        // Reverse update upvotes table
        foreach ($reverseMorphMap as $alias => $className) {
            DB::table('upvotes')
                ->where('upvotable_type', $alias)
                ->update(['upvotable_type' => $className]);
        }

        // Reverse update upvote_summaries table
        foreach ($reverseMorphMap as $alias => $className) {
            DB::table('upvote_summaries')
                ->where('upvotable_type', $alias)
                ->update(['upvotable_type' => $className]);
        }

        // Reverse update notifications table
        foreach ($reverseMorphMap as $alias => $className) {
            DB::table('notifications')
                ->where('notifiable_type', $alias)
                ->update(['notifiable_type' => $className]);
        }

        // Reverse update personal_access_tokens table
        foreach ($reverseMorphMap as $alias => $className) {
            DB::table('personal_access_tokens')
                ->where('tokenable_type', $alias)
                ->update(['tokenable_type' => $className]);
        }

        // Reverse update activity_log table (subject and causer)
        foreach ($reverseMorphMap as $alias => $className) {
            DB::table('activity_log')
                ->where('subject_type', $alias)
                ->update(['subject_type' => $className]);

            DB::table('activity_log')
                ->where('causer_type', $alias)
                ->update(['causer_type' => $className]);
        }

        // Reverse update taggables table
        foreach ($reverseMorphMap as $alias => $className) {
            DB::table('taggables')
                ->where('taggable_type', $alias)
                ->update(['taggable_type' => $className]);
        }
    }
};
