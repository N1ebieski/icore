<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

// phpcs:ignore
class CreateBansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bans_models', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('model_type')->index();
            $table->bigInteger('model_id')->unsigned();
            $table->timestamps();
        });

        Schema::create('bans_values', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type')->index();
            $table->string('value');
            $table->timestamps();
        });

        // Full Text Index
        DB::statement('ALTER TABLE bans_values ADD FULLTEXT fulltext_index (value)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bans_models');
        Schema::dropIfExists('bans_values');
    }
}
