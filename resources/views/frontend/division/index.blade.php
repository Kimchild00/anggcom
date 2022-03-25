@extends('frontend.layouts.app')
@section('title', ' | Division')
@section('content')

    <div class="w-full mt-5">
        @include('frontend.layouts.alert-message')
    </div>
    <div class="w-full mt-5 2xl:flex xl:flex lg:flex items-center 2xl:justify-between xl:justify-between lg:justify-between">
        <div>
            @if(\Illuminate\Support\Facades\Auth::user()->level == 'master')
                <a href="{{url('division/create') }}"
                   class="cursor-pointer mt-3 w-full inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Create
                </a>
            @endif
        </div>
        <form class="sm:flex sm:items-center justify-end mt-2" action="{{ url('division') }}" method="GET">
            <div class="w-full sm:max-w-xs m-1">
                <label for="email" class="sr-only">Title</label>
                <input type="text" name="title" value="{{isset($filters['title']) ? $filters['title'] : ''}}"
                       class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md py-2 px-2"
                       placeholder="Title">
            </div>
            <div class="w-full sm:max-w-xs m-1">
                <label for="email" class="sr-only">Title</label>
                <input type="text" name="director_email" value="{{isset($filters['director_email']) ? $filters['director_email'] : ''}}"
                       class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md py-2 px-2"
                       placeholder="Director Email">
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
                        Title
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider">
                        Director Email
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider">
                        Director Phone
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider">
                        Disbursement/Payout Name
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider">
                        Journal Name
                    </th>
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Action</span>
                    </th>
                </tr>
                </thead>
                <tbody>
                @if(count($divisions) > 0)
                    @foreach ($divisions as $key => $item)
                        <tr class="{{($key % 2) == 0 ? 'bg-white' : 'bg-gray-50'}}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{$key + 1}}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <a href="{{ url('division/' . $item->id) }}"
                                   class="text-blue-500 font-bold capitalize">{{$item->title}}</a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{$item->director_email}}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{$item->director_phone}}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                @if($item->type_disbursement == 'flip')
                                    Flip Name: {{ $item->division_flip ? $item->division_flip->flip_name : 'Default Flip' }}
                                @else
                                    Xendit Name: {{ $item->division_xendit ? $item->division_xendit->xendit_name : 'Default Xendit' }}
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $item->division_journal ? $item->division_journal->journal_name : 'Not have a journal' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-medium">
                                <a href="{{ url('division/' . $item->id) }}"
                                   class="text-indigo-600 hover:text-indigo-500">
                                    Detail
                                </a>
                                @if(\Auth::user()->level == 'master')
                                    |<a href="{{ url('division/edit/' . $item->id) }}"
                                       class="text-indigo-600 hover:text-indigo-500">
                                        Edit
                                    </a>|
                                    <a href="{{ url('division/delete/' . $item->id) }}"
                                       class="text-indigo-600 hover:text-indigo-500"
                                       onclick="return confirm('Are you sure?')">
                                        Delete
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Not Found</td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>
    <div class="w-full flex justify-center mt-5">
        {!! $divisions->appends($filters)->links('frontend.components.paginate') !!}
    </div>
@endsection
