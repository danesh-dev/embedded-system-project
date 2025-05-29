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
       Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->decimal('lat', 10, 8);
            $table->decimal('long', 11, 8);
            $table->timestamp('date');
            $table->string('deviceId');
            $table->timestamps();

            $table->index(['deviceId', 'date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('locations');
    }
};
