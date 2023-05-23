<?php

use App\Models\Customer;
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
        Schema::create('ar_aps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignIdFor(Customer::class, 'customer_id')->index();
            $table->enum('type', ['ar', 'ap']);
            $table->date('date');
            $table->decimal('ar', 19, 2)->default(0);
            $table->decimal('ap', 19, 2)->default(0);
            $table->string('description')->nullable();
            $table->string('document')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ar_aps');
    }
};
