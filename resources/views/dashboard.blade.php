@extends('layouts.app')
@section('content')
    <div class="content">
        @if ($message = Session::get('msg'))
        <div class="alert alert-succes" role="alert">
           {{$message}}
          </div>
        @endif
        <div class="card">
            <div class="card-body">


                <!-- Button trigger modal -->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    Top Up Saldo
                </button>
                Your Balance : {{ Auth::user()->balance }}
            </div>
        </div>


    </div>
    <div class="card p-5">
        <form method="POST" id="formCheckout">
            @csrf
            <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label">Transfer Amount</label>
                <input type="number" id="transfer_amount" name="transfer_amount" class="form-control" id="exampleInputPassword1">
            </div>

            <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label">Bank Account Number</label>
                <input type="text" id="bank_account_number" name="bank_account_number" class="form-control" id="exampleInputPassword1">
            </div>

            <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label">BANK</label>
                <select name="BANK_pembayaran" id="BANK_pembayaran" class="form-control">
                    @foreach ($getVABanks as $item)
                        <option value="{{ $item['code'] }}" {{ $item['is_activated'] == true ? '' : 'disabled' }}>
                            {{ $item['name'] }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-success">bayar</button>
        </form>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Virtual ACCOUNT</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form  method="POST" id="formPemabayaranVA">
                <div class="modal-body">
                        @csrf
                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">BANK</label>
                            {{-- {{dd($getVABanks[1]['code'])}} --}}
                            <select name="bank" id="bank" class="form-control">
                                <option value="" selected>-- Pilih Bank --</option>
                                @foreach ($getVABanks as $item)
                                    <option value="{{ $item['code'] }}"
                                        {{ $item['is_activated'] == true ? '' : 'disabled' }}>{{ $item['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            <span id="error_bank" class="text-danger"></span>
                            {{-- <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp"> --}}
                            {{-- <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div> --}}
                        </div>
                        <div class="mb-3">
                            <label for="exampleInputPassword1" class="form-label">nama</label>
                            <input type="text" name="email" id="email" class="form-control" id="exampleInputPassword1">
                            <span id="error_email" class="text-danger"></span>

                        </div>
                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Nominal</label>
                            {{-- {{dd($getVABanks[1]['code'])}} --}}
                            <select name="price" id="price" class="form-control">
                                <option value="" selected>-- Pilih Nominal --</option>
                                <option value="100000">Rp. 100.000</option>
                                <option value="150000">Rp. 150.000</option>
                                <option value="200000">Rp. 200.000</option>
                                <option value="250000">Rp. 250.000</option>
    
                            </select>
                            <span id="error_price" class="text-danger"></span>
                            
                        </div>


                     
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <div class="p-5">
        <div class="row">
            {{-- {{dd($getVA)}} --}}
         {{-- @forelse ($VA_checkout as $item) --}}
        @if ($getVA != null )
        <div class="card col-8">
            <div class="card-header row">
                    <div class="col-3">
                        Kode Pembayaran
                    </div>
                    <div class="col-3">
                        Bayar Sebelum :
                    </div>
                    <div class="col-4"  id="countdown"> 
                    </div>

            </div>
            <div class="card-body row">
             
                <div class="col-6">
                    <h5 class="card-title">{{$getVA['bank_code'] != null ? $getVA['bank_code'] : ''   }} Virtual Account</h5>
                    <p class="card-text">
                    <h2 id="account_number">{{$getVA['account_number'] != null ? $getVA['account_number'] : ''   }} </h2>
                    </p>
                    <p class="card-text">Jumlah yang harus di bayar</p>
                    <h4>Rp. 
                        {{$getVA['expected_amount'] != null ? $getVA['expected_amount'] : ''   }}
                    </h4>
                    <span>Potongan Biaya Admin Rp . 5.000</span>
                    <button class="btn btn-outline-success" onclick="copyToClipboard('#account_number')">
                            Copy 
                    </button>
                </div>
                <div class="col-5">
                    @if ($getVA['bank_code'] == 'BCA')
                      <img src="{{ asset('image/bank_bca.png') }}" style="width: 100px; height: 100px;" class="card-img-top" alt="...">
                        @elseif($getVA['bank_code'] == 'BNI')
                      <img src="{{ asset('image/bank_bni.png') }}" style="width: 100px; height: 100px;" class="card-img-top" alt="...">
                        @elseif($getVA['bank_code'] == 'MANDIRI')
                      <img src="{{ asset('image/bank_mandiri.png') }}" style="width: 100px; height: 100px;" class="card-img-top" alt="...">
                        @elseif($getVA['bank_code'] == 'PERMATA')
                      <img src="{{ asset('image/bank_permata.png') }}" style="width: 100px; height: 100px;" class="card-img-top" alt="...">
                        @elseif($getVA['bank_code'] == 'SAHABAT_SAMPOERNA')
                      <img src="{{ asset('image/bank_sahabat_sampoerna.png') }}" style="width: 100px; height: 100px;" class="card-img-top" alt="...">
                        @elseif($getVA['bank_code'] == 'BRI')
                      <img src="{{ asset('image/bank_bri.png') }}" style="width: 100px; height: 100px;" class="card-img-top" alt="...">
                      @elseif($getVA['bank_code'] == 'CIMB')
                      <img src="{{ asset('image/bank_cimb.png') }}" style="width: 100px; height: 100px;" class="card-img-top" alt="...">
                      @elseif($getVA['bank_code'] == 'BSI')
                      <img src="{{ asset('image/bank_bsi.png') }}" style="width: 100px; height: 100px;" class="card-img-top" alt="...">
                      @elseif($getVA['bank_code'] == 'BJB')
                      <img src="{{ asset('image/bank_bjb.png') }}" style="width: 100px; height: 100px;" class="card-img-top" alt="...">
                        @else
                      <img src="{{ asset('image/bank_dbs.png') }}" style="width: 100px; height: 100px;" class="card-img-top" alt="...">

                    @endif
                </div>
            </div>
        </div>
        @endif
      {{-- @empty 

         @endforelse --}}

        </div>
    </div> 


     
    @endsection
