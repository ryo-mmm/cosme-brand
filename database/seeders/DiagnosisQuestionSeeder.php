<?php

namespace Database\Seeders;

use App\Models\DiagnosisQuestion;
use Illuminate\Database\Seeder;

class DiagnosisQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $questions = [
            [
                'order' => 1,
                'text'  => '洗顔後、何もしないでいると肌はどうなりますか？',
                'options' => [
                    ['label' => 'すぐにつっぱる', 'score' => 0],
                    ['label' => '少しつっぱる', 'score' => 1],
                    ['label' => 'べたつく', 'score' => 3],
                    ['label' => 'ほとんど変わらない', 'score' => 2],
                ],
            ],
            [
                'order' => 2,
                'text'  => '昼頃、Tゾーン（額・鼻）の状態は？',
                'options' => [
                    ['label' => 'サラサラしている', 'score' => 0],
                    ['label' => '少し光る', 'score' => 2],
                    ['label' => 'かなりテカる', 'score' => 3],
                    ['label' => 'ゾーンによって異なる', 'score' => 2],
                ],
            ],
            [
                'order' => 3,
                'text'  => '肌のトラブルで一番多いものは？',
                'options' => [
                    ['label' => '乾燥・粉ふき', 'score' => 0],
                    ['label' => 'ニキビ・吹き出物', 'score' => 3],
                    ['label' => '赤み・かぶれ', 'score' => 4],
                    ['label' => '毛穴の黒ずみ', 'score' => 3],
                ],
            ],
            [
                'order' => 4,
                'text'  => '季節の変わり目に肌は？',
                'options' => [
                    ['label' => 'すぐに乾燥する', 'score' => 0],
                    ['label' => 'ニキビが増える', 'score' => 3],
                    ['label' => '敏感に反応する', 'score' => 4],
                    ['label' => 'あまり変化しない', 'score' => 2],
                ],
            ],
            [
                'order' => 5,
                'text'  => '化粧水を使ったとき、肌はどう感じますか？',
                'options' => [
                    ['label' => 'すぐに乾く', 'score' => 0],
                    ['label' => 'ちょうどよい', 'score' => 2],
                    ['label' => 'べたつく', 'score' => 3],
                    ['label' => '刺激を感じることがある', 'score' => 4],
                ],
            ],
            [
                'order' => 6,
                'text'  => '毛穴の状態は？',
                'options' => [
                    ['label' => '目立たない', 'score' => 0],
                    ['label' => '少し気になる', 'score' => 2],
                    ['label' => 'かなり目立つ', 'score' => 3],
                    ['label' => '赤みを伴う', 'score' => 4],
                ],
            ],
            [
                'order' => 7,
                'text'  => 'スキンケアにかける時間は？',
                'options' => [
                    ['label' => '3分以内', 'score' => 2],
                    ['label' => '5〜10分', 'score' => 2],
                    ['label' => '10分以上', 'score' => 2],
                    ['label' => 'できるだけ短く', 'score' => 2],
                ],
            ],
        ];

        foreach ($questions as $q) {
            DiagnosisQuestion::create($q);
        }
    }
}
