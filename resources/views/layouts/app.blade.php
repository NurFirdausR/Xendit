<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <title>Hello, world!</title>
  </head>
  <body>
      @include('navbar')
      <div class="row">
        @yield('content')
      </div>

    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
    integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    -->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
            //  CountDownTimer('{{$end}}', 'countdown');
			// 	function CountDownTimer(dt, id)
			// 	{
			// 		var end = new Date('{{$start}}');
			// 		var _second = 1000;
			// 		var _minute = _second * 60;
			// 		var _hour = _minute * 60;
			// 		var _day = _hour * 24;
			// 		var timer;
			// 		function showRemaining() {
			// 			var now = new Date();
			// 			var distance = end - now;
			// 			if (distance < 0) {

			// 				clearInterval(timer);
			// 				document.getElementById(id).innerHTML = '<b>Expired</b> ';
			// 				return;
			// 			}
			// 			var days = Math.floor(distance / _day);
			// 			var hours = Math.floor((distance % _day) / _hour);
			// 			var minutes = Math.floor((distance % _hour) / _minute);
			// 			var seconds = Math.floor((distance % _minute) / _second);

			// 			document.getElementById(id).innerHTML = days + 'days ';
			// 			document.getElementById(id).innerHTML += hours + 'hrs ';
			// 			document.getElementById(id).innerHTML += minutes + 'mins ';
			// 			document.getElementById(id).innerHTML += seconds + 'secs';
			// 			// document.getElementById(id).innerHTML +='<h2> Belum Expired</h2>';
			// 		}
			// 		timer = setInterval(showRemaining, 1000);
			// 	}
function copyToClipboard(element) {
  var $temp = $("<input>");
  $("body").append($temp);
  $temp.val($(element).text()).select();
  document.execCommand("copy");
  $temp.remove();
}
         $(document).ready(function () {
             
            $('body').on('submit','#formPemabayaranVA', function (e) {
                e.preventDefault();
                console.log('ok')

                axios.post("{{route('api.pembayaranVA')}}", {
                    bank: $('#bank').val(),
                    email: $('#email').val(),
                    price: $('#price').val(),
                    user_id: $('#user_id').val()
                })
                .then(function (response) {
                    $('#exampleModal').modal('hide');
                         $('#bank').val('')
                   $('#email').val('')
                     $('#price').val('')
                    console.log(response.data.data);
                    $('#CheckoutPembayaran').append(response.data.data);
                    Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.msg,
                    })

                })
                .catch(function (error) {
                    console.log(error.response.data.errors);
                    $.each(error.response.data.errors, function (indexInArray, valueOfElement) { 
                        $('#error_'+indexInArray).text(valueOfElement) 
                         
                    });
                        
                });
            });


            $('body').on('submit','#formCheckout', function (e) {
                e.preventDefault();

             
                axios.post("{{route('api.checkoutVA')}}", {
                    transfer_amount: $('#transfer_amount').val(),
                    bank_account_number: $('#bank_account_number').val(),
                    BANK_pembayaran: $('#BANK_pembayaran').val(),
                    payment_id: $('#payment_checkout_id').val()
                })
                .then(function (response) {
                 
                    // Swal.fire({
                    //             icon: 'success',
                    //             title: 'Berhasil melakukan pembayaran!',
                    //             text: response.msg,
                    //             })
                    const token = 'eG5kX2RldmVsb3BtZW50XzBKblJhM0xBV2RpU1Jia2FmUkVtc0tDNzZvTk5QRE1kc2hNaEJ1R1FmQlJtN2JTMzMxVHFnVVY2bTdkVmxEVA=='

                    // console.log()
                    // const token = Buffer.from(`${username}:${password}`, 'utf8').toString('base64')
                    console.log(response.data.data['transfer_amount'])
                    axios({
                        method: 'post',
                        url: 'https://api.xendit.co/pool_virtual_accounts/simulate_payment',
                        data: {
                                transfer_amount: response.data.data['transfer_amount'],
                                bank_account_number: response.data.data['bank_account_number'],
                                bank_code: response.data.data['bank_code']
                        },
                        header: {
                            "Content-Type": "application/json",
                            Authorization: "Basic "+token,
                            Accept: "application/json",
                            "Cache-Control": "no-cache",
                            Host: "api.xendit.co"

                        }
                    }).then(function (response) {
                                    $('#transfer_amount').val('')
                                $('#bank_account_number').val('')
                                $('#BANK_pembayaran').val('')
                                console.log(response);
                                Swal.fire({
                                icon: 'success',
                                title: 'Berhasil melakukan pembayaran!',
                                text: response.msg,
                                })
                            })
                            .catch(function (error) {
                                console.log(error);
                              
                                    
                            });
                })
                .catch(function (error) {
                    console.log(error);
                    // $.each(error.response.data.errors, function (indexInArray, valueOfElement) { 
                    //     $('#error_'+indexInArray).text(valueOfElement) 
                         
                    // });
                        
                });

            
            });
         });
          
    </script>
  </body>
</html>