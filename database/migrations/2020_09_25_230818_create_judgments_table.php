<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJudgmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('judgments', function (Blueprint $table) {
            $table->id();
            $table->string('judgment'); // Very Relevant, Relevant, Marginally Relevant, Not Relevant
            $table->text('observation')->nullable();
            $table->boolean('untie')->default(false); // Identify a tiebreaker judgment
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('query_id')->constrained()->onDelete('cascade');
            $table->foreignId('document_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->unique(['query_id', 'document_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('judgments');
    }
}
