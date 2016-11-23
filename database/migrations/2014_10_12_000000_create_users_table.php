<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('employee_id')->index();
            $table->unsignedBigInteger('inviter_id')->index();
            $table->string('real_name')->nullable();
            $table->string('email')->unique();
            $table->string('tel')->unique();
            $table->string('avatar_url')->nullable();
            $table->unsignedInteger('invite_count')->default(0);
            $table->enum('apply_status', ['applying', 'reject', 'approve'])->default('applying');
            $table->boolean('is_admin')->default(false);
            $table->boolean('info_complete')->default(false);
            $table->enum('type', ['person', 'bot'])->default('person');
            $table->timestamp('first_login_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('verify_code', 16)->nullable();
            $table->timestamp('verify_code_expire_at')->nullable();
            $table->timestamp('verify_code_refresh_at')->nullable();
            $table->unsignedInteger('verify_code_retry_times')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
