@extends('frontend.layouts.app')
@section('title', isset($inquiry) ? ' | Edit Inquiry' : ' | Create Inquiry')
@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
@endsection
@section('content')
    <div class="w-full mt-5">
        <div class="w-full md:w-3/5">
            @include('frontend.layouts.alert-message')
        </div>
        <div class="w-full md:w-3/5 bg-white rounded-sm">
            <form action="{{ isset($inquiry)  ? url('inquiry/update') : url('inquiry') }}" method="post"
                  class="w-full py-5 px-3">
                @if (isset($inquiry))
                    <p class="pb-5 text-sm text-gray-600 font-bold"> Edit inquiry {{ $inquiry->name_by_input }}</p>
                    <input type="hidden" name="id" value="{{ $inquiry->id }}">
                @else
                    <p class="pb-5 text-sm text-gray-600 font-bold"> Create inquiry</p>
                @endif
                {{ csrf_field() }}
                <div class="sm:col-span-3 mb-5">
                    <label for="name_by_input" class="block text-sm font-medium text-gray-700">
                        Account Name <span class="text-red-600 font-bold italic">*) required</span>
                    </label>
                    <div class="mt-1">
                        <input type="text" name="name_by_input" autocomplete="name_by_input"
                               value="{{ isset($inquiry) ? $inquiry->name_by_input : '' }}" required
                               class="border border-gray-300 px-2 py-2 focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                </div>
                <div class="sm:col-span-3 mb-5">
                    <label for="account_number" class="block text-sm font-medium text-gray-700">
                        Account Number <span class="text-red-600 font-bold italic">*) required</span>
                    </label>
                    <div class="mt-1">
                        <input type="text" name="account_number" autocomplete="account_number"
                               value="{{ isset($inquiry) ? $inquiry->account_number : '' }}" required
                               class="border border-gray-300 px-2 py-2 focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                </div>
                <div class="sm:col-span-3 mb-5">
                    <label for="bank" class="block text-sm font-medium text-gray-700">
                        Bank <span class="text-red-600 font-bold italic">*) required</span>
                    </label>
                    <div class="mt-1">
                        <select name="bank" autocomplete="bank" required
                                class="rounded-md focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-200  py-2.5 px-2 bg-white capitalize" id="listBanks">
                            @foreach($banks as $item)
                                <option value="{{ $item['bank_code'] }}" {{ isset($inquiry) ? ($inquiry->bank_code == $item['bank_code'] ? 'selected' : '') : '' }}>
                                    {{ strtoupper($item['name']) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="sm:col-span-3 mb-5">
                    <label for="bank_city" class="block text-sm font-medium text-gray-700">
                        Bank City <span class="text-red-600 font-bold italic">*) required</span>
                    </label>
                    <div class="mt-1">
                        <select name="bank_city" autocomplete="bank_city" required
                                class="rounded-md focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-200  py-2.5 px-2 bg-white capitalize" id="listBankCities">
                            @foreach($cities as $key=>$city)
                                <option value="{{ $key }}|{{ $city }}" {{ isset($inquiry) ? ($inquiry->bank_city_id == $key ? 'selected' : '') : '' }}>
                                    {{ $city }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="w-full">
                    <button type="submit"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Save
                    </button>
                    <a href="{{ url('inquiry') }}" class="btn btn-warning">
                        Back
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#listBanks , #listBankCities").select2({
                    sorter: function(data) {
                        return data.sort(function(a , b) {
                            return a.text < b.text ? -1 : a.text > b.text ? 1 : 0
                        });
                    }
                }).on("select2:select", function (e) { 
	                $('.select2-selection__rendered li.select2-selection__choice').sort(function(a, b) {
        	        return $(a).text() < $(b).text() ? -1 : $(a).text() > $(b).text() ? 1 : 0;
                }).prependTo('.select2-selection__rendered');
            });
        })
    </script>
@endsection
