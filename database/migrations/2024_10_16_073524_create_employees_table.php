<?php

use App\Models\City;
use App\Models\Country;
use App\Models\Department;
use App\Models\State;
use App\Models\Team;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Country::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(State::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(City::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(Department::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(Team::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->string('first_name', 20);
            $table->string('middle_name', 20);
            $table->string('last_name', 20);
            $table->text('address');
            $table->string('zip_code');
            $table->date('date_of_birth');
            $table->date('date_hired');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
