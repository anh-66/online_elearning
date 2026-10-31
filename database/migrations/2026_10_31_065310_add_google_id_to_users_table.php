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
        if (! Schema::hasColumn('users', 'google_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('google_id')->nullable()->after('email');
            });
        }

        $hasGoogleIdUniqueIndex = collect(Schema::getIndexes('users'))
            ->contains(fn (array $index): bool => $index['name'] === 'users_google_id_unique');

        if (! $hasGoogleIdUniqueIndex) {
            Schema::table('users', function (Blueprint $table) {
                $table->unique('google_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'google_id')) {
            $hasGoogleIdUniqueIndex = collect(Schema::getIndexes('users'))
                ->contains(fn (array $index): bool => $index['name'] === 'users_google_id_unique');

            Schema::table('users', function (Blueprint $table) use ($hasGoogleIdUniqueIndex): void {
                if ($hasGoogleIdUniqueIndex) {
                    $table->dropUnique(['google_id']);
                }

                $table->dropColumn('google_id');
            });
        }
    }
};
