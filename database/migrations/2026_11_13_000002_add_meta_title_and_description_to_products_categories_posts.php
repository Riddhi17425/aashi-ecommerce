<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // SEO: editable meta title and description. Empty = the website uses its automatic text.
    public function up(): void
    {
        foreach (['products', 'categories', 'posts'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->string('meta_title')->nullable();
                $table->string('meta_description', 500)->nullable();
            });
        }
    }

    public function down(): void
    {
        foreach (['products', 'categories', 'posts'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn(['meta_title', 'meta_description']);
            });
        }
    }
};
