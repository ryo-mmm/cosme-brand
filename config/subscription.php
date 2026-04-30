<?php

return [
    // スキップ受付期限（配送日の何日前まで）
    'skip_days_threshold'  => (int) env('SUBSCRIPTION_SKIP_DAYS_THRESHOLD', 3),

    // スキップ時に延長する月数
    'skip_duration_months' => (int) env('SUBSCRIPTION_SKIP_DURATION_MONTHS', 1),

    // 返金可能期間（日数）
    'refund_window_days'   => (int) env('SUBSCRIPTION_REFUND_WINDOW_DAYS', 14),

    // 単品購入の送料（円）
    'shipping_fee'         => (int) env('SUBSCRIPTION_SHIPPING_FEE', 550),
];
