<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameRealDepthInAllTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->renameColumn('real_depth', 'depth');
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->renameColumn('real_depth', 'depth');
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->renameColumn('real_depth', 'depth');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->renameColumn('depth', 'real_depth');
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->renameColumn('depth', 'real_depth');
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->renameColumn('depth', 'real_depth');
        });
    }
}
