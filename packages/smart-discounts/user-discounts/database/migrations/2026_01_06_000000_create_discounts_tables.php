<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->enum('type', ['percentage', 'fixed']);
            $table->decimal('value', 10, 2);
            $table->integer('usage_limit_per_user')->default(1);
            $table->boolean('active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('user_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id'); // Assumes generic user_id
            $table->foreignId('discount_id')->constrained()->onDelete('cascade');
            $table->integer('usage_count')->default(0);
            $table->boolean('is_revoked')->default(false);
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamps();

            $table->unique(['user_id', 'discount_id']);
        });

        Schema::create('discount_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('discount_id')->constrained()->onDelete('cascade');
            $table->string('event'); // assigned, revoked, applied
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('discount_audits');
        Schema::dropIfExists('user_discounts');
        Schema::dropIfExists('discounts');
    }
};
