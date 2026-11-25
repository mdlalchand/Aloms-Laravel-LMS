<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Course;
use App\Models\Ebook;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /** @return array<int, array{key:string,type:string,id:int,title:string,price:float,image:string,url:string}> */
    public static function items(): array
    {
        $rows = [];
        foreach ((array) session('cart', []) as $key) {
            [$type, $id] = array_pad(explode(':', $key), 2, null);
            $id = (int) $id;

            if ($type === 'course' && ($c = Course::find($id))) {
                $rows[] = [
                    'key' => $key, 'type' => 'course', 'id' => $id, 'title' => $c->title,
                    'price' => $c->final_price, 'image' => $c->thumbnail_url,
                    'url' => route('courses.show', $c->slug),
                ];
            }

            if ($type === 'ebook' && ($e = Ebook::find($id))) {
                $rows[] = [
                    'key' => $key, 'type' => 'ebook', 'id' => $id, 'title' => $e->title,
                    'price' => $e->is_free ? 0 : (float) $e->price, 'image' => $e->coverUrl(),
                    'url' => route('ebooks.show', $e->slug),
                ];
            }
        }
        return $rows;
    }

    public static function totals(): array
    {
        $items = self::items();
        $subtotal = round(array_sum(array_column($items, 'price')), 2);
        $coupon = null;
        $discount = 0.0;

        if ($code = session('coupon')) {
            $coupon = Coupon::where('code', $code)->first();
            if ($coupon && $coupon->isValidFor($subtotal)) {
                $discount = $coupon->discountFor($subtotal);
            } else {
                $coupon = null;
            }
        }

        return [
            'items' => $items,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => max(0, round($subtotal - $discount, 2)),
            'coupon' => $coupon,
            'count' => count($items),
        ];
    }

    public function index()
    {
        return view('cart.index', self::totals());
    }

    public function add(Request $request, string $type, int $id)
    {
        abort_unless(in_array($type, ['course', 'ebook'], true), 404);

        if ($type === 'course') {
            $course = Course::published()->findOrFail($id);
            if ($request->user()->hasCourse($course)) {
                return back()->with('info', 'You already own this course.');
            }
            if ($course->final_price <= 0) {
                return redirect()->route('courses.enroll-free', $course->slug);
            }
        } else {
            $ebook = Ebook::findOrFail($id);
            if ($ebook->ownedBy($request->user())) {
                return back()->with('info', 'You already own this e-book.');
            }
        }

        $key = "{$type}:{$id}";
        $cart = (array) session('cart', []);
        if (! in_array($key, $cart, true)) {
            $cart[] = $key;
        }
        session(['cart' => $cart]);

        return redirect()->route('cart.index')->with('success', 'Added to cart.');
    }

    public function remove(string $key)
    {
        session(['cart' => array_values(array_diff((array) session('cart', []), [$key]))]);
        return back()->with('success', 'Removed from cart.');
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        $coupon = Coupon::where('code', strtoupper(trim($request->code)))->first();
        $subtotal = self::totals()['subtotal'];

        if (! $coupon || ! $coupon->isValidFor($subtotal)) {
            return back()->withErrors(['code' => 'This coupon is invalid or expired.']);
        }

        session(['coupon' => $coupon->code]);
        return back()->with('success', 'Coupon applied: '.$coupon->code);
    }

    public function removeCoupon()
    {
        session()->forget('coupon');
        return back()->with('success', 'Coupon removed.');
    }
}
