<?php

namespace App\Http\Controllers\Api\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\VARequest;
use Xendit\Xendit;
use Carbon\Carbon;
use App\Models\Payment;
use Auth;
use App\Models\User;


class XenditRealCaseController extends Controller
{
    private $token = "xnd_development_0JnRa3LAWdiSRbkafREmsKC76oNNPDMdshMhBuGQfBRm7bS331TqgUV6m7dVlDT";
    public function PembayaranVA(VARequest $request)
    {
        // dd($request);


        // return response()->json(['msg' => Auth::user()]);
        

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
            'user_id' => $request->user_id,
    ]);

    // $VA_checkout =   Payment::where('user_id',Auth::user()->id)->where('status','0')->first();
        $getVA = \Xendit\VirtualAccounts::retrieve($createVA['id']);
        $start =  Carbon::parse($getVA['expiration_date']);
        $end   =  Carbon::parse($getVA['expiration_date'])->subDays(1);
    if ($getVA['bank_code'] == 'BCA'){
       $gambar = "<img src='{{ asset('image/bank_bca.png') }}' style='width: 100px; height: 100px;' class='card-img-top' alt='...'>";
    }elseif($getVA['bank_code'] == 'BNI'){
         $gambar = "<img src='{{ asset('image/bank_bni.png') }}' style='width: 100px; height: 100px;' class='card-img-top' alt='...'>";
      }elseif($getVA['bank_code'] == 'MANDIRI'){
         $gambar = "<img src='{{ asset('image/bank_mandiri.png') }}' style='width: 100px; height: 100px;' class='card-img-top' alt='...'>";
      }elseif($getVA['bank_code'] == 'PERMATA'){
         $gambar = "<img src='{{ asset('image/bank_permata.png') }}' style='width: 100px; height: 100px;' class='card-img-top' alt='...'>";
      }elseif($getVA['bank_code'] == 'SAHABAT_SAMPOERNA'){
         $gambar = "<img src='{{ asset('image/bank_sahabat_sampoerna.png') }}' style='width: 100px; height: 100px;' class='card-img-top' alt='...'>";
      }elseif($getVA['bank_code'] == 'BRI'){
         $gambar = "<img src='{{ asset('image/bank_bri.png') }}' style='width: 100px; height: 100px;' class='card-img-top' alt='...'>";
      }elseif($getVA['bank_code'] == 'CIMB'){
       $gambar = "<img src='{{ asset('image/bank_cimb.png') }}' style='width: 100px; height: 100px;' class='card-img-top' alt='...'>";
    }elseif($getVA['bank_code'] == 'BSI'){
       $gambar = "<img src='{{ asset('image/bank_bsi.png') }}' style='width: 100px; height: 100px;' class='card-img-top' alt='...'>";
    }elseif($getVA['bank_code'] == 'BJB'){
       $gambar = "<img src='{{ asset('image/bank_bjb.png') }}' style='width: 100px; height: 100px;' class='card-img-top' alt='...'>";
    }else{
         $gambar = "<img src='{{ asset('image/bank_dbs.png') }}' style='width: 100px; height: 100px;' class='card-img-top' alt='...'>";
      }
    return response()->json([
        'data' => "<div class='col-6'>".
         "<h5 class='card-title'>".$getVA['bank_code']." Virtual Account</h5>".
         "<p class='card-text'>".
         "<h2 id='account_number'>".$getVA['account_number'] ."</h2>".
         "</p>".
         "<p class='card-text'>Jumlah yang harus di bayar</p>".
         "<h4>Rp. ".$getVA['expected_amount']."</h4>".
         "<span>Potongan Biaya Admin Rp . 5.000</span>".
         "<button class='btn btn-outline-success' onclick='copyToClipboard('#account_number')'>Copy</button>".
     "</div>".
     "<div class='col-5'>".$gambar. "</div>",
     'msg' => 'Success ok'
    ]);


    }
    public function CheckoutVA(Request $request)
    {
        Xendit::setApiKey($this->token);

        $getVA = \Xendit\VirtualAccounts::retrieve($request->payment_id);
   
        $external_id = $getVA['external_id'];
        $status = $getVA['status'];
        $payment = Payment::where('external_id',$external_id)->exists();
        // return response()->json([
        //     'msg' => $payment
        // ]);
        if ($payment) {
            if ($status == "ACTIVE") {
             
                $user =  Payment::where('external_id',$external_id)->first();
               $update =  Payment::where('external_id',$external_id)->update([
                    'status' => '1'
                ]);
                if ($update) {
                    $balance =  User::where('id',$user->user_id)->first()->balance;
                    $jml_balance = $balance + $user->price - 5000;
                     User::where('id',$user->user_id)->update([
                         'balance' => $jml_balance
                     ]);
                }
                if ($update > 0) {
                    return response()->json([
                        'msg' => 'OK!',
                        'data' => [
                            'transfer_amount' => $request->transfer_amount,
                            'bank_account_number' => $request->bank_account_number,
                            'bank_code' => $request->BANK_pembayaran 
                        ]
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
