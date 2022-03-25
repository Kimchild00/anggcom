@extends('frontend.layouts.app')
@section('title', ' | Detail Division')
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
@section('js')
    <script type="text/javascript">
        $(".pushedFlip").on('click', function () {
            MODAL.show('.finance-master-modal')
        });

        $('#users').select2({
            width: '100%'
        });
    </script>
@endsection

@section('content')
    <div class="w-full md:w-3/5 mt-5">
        @include('frontend.layouts.alert-message')
    </div>
    <div class="w-full mt-5 bg-white py-3 px-3 text-sm">
        <ul class="tab-menu w-full flex border-b border-gray-100 pt-2 uppercase">
            <li data-target="tab-1"
                class="tab-menu-list px-5 py-2 border-r bg-blue-200 rounded-sm  text-gray-700 cursor-pointer">Detail
            </li>
            <li data-target="tab-2"
                class="tab-menu-list px-5 py-2 rounded-sm border-r hover:bg-blue-200 border-gray-100 text-gray-700 cursor-pointer">
                Users
            </li>
            <li data-target="tab-3"
                class="tab-menu-list px-5 py-2 rounded-sm border-r hover:bg-blue-200 border-gray-100 text-gray-700 cursor-pointer">
                Last Transaction
            </li>
        </ul>

        <div class="w-full tab-content mt-5">
            <div class="w-full tab-1">
                <h1 class="text-base font-bold">Division Detail</h1>
                <table class="w-full divide-y divide-gray-200 mt-3">
                    <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                            Title
                        </th>
                        <td class="text-gray-900">
                            {{ $division->title }}
                        </td>
                    </tr>
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                            Director Email
                        </th>
                        <td class="text-gray-900">
                            {{ $division->director_email }}
                        </td>
                    </tr>
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                            Director Phone
                        </th>
                        <td class="text-gray-900">
                            {{ $division->director_phone }}
                        </td>
                    </tr>

                    @if($division->type_disbursement == 'flip')
                        @if ($division->division_flip)
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                    Flip Name
                                </th>
                                <td class="text-gray-900">
                                    {{ $division->division_flip->flip_name }}
                                </td>
                            </tr>
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                    ID Big Flip
                                </th>
                                <td class="text-gray-900">
                                    {{ $division->division_flip->id_big_flip }}
                                </td>
                            </tr>
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                    Flip key
                                </th>
                                <td class="text-gray-900">
                                    *************************
                                </td>
                            </tr>
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                    Flip Token
                                </th>
                                <td class="text-gray-900">
                                    *************************
                                </td>
                            </tr>
                        @endif
                    @else
                        @if ($division->division_xendit)
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                    Xendit Name
                                </th>
                                <td class="text-gray-900">
                                    {{ $division->division_xendit->xendit_name }}
                                </td>
                            </tr>
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                    Xendit key
                                </th>
                                <td class="text-gray-900">
                                    *************************
                                </td>
                            </tr>
                        @endif
                    @endif

                    @if($division->division_journal)
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                Journal Name
                            </th>
                            <td class="text-gray-900">
                                {{ $division->division_journal->journal_name }}
                            </td>
                        </tr>
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                Journal Key
                            </th>
                            <td class="text-gray-900">
                                *************************
                            </td>
                        </tr>
                    @endif
                    </thead>
                </table>
            </div>

            <div class="w-full tab-2 hidden">
                <h1 class="text-base font-bold">Division Users</h1>
                <div class="w-full mt-5">

                    <div class="rounded-md bg-blue-500-50 py-2 mb-5">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <!-- Heroicon name: solid/x-circle -->
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><g fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M12 1C5.925 1 1 5.925 1 12s4.925 11 11 11s11-4.925 11-11S18.075 1 12 1zm-.5 5a1 1 0 1 0 0 2h.5a1 1 0 1 0 0-2h-.5zM10 10a1 1 0 1 0 0 2h1v3h-1a1 1 0 1 0 0 2h4a1 1 0 1 0 0-2h-1v-4a1 1 0 0 0-1-1h-2z" fill="currentColor"/></g></svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-700">
                                    Notes:
                                </h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <ul role="list" class="list-disc pl-5 space-y-1">
                                        <li>Role consist of 5 types: Admin, Operator, Analyst, Master Finance, Finance</li>
                                        <li>Admin role can add users to divisions, create budgets/transactions and only see their transactions</li>
                                        <li>Finance can create budgets/transactions, see all transactions, and approve finance</li>
                                        <li>Operator role only create budgets/transactions and only see their transactions</li>
                                        <li>Analyst can create budgets/transactions and see all transactions.</li>
                                        <li>Master Finance can create budgets/transactions, see all transactions, and push transaction to flip</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-full mb-5 flex justify-end">
                    <button class="bg-blue-400 p-2 text-sm text-white inline-flex items-center space-x-2 rounded mr-1 newUser">
                        Add user
                    </button>
                </div>
                <div class='w-full overflow-auto'>
                    <table class="w-full divide-y divide-gray-200 mt-3"> 
                        <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                No
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                Name
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                Email
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                Role
                            </th>
                            @if($isAdmin)
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                    #
                                </th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($divisionUsers as $key => $item)
                            <tr class="{{($key % 2) == 0 ? 'bg-white' : 'bg-gray-50'}}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{$key + 1}}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{$item->user->name}}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{$item->user->email}}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{$item->role}}
                                </td>
                                @if($isAdmin)
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-medium">
                                        <a href="{{ url('division/delete-user/' . $item->division_id . '/'. $item->user_id . '/'. $item->role ) }}"
                                           class="text-indigo-600 hover:text-indigo-500"
                                           onclick="return confirm('Are you sure?')">
                                            Delete
                                        </a>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="w-full tab-3 hidden">
                <h1 class="text-base font-bold">Last Transaction</h1>
                <div class='w-full overflow-auto'>
                    <table class="w-full divide-y divide-gray-200 mt-3">
                        <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                No
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                Title
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                Description
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                Remark
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                #
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($transactions as $key => $item)
                            <tr class="{{($key % 2) == 0 ? 'bg-white' : 'bg-gray-50'}}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{$key + 1}}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{$item->title}}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{$item->description}}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{$item->remark}}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <span class="text-green-500">{!! $item->status_label !!}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <a href="{{ url('transaction/' . $item->id) }}"
                                       class="bg-blue-600 hover:bg-green-700 px-3 py-1 rounded text-white mr-1 ">Detail</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="w-full mt-5">
                    <a href="{{ url('transaction?division=' . $division->id) }}"
                       class="bg-green-600 hover:bg-blue-700 px-3 py-1 rounded text-white mr-1 ">Show all</a>
                </div>
            </div>
        </div>
    </div>
    {{-- user modal --}}
    <div class="modal user-modal z-3 h-screen w-full fixed left-0 top-0 flex justify-center items-center bg-black bg-opacity-50 hidden">
        <div class="bg-white rounded shadow-lg w-10/12 md:w-2/5">
            <div class="border-b px-4 py-2 flex justify-between items-center">
                <h3 class="font-semibold text-lg">Add Division User</h3>
                <button class="text-black close-modal">&cross;</button>
            </div>
            <form action="{{ url('division/create-user') }}" method="POST" id="userForm">
                <div class="w-full p-3">
                    {{ csrf_field() }}
                    <input type="hidden" name="division_id" value="{{ $division->id }}">
                    <div class="sm:col-span-3 mb-5">
                        <label for="user_id" class="block text-sm font-medium text-gray-700">
                            User <span class="text-red-600 font-bold italic">*) required</span>
                        </label>
                        <div class="mt-1">
                            <select name="user_id" autocomplete="user_id" required
                                    class="rounded-md focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-200  py-2.5 px-2 bg-white" id="users">
                                <option value="">-- Select user --</option>
                                @foreach($users as $item)
                                    <option value="{{ $item->id }}">
                                        {{ $item->email }} | {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="sm:col-span-3 mb-5">
                        <label for="role" class="block text-sm font-medium text-gray-700">
                            Role <span class="text-red-600 font-bold italic">*) required</span>
                        </label>
                        <div class="mt-1">
                            <select name="role" autocomplete="role" required
                                    class="rounded-md focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-200  py-2.5 px-2 bg-white">
                                <option value="">-- Select Role --</option>
                                <option value="admin">Admin</option>
                                <option value="operator">Operator</option>
                                @if($division->director_email == \Illuminate\Support\Facades\Auth::user()->email)
                                    <option value="finance">Finance</option>
                                    <option value="analyst">Analyst</option>
                                    <option value="master_finance">Master Finance</option>
                                @else
                                    <option value="" disabled="">finance, analyst and master_finance role are only be displayed by Director</option>
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end items-center w-100 border-t p-3">
                    <button class="bg-red-600 hover:bg-red-700 px-3 py-1 rounded text-white mr-1 close-modal" type="button">Cancel</button>
                    <button class="bg-blue-600 hover:bg-blue-700 px-3 py-1 rounded text-white submitFinanceModal" type="submit">Submit</button>
                </div>
            </form>
        </div>
    </div>
@endsection
