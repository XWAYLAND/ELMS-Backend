<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $coverFiles = [
            '978-979-461-487-1' => 'covers/978-602-0000-01-1.webp',
            '978-602-03-2478-3' => 'covers/978-602-0000-02-2.webp',
            '978-602-06-3317-6' => 'covers/978-602-0000-03-3.webp',
            '978-602-03-1157-8' => 'covers/978-602-0000-04-4.webp',
            '978-602-8811-55-2' => 'covers/978-602-0000-05-5.webp',
            '978-602-06-3318-3' => 'covers/978-602-0000-06-6.webp',
            '978-602-03-3295-5' => 'covers/978-602-0000-07-7.webp',
            '978-602-03-8591-3' => 'covers/978-602-0000-08-8.webp',
            '978-979-22-9224-7' => 'covers/978-602-0000-09-9.webp',
            '978-979-91-0224-8' => 'covers/978-602-0000-10-5.webp',
            '978-979-433-397-2' => 'covers/978-602-0000-11-2.webp',
            '978-006-112-241-5' => 'covers/978-602-0000-12-9.webp',
        ];

        foreach ($coverFiles as $isbn => $cover) {
            DB::table('buku')->where('isbn', $isbn)->update(['cover' => $cover]);
        }
    }

    public function down(): void
    {
    }
};