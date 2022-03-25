@extends('frontend.layouts.app')
@section('title', ' | Detail Transaction')
@section('js')
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script type="text/javascript">

        $('.changeCOA').on('click', function(){
            MODAL.show('.coa-modal')
        })

        $('.FinanceNote').on('click', function(){
            MODAL.show('.finance-note');
        })

        $('.RejectNote').on('click', function(){
            MODAL.show('.reject-note');
        })

        $('.EditFinanceNote').on('click', function(){
            MODAL.show('.finance-note');

        })

        const isMasterFinance = "{{ ($isMasterFinance) ? true : false }}";
        const isFinance       = "{{ $isFinance }}";
        
        if(isMasterFinance || isFinance){
            $('#btn-finance-note').attr('disabled', false);
        }else{
            $('#btn-finance-note').attr('disabled', true);
            $('#btn-finance-note').css('cursor', 'not-allowed');
        }

        changeCOA();
        function changeCOA() {
            $.ajax({
                url: "{{ url('transaction/chart-of-accounts/get/') }}/{{ $transaction->division_id }}",
                type: "get",
                success: function (response) {
                    $("#journal_field_id").html('');
                    $("#division_field_id").attr('disabled', false)
                    if (response.status == true) {
                        var data = response.message
                        if (data.length > 0) {
                            var i;
                            $("#journal_field_id").append('' +
                                '<option value="">-- Select Account --</option>');
                            for (i = 0; i < data.length; ++i) {
                                $("#journal_field_id").append('' +
                                    '<option value="' + data[i].id + '">' + data[i].text + '</option>');
                            }
                            $("#journal_field_id").select2()
                        }
                    }
                },
                error: function() {
                }
            });
        }

        async function getSaldoFlip(id) {
            var loading = '<i class="fa fa-refresh fa-spin" style="font-size:15px;color:black"></i> Loading...';
            $('#saldoFlip').html(loading)
            const url = "{{ url('transaction/get-saldo-flip') }}" + "/" + id
            const cek = await fetch(url)
            const result = await cek.json();

            const button = $('#saldoFlip').hide();
            const amount = $('#amount').html(result.status ? 'Rp. ' + result.message : result.message);
        }

        function pushManual(id) {
            swal({
                title : "Are you sure?",
                text : "You will push manual transaction to journal",
                icon : "warning",
                buttons : true,
                dangerMode : true,
            }).then( function(isConfirm){
                if(isConfirm) {
                    this.disabled = true;
                    pushManualJournal(id)     
                }
            })
        }

        async function pushManualJournal(id) {
            let button = document.getElementById(id)
            button.setAttribute('disabled', true)
            const url = "{{ url('transaction/push-manual-journal') }}" + "/" + id 
            const response = await fetch(url)
            let result = await response.json();
            if(result.status) {
                icon = "success"
                swal("Success push manual Journal", {
                icon: "success",});
                button.removeAttribute('disabled')
            } else {
                swal("Failed push manual Journal ! : " + result.data, {
                icon: "error",})
                button.removeAttribute('disabled')
            }
        }

        function financeNoteInput() 
        {
            let financeNotedValue = document.getElementById('finance_noted_input').value
            if(financeNotedValue.length > 0) {
                document.getElementById('btn_finance_noted').removeAttribute('disabled')
                document.getElementById("btn_finance_noted").style.cursor = "pointer"; 
            } else {
                document.getElementById('btn_finance_noted').setAttribute('disabled', 'disabled')
                document.getElementById("btn_finance_noted").style.cursor = "not-allowed"; 
            }
        }
    </script>
@endsection

@section('content')
    <div class="w-full md:w-3/5 mt-5">
        @include('frontend.layouts.alert-message')
    </div>
    <div class="w-full flex mt-5">
        @if(empty($transaction->current_status) AND $transaction->user_id == \Illuminate\Support\Facades\Auth::user()->id)
        <a href="{{ url('transaction/approve/user/' . $transaction->id) }}" class="cursor-pointer w-full inline-flex items-center justify-center px-2 text-xs py-1 border border-transparent shadow-sm font-medium rounded-md text-gray-600 bg-green-200 border-green-300 hover:bg-green-400 focus:outline-none focus:ring-2 focus:ring-offset-2  sm:ml-3 sm:w-auto"  onclick="return confirm('Are you sure to approve this transaction?')">
            Approve User / Creator
        </a>
        @elseif(empty($transaction->current_status))
            <i class="fa fa-exclamation-circle"></i><span class="text-blue-700"> Waiting to be approved by Creator</span>
        @endif

        @if($transaction->current_status == 'approved_user' && \Illuminate\Support\Facades\Auth::user()->email == $transaction->division->director_email)
            <a href="{{ url('transaction/approve/director/' . $transaction->id) }}" class="cursor-pointer w-full inline-flex items-center justify-center px-2 text-xs py-1 border border-transparent shadow-sm font-medium rounded-md text-gray-600 bg-green-200 border-green-300 hover:bg-green-400 focus:outline-none focus:ring-2 focus:ring-offset-2  sm:ml-3 sm:w-auto" onclick="return confirm('Are you sure to approve this transaction?')">
                Approve Director
            </a>
            <button class="cursor-pointer w-full inline-flex items-center justify-center px-2 text-xs py-1 border border-transparent shadow-sm font-medium rounded-md text-gray-600 bg-red-200 border-green-300 hover:bg-red-400 focus:outline-none focus:ring-2 focus:ring-offset-2  sm:ml-3 sm:w-auto RejectNote"  >
                Reject Director
            </button>
        @elseif($transaction->current_status == 'approved_user')
            <i class="fa fa-exclamation-circle"></i><span class="text-blue-700"> Waiting to be approved by Director</span>
        @endif
        @if($transaction->current_status == 'approved_director' AND $isFinance)
            <a href="{{ url('transaction/approve/finance/' . $transaction->id) }}" class="cursor-pointer w-full inline-flex items-center justify-center px-2 text-xs py-1 border border-transparent shadow-sm font-medium rounded-md text-gray-600 bg-green-200 border-green-300 hover:bg-green-400 focus:outline-none focus:ring-2 focus:ring-offset-2  sm:ml-3 sm:w-auto" onclick="return confirm('Are you sure to approve this transaction?')">
                Approve Finance
            </a>
            <button class="cursor-pointer w-full inline-flex items-center justify-center px-2 text-xs py-1 border border-transparent shadow-sm font-medium rounded-md text-gray-600 bg-red-200 border-green-300 hover:bg-red-400 focus:outline-none focus:ring-2 focus:ring-offset-2  sm:ml-3 sm:w-auto RejectNote"  >
                Reject Finance
            </button>
        @elseif($transaction->current_status == 'approved_director')
            <i class="fa fa-exclamation-circle"></i><span class="text-blue-700"> Waiting to be approved by Finance</span>
        @endif
        @if($transaction->aprove_finance_is_master_finance)
            @if($transaction->finance_noted_have_journal)
                @if ($transaction->division->type_disbursement == "flip")
                    <a href="{{ url('transaction/push-payment/' . $transaction->id) }}" class="cursor-pointer w-full inline-flex items-center justify-center px-2 text-xs py-1 border border-transparent shadow-sm font-medium rounded-md text-gray-600 bg-gray-200 border-gray-300 hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2  sm:ml-3 sm:w-auto" onclick="return confirm('Are you sure to push payment this transaction?')">
                        Push Payment Flip
                    </a>
                @else
                    <a href="{{ url('transaction/push-payment/' . $transaction->id) }}" class="cursor-pointer w-full inline-flex items-center justify-center px-2 text-xs py-1 border border-transparent shadow-sm font-medium rounded-md text-gray-600 bg-gray-200 border-gray-300 hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2  sm:ml-3 sm:w-auto" onclick="return confirm('Are you sure to push payment this transaction?')">
                        Push Payment Xendit
                    </a>
                @endif
            @else
                <span class="text-red-700">You have to add finance noted first, before push payment</span>
            @endif
            <button class="cursor-pointer w-full inline-flex items-center justify-center px-2 text-xs py-1 border border-transparent shadow-sm font-medium rounded-md text-gray-600 bg-red-200 border-green-300 hover:bg-red-400 focus:outline-none focus:ring-2 focus:ring-offset-2  sm:ml-3 sm:w-auto RejectNote"  >
                Reject Master Finance
            </button>
        @elseif($transaction->current_status == 'approved_finance')
            <i class="fa fa-exclamation-circle"></i><span class="text-blue-700"> Waiting to be  pushed by Master Finance</span>
        @endif
    </div>

    <div class="w-full 2xl:flex xl:flex lg:flex md:flex">
        <div class="2xl:w-1/2 xl:w-1/2 lg:w-1/2 px-2 md:w-3/5 w-full mt-5 bg-white px-3 py-5 rounded-sm">
            @if($transaction->show_balance)
            <label for="" class="m-2">
                Saldo : 
                <span id="amount"><button class="btn btn-xs btn-primary saldo-flip" onclick="getSaldoFlip({{ $transaction->id }})" id="saldoFlip">Check Saldo</button></span>
            </label>
            @else
                <label for="">Balance check is not showing</label>
            @endif
            <div class="w-full text-sm mb-5">
                <h1 class="text-base font-bold uppercase">Summary</h1>
                <table class="min-w-full divide-y divide-gray-200 mt-3">
                    <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                            Last Status
                        </th>
                        <td class="text-gray-900">
                            {!! $transaction->status_label !!}
                        </td>
                    </tr>
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                            Title
                        </th>
                        <td class="text-gray-900">
                            {{ $transaction->title }}
                        </td>
                    </tr>
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                            Description
                        </th>
                        <td class="text-gray-900">
                            {{ $transaction->description }}
                        </td>
                    </tr>
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                            Amount
                        </th>
                        <td class="text-gray-900">
                            Rp {{ number_format($transaction->amount, 0) }}
                        </td>
                    </tr>
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                            Remark
                        </th>
                        <td class="text-gray-900">
                            {{ $transaction->remark }}
                        </td>
                    </tr>
                    @if($transaction->ott_code > 0)
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                Chart of Account (Journal)
                            </th>
                            <td class="text-gray-900">
                                {{ $transaction->ott_name }} | {{ $transaction->ott_code }}
                            </td>
                        </tr>
                    @else
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                Chart of Account (Journal)
                            </th>
                            <td>
                                <i class="fa fa-exclamation-circle"></i><span class="text-blue-700">
                                    {{ $transaction->division->title }} division did not manage journal accounts when this transaction was made
                                </span>
                            </td>
                        </tr>
                    @endif
                    @if($transaction->division->division_journal)

                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                Change Chart of Account (Journal)
                            </th>
                            <td>
                                <button class="bg-blue-400 p-2 text-sm text-white inline-flex items-center space-x-2 rounded mr-1 changeCOA">
                                    Change COA
                                </button>
                            </td>
                        </tr>
                    @endif
                    @if(count($transaction->transaction_files) > 0)
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">File report</th>
                            <td>
                                @foreach($transaction->transaction_files as $file)
                                    <a href="{{ env('CDN_URL') }}images/{{ $file->file_name }}" target="_blank" download="" class="btn btn-success btn-sm" title="Download File Report">
                                        <i class="fa fa-download"></i>
                                    </a> | Amount: Rp. {{ number_format($file->amount) }} | Note: {{ $file->note }}
                                    <br>
                                @endforeach
                            </td>
                        </tr>
                    @endif
                    </thead>
                </table>
            </div>

            <div class="w-full text-sm mb-5">
                <h1 class="text-base font-bold uppercase">Inquiry / Account Number Detail</h1>
                <table class="min-w-full divide-y divide-gray-200 mt-3">
                    <thead class="bg-gray-50">
                    @if($transaction->inquiry)
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                Name BY Server
                            </th>
                            <td class="text-gray-900">
                                {{ $transaction->inquiry->name_by_server }}
                            </td>
                        </tr>
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                Account Number
                            </th>
                            <td class="text-gray-900">
                                {{ $transaction->inquiry->account_number }}
                            </td>
                        </tr>
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                Bank Code
                            </th>
                            <td class="text-gray-900 uppercase">
                                {{ $transaction->inquiry->bank_code }}
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td class="text-gray-900 uppercase" colspan="2">
                                Inquiry not set
                            </td>
                        </tr>
                    @endif
                    </thead>
                </table>
            </div>

            <div class="w-full text-sm mb-5">
                <h1 class="text-base font-bold uppercase">Status History</h1>
                <div class="flow-root mt-5">
                    <ul role="list" class="">
                        @foreach($transaction->transaction_statuses as $key => $status)
                            <li>
                                <div class="relative pb-8">
                                    @if ($status->status_label !== 'DONE')
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        @if ($status->status_label == 'Rejected Director' || $status->status_label == 'Rejected Finance' || $status->status_label == 'Rejected Master' || $status->status_label == 'FAILED' )
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-red-500 flex items-center justify-center ring-8 ring-white">
                                                    <!-- Heroicon name: solid/check -->
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                      </svg>
                                                    </svg>
                                                </span>
                                            </div>
                                        @else
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                    <!-- Heroicon name: solid/check -->
                                                    <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                </span>
                                            </div>
                                        @endif
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-900">{{$status->status_label}} 
                                                    <a href="#" class="font-medium text-gray-900">{{ $status->user ? $status->user->name : 'AUTO'}}</a>
                                                <br>
                                                    @if ($status->status_label == 'Rejected Director' || $status->status_label == 'Rejected Finance' || $status->status_label == 'Rejected Master' || $status->status_label == 'FAILED')
                                                        @if ($status->message)
                                                            <i style="color: red">({{$status->message}})</i>
                                                        @endif
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="text-right text-xs whitespace-nowrap text-gray-900">
                                                <time datetime="2020-09-28">{{ date("d-M-Y H:i:s", strtotime($status->created_at)) }}</time>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="2xl:w-1/2 xl:w-1/2 lg:w-1/2 px-2 md:w-3/5 w-full mt-5 bg-white px-3 py-5 rounded-sm">
            @if($transaction->transaction_flip)
            <div class="w-full text-sm mb-5">
                <h1 class="text-base font-bold uppercase">Flip Detail</h1>
                <table class="min-w-full divide-y divide-gray-200 mt-3">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                Server Status
                            </th>
                            <td class="text-gray-900">
                                {{ $transaction->transaction_flip->server_status }}
                            </td>
                        </tr>
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                Server ID
                            </th>
                            <td class="text-gray-900">
                                {{ $transaction->transaction_flip->server_id }}
                            </td>
                        </tr>
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                Fee
                            </th>
                            <td class="text-gray-900">
                                Rp. {{ number_format($transaction->transaction_flip->fee, 0) }}
                            </td>
                        </tr>
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                Server Receipt
                            </th>
                            <td class="text-gray-900 uppercase">
                                <a href="{{ env('CDN_URL') . 'images/' . $transaction->transaction_flip->server_receipt }}" target="_blank" download="" class="btn btn-success btn-sm" title="Download Server Receipt">
                                    <i class="fa fa-download"></i>
                                </a>
                            </td>
                        </tr>
                    </thead>
                </table>
            </div>
            @endif
        
           
            @if ($transaction->check_button_push)
                <div class="w-full text-sm mb-5">
                    <button class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded" id="{{ $transaction->id }}" onclick="pushManual(this.id)">
                        Push Manual
                    </button>
                </div>
            @endif

            @if($transaction->transaction_tax)
            <div class="w-full text-sm mb-5">
                <h1 class="text-base font-bold uppercase">Tax Detail</h1>
                <table class="min-w-full divide-y divide-gray-200 mt-3">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                Type
                            </th>
                            <td class="text-gray-900">
                                {{ $transaction->transaction_tax->type }}
                            </td>
                        </tr>
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                Amount
                            </th>
                            <td class="text-gray-900">
                                Rp. {{ number_format($transaction->transaction_tax->amount, 0) }}
                            </td>
                        </tr>
                    </thead>
                </table>
            </div>
            @endif

            <div class="w-full text-sm mb-5">
                <h1 class="text-base font-bold uppercase">Division Detail</h1>
                <table class="min-w-full divide-y divide-gray-200 mt-3">
                    <thead class="bg-gray-50">
                    @if($transaction->division)
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                Division
                            </th>
                            <td class="text-gray-900">
                                <a href="{{ url('division/' . $transaction->division->id) }}" class="btn btn-primary">{{ $transaction->division->title }}</a>
                            </td>
                        </tr>
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                Director Email
                            </th>
                            <td class="text-gray-900">
                                {{ $transaction->division->director_email }}
                            </td>
                        </tr>
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                Director Phone
                            </th>
                            <td class="text-gray-900 uppercase">
                                {{ $transaction->division->director_phone }}
                            </td>
                        </tr>
                        
                        @if ($transaction->division->type_disbursement == "flip")
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                    Flip Name
                                </th>
                                <td class="text-gray-900 uppercase">
                                    {{ $transaction->division->division_flip->flip_name }}
                                </td>
                            </tr>
                        @else 
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                    Xendit Name
                                </th>
                                <td class="text-gray-900 uppercase">
                                    {{ $transaction->division->division_xendit->xendit_name }}
                                </td>
                            </tr>
                        @endif
                        
                        @if($transaction->division->division_journal)
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                    Journal Name
                                </th>
                                <td class="text-gray-900 uppercase">
                                    {{ $transaction->division->division_journal->journal_name }}
                                </td>
                            </tr>
                        @else
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                    Journal
                                </th>
                                <td class="text-gray-900 uppercase">
                                    This division not have a journal
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                Finance note
                                <p class="lowercase" style="font-size: 10px; color: red">*Only Finance or Master Finance can create finance note</p>
                            </th>
                            <td class="text-gray-900 uppercase">
                                {{ ($transaction->transaction_finance_noted) ? $transaction->transaction_finance_noted->noted : '-' }} | {{ ($transaction->transaction_finance_noted) ? $transaction->transaction_finance_noted->created_by : '-' }}
                                @if($transaction->check_status_finance_note)
                                    @if(is_null($transaction->transaction_finance_noted ))
                                        <button class="bg-blue-400 p-2 mt-2 text-sm text-white inline-flex items-right space-x-2 rounded mr-1 FinanceNote" id="btn-finance-note">
                                            Add finance noted
                                        </button>
                                        
                                    @elseif($transaction->transaction_finance_noted && $isMasterFinance)
                                        <button class="bg-green-400 p-2 mt-2 text-sm text-white inline-flex items-right space-x-2 rounded mr-1 EditFinanceNote">
                                            Edit finance noted
                                        </button>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td class="text-gray-900 uppercase" colspan="2">
                                division not set
                            </td>
                        </tr>
                    @endif
                    </thead>
                </table>
            </div>
        </div>
    </div>


    {{-- COA modal --}}
    <div class="modal coa-modal z-3 h-screen w-full fixed left-0 top-0 flex justify-center items-center bg-black bg-opacity-50 hidden">
        <div class="bg-white rounded shadow-lg w-10/12 md:w-2/5">
            <div class="border-b px-4 py-2 flex justify-between items-center">
                <h3 class="font-semibold text-lg">Change Chart of Account</h3>
                <button class="text-black close-modal">&cross;</button>
            </div>
            <div class="w-full p-3">
                <form action="{{ url('transaction/chart-of-accounts/change') }}" method="POST" id="financeForm">
                    {{ csrf_field() }}
                    <input type="hidden" name="transaction_id" value="{{ $transaction->id }}">
                    <div class="sm:col-span-3 mb-5">
                        <label for="role" class="block text-sm font-medium text-gray-700">
                            Finance  <span class="text-red-600 font-bold italic">*) required</span>
                        </label>
                        <div class="mt-1 w-full">
                            <select name="coa" autocomplete="role" required id="journal_field_id"
                                    class="rounded-md focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-200  py-2.5 px-2 bg-white">
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="flex justify-end items-center w-100 border-t p-3">
                <button class="bg-red-600 hover:bg-red-700 px-3 py-1 rounded text-white mr-1 close-modal">Cancel</button>
                <button class="bg-blue-600 hover:bg-blue-700 px-3 py-1 rounded text-white submitFinanceModal">Submit</button>
            </div>
        </div>
    </div>
    <div class="modal finance-note z-3 h-screen w-full fixed left-0 top-0 flex justify-center items-center bg-black bg-opacity-50 hidden">
        <div class="bg-white rounded shadow-lg w-10/12 md:w-2/5">
            <div class="border-b px-4 py-2 flex justify-between items-center">
                <h3 class="font-semibold text-lg">{{ ($transaction->transaction_finance_noted) ? "Edit" : 'Add' }} note finance</h3>
                <button class="text-black close-modal">&cross;</button>
            </div>
            <div class="w-full p-3">
                <form action="{{ url('transaction/finance-note') }}" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" name="transaction_id" value="{{ $transaction->id }}">
                    <input type="hidden" name="finance_note_id" id="finance_note_id" value='{{ !is_null($transaction->transaction_finance_noted) ? $transaction->transaction_finance_noted->id : ''  }}'>
                    <div class="sm:col-span-3 mb-5">
                        <input type="hidden" value="">
                        <label for="role" class="block text-sm font-medium text-gray-700">
                            Note Finance  <span class="text-red-600 font-bold italic">*) required</span>
                        </label>
                        <div class="mt-1">
                            <input type="text" name="note"
                                value="{{ !is_null($transaction->transaction_finance_noted) ? $transaction->transaction_finance_noted->noted : '' }}"
                                autocomplete="note"
                                class="border border-gray-300 px-2 py-2 focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" id="finance_noted_input" onchange="financeNoteInput()" required>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end items-center w-100 border-t p-3">
                    <button type="button" class="bg-red-600 hover:bg-red-700 px-3 py-1 rounded text-white mr-1 close-modal">Cancel</button>
                    <button class="bg-blue-600 hover:bg-blue-700 px-3 py-1 rounded text-white submitFinanceModal"  id="btn_finance_noted" disabled style="cursor: not-allowed;">Submit</button>
                </div>
            </form>
        </div>
    </div>

    {{-- modal reject --}}
    <div class="modal reject-note z-3 h-screen w-full fixed left-0 top-0 flex justify-center items-center bg-black bg-opacity-50 hidden">
        <div class="bg-white rounded shadow-lg w-10/12 md:w-2/5">
            <div class="border-b px-4 py-2 flex justify-between items-center">
                <h3 class="font-semibold text-lg">{{ ($transaction->transaction_finance_noted) ? "Edit" : 'Add' }} note reject</h3>
                <button class="text-black close-modal">&cross;</button>
            </div>
            <div class="w-full p-3">
                @if ($transaction->current_status == 'approved_user')
                    <form action="{{ url('transaction/reject/director/' . $transaction->id) }}" method="POST">
                @endif
                @if ($transaction->current_status == 'approved_director')
                    <form action="{{ url('transaction/reject/finance/' . $transaction->id) }}" method="POST">
                @endif
                @if ($transaction->current_status == 'approved_finance')
                    <form action="{{ url('transaction/reject/master-finance/' . $transaction->id) }}" method="POST">
                @endif
                    {{ csrf_field() }}
                    <input type="hidden" name="transaction_id" value="{{ $transaction->id }}">
                    <input type="hidden" name="finance_note_id" id="finance_note_id" value='{{ !is_null($transaction->transaction_finance_noted) ? $transaction->transaction_finance_noted->id : ''  }}'>
                    <div class="sm:col-span-3 mb-5">
                        <input type="hidden" value="">
                        <label for="role" class="block text-sm font-medium text-gray-700">
                            Note Reject  <span class="text-red-600 font-bold italic"></span>
                        </label>
                        <div class="mt-1">
                            <input type="text" name="note"
                                value=""
                                autocomplete="note"
                                class="border border-gray-300 px-2 py-2 focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>
                </div>
                <div class="flex justify-end items-center w-100 border-t p-3">
                    <button type="button" class="bg-red-600 hover:bg-red-700 px-3 py-1 rounded text-white mr-1 close-modal">Cancel</button>
                    <button class="bg-blue-600 hover:bg-blue-700 px-3 py-1 rounded text-white submitFinanceModal" onclick="return confirm('Are you sure to reject this transaction?')">Submit</button>
                </div>
            </form>
        </div>
    </div>
@endsection
