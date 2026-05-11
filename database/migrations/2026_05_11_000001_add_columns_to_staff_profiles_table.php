<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff_profiles', function (Blueprint $table) {
            $table->string('father_name', 50)->nullable()->after('full_name');
            $table->tinyInteger('gender')->nullable()->after('father_name');
            // 1 = Male, 2 = Female
            $table->tinyInteger('marital_status')->nullable()->after('gender');
            // 1 = Single, 2 = Married
            $table->string('religion', 50)->nullable()->after('marital_status');
            $table->string('ethnic_group', 50)->nullable()->after('religion');
            $table->enum('uniform_size', ['S', 'M', 'L', 'XL', 'XXL'])->nullable()->after('ethnic_group');
            $table->text('education_qualification')->nullable()->after('uniform_size');
            $table->text('work_experience')->nullable()->after('uniform_size');
            $table->text('home_address')->nullable()->after('address');
            $table->text('note')->nullable()->after('work_experience');
            $table->string('photo')->nullable()->after('note');
        });
    }

    public function down(): void
    {
        Schema::table('staff_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'father_name',
                'gender',
                'marital_status',
                'religion',
                'ethnic_group',
                'uniform_size',
                'education_qualification',
                'work_experience',
                'home_address',
                'note',
                'photo',
            ]);
        });
    }
};
