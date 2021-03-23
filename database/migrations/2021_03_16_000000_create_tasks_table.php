<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_code')->unique();
            $table->foreignId('task_id')->nullable()->constrained('tasks');
            $table->foreignId('priority_id')->constrained('nova_priorities');
            $table->auth();
            $table->auth('agent');
            $table->morphs('taskable');
            $table->string('marked_as')->default('draft');
            $table->text('note')->nullable();
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
        Schema::dropIfExists('tasks');
    }
}
