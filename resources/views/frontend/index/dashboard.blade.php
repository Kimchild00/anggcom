@extends('frontend.layouts.app')
@section('title', '| Dashboard')
@section('content')
    <div class="w-full mt-5">
        @include('frontend.layouts.alert-message')
    </div>
    <div class="w-full">
        <div class="mt-5">
            <form class="w-full mb-5" action="{{ url('dashboard') }}" method="GET"">
                <div class="2xl:flex xl:flex lg:flex md:flex sm:flex">
                    <div date-rangepicker datepicker-format="yyyy/mm/dd" class="mb-2 flex rounded-md 2xl:mx-2 xl:mx-2 lg:mx-2 md:mx-2 2xl:w-4/12 xl:w-4/12 lg:w-4/12 w-full">
                        <span class="px-2 py-2 rounded-l-md border border-r-0 border-gray-300 bg-gray-400 w-2/3 text-gray-700 text-sm">
                            Strat Date
                        </span>
                        <input datepicker type="text" id="start_date" name="start_date" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md py-2 px-2 w-full"
                            placeholder="Start Date" autocomplete="off" value="{{ isset($filters['start_date']) ? $filters['start_date'] : '' }}">
                    </div>

                    <div date-rangepicker datepicker-format="yyyy/mm/dd" class="mb-2 flex rounded-md 2xl:mx-2 xl:mx-2 lg:mx-2 md:mx-2 2xl:w-4/12 xl:w-4/12 w-full">
                        <span class="px-2 py-2 rounded-l-md border border-r-0 border-gray-300 bg-gray-400 w-2/3 text-gray-700 text-sm">
                            End Date
                        </span>
                        <input datepicker type="text" id="end_date" name="end_date" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md py-2 px-2"
                            placeholder="End Date" autocomplete="off" value="{{ isset($filters['end_date']) ? $filters['end_date'] : '' }}">
                    </div>

                    <button type="submit" class="w-full px-4 py-2 border border-transparent shadow-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="checkDate()">
                        Search
                    </button>
                </div>
            </form>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="w-full bg-white rounded-lg shadow-sm overflow-hidden flex flex-col justify-center items-center">
                    <div class="text-center py-8 sm:py-6">
                        <p id="new_transaction" class="text-xl text-gray-700 font-bold mb-2">New Transaction</p>
                        <p class="text-base text-gray-900 font-normal">{{ $new }} Transactions</p>
                        <button  type="button" class="text-white-700 bg-white rounded-md p-2 mt-2" onclick="shortCut('new')">
                            <svg class="text-indigo-400 group-hover:text-gray-500 mr-4 flex-shrink-0 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="w-full bg-white rounded-lg shadow-sm overflow-hidden flex flex-col justify-center items-center">
                    <div class="text-center py-8 sm:py-6">
                        <p class="text-xl text-gray-700 font-bold mb-2">Approved Creator</p>
                        <p class="text-base text-gray-900 font-normal">{{ $approvedCreator }} Transactions</p>
                        <button  type="button" class="text-white-700 bg-white rounded-md p-2 mt-2" onclick="shortCut('approved_user')">
                            <svg class="text-indigo-400 group-hover:text-gray-500 mr-4 flex-shrink-0 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="w-full bg-white rounded-lg shadow-sm overflow-hidden flex flex-col justify-center items-center">
                    <div class="text-center py-8 sm:py-6">
                        <p class="text-xl text-gray-700 font-bold mb-2">Approved Director</p>
                        <p class="text-base text-gray-900 font-normal">{{ $approvedDirector }} Transactions</p>
                        <button  type="button" class="text-white-700 bg-white rounded-md p-2 mt-2" onclick="shortCut('approved_director')">
                            <svg class="text-indigo-400 group-hover:text-gray-500 mr-4 flex-shrink-0 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="w-full bg-white rounded-lg shadow-sm overflow-hidden flex flex-col justify-center items-center">
                    <div class="text-center py-8 sm:py-6">
                        <p class="text-xl text-gray-700 font-bold mb-2">Rejected Director</p>
                        <p class="text-base text-gray-900 font-normal">{{ $rejectedDirector }} Transactions</p>
                        <button  type="button" class="text-white-700 bg-white rounded-md p-2 mt-2" onclick="shortCut('rejected_director')">
                            <svg class="text-indigo-400 group-hover:text-gray-500 mr-4 flex-shrink-0 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="w-full bg-white rounded-lg shadow-sm overflow-hidden flex flex-col justify-center items-center">
                    <div class="text-center py-8 sm:py-6">
                        <p class="text-xl text-gray-700 font-bold mb-2">Approved Finance</p>
                        <p class="text-base text-gray-900 font-normal">{{ $approvedFinance }} Transactions</p>
                        <button  type="button" class="text-white-700 bg-white rounded-md p-2 mt-2" onclick="shortCut('approved_finance')">
                            <svg class="text-indigo-400 group-hover:text-gray-500 mr-4 flex-shrink-0 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="w-full bg-white rounded-lg shadow-sm overflow-hidden flex flex-col justify-center items-center">
                    <div class="text-center py-8 sm:py-6">
                        <p class="text-xl text-gray-700 font-bold mb-2">Rejected Finance</p>
                        <p class="text-base text-gray-900 font-normal">{{ $rejectedFinance }} Transactions</p>
                        <button  type="button" class="text-white-700 bg-white rounded-md p-2 mt-2" onclick="shortCut('rejected_finance')">
                            <svg class="text-indigo-400 group-hover:text-gray-500 mr-4 flex-shrink-0 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="w-full bg-white rounded-lg shadow-sm overflow-hidden flex flex-col justify-center items-center">
                    <div class="text-center py-8 sm:py-6">
                        <p class="text-xl text-gray-700 font-bold mb-2">Push Payment</p>
                        <p class="text-base text-gray-900 font-normal">{{ $transferred }} Transactions</p>
                        <button  type="button" class="text-white-700 bg-white rounded-md p-2 mt-2" onclick="shortCut('transferred')">
                            <svg class="text-indigo-400 group-hover:text-gray-500 mr-4 flex-shrink-0 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="w-full bg-white rounded-lg shadow-sm overflow-hidden flex flex-col justify-center items-center">
                    <div class="text-center py-8 sm:py-6">
                        <p class="text-xl text-gray-700 font-bold mb-2">DONE</p>
                        <p class="text-base text-gray-900 font-normal">{{ $done }} Transactions</p>
                        <button  type="button" class="text-white-700 bg-white rounded-md p-2 mt-2" onclick="shortCut('DONE')">
                            <svg class="text-indigo-400 group-hover:text-gray-500 mr-4 flex-shrink-0 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="w-full">
        <div class="mt-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="w-full bg-white rounded-lg shadow-sm overflow-hidden flex flex-col justify-center items-center">
                    <div class="text-center py-8 sm:py-6">
                        <p class="text-xl text-gray-700 font-bold mb-2">User Accounts</p>
                        <p class="text-base text-gray-900 font-normal">{{ $users }} Users</p>
                    </div>
                </div>
                <div class="w-full bg-white rounded-lg shadow-sm overflow-hidden flex flex-col justify-center items-center">
                    <div class="text-center py-8 sm:py-6">
                        <p class="text-xl text-gray-700 font-bold mb-2">Division</p>
                        <p class="text-base text-gray-900 font-normal">{{ $divisions }} Divisions</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        function checkDate(){
            let startDate = document.getElementById('start_date').value;
            let endDate = document.getElementById('end_date').value;
            
            let convertStartDate = new Date(startDate)
            let convertEndDate = new Date(endDate)
            
            if(convertStartDate > convertEndDate) {
                alert('Your end data date is smaller than your start date');
                event.preventDefault();
            }

            const dayConst = 1000 * 60 * 60 * 24;
            let diffInTime = convertEndDate.getTime() - convertStartDate.getTime();
            let diffInDays = Math.round(diffInTime / dayConst);

            if(diffInDays > 30) {
                alert('Data taken can not be more than 1 month');
                event.preventDefault();
            }
        }

        let params = ''
        function shortCut(params){
            let start_date = $('#start_date').val()
            let end_date = $('#end_date').val()
            let url = window.location.href
            url = url.split('/')
            if (start_date == "" && end_date == "" ) {
                console.log('ok')
                window.location.href = "/transaction?status="+ params +"&filter_for=transaction"
            } else {
                start_date = start_date.split('/')
                end_date = end_date.split('/')
                console.log(start_date[0]);
                window.location.href = "/transaction?start_transferred_date=" + start_date[0] + "%2F"+ start_date[1] + "%2F"+ start_date[2] +"&end_transferred_date=" + end_date[0] + "%2F" + end_date[1] + "%2F"+ end_date[2] +"&status="+ params +"&filter_for=transaction"
            } 
        } 

    </script>
@endsection
