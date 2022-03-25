@extends('frontend.layouts.app')
@section('title', ' | Inquiry')
@section('content')

    <div class="w-full mt-5">
        @include('frontend.layouts.alert-message')
    </div>
    <div class="w-full mt-5 2xl:flex xl:flex lg:flex items-center 2xl:justify-between xl:justify-between lg:justify-between">
        <div class="2xl:w-2/6 xl:w-2/6 md:w-2/6 sm:w-full">
            <a href="{{url('inquiry/create') }}"
               class="cursor-pointer mt-3 w-full inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                Create
            </a>
        </div>
        <form class="w-full sm:flex sm:items-center justify-end mt-2" action="{{ url('inquiry') }}" method="GET">
            <div class="w-full sm:max-w-xs m-1">
                <div class="mt-1 flex w-full">
                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-400 text-gray-700 text-sm">
                        Name
                    </span>
                    <input type="text" name="name" value="{{isset($filters['name']) ? $filters['name'] : ''}}"
                        class="col-span-2 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md py-2 px-2" placeholder="name">
                </div>
            </div>
            <div class="w-full sm:max-w-xs m-1">
                <div class="mt-1 flex w-full">
                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-400 text-gray-700 text-sm w-full">
                        Account Number
                    </span>
                    <input type="text" name="account_number"
                       value="{{ isset($filters['account_number']) ? $filters['account_number'] : '' }}"
                       class="col-span-2 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md py-2 px-2
                       placeholder="Account number">
                </div>
            </div>
            <div class="w-full sm:max-w-xs m-1">
                <div class="mt-1 flex w-full">
                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-400 text-gray-700 text-sm w-6/12">
                        Status
                    </span>
                    <select name="status" id="status" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md py-2 px-2 bg-white py-2 px-2">
                        <option value="">All Status</option>
                        <option value="SUCCESS" {{ (isset($filters['status']) && $filters['status'] == 'SUCCESS') ? 'selected' : ''}}>SUCCESS</option>
                        <option value="PENDING" {{ (isset($filters['status']) && $filters['status'] == 'PENDING') ? 'selected' : ''}}>PENDING</option>
                        <option value="INVALID_ACCOUNT_NUMBER" {{ (isset($filters['status']) && $filters['status'] == 'INVALID_ACCOUNT_NUMBER') ? 'selected' : ''}}>INVALID ACCOUNT NUMBER</option>
                    </select>
                </div>
            </div>
            <button type="submit"
                    class="mt-3 w-full inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                Search
            </button>
        </form>
    </div>
    <div class="flex flex-col w-full bg-white shadow-sm mt-5">
        <div class='overflow-x-auto w-full'>
            <table class="min-w-full divide-y divide-gray-200 w-full">
                <thead class="bg-gray-50">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider">
                        #
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider">
                        Name By Input
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider">
                        Name By Server
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider">
                        Account Number
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider">
                        Bank Code
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider">
                        Bank City
                    </th>
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Action</span>
                    </th>
                </tr>
                </thead>
                <tbody>
                @if(count($inquiries) > 0)
                    @foreach($inquiries as $key=>$item)
                        <tr class="{{($key % 2) == 0 ? 'bg-white' : 'bg-gray-50'}}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{$key + 1}}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{$item->name_by_input}}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{$item->name_by_server}}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{$item->account_number}}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <span class="{{ $item->status == 'SUCCESS' ? 'text-green-500' : 'text-red-600' }}">{{$item->status}}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 uppercase">
                                {{$item->bank_code}}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{$item->bank_city_text}}
                            </td>
                            <td>
                                @if(!$item->name_by_server)
                                    @if (Auth::user()->level == 'master' || Auth::user()->level == 'finance')
                                    <a href="{{ url('inquiry/update/' . $item->id) }}"
                                        class="text-indigo-600 hover:text-indigo-500">
                                         Edit
                                     </a>
                                     |<a href="{{ url('inquiry/delete/' . $item->id) }}"
                                         class="text-indigo-600 hover:text-indigo-500"
                                         onclick="return confirm('Are you sure?')">
                                         Delete
                                     </a>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"> Not
                            Found
                        </td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>
    <div class="w-full flex justify-center mt-5">
        {!! $inquiries->appends(Request::only(['keyword']))->links('frontend.components.paginate') !!}
    </div>
@endsection
