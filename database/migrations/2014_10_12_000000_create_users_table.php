<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('username')->unique();
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->string('timezone')->default('UTC')->nullable();
            $table->timestamp('seen_notifications_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->schemalessAttributes('extra_attributes');
            $table->timestamps();
        });
    }
}
