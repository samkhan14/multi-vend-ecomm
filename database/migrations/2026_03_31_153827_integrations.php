<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('integrations', function (Blueprint $table) {
            $table->id();
            
            // Google & SEO
            $table->text('google_console')->nullable();
            $table->text('google_analytics')->nullable();
            $table->text('google_tag_manager')->nullable();
            $table->text('sitemap_submission')->nullable();
            $table->text('robots_txt')->nullable();
            $table->text('meta_tags')->nullable();
            $table->text('schema_markup')->nullable();
            $table->text('on_page_scripts')->nullable();
            
            // Chat & Communication
            $table->text('live_chat')->nullable();
            $table->text('whatsapp_chat')->nullable();
            $table->text('messenger_chat')->nullable();
            $table->text('chatbot_scripts')->nullable();
            
            // Tracking
            $table->text('facebook_pixel')->nullable();
            $table->text('conversion_tracking')->nullable();
            $table->text('remarketing_tags')->nullable();
            
            // WhatsApp
            $table->string('country_code', 10)->default('+92');
            $table->string('phone_number', 20)->nullable();
            $table->boolean('whatsapp_on')->default(false);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integrations');
    }
};