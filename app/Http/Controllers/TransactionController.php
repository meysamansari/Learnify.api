<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Transaction;
use Evryn\LaravelToman\CallbackRequest;
use Evryn\LaravelToman\Facades\Toman;

class TransactionController extends Controller
{
    public function pay($order_id)
    {
        $transaction = Order::find($order_id);

        $total_price = $transaction->total_price;
        $request = Toman::amount($total_price)
            ->description('Subscribing to Plan A')
            ->callback(route('payment.callback'))
            ->request();

        if ($request->successful()) {
            $transaction = Transaction::create([
                'user_id' => 1,
                'order_id' => $order_id,
                'gateway_result' => ['transactionId' => $request->transactionId()],
                'total_price' => $total_price,
                'status' => 'pending',
            ]);
            return response()->json(['paymentUrl' => $request->paymentUrl()]);
        } else {
            return $request->messages();
        }
    }

    public function callback(CallbackRequest $request)
    {

        $transaction = Transaction::where('gateway_result->transactionId', $request->transactionId())->first();
        $order = Order::find($transaction->order_id);
        $payment = $request
            ->amount($order->total_price)
            ->verify();
//        dd($transaction);

        $order->update(['status' => 'paid']);

        if ($payment->successful()) {
            $referenceId = $payment->referenceId();

            $transaction->forcefill([
                'gateway_result->reference_id' => $referenceId,
                'status' => 'paid',
            ])->save();

            return response()->json([
                'reference_id' => $referenceId,
                'transaction' => $transaction,
                'order' => $order
            ]);

        }

        if ($payment->alreadyVerified()) {
            dd('already_verified');
        }

        if ($payment->failed()) {
            $transaction->forcefill([
                'gateway_result->messages' => $payment->messages(),
                'status' => 'failed',
            ])->save();
            $order->update(['status' => 'failed']);
            return response()->json(['transaction' => $transaction]);
        }
    }
}
