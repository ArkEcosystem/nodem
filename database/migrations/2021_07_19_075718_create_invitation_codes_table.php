<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class CreateInvitationCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invitation_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('issuer_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('username')->unique();
            $table->string('code')->unique();
            $table->string('role');
            $table->timestamp('redeemed_at')->nullable();
            $table->timestamps();
        });
    }
}
