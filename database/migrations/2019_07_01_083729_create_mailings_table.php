<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateMailingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mailings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->longText('content_html');
            $table->longText('content');
            $table->integer('status')->unsigned();
            $table->timestamp('activation_at')->nullable();
            $table->timestamps();
        });

        Schema::create('mailings_emails', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('mailing_id')->index();
            $table->string('model_type')->nullable();
            $table->integer('model_id')->nullable();
            $table->string('email');
            $table->integer('sent')->unsigned();
            $table->timestamps();

            $table->foreign('mailing_id')
                ->references('id')
                ->on('mailings')
                ->onDelete('cascade');

            $table->unique(['mailing_id', 'email']);
        });

        // Full Text Index
        DB::statement('ALTER TABLE mailings ADD FULLTEXT fulltext_index (title, content)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mailings');
        Schema::dropIfExists('mailings_emails');
    }
}
