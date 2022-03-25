@extends('frontend.layouts.app')
@section('title', '| Report Finance')
@section('css')
    <style>
        
        @media(max-width: 639px) {
            .div-input {
                width: 100% !Important;
            }
        }

    </style>
@endsection
@section('content')
    <div class="w-full mt-5">
        @include('frontend.layouts.alert-message')
    </div>
    <div class="w-full mt-5">
        <form class="w-full" action="{{ url('transaction/finance-report') }}" method="GET" id="userdetail">
            <div class="2xl:flex xl:flex lg:flex w-full">
                <div class="mt-1 flex rounded-md mx-2 2xl:w-4/12 xl:w-4/12 lg:w-4/12 w-full">
                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-400 text-gray-700 text-sm">
                        Title
                    </span>
                    <input type="text" name="title" value="{{isset($filters['title']) ? $filters['title'] : ''}}"
                        class=" col-span-2 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md py-2 px-2" placeholder="Title">
                </div>

                <div class="mt-1 flex rounded-md mx-2 2xl:w-4/12 xl:w-4/12 lg:w-4/12 w-full">
                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-400 text-gray-700 text-sm">
                        Division
                    </span>
                    <select name="division" autocomplete="division"
                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md py-2 px-2 bg-white">
                        <option value="">All Division</option>
                        @foreach($divisions as $division)
                            <option value="{{ $division->id }}" {{ (isset($filters['division']) AND ($filters['division'] == $division->id)) ? 'selected' : '' }}>
                                {{ $division->title }} Division
                            </option>
                        @endforeach
                    </select>
                </div>

                <div date-rangepicker datepicker-format="yyyy/mm/dd" class="mt-1 flex rounded-md mx-2 2xl:w-4/12 xl:w-4/12 lg:w-4/12 w-full">
                    <span class="px-2 py-2 rounded-l-md border border-r-0 border-gray-300 bg-gray-400 w-2/3 text-gray-700 text-sm w-full">
                        Strat Transfer Date
                    </span>
                    <input datepicker type="text" id="start_date" name="start_transferred_date" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md py-2 px-2 w-full"
                        placeholder="Start Transferred Date" autocomplete="off" value="{{ isset($filters['start_transferred_date']) ? $filters['start_transferred_date'] : '' }}">
                </div>

                <div date-rangepicker datepicker-format="yyyy/mm/dd" class="mt-1 flex rounded-md mx-2 2xl:w-4/12 xl:w-4/12 w-full">
                    <span class="px-2 py-2 rounded-l-md border border-r-0 border-gray-300 bg-gray-400 w-2/3 text-gray-700 text-sm">
                        End Transfer Date
                    </span>
                    <input datepicker type="text" id="end_date" name="end_transferred_date" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md py-2 px-2"
                        placeholder="End Transferred Date" autocomplete="off" value="{{ isset($filters['end_transferred_date']) ? $filters['end_transferred_date'] : '' }}">
                </div>
            </div>

            <div class="2xl:flex xl:flex lg:flex w-full mt-2">
                <div class="mt-1 flex rounded-md mx-2 2xl:w-4/12 xl:w-4/12 lg:w-4/12 w-full ">
                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-400 text-gray-700 text-sm w-6/12">
                        Push or not
                    </span>
                    <select name="is_push" autocomplete="is_push"
                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md py-2 px-2 bg-white">
                        <option value="">-- Select Push Transfer --</option>
                        <option value="push" {{ (isset($filters['is_push']) && $filters['is_push'] == 'push') ? 'selected' : '' }}>Push</option>
                        <option value="not" {{ (isset($filters['is_push']) && $filters['is_push'] == 'not') ? 'selected' : '' }}>Not</option>
                    </select>
                </div>
                <div class="mt-1 flex rounded-md mx-2 2xl:w-9/12 xl:w-9/12 lg:w-9/12 w-full 2xl:justify-items-end xl:justify-items-end lg:justify-items-end md:justify-items-end  2xl:justify-end xl:justify-end lg:justify-end ">
                    <a  style="float: right" class="mt-3 w-full inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm font-medium rounded-md text-indigo-100 bg-green-500 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" href="{{url('transaction/finance-report-xlsx?'. http_build_query(Input::all())) }}" >Export</a>
                    <button type="submit" style="float: right"
                            class="mt-3 w-full inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="checkDate()">
                        Search
                    </button>
                </div>
            </div>
        </form>
    </div>
    <div class="flex flex-col w-full bg-white shadow-sm mt-5">
        <div class='overflow-x-auto w-full'>
            <table class="min-w-full divide-y divide-gray-200 w-full">
                <thead class="bg-gray-50">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        No
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Title
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Amount
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Division
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Created At
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        TRANSFERRED DATE
                    </th>
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Action</span>
                    </th>
                </tr>
                </thead>
                <tbody>
                @if(count($resultTransactions) > 0)
                    @foreach ($resultTransactions as $key => $item)
                        <tr class="{{($key % 2) == 0 ? 'bg-white' : 'bg-gray-50'}}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $key + 1 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                @if(strlen($item->title) > 15)
                                    <a href="javascript:void" style="color:blue" class="title" onclick="popupTitle('{{ $item->title }}')">{{ str_limit($item->title, $limit = 15, $end = '...') }}</a>
                                @else
                                    {{ $item->title }}                                   
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                Rp. {{ number_format($item->amount, 0) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $item->division->title }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <span class="text-xs">{!! $item->status_label !!}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $item->created_at }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                @if($item->transaction_status_transferred)
                                    {{ date("d-M-Y H:i:s", strtotime($item->transaction_status_transferred->created_at)) }}
                                @else
                                    -
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
        {!! $resultTransactions->appends($filters)->links('frontend.components.paginate') !!}
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

        function checkDate(){
            let startDate = document.getElementById('start_date').value;
            let endDate = document.getElementById('end_date').value;
            
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
    </script>
@endsection