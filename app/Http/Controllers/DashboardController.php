<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Xendit\Xendit;
use Auth;
use App\Models\Payment;
use Carbon\Carbon;
class DashboardController extends Controller
{
    private $token = "xnd_development_0JnRa3LAWdiSRbkafREmsKC76oNNPDMdshMhBuGQfBRm7bS331TqgUV6m7dVlDT";

    public function index()
    {
        Xendit::setApiKey($this->token);

        $getVABanks = \Xendit\VirtualAccounts::getVABanks();
     
        $VA_checkout =   Payment::where('user_id',Auth::user()->id)->where('status','0')->first();
        if ($VA_checkout !=  null) {
            $getVA = \Xendit\VirtualAccounts::retrieve($VA_checkout->payment_id);
            // dd(,);
    $start =  Carbon::parse($getVA['expiration_date']);
    $end   =  Carbon::parse($getVA['expiration_date'])->subDays(1);
        }else{
            $getVA = null;
            $start = null;
            $end = null;
        }

        // $response = Http::withHeaders([
        //     'key' => 'xnd_development_0JnRa3LAWdiSRbkafREmsKC76oNNPDMdshMhBuGQfBRm7bS331TqgUV6m7dVlDT'
        // ])->get('https://api.xendit.co/available_virtual_account_banks')->json();
        // $err = curl_error($curl);
        // curl_close($curl);

        // if ($err) {
        //     echo "cURL Error #:" . $err;
        // } else {
        //     print_r(json_decode($response));
        // }
        // dd($getVA);
        return view('dashboard',compact('getVABanks','VA_checkout','getVA','start','end'));
        # code...
    }
}
