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
        Schema::create('companies', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->string('password');
        $table->string('cnpj')->unique();
        $table->string('cnae');
        $table->longText('logo')->nullable();
        $table->string('primary_color')->nullable();
        $table->string('secondary_color')->nullable();
        $table->string('text_color')->nullable();
        $table->string('button_color')->nullable();
        $table->longText('banner')->nullable();
        $table->string('font')->nullable();
        $table->rememberToken();


        $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
