<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class CreateResourceIndicatorsTable extends Migration
{
    public function up()
    {
        Schema::create('resource_indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id')->constrained()->cascadeOnDelete();
            $table->unsignedDecimal('cpu');
            $table->unsignedBigInteger('ram');
            $table->unsignedBigInteger('disk');
            $table->timestamps();
        });
    }
}
