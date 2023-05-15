<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class CreateServersTable extends Migration
{
    public function up()
    {
        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascade('onDelete');
            // General
            $table->string('provider');
            $table->string('name');
            $table->string('host');
            $table->string('process_type');
            // Performance
            $table->unsignedInteger('ping')->nullable();
            $table->unsignedBigInteger('height')->nullable();
            // Hardware
            $table->unsignedBigInteger('cpu_total')->nullable();
            $table->unsignedDecimal('cpu_used')->nullable();
            $table->unsignedDecimal('cpu_available')->nullable();
            $table->unsignedBigInteger('ram_total')->nullable();
            $table->unsignedBigInteger('ram_used')->nullable();
            $table->unsignedBigInteger('ram_available')->nullable();
            $table->unsignedBigInteger('disk_total')->nullable();
            $table->unsignedBigInteger('disk_used')->nullable();
            $table->unsignedBigInteger('disk_available')->nullable();
            // Core
            $table->string('core_version_current')->nullable();
            $table->string('core_version_latest')->nullable();
            // Authentication
            $table->text('auth_username')->nullable();
            $table->text('auth_password')->nullable();
            $table->text('auth_access_key')->nullable();
            $table->boolean('uses_bip38_encryption')->default(false);

            $table->schemalessAttributes('extra_attributes');
            $table->timestamps();
        });
    }
}
