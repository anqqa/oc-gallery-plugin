<?php namespace Klubitus\Gallery\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class SystemFilesAddImageSize extends Migration {

    public function up() {
        Schema::table('system_files', function($table) {
            $table->integer('image_width')->nullable();
            $table->integer('image_height')->nullable();
            $table->string('source')->nullable();
        });
    }


    public function down() {
        Schema::table('system_files', function($table) {
            $table->dropColumn('image_width', 'image_height', 'source');
        });
    }

}
