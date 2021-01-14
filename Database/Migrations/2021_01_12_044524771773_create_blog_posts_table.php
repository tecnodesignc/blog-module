<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlogPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blog__posts', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('template');
            $table->text('options')->nullable();
            $table->integer('status')->default(0);
            $table->integer('category_id')->unsigned()->index();
            $table->integer('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on(config('auth.table', 'users'))->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {Schema::table('blog__posts', function (Blueprint $table) {
        $table->dropForeign('blog__posts_user_id_foreign');
        $table->dropForeign('blog__posts_category_id_foreign');
    });
        Schema::dropIfExists('blog__posts');
    }
}
