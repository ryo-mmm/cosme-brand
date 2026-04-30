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
        // products: カテゴリ絞り込み・有効商品一覧取得
        Schema::table('products', function (Blueprint $table) {
            $table->index('category');
            $table->index('is_active');
            $table->index(['is_active', 'category']);
        });

        // users: email_verified_at（stripe_idはCashierマイグレーション済み）
        Schema::table('users', function (Blueprint $table) {
            $table->index('email_verified_at');
        });

        // skin_diagnoses: ユーザーごとの診断履歴取得
        Schema::table('skin_diagnoses', function (Blueprint $table) {
            $table->index(['user_id', 'created_at']);
        });

        // subscription_skips: ユーザーのスキップ履歴取得
        Schema::table('subscription_skips', function (Blueprint $table) {
            $table->index(['user_id', 'created_at']);
        });

        // diagnosis_questions: 有効な質問の表示順取得
        Schema::table('diagnosis_questions', function (Blueprint $table) {
            $table->index(['is_active', 'order']);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['category']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['is_active', 'category']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['email_verified_at']);
        });

        Schema::table('skin_diagnoses', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'created_at']);
        });

        Schema::table('subscription_skips', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'created_at']);
        });

        Schema::table('diagnosis_questions', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'order']);
        });
    }
};
