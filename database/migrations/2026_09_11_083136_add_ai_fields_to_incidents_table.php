<?php

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
        Schema::table('incidents', function (Blueprint $table) {
            $table->decimal('ai_category_confidence', 5, 4)
                ->nullable()
                ->after('ai_suggested_category');

            $table->decimal('ai_priority_confidence', 5, 4)
                ->nullable()
                ->after('ai_category_confidence');

            $table->text('ai_priority_reason')
                ->nullable()
                ->after('ai_priority_confidence');

            $table->text('ai_suggested_action')
                ->nullable()
                ->after('ai_priority_reason');

            $table->string('ai_source')
                ->nullable()
                ->after('ai_suggested_action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropColumn([
                'ai_category_confidence',
                'ai_priority_confidence',
                'ai_priority_reason',
                'ai_suggested_action',
                'ai_source',
            ]);
        });
    }
};