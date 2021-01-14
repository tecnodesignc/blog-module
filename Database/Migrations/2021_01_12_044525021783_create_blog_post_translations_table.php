<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlogPostTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blog__post_translations', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            // Your translatable fields
            $table->string('title');
            $table->string('slug');
            $table->boolean('status')->default(1);
            $table->text('summary');
            $table->text('content');
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('og_title')->nullable();
            $table->string('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->string('og_type')->nullable();
            $table->integer('post_id')->unsigned();
            $table->string('locale')->index();
            $table->unique(['post_id', 'locale']);
            $table->foreign('post_id')->references('id')->on('blog__posts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('blog__post_translations', function (Blueprint $table) {
            $table->dropForeign(['post_id']);
        });
        Schema::dropIfExists('blog__post_translations');
    }
}
