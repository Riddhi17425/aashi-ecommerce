<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // SEO: editable intro text and FAQ for category and sub-category pages.
    // "faq" holds a JSON list of question/answer pairs.
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->text('intro_content')->nullable()->after('summary');
            $table->text('faq')->nullable()->after('intro_content');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['intro_content', 'faq']);
        });
    }
};
