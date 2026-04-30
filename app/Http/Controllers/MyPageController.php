<?php

namespace App\Http\Controllers;

use App\Mail\SubscriptionCancelledMail;
use App\Mail\SubscriptionSkippedMail;
use App\Models\SubscriptionSkip;
use App\Services\StripeErrorService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\Stripe;

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
        $isPaused = false;
        $billingDay = null;

        if ($subscription && $subscription->active()) {
            $stripeSubscription = $subscription->asStripeSubscription();
            $isPaused = !empty($stripeSubscription->pause_collection);

            $periodEnd = $stripeSubscription->current_period_end;
            if ($periodEnd !== null) {
                $nextBillingDate = Carbon::createFromTimestamp($periodEnd);
                $billingDay = $nextBillingDate->day;
                $canSkip = !$isPaused
                    && $nextBillingDate->diffInDays(now()) > config('subscription.skip_days_threshold');
            }
        }

        $charges = collect();
        if ($user->stripe_id) {
            try {
                $charges = $user->charges(10);
            } catch (\Exception $e) {
                Log::warning('Failed to fetch charges: ' . $e->getMessage());
            }
        }

        return view('mypage', compact(
            'user', 'subscription', 'diagnoses',
            'nextBillingDate', 'canSkip', 'charges',
            'isPaused', 'billingDay'
        ));
    }

    public function skipSubscription()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $subscription = $user->subscription('default');

        if (!$subscription || !$subscription->active()) {
            return back()->with('error', '有効なサブスクリプションが見つかりません。');
        }

        $threshold = config('subscription.skip_days_threshold');
        $stripeSubscription = $subscription->asStripeSubscription();
        $originalDate = Carbon::createFromTimestamp($stripeSubscription->current_period_end);

        if ($originalDate->diffInDays(now()) <= $threshold) {
            return back()->with('error', "配送予定日の{$threshold}日前を過ぎているためスキップできません。");
        }

        $months = config('subscription.skip_duration_months');
        $newDate = $originalDate->copy()->addMonths($months);

        try {
            $subscription->updateStripeSubscription([
                'trial_end'          => $newDate->timestamp,
                'proration_behavior' => 'none',
            ]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error('Subscription skip error: ' . $e->getMessage());
            return back()->with('error', StripeErrorService::fromCode($e->getStripeCode() ?? ''));
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

        Mail::to($user)->queue(new SubscriptionSkippedMail($user, $newDate));

        return back()->with('success', '次回配送を1ヶ月スキップしました。新しい配送日: ' . $newDate->format('Y年m月d日'));
    }

    public function changePlan(Request $request)
    {
        $request->validate([
            'plan' => ['required', 'string', 'in:' . implode(',', array_keys(config('cashier.plans', [])))],
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $subscription = $user->subscription('default');

        if (!$subscription || !$subscription->active()) {
            return back()->with('error', '有効なサブスクリプションが見つかりません。');
        }

        $plan = config("cashier.plans.{$request->plan}");

        if (!$plan || empty($plan['price_id'])) {
            return back()->with('error', '選択されたプランが見つかりません。');
        }

        if ($subscription->stripe_price === $plan['price_id']) {
            return back()->with('error', '現在すでにそのプランをご利用中です。');
        }

        try {
            $subscription->swap($plan['price_id']);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error('Plan change error: ' . $e->getMessage());
            return back()->with('error', StripeErrorService::toJapanese($e));
        } catch (\Exception $e) {
            Log::error('Plan change error: ' . $e->getMessage());
            return back()->with('error', 'プランの変更中にエラーが発生しました。');
        }

        return back()->with('success', "プランを「{$plan['name']}」に変更しました。");
    }

    public function changeBillingDate(Request $request)
    {
        $request->validate([
            'billing_day' => ['required', 'integer', 'min:1', 'max:28'],
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $subscription = $user->subscription('default');

        if (!$subscription || !$subscription->active()) {
            return back()->with('error', '有効なサブスクリプションが見つかりません。');
        }

        $day = (int) $request->billing_day;
        $nextDate = now()->copy()->day($day)->startOfDay();
        if ($nextDate->lte(now())) {
            $nextDate->addMonth();
        }

        try {
            $subscription->updateStripeSubscription([
                'trial_end'          => $nextDate->timestamp,
                'proration_behavior' => 'none',
            ]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error('Billing date change error: ' . $e->getMessage());
            return back()->with('error', StripeErrorService::fromCode($e->getStripeCode() ?? ''));
        } catch (\Exception $e) {
            Log::error('Billing date change error: ' . $e->getMessage());
            return back()->with('error', '請求日の変更中にエラーが発生しました。');
        }

        return back()->with('success', "次回の請求日を {$nextDate->format('Y年m月d日')} に変更しました。");
    }

    public function pauseSubscription()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $subscription = $user->subscription('default');

        if (!$subscription || !$subscription->active()) {
            return back()->with('error', '有効なサブスクリプションが見つかりません。');
        }

        $stripeSubscription = $subscription->asStripeSubscription();
        if (!empty($stripeSubscription->pause_collection)) {
            return back()->with('error', 'すでに一時停止中です。');
        }

        try {
            $subscription->updateStripeSubscription([
                'pause_collection' => ['behavior' => 'void'],
            ]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error('Subscription pause error: ' . $e->getMessage());
            return back()->with('error', StripeErrorService::fromCode($e->getStripeCode() ?? ''));
        } catch (\Exception $e) {
            Log::error('Subscription pause error: ' . $e->getMessage());
            return back()->with('error', '一時停止処理中にエラーが発生しました。');
        }

        return back()->with('success', '定期便を一時停止しました。再開するまで請求は発生しません。');
    }

    public function resumeSubscription()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $subscription = $user->subscription('default');

        if (!$subscription) {
            return back()->with('error', 'サブスクリプションが見つかりません。');
        }

        try {
            $subscription->updateStripeSubscription([
                'pause_collection' => '',
            ]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error('Subscription resume error: ' . $e->getMessage());
            return back()->with('error', StripeErrorService::fromCode($e->getStripeCode() ?? ''));
        } catch (\Exception $e) {
            Log::error('Subscription resume error: ' . $e->getMessage());
            return back()->with('error', '再開処理中にエラーが発生しました。');
        }

        return back()->with('success', '定期便を再開しました。次回の請求日から配送が再開されます。');
    }

    public function refund(string $chargeId)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        try {
            Stripe::setApiKey(config('cashier.secret'));

            $charge = \Stripe\Charge::retrieve($chargeId);
            if ($charge->customer !== $user->stripe_id) {
                abort(403);
            }

            if ($charge->refunded) {
                return back()->with('error', 'この請求はすでに返金済みです。');
            }

            $window = config('subscription.refund_window_days');
            $chargeDate = Carbon::createFromTimestamp($charge->created);
            if ($chargeDate->diffInDays(now()) > $window) {
                return back()->with('error', "返金申請は決済から{$window}日以内のみ受け付けています。");
            }

            \Stripe\Refund::create(['charge' => $chargeId]);

        } catch (\Stripe\Exception\InvalidRequestException $e) {
            Log::error('Refund failed', ['charge_id' => $chargeId, 'user_id' => $user->id, 'message' => $e->getMessage()]);
            return back()->with('error', StripeErrorService::toJapanese($e));
        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error('Refund API error', ['charge_id' => $chargeId, 'user_id' => $user->id, 'message' => $e->getMessage()]);
            return back()->with('error', StripeErrorService::toJapanese($e));
        } catch (\Exception $e) {
            Log::error('Refund error', ['charge_id' => $chargeId, 'user_id' => $user->id, 'message' => $e->getMessage()]);
            return back()->with('error', '返金処理中にエラーが発生しました。');
        }

        Log::info('Refund processed', ['charge_id' => $chargeId, 'user_id' => $user->id]);

        return back()->with('success', '返金処理を受け付けました。数日以内にご登録のカードへ返金されます。');
    }

    public function cancelSubscription()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $subscription = $user->subscription('default');

        if (!$subscription || !$subscription->active()) {
            return back()->with('error', '有効なサブスクリプションが見つかりません。');
        }

        try {
            $subscription->cancel();
        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error('Subscription cancel error: ' . $e->getMessage());
            return back()->with('error', StripeErrorService::fromCode($e->getStripeCode() ?? ''));
        } catch (\Exception $e) {
            Log::error('Subscription cancel error: ' . $e->getMessage());
            return back()->with('error', '解約処理中にエラーが発生しました。');
        }

        Mail::to($user)->queue(new SubscriptionCancelledMail($user));

        return redirect()->route('mypage')
            ->with('success', '定期便の解約を受け付けました。現在の請求期間終了まではご利用いただけます。');
    }
}
