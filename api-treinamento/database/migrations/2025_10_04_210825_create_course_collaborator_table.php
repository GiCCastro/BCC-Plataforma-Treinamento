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
       Schema::create('course_collaborator', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('collaborator_id')->constrained()->onDelete('cascade');

            $table->decimal('progress', 5, 2)->default(0);
            $table->boolean('completed')->default(false); 
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->unique(['course_id', 'collaborator_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_collaborator');
    }
};
