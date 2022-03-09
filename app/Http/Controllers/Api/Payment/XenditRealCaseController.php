<?php

namespace App\Http\Controllers\Api\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\VARequest;
use Xendit\Xendit;
use Carbon\Carbon;
use App\Models\Payment;
use Auth;

class XenditRealCaseController extends Controller
{
    private $token = "xnd_development_0JnRa3LAWdiSRbkafREmsKC76oNNPDMdshMhBuGQfBRm7bS331TqgUV6m7dVlDT";
    public function PembayaranVA(VARequest $request)
    {
        // dd($request);
        // return $request;

        Xendit::setApiKey($this->token);
        $external_id = 'va-'.time();
        $params = [
                   "external_id" => $external_id,
     "bank_code" => $request->bank,
     "name" => $request->email,
     "expected_amount" => $request->price,
     'is_closed' => true,
     'expiration_date' => Carbon::now()->addDays(1)->toISOString(),
     'is_single_use' => true
    ];
    $createVA = \Xendit\VirtualAccounts::create($params);
    // return $createVA['id'];
    $insert = Payment::insert([
            'external_id' => $external_id,
            'payment_id' => $createVA['id'],
            'payment_channel' => 'Virtual Account',
            'email' => $request->email,
            'price' => $request->price,
            'status' => '0',
            'user_id' => 1
    ]);
    return response()->json(['msg'=> ' Checkout! Berhasil']);


    }
    public function CheckoutVA(Request $request)
    {

        $external_id = $request->external_id;
        $status = $request->status;
        $payment = Payment::where('external_id',$external_id)->exists();
        // return response()->json([
        //     'msg' => $payment
        // ]);
        if ($payment) {
            if ($status == "ACTIVE") {
                $user =  Payment::where('external_id',$external_id)->first();
               $update =  Payment::where('external_id',$external_id)->update([
                    'status' => 1
                ]);
               $balance =  User::where('id',$user->user_id)->first()->balance;
               $jml_balance = $balance + $user->price;
                User::where('id',$user->user_id)->update([
                    'balance' => $jml_balance
                ]);
                if ($update > 0) {
                    return response()->json([
                        'msg' => 'OK!'
                    ]);
                }
                return response()->json([
                    'msg' => 'false!'
                ]);
            }

        }else{
            return response()->json([
                'msg' => 'Data tidak ada!'
            ]);
        }

    }
}
