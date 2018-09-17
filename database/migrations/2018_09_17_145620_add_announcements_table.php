<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAnnouncementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description')->nullable(false);
            $table->text('content')->nullable(false);
            $table->datetime('display_from')->nullable(false);
            $table->datetime('display_till')->nullable(false);
            $table->boolean('show_guests')->nullable(false)->default(false);
            $table->boolean('show_users')->nullable(false)->default(false);
            $table->boolean('show_members')->nullable(false)->default(true);
            $table->boolean('show_only_homepage')->nullable(false)->default(true);
            $table->boolean('show_only_new')->nullable(false)->default(false);
            $table->boolean('show_only_firstyear')->nullable(false)->default(false);
            $table->boolean('show_only_active')->nullable(false)->default(false);
            $table->boolean('show_as_popup')->nullable(false)->default(false);
            $table->tinyInteger('show_style')->nullable(false)->default(0);
            $table->boolean('is_dismissable')->nullable(false)->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('announcements');
    }
}
