<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // 乾燥肌向け
            [
                'name' => 'ローズヒップ ディープモイスチャー 洗顔料',
                'slug' => 'rosehip-deep-moisture-cleanser',
                'description' => '天然ローズヒップオイルを贅沢に配合した、乾燥肌のための保湿洗顔料。洗いながら潤いを与え、洗い上がりはしっとりなめらか。敏感肌にも使えるpHバランス処方。',
                'ingredients' => 'ローズヒップ果実エキス、ヒアルロン酸Na、アロエベラジェル、グリセリン、スクワラン、カモミールエキス、ラベンダー花水',
                'price' => 4800,
                'subscription_price' => 3840,
                'category' => 'cleanser',
                'skin_types' => ['dry', 'sensitive'],
                'volume_ml' => 120,
            ],
            [
                'name' => 'セラミドリッチ 保湿化粧水',
                'slug' => 'ceramide-rich-toner',
                'description' => '植物由来セラミドとヒアルロン酸を高濃度配合。乾燥した肌のバリア機能を補い、もっちりとしたハリ感を与える濃密化粧水。',
                'ingredients' => 'セラミドEOP、セラミドNP、ヒアルロン酸Na、ナイアシンアミド、アロエベラ葉エキス、甘草根エキス',
                'price' => 6200,
                'subscription_price' => 4960,
                'category' => 'toner',
                'skin_types' => ['dry'],
                'volume_ml' => 150,
            ],
            [
                'name' => 'シアバター リッチ モイスチャークリーム',
                'slug' => 'shea-butter-rich-moisture-cream',
                'description' => '天然シアバターをベースに、アルガンオイルとツバキオイルをブレンド。乾燥肌に必要な油分をたっぷり補給し、一日中しっとりした肌が続きます。',
                'ingredients' => 'シア脂、アルガニアスピノサ核油、ツバキ種子油、スクワラン、ホホバ種子油、ローズマリー葉エキス',
                'price' => 7800,
                'subscription_price' => 6240,
                'category' => 'moisturizer',
                'skin_types' => ['dry', 'sensitive'],
                'volume_ml' => 50,
            ],
            // オイリー肌向け
            [
                'name' => 'グリーンティー バランシング 洗顔料',
                'slug' => 'green-tea-balancing-cleanser',
                'description' => '抹茶由来の茶ポリフェノールと天然クレイを配合した、オイリー肌のための洗顔料。余分な皮脂をすっきり取り除きながら、水分バランスを整えます。',
                'ingredients' => '茶葉エキス、カオリン、サリチル酸、ティーツリー葉油、スペアミント葉水、ウィッチヘーゼル葉エキス',
                'price' => 4200,
                'subscription_price' => 3360,
                'category' => 'cleanser',
                'skin_types' => ['oily', 'combination'],
                'volume_ml' => 150,
            ],
            [
                'name' => 'ノンコメドジェニック 軽量化粧水',
                'slug' => 'noncomedogenic-light-toner',
                'description' => '毛穴を詰まらせない処方（ノンコメドジェニックテスト済）のさらっとした化粧水。ナイアシンアミド配合で毛穴の目立ちを抑え、皮脂コントロールをサポートします。',
                'ingredients' => 'ナイアシンアミド、ウィッチヘーゼル葉エキス、サリチル酸Na、アロエベラジェル、緑茶エキス、BHA',
                'price' => 5500,
                'subscription_price' => 4400,
                'category' => 'toner',
                'skin_types' => ['oily'],
                'volume_ml' => 200,
            ],
            // 敏感肌向け
            [
                'name' => 'カモミール カームダウン 化粧水',
                'slug' => 'chamomile-calmdown-toner',
                'description' => 'カモミール、カレンデュラなど鎮静植物エキスを贅沢に配合。香料・アルコール・パラベン不使用の低刺激処方で、赤みや炎症が気になる敏感肌を穏やかにケア。',
                'ingredients' => 'カモミール花エキス、カレンデュラ花エキス、アズレン、アロエベラジェル、ビサボロール、セラミドEOP、ツボクサエキス',
                'price' => 6800,
                'subscription_price' => 5440,
                'category' => 'toner',
                'skin_types' => ['sensitive'],
                'volume_ml' => 120,
            ],
            [
                'name' => 'センシティブスキン バリアリペア セラム',
                'slug' => 'sensitive-barrier-repair-serum',
                'description' => '敏感肌のバリア機能を修復・強化する集中美容液。パンテノール、セラミド複合体、スクワランが肌を保護し、外的刺激から守ります。',
                'ingredients' => 'パンテノール、セラミドEOP、セラミドNP、セラミドAP、スクワラン、ナイアシンアミド、アロエベラ葉エキス',
                'price' => 9800,
                'subscription_price' => 7840,
                'category' => 'serum',
                'skin_types' => ['sensitive', 'dry'],
                'volume_ml' => 30,
            ],
            // 混合肌向け
            [
                'name' => 'ビタミンC マルチバランス 美容液',
                'slug' => 'vitamin-c-multibalance-serum',
                'description' => '安定型ビタミンC誘導体を高配合。くすみを改善しながら、乾燥とオイリーが混在する混合肌のバランスを整えます。朝夜問わず使えるオールラウンダー美容液。',
                'ingredients' => 'アスコルビルグルコシド（ビタミンC誘導体）、ナイアシンアミド、フラーレン、ヒアルロン酸Na、ツボクサエキス',
                'price' => 8500,
                'subscription_price' => 6800,
                'category' => 'serum',
                'skin_types' => ['combination', 'oily'],
                'volume_ml' => 30,
            ],
            [
                'name' => 'バランシング デュアルゾーン 保湿クリーム',
                'slug' => 'balancing-dual-zone-moisturizer',
                'description' => 'Tゾーンのテカりは抑えつつ、乾燥しやすい頬はしっかり保湿。混合肌の2つの悩みを1本で解決するオールインワンクリーム。',
                'ingredients' => 'ナイアシンアミド、ヒアルロン酸Na、スクワラン、グリセリン、アロエベラジェル、緑茶エキス、ホホバ種子油',
                'price' => 6500,
                'subscription_price' => 5200,
                'category' => 'moisturizer',
                'skin_types' => ['combination'],
                'volume_ml' => 50,
            ],
            // アイケア（全肌タイプ）
            [
                'name' => 'レチノール アイ リバイタライジング クリーム',
                'slug' => 'retinol-eye-revitalizing-cream',
                'description' => '植物由来のバイオレチノールと、ペプチド複合体を配合したアイクリーム。目元のハリ感を高め、乾燥による小じわをケアします。',
                'ingredients' => 'バイオレチノール（ローズヒップ種子油）、アセチルヘキサペプチド-3、コエンザイムQ10、ヒアルロン酸Na、シアバター',
                'price' => 11000,
                'subscription_price' => 8800,
                'category' => 'eye',
                'skin_types' => ['dry', 'combination', 'oily', 'sensitive'],
                'volume_ml' => 15,
            ],
        ];

        foreach ($products as $data) {
            Product::updateOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
