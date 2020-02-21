<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->string('last_name')->nullable();
            $table->string('alias')->nullable();
            $table->string('phone')->nullable();
            $table->string('profession')->nullable();
            $table->string('address')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropColumn('last_name');
            $table->dropColumn('alias');
            $table->dropColumn('phone');
            $table->dropColumn('profession');
            $table->dropColumn('address');
        });
    }
}
