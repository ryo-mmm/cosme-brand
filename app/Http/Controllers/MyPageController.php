<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionSkip;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MyPageController extends Controller
{
    public function index()
    {
        $user = auth()->user();
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
        $user = auth()->user();
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

        // Stripe APIで次回請求日を更新
        // \Stripe\Subscription::update($stripeSubscription->id, [
        //     'trial_end' => $newDate->timestamp,
        //     'proration_behavior' => 'none',
        // ]);

        SubscriptionSkip::create([
            'user_id' => $user->id,
            'stripe_subscription_id' => $stripeSubscription->id,
            'original_next_billing_date' => $originalDate,
            'new_next_billing_date' => $newDate,
        ]);

        return back()->with('success', '次回配送を1ヶ月スキップしました。新しい配送日: ' . $newDate->format('Y年m月d日'));
    }
}
