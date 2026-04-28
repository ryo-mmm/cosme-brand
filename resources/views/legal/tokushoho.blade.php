<x-app-layout>
    <x-slot name="title">特定商取引法に基づく表記</x-slot>

    <div class="max-w-3xl mx-auto" style="padding: 4rem 1.5rem;">
        <h1 style="font-family:'Noto Serif JP', serif; font-size:1.5rem; letter-spacing:0.1em; color:#2E3A3B; margin-bottom:2.5rem; padding-bottom:1rem; border-bottom:1px solid #E8E4DE;">
            特定商取引法に基づく表記
        </h1>

        <table style="width:100%; border-collapse:collapse; font-size:0.875rem; line-height:1.9; color:#4A5859;">
            @foreach([
                ['項目' => '販売業者', '内容' => 'LUMIÈRE BOTANIQUE'],
                ['項目' => '運営責任者', '内容' => '（代表者名）'],
                ['項目' => '所在地', '内容' => '〒000-0000 東京都○○区○○ 0-0-0'],
                ['項目' => '電話番号', '内容' => '（お問い合わせフォームよりご連絡ください）'],
                ['項目' => 'メールアドレス', '内容' => 'info@lumiere-botanique.jp'],
                ['項目' => '販売価格', '内容' => '各商品ページに記載の金額（税込）'],
                ['項目' => '商品代金以外の費用', '内容' => '送料：全国一律550円（税込）'],
                ['項目' => 'お支払い方法', '内容' => 'クレジットカード（Stripe経由）'],
                ['項目' => 'お支払い時期', '内容' => '毎月定期自動引き落とし'],
                ['項目' => '商品の引渡し時期', '内容' => 'お申し込み後、5〜7営業日以内に発送'],
                ['項目' => '返品・交換', '内容' => '商品到着後7日以内に限り、未開封のものに限り返品可能。返送料はお客様負担となります。'],
                ['項目' => '定期便の解約', '内容' => 'マイページよりいつでも解約可能です。次回発送予定日の3日前までにお手続きください。'],
            ] as $row)
            <tr style="border-bottom:1px solid #E8E4DE;">
                <th style="padding:1rem 1.25rem; background:#F5F5F0; font-weight:500; text-align:left; white-space:nowrap; vertical-align:top; width:40%;">{{ $row['項目'] }}</th>
                <td style="padding:1rem 1.25rem;">{{ $row['内容'] }}</td>
            </tr>
            @endforeach
        </table>

        <p style="margin-top:2rem; font-size:0.75rem; color:#8A9899;">
            ※ 上記の情報は予告なく変更される場合があります。最新の情報はこのページをご確認ください。
        </p>
    </div>
</x-app-layout>
