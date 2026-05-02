<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. skin_types マスタ
        Schema::create('skin_types', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 30)->unique();
            $table->string('label', 30);
            $table->timestamps();
        });

        DB::table('skin_types')->insert([
            ['slug' => 'dry',         'label' => '乾燥肌',      'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'oily',        'label' => 'オイリー肌',  'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'combination', 'label' => '混合肌',      'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'sensitive',   'label' => '敏感肌',      'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'normal',      'label' => '普通肌',      'created_at' => now(), 'updated_at' => now()],
        ]);

        // 2. 中間テーブル
        Schema::create('product_skin_type', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('skin_type_id')->constrained()->cascadeOnDelete();
            $table->primary(['product_id', 'skin_type_id']);
            $table->index('skin_type_id');
        });

        // 3. 既存の JSON データを中間テーブルへ移行
        $skinTypeMap = DB::table('skin_types')->pluck('id', 'slug');

        DB::table('products')->whereNotNull('skin_types')->get()->each(function ($product) use ($skinTypeMap) {
            $slugs = json_decode($product->skin_types, true) ?? [];
            foreach ($slugs as $slug) {
                if (isset($skinTypeMap[$slug])) {
                    DB::table('product_skin_type')->insertOrIgnore([
                        'product_id'   => $product->id,
                        'skin_type_id' => $skinTypeMap[$slug],
                    ]);
                }
            }
        });

        // 4. JSON カラムを削除
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('skin_types');
        });
    }

    public function down(): void
    {
        // JSON カラムを復元
        Schema::table('products', function (Blueprint $table) {
            $table->json('skin_types')->nullable()->after('category');
        });

        // 中間テーブルからデータを逆移行
        DB::table('products')->get()->each(function ($product) {
            $slugs = DB::table('product_skin_type')
                ->join('skin_types', 'skin_types.id', '=', 'product_skin_type.skin_type_id')
                ->where('product_skin_type.product_id', $product->id)
                ->pluck('skin_types.slug')
                ->toArray();

            DB::table('products')
                ->where('id', $product->id)
                ->update(['skin_types' => json_encode($slugs)]);
        });

        Schema::dropIfExists('product_skin_type');
        Schema::dropIfExists('skin_types');
    }
};
