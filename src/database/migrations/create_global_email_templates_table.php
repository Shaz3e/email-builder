<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Shaz3e\EmailBuilder\App\Models\GlobalEmailTemplate;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('global_email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('header_image')->nullable();
            $table->longText('header_text')->nullable();
            $table->string('header_text_color')->nullable();
            $table->string('header_background_color')->nullable();
            $table->string('footer_image')->nullable();
            $table->longText('footer_text')->nullable();
            $table->string('footer_text_color')->nullable();
            $table->string('footer_background_color')->nullable();
            $table->string('footer_bottom_image')->nullable();
            $table->timestamps();
        });

        $globalEmailTempaltes = [
            'header_image' => null,
            'header_text' => null,
            'header_text_color' => null,
            'header_background_color' => null,
            'footer_image' => null,
            'footer_text' => null,
            'footer_text_color' => null,
            'footer_background_color' => null,
            'footer_bottom_image' => null,
        ];

        GlobalEmailTemplate::create($globalEmailTempaltes);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_email_templates');
    }
};
