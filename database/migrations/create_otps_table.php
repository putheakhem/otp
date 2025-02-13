<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('otps', function (Blueprint $table) {
            $table->id();
            $table->string('identifier');
            $table->string('otp');
            $table->boolean('used')->default(false);
            $table->integer('attempts')->default(0);
            $table->string('session_id')->nullable();
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('otps');
    }
};
