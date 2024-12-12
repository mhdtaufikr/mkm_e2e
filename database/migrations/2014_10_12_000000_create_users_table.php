<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Full name of the user
            $table->string('username')->unique(); // Username field, unique for each user
            $table->string('email')->unique(); // Email address
            $table->timestamp('email_verified_at')->nullable(); // Email verification timestamp
            $table->string('password'); // Password field
            $table->rememberToken(); // Token for "remember me" functionality
            $table->string('role')->nullable(); // User role (e.g., supplier, admin, etc.)
            $table->dateTime('last_login')->nullable(); // Timestamp of the last login
            $table->integer('login_counter')->nullable(); // Tracks the number of logins
            $table->boolean('is_active')->default(true); // Indicates if the user is active
            $table->timestamps(); // Created at and updated at timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
