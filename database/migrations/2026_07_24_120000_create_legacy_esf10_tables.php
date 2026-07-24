<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grading', function (Blueprint $table) {
            $table->id();
            $table->string('grading', 20);
            $table->string('sy', 20);
            $table->timestamps();
        });

        Schema::create('grading_terms', function (Blueprint $table) {
            $table->id();
            $table->string('term_name', 100);
            $table->integer('term_order')->unique();
            $table->timestamps();
        });

        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('grade_yr', 20);
            $table->string('section', 30);
            $table->timestamps();
        });

        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('subject', 60);
            $table->timestamps();
        });

        Schema::create('sy', function (Blueprint $table) {
            $table->id();
            $table->string('sy', 25);
            $table->timestamps();
        });

        Schema::create('students', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->string('fname', 55);
            $table->string('mname', 55);
            $table->string('lname', 55);
            $table->string('ext', 20)->nullable()->default('');
            $table->string('bday', 20);
            $table->string('sex', 20);
            $table->string('sname', 80);
            $table->string('grade', 40);
            $table->string('section', 40);
            $table->string('uid', 11);
            $table->string('grading', 40);
            $table->string('sy', 20);
            $table->string('lrn', 40);
            $table->string('final_rating', 20)->nullable()->default('');
            $table->string('status', 30)->nullable()->default('');
            $table->timestamps();
            $table->index(['sy', 'grade', 'section']);
        });

        Schema::create('inputted_grades', function (Blueprint $table) {
            $table->id();
            $table->string('sname_id', 20);
            $table->string('uid', 20);
            $table->string('section', 20);
            $table->string('grade', 20);
            $table->string('grading', 25);
            $table->string('sy', 20);
            $table->string('s1', 20)->nullable()->default('');
            $table->string('s2', 20)->nullable()->default('');
            $table->string('s3', 20)->nullable()->default('');
            $table->string('s4', 20)->nullable()->default('');
            $table->string('s5', 20)->nullable()->default('');
            $table->string('s6', 20)->nullable()->default('');
            $table->string('s7', 20)->nullable()->default('');
            $table->string('s8', 20)->nullable()->default('');
            $table->string('s9', 20)->nullable()->default('');
            $table->string('s10', 20)->nullable()->default('');
            $table->string('s11', 20)->nullable()->default('');
            $table->string('s12', 20)->nullable()->default('');
            $table->timestamps();
            $table->unique(['sname_id', 'grading', 'sy']);
            $table->index(['uid', 'sy']);
        });

        Schema::create('records', function (Blueprint $table) {
            $table->id();
            $table->string('sname_id', 20);
            $table->string('uid', 20);
            $table->string('section', 20);
            $table->string('grade', 20);
            $table->string('grading', 25);
            $table->string('sy', 20);
            $table->string('s1', 20)->nullable()->default('');
            $table->string('s2', 20)->nullable()->default('');
            $table->string('s3', 20)->nullable()->default('');
            $table->string('s4', 20)->nullable()->default('');
            $table->string('s5', 20)->nullable()->default('');
            $table->string('s6', 20)->nullable()->default('');
            $table->string('s7', 20)->nullable()->default('');
            $table->string('s8', 20)->nullable()->default('');
            $table->string('s9', 20)->nullable()->default('');
            $table->string('s10', 20)->nullable()->default('');
            $table->string('s11', 20)->nullable()->default('');
            $table->string('s12', 20)->nullable()->default('');
            $table->string('status', 30)->nullable()->default('');
            $table->timestamps();
            $table->index(['sname_id', 'sy']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('records');
        Schema::dropIfExists('inputted_grades');
        Schema::dropIfExists('students');
        Schema::dropIfExists('sy');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('sections');
        Schema::dropIfExists('grading_terms');
        Schema::dropIfExists('grading');
    }
};
