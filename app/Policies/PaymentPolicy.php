<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('review_payments') || $user->can('buy_plans');
    }

    public function view(User $user, Payment $payment): bool
    {
        if ($user->can('review_payments')) {
            return true;
        }

        return $user->can('buy_plans') && (int) $payment->user_id === (int) $user->id;
    }

    public function create(User $user): bool
    {
        return $user->can('buy_plans');
    }

    public function review(User $user, Payment $payment): bool
    {
        return $user->can('review_payments') && $payment->isPending();
    }
}
