<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class CreateProcessesTable extends Migration
{
    public function up()
    {
        Schema::create('processes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('name');
            $table->unsignedInteger('pid');
            $table->decimal('cpu');
            $table->decimal('ram');
            $table->string('status');
            $table->timestamps();

            $table->unique(['server_id', 'type']);
        });
    }
}
