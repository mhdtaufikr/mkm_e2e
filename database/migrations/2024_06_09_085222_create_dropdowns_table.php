<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDropdownsTable extends Migration
{
    public function up()
    {
        Schema::create('dropdowns', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->string('name_value');
            $table->string('code_format');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dropdowns');
    }
}

