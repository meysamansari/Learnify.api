<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;

class OrderController extends Controller
{
    public function store(StoreOrderRequest $request)
    {
        $user = auth()->user();

        $order = Order::firstOrCreate(['user_id' => $user->id, 'status' => 'cart',]);

        $order->courses()->sync($request->course_id);

        $total_price = $order->courses()->sum('price');

        $order->update(['total_price' => $total_price]);

        return response()->json(['message' => 'Course added to cart successfully', 'order' => $order,]);
    }

    public function destroy($course_id)
    {
        $user = auth()->user();

        $order = Order::where('user_id', $user->id)->where('status', 'cart')->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found',], 404);
        }
        $order->courses()->findOrFail($course_id);
        $order->courses()->detach($course_id);

        $total_price = $order->courses()->sum('price');

        $order->update(['total_price' => $total_price]);

        return response()->json(['message' => 'Course removed from cart successfully', 'order' => $order,]);
    }
    public function show()
    {
        $user = auth()->user();
        $order = Order::with('courses')->where('user_id', $user->id)->where('status', 'cart')->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found',], 404);
        }

        return response()->json(['order' => $order]);
    }
}
