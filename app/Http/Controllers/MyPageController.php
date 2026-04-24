<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionSkip;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MyPageController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $subscription = $user->subscription('default');
        $diagnoses = $user->skinDiagnoses()->latest()->take(3)->get();

        $nextBillingDate = null;
        $canSkip = false;

        if ($subscription && $subscription->active()) {
            $nextBillingDate = Carbon::createFromTimestamp(
                $subscription->asStripeSubscription()->current_period_end
            );
            $canSkip = $nextBillingDate->diffInDays(now()) > 3;
        }

        return view('mypage', compact('user', 'subscription', 'diagnoses', 'nextBillingDate', 'canSkip'));
    }

    public function skipSubscription(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $subscription = $user->subscription('default');

        if (!$subscription || !$subscription->active()) {
            return back()->with('error', '有効なサブスクリプションが見つかりません。');
        }

        $stripeSubscription = $subscription->asStripeSubscription();
        $originalDate = Carbon::createFromTimestamp($stripeSubscription->current_period_end);

        if ($originalDate->diffInDays(now()) <= 3) {
            return back()->with('error', '配送予定日の3日前を過ぎているためスキップできません。');
        }

        $newDate = $originalDate->copy()->addMonth();

        try {
            $subscription->updateStripeSubscription([
                'trial_end'          => $newDate->timestamp,
                'proration_behavior' => 'none',
            ]);
        } catch (\Exception $e) {
            Log::error('Subscription skip error: ' . $e->getMessage());
            return back()->with('error', 'スキップ処理中にエラーが発生しました。');
        }

        SubscriptionSkip::create([
            'user_id'                    => $user->id,
            'stripe_subscription_id'     => $stripeSubscription->id,
            'original_next_billing_date' => $originalDate,
            'new_next_billing_date'      => $newDate,
        ]);

        return back()->with('success', '次回配送を1ヶ月スキップしました。新しい配送日: ' . $newDate->format('Y年m月d日'));
    }

    public function cancelSubscription(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $subscription = $user->subscription('default');

        if (!$subscription || !$subscription->active()) {
            return back()->with('error', '有効なサブスクリプションが見つかりません。');
        }

        try {
            // 期間終了時に解約（即時解約ではなく次回更新日まで有効）
            $subscription->cancel();
        } catch (\Exception $e) {
            Log::error('Subscription cancel error: ' . $e->getMessage());
            return back()->with('error', '解約処理中にエラーが発生しました。');
        }

        return redirect()->route('mypage')
            ->with('success', '定期便の解約を受け付けました。現在の請求期間終了まではご利用いただけます。');
    }
}
