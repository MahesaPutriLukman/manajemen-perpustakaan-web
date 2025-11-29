<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');            
            $table->string('author');           
            $table->string('publisher');        
            $table->integer('publication_year');   
            $table->string('category');         
            $table->integer('stock');           
            
            $table->integer('max_loan_days')->default(7); 
            $table->decimal('fine_per_day', 8, 2)->default(1000); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
