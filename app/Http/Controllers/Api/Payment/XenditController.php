<?php

namespace App\Http\Controllers\Api\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Xendit\Xendit;
use Carbon\Carbon;
use App\Models\Payment;
use App\Models\User;
class XenditController extends Controller
{
    //
    private $token = "xnd_development_0JnRa3LAWdiSRbkafREmsKC76oNNPDMdshMhBuGQfBRm7bS331TqgUV6m7dVlDT";
    public function getListVa()
    {
        Xendit::setApiKey($this->token);
        $getVABanks = \Xendit\VirtualAccounts::getVABanks();
    // var_dump($getVABanks);
    return response()->json([
        
        'data' => $getVABanks
    ])->setStatusCode(200);

    }
    public function createVa(Request $request)
    {
        
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
    $insert = Payment::insert([
            'external_id' => $external_id,
            'payment_channel' => 'Virtual Account',
            'email' => $request->email,
            'price' => $request->price,
            'status' => '0',
            'user_id' => Auth::user()->id
    ]);
     //      $params = ["external_id" => \uniqid(),
//      "bank_code" => $request->bank,
//      "name" => $request->user_name,
//      "expected_amount" => 50000,
//      'is_closed' => true
//   ];
     $createVA = \Xendit\VirtualAccounts::create($params);
     return response()->json([
         'success' => true,
        'data' => $createVA
    ])->setStatusCode(200);
    }
    public function getFVA($id)
    {
        Xendit::setApiKey($this->token);

        $id = $id;
        $getVA = \Xendit\VirtualAccounts::retrieve($id);
        return response()->json([
            'data' => $getVA
        ]);
      
    }
    public function cekPembayaranVA($id)
    {

        Xendit::setApiKey($this->token);

        $paymentID = $id;

        $getFVAPayment = \Xendit\VirtualAccounts::getFVAPayment($paymentID);
        // var_dump($getFVAPayment);
        return response()->json([
            'data' => $getFVAPayment
        ]);
    }

    public function callbackVa(Request $request)
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
   
    
    public function showBalance()
    {
        Xendit::setApiKey($this->token);
        $getBalance = \Xendit\Balance::getBalance('CASH');
        return response()->json([
            'msg' => $getBalance
        ]);
    }
}
