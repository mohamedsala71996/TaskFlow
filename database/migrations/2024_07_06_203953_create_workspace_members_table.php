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
        Schema::create('workspace_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade'); // Cascade delete when the user is deleted

            $table->foreignId('workspace_id')
                  ->constrained('workspaces')
                  ->onDelete('cascade'); // Cascade delete when the workspace is deleted
            // $table->timestamp('added_at');
            // $table->timestamp('removed_at')->nullable();
            $table->unique(['user_id','workspace_id']);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workspace_members');
    }
};
