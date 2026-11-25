<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Ebook;
use App\Models\EbookPurchase;
use App\Models\Enrollment;
use App\Models\Order;

class OrderService
{
    /** Give the buyer access to everything in the order. */
    public static function fulfill(Order $order): void
    {
        foreach ($order->items as $item) {
            if ($item->item_type === 'course') {
                Enrollment::firstOrCreate(
                    ['user_id' => $order->user_id, 'course_id' => $item->item_id],
                    [
                        'order_id' => $order->id,
                        'price_paid' => $item->price,
                        'status' => 'active',
                        'enrolled_at' => now(),
                    ]
                );
            }

            if ($item->item_type === 'ebook') {
                EbookPurchase::firstOrCreate(
                    ['user_id' => $order->user_id, 'ebook_id' => $item->item_id],
                    ['order_id' => $order->id]
                );
            }
        }

        $order->update(['status' => 'paid']);
    }

    /** Issue a certificate when the student finished 100% of a course. */
    public static function maybeIssueCertificate(int $userId, Course $course): ?Certificate
    {
        if (! $course->has_certificate) {
            return null;
        }

        $progress = $course->progressFor(\App\Models\User::find($userId));
        if ($progress < 100) {
            return null;
        }

        Enrollment::where('user_id', $userId)->where('course_id', $course->id)
            ->update(['completed_at' => now()]);

        return Certificate::firstOrCreate(
            ['user_id' => $userId, 'course_id' => $course->id],
            ['code' => 'CERT-'.strtoupper(\Illuminate\Support\Str::random(10)), 'issued_at' => now()]
        );
    }
}
