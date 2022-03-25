@extends('frontend.layouts.app')
@section('title', ' | Transaction')
@section('css')
    <style>
    .select2-container--default .select2-selection--single {
        height: 38px !important;
        /* padding: 10px 16px; */
        padding: 5px;
        /* font-size: 18px;  */
        line-height: 1.33;
        border-radius: 6px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow b {
        top: 75% !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 26px !important;
    }

    .select2-container--default .select2-selection--single {
        border: 1px solid #CCC !important;
        box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.075) inset;
        transition: border-color 0.15s ease-in-out 0s, box-shadow 0.15s ease-in-out 0s;
    }
    </style>
@endsection
@section('content')

    <div class="w-full mt-5">
        @include('frontend.layouts.alert-message')
    </div>
    <div>
        <a href="{{url('transaction/create') }}"
           class="cursor-pointer mt-3 w-full inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
            Create
        </a>
    </div>
    <div class="w-full mt-5 flex items-center justify-between">
        <form class="w-full" action="{{ url('transaction') }}" method="GET">
            <div class="2xl:w-full">
                <div class="2xl:flex xl:flex lg:flex md:flex sm:flex">
                    <div class="mt-1 flex rounded-md mx-2 2xl:w-9/12 xl:w-9/12 lg:w-9/12 w-full">
                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-400 text-gray-700 text-sm">
                            ID
                        </span>
                        <input type="text" name="id" id="filter_id" value="{{isset($filters['id']) ? $filters['id'] : ''}}"
                               class="col-span-2 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md py-2 px-2"
                               placeholder="ID">
                    </div>
                    <div class="mt-1 flex rounded-md mx-2 2xl:w-9/12 xl:w-9/12 lg:w-9/12 w-full">
                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-400 text-gray-700 text-sm">
                            Title
                        </span>
                        <input type="text" name="title" id="filter_title" value="{{isset($filters['title']) ? $filters['title'] : ''}}"
                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md py-2 px-2"
                               placeholder="Title">
                    </div>
                    <div class="mt-1 flex rounded-md mx-2 2xl:w-9/12 xl:w-9/12 lg:w-9/12 w-full">
                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-400 text-gray-700 text-sm">
                            Remark
                        </span>
                        <input type="text" name="remark" id="filter_remark" value="{{isset($filters['remark']) ? $filters['remark'] : ''}}"
                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md py-2 px-2"
                               placeholder="Remark">
                    </div>
                    <div date-rangepicker datepicker-format="yyyy/mm/dd" class="mt-1 flex rounded-md mx-2 2xl:w-9/12 xl:w-9/12 lg:w-9/12 w-full">
                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-400 text-gray-700 text-sm">
                            Start Date
                        </span>
                        <input datepicker type="text" name="start_transferred_date" id="start_date" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md py-2 px-2"
                               placeholder="Transferred Date" autocomplete="off" value="{{ isset($filters['start_transferred_date']) ? $filters['start_transferred_date'] : '' }}">
                    </div>
                    <div date-rangepicker datepicker-format="yyyy/mm/dd" class="mt-1 flex rounded-md mx-2 2xl:w-9/12 xl:w-9/12 lg:w-9/12 w-full">
                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-400 text-gray-700 text-sm">
                            End Date
                        </span>
                        <input datepicker type="text" name="end_transferred_date" id="end_date" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md py-2 px-2"
                               placeholder="Transferred Date" autocomplete="off" value="{{ isset($filters['end_transferred_date']) ? $filters['end_transferred_date'] : '' }}">
                    </div>
                    <div class="mt-1 flex rounded-md mx-2 2xl:w-9/12 xl:w-9/12 lg:w-9/12 w-full">
                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-400 text-gray-700 text-sm">
                            Status
                        </span>
                        <select name="status" autocomplete="division" id="filter_status"
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md py-2.5 px-2 bg-white">
                            <option value="">All Status</option>
                            <option value="new"{{ (isset($filters['status']) && $filters['status'] == 'new') ? 'selected' : '' }}>New Transaction</option>
                            <option value="approved_user" {{ (isset($filters['status']) && $filters['status'] == 'approved_user') ? 'selected' : '' }}>Approved User</option>
                            <option value="approved_director" {{ (isset($filters['status']) && $filters['status'] == 'approved_director') ? 'selected' : '' }}>Approved Director</option>
                            <option value="rejected_director" {{ (isset($filters['status']) && $filters['status'] == 'rejected_director' ) ? 'selected' : '' }}>Rejected Director</option>
                            <option value="approved_finance" {{ (isset($filters['status']) && $filters['status'] == 'approved_finance' ) ? 'selected' : '' }}>Approved Finance</option>
                            <option value="rejected_finance" {{ (isset($filters['status']) && $filters['status'] == 'rejected_finance' ) ? 'selected' : '' }}>Rejected Finance</option>
                            <option value="transferred" {{ (isset($filters['status']) && $filters['status'] == 'transferred' ) ? 'selected' : '' }}>Transferred</option>
                            <option value="DONE" {{ (isset($filters['status']) && $filters['status'] == 'DONE' ) ? 'selected' : '' }}>Done</option>
                        </select>
                    </div>
                    <div class="mt-1 flex rounded-md mx-2 2xl:w-9/12 xl:w-9/12 lg:w-9/12 w-full">
                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-400 text-gray-700 text-sm">
                            Division
                        </span>
                        <select name="division" autocomplete="division" id="filter_division"
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md py-2.5 px-3 bg-white">
                            <option value="">All Division</option>
                            @foreach($divisions as $division)
                                <option value="{{ $division->id }}" {{ (isset($filters['division']) AND ($filters['division'] == $division->id)) ? 'selected' : '' }}>
                                    {{ $division->title }} Division
                                </option>
                            @endforeach
                        </select>
                    </div>
                    {{-- <button type="submit" class="mt-3 w-full inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Search
                    </button> --}}
                </div>
                <div class="2xl:flex xl:flex lg:flex md:flex sm:flex">
                    <div class="mt-2 flex rounded-md mx-2 2xl:w-9/12 xl:w-9/12 lg:w-9/12 w-full">
                        <span class="inline-flex items-center px-2 rounded-l-md border border-r-0 border-gray-300 bg-gray-400 text-gray-700 text-sm  w-6/12">
                          Push Or Not
                        </span>
                        <select name="is_push" id="filter_push_or_not" autocomplete="is_push"
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md py-2.5 px-2 bg-white">
                            <option value="">-- Select Push Transfer --</option>
                            <option value="push" {{ (isset($filters['is_push']) && $filters['is_push'] == 'push') ? 'selected' : '' }}>Push</option>
                            <option value="not" {{ (isset($filters['is_push']) && $filters['is_push'] == 'not') ? 'selected' : '' }}>Not</option>
                        </select>
                    </div>
                    <div class="mt-2 flex rounded-md mx-2 2xl:w-9/12 xl:w-9/12 lg:w-9/12 w-full">
                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-400 text-gray-700 text-sm">
                           Created
                        </span>
                        <label for="email" class="sr-only">Created By</label>
                        <input type="text" name="created_by" id="filter_created_by" value="{{isset($filters['created_by']) ? $filters['created_by'] : ''}}"
                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md py-2 px-2"
                               placeholder="Created By">
                    </div>
                    <input type="hidden" name="filter_for" id="filter_title" value="transaction"
                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md py-2 px-2"
                               placeholder="Title">
                    <div class="2xl:w-9/12 xl:w-9/12 lg:w-9/12 md:xl:w-9/12 mt-2 2xl:grid xl:grid lg:grid md:grid 2xl:justify-items-end xl:justify-items-end lg:justify-items-end md:justify-items-end">
                        <div class="">
                            <button type="submit" class="w-full px-4 py-2 border border-transparent shadow-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="checkDate()">
                                Search
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="w-full mt-5 flex items-center justify-start">
        <div class="count-transaction m-2">
            <label for="">Total Transaction : </label>
            <span id="countTransaction"><button class="btn btn-xs btn-primary m-1" id="btn-count-trx" onclick="countTransaction('ct')">Count Transaction</button></span>
        </div>
        <div class="sum-amount m-2">
            <label for="">Total Amount : </label>
            <span id="sumAmount"><button class="btn btn-xs btn-primary m-1" id="btn-sum-amount" onclick="countTransaction('sa')">Sum Amount</button></span>
        </div>
    </div>
    <div class="flex flex-col w-full bg-white shadow-sm mt-5">
        <div class='overflow-x-auto w-full'>
            <table class="min-w-full divide-y divide-gray-200 w-full">
                <thead class="bg-gray-50">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        ID
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Division
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Title
                    </th>
                    {{-- <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Description
                    </th> --}}
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Amount
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Created Date
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Created By
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Transferred Date
                    </th>
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Action</span>
                    </th>
                </tr>
                </thead>
                <tbody>
                @if(count($transactions) > 0)
                    @foreach ($transactions as $key => $item)
                        <tr class="{{($key % 2) == 0 ? 'bg-white' : 'bg-gray-50'}}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-500">
                                ID{{ $item->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-500">
                                <a href="{{ url('division/' . $item->division_id) }}" class="text-blue-700 font-bold capitalize">
                                    {{$item->division->title}}
                                </a>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" >
                                @if(strlen($item->title) > 25)
                                    <a href="javascript:void" style="color:blue" class="title" onclick="popupTitle('{{ $item->title }}')">{{ str_limit($item->title, $limit = 25, $end = '...') }}</a>
                                @else
                                    {{ $item->title }}                                   
                                @endif

                                @if($item->transaction_tax)
                                    <br>
                                    <span style="
                                        font-size: 10px;
                                        color: grey;
                                    ">
                                    Type: {{ $item->transaction_tax->type }} | Amount: Rp. {{ number_format($item->transaction_tax->amount, 0) }}
                                    </span>
                                @endif
                            </td>
                            {{-- <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-500">
                                {{$item->description}}
                            </td> --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-500">
                                Rp. {{ number_format($item->amount, 0) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-500">
                                <span class="text-xs">{!! $item->status_label !!}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-500">
                                {{ date("d-M-Y H:i:s", strtotime($item->created_at)) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-500">
                                {{ $item->created_by->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-500">
                                @if($item->transaction_status_transferred)
                                    {{ date("d-M-Y H:i:s", strtotime($item->transaction_status_transferred->created_at)) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-medium">
                                <a href="{{ url('transaction/' . $item->id) }}" class="text-indigo-600 hover:text-indigo-500">
                                    Detail
                                </a>
                                @if (empty($item->current_status) OR $item->current_status == 'approved_user')
                                    |<a href="{{url('transaction/edit/' . $item->id) }}" class="text-indigo-600 hover:text-indigo-500">
                                        Edit
                                    </a>
                                @endif
                                @if($item->enable_delete)
                                    |<a href="{{ url('transaction/delete-transaction/'.$item->id) }}" class="text-indigo-600 hover:text-indigo-500"
                                       onclick="return confirm('Are you sure?')">
                                        Delete
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @else
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-500" colspan="11" align="center">
                        Not Found
                    </td>
                @endif
                </tbody>
            </table>
        </div>
    </div>
    <div class="w-full flex justify-center mt-5">
        {!! $transactions->appends($filters)->links('frontend.components.paginate') !!}
    </div>
@endsection
@section('js')
<script>
    function popupTitle(e){
        Swal.fire({
            title: 'Info Title',
            text: e,
            icon: 'info',
            confirmButtonText: 'Close'
        })
    }

    function countTransaction(i) {
        var loading = '<i class="fa fa-refresh fa-spin" style="font-size:15px;color:black"></i> Loading...'
        if(i == 'ct') {
            $('#btn-count-trx').html(loading)
        } else {
            $('#btn-sum-amount').html(loading)
        }
        $.ajax({
            url : "{{ url('transaction/get-extra-info') }}",
            type: 'POST',
            data : {
                _token : "{{ csrf_token() }}",
                'id' : document.getElementById('filter_id').value,
                'title' : document.getElementById('filter_title').value,
                'remark' : document.getElementById('filter_remark').value,
                'division' : document.getElementById('filter_division').value,
                'status' : document.getElementById('filter_status').value,
                // 'transferred_date' : document.getElementById('filter_transferred_date').value,
                'is_push' : document.getElementById('filter_push_or_not').value,
                'created_by' : document.getElementById('filter_created_by').value
            },
            success: function (response) {
                if(response.status == true) {
                    let data = response.message
                    if(i == 'ct') {
                        $('#btn-count-trx').hide();
                        $('#countTransaction').html(response.message.countData);
                    } else {
                        $('#btn-sum-amount').hide();
                        let nominal = response.message.sumAmount
                        let formatRupiah = new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR" }).format(nominal);
                        $('#sumAmount').html(formatRupiah);
                    }
                }
            }
        })
    }

    function checkDate(){
        let startDate = document.getElementById('start_date').value;
        let endDate = document.getElementById('end_date').value;
        console.log(startDate, endDate)
        
        let convertStartDate = new Date(startDate)
        let convertEndDate = new Date(endDate)
        
        if(convertStartDate > convertEndDate) {
            alert('Your end transfer date is smaller than your start transfer date');
            event.preventDefault();
        }

        const dayConst = 1000 * 60 * 60 * 24;
        let diffInTime = convertEndDate.getTime() - convertStartDate.getTime();
        let diffInDays = Math.round(diffInTime / dayConst);

        if(diffInDays > 30) {
            alert('Data transfer taken can not be more than 1 month');
            event.preventDefault();
        }
    }

    $('#filter_division').select2({
        width: '100%'
    });

    $('#filter_status').select2({
        width: '100%'
    });

</script>
    
@endsection