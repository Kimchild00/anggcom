@extends('frontend.layouts.app')

@section('js')

    <!-- TODO: Remove ".sandbox" from script src URL for production environment. Also input your client key in "data-client-key" -->
    <script src="https://app{{ (env('MIDTRANS_PROD') ? '' : '.sandbox') }}.midtrans.com/snap/snap.js"></script>
    <script type="text/javascript">
        document.getElementById('pay-button').onclick = function(){
            $("#pay-button").prop("disabled", true).html("Loading...");
            var requestBody =
                {
                    order_id: '{{ $userMemberOrder->id }}',
                    type:"MEMBERSHIP"
                };

            getSnapToken(requestBody, function(response){
                var result = JSON.parse(response);
                if (result.status == false) {
                    alert(result.message)
                    return;
                }
                result = result.message
                var options = {
                    showOrderId: false
                };
                snap.pay(result.token, {
                    onSuccess: function(result){
                        console.log('success');
                        console.log(result);
                    },
                    onPending: function(result){
                        $.ajax({
                            url: "{{ url('api/midtrans/save-info/' . $userMemberOrder->id) }}",
                            type: "post",
                            data: {
                                "data":result
                            },
                            success: function (response) {
                                location.reload();
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                // alert("Update error");
                            }
                        });
                        console.log('pending');
                        console.log(result);
                    },
                    onError: function(result){console.log('error');console.log(result);},
                    onClose: function(){console.log('customer closed the popup without finishing the payment');}
                });
            })
        };
        /**
         * Send AJAX POST request to checkout.php, then call callback with the API response
         * @param {object} requestBody: request body to be sent to SNAP API
         * @param {function} callback: callback function to pass the response
         */
        function getSnapToken(requestBody, callback) {
            var xmlHttp = new XMLHttpRequest();
            xmlHttp.onreadystatechange = function() {
                if(xmlHttp.readyState == 4 && xmlHttp.status == 200) {
                    $("#pay-button").prop("disabled", false).html("Pay Now");
                    callback(xmlHttp.responseText);
                }
            };
            xmlHttp.open("post", "{{ url('api/midtrans/checkout') }}");
            xmlHttp.send(JSON.stringify(requestBody));
        }
    </script>
@endsection

@section('content')
    <div class="w-full mt-5">
        @include('frontend.layouts.alert-message')
    </div>
    <div class="flex flex-col w-full bg-white shadow-sm mt-5">
        <div class='overflow-x-auto w-full'>
            <table class="min-w-full divide-y divide-gray-200 w-full">
                <tbody>
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice Number</th>
                    <td>{{ $userMemberOrder->invoice_number }}</td>
                </tr>
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <td>{{ $userMemberOrder->email }}</td>
                </tr>
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Package Name</th>
                    <td>{{ $userMemberOrder->package_name }}</td>
                </tr>
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Package Price</th>
                    <td>Rp. {{ number_format($userMemberOrder->package_price, 0) }}</td>
                </tr>
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Long Expired</th>
                    <td>{{ $userMemberOrder->long_expired }}</td>
                </tr>
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <td>
                        @if($userMemberOrder->paid_at)
                            <label class="label label-success">PAID</label> at: {{ date("d-M-Y H:i:s", strtotime($userMemberOrder->paid_at)) }}, with: {{ $userMemberOrder->paid_with }}
                        @else
                            <label class="label label-warning">Not PAID</label>
                        @endif
                    </td>
                </tr>
                @if(!$userMemberOrder->paid_at)
                    <tr>
                        <td colspan="2" scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            @if($userMemberOrder->dump)
                                <?php $dump   = \App\Helpers\HelperService::dumpComInvoice($userMemberOrder->dump);?>
                                <div class="alert alert-info">
                                    <strong>Info!</strong><br/>
                                    <p>
                                        Please make a payment to the Account No  <strong>{{ $dump['account_number'] }}</strong> at the bank {{ mb_strtoupper($dump['flag']) }}
                                        Rp. {{ number_format($dump['gross_amount'], 0) }}
                                    </p>
                                    @if(isset($dump['kode_perusahaan']))
                                        <p>Bank code: {{ $dump['kode_perusahaan'] }}</p>
                                    @endif
                                    <p>
                                                <span style="font-size: 2em; color: red">
                                                    Please read the tutorial for making payment transactions.
                                                    <a href="{{ $dump['pdf_url'] }}" target="_blank" class="btn btn-primary">click here</a>
                                                </span>
                                    </p>
                                    <p>
                                        If you want to cancel the payment this way please click
                                        <a href="{{ url('cancel-option/' . $userMemberOrder->id) }}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to cancel this payment ?')">Cancel</a>
                                    </p>
                                </div>
                            @else
                                <button type="button" class="btn btn-primary" id="pay-button">
                                    Pay Now
                                </button>
                            @endif
                        </td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>

@endsection
