@extends('frontend.layouts.app')
@section('title', isset($division) ? ' | Edit Division' : ' | Create Division')
@section('js')
    <script>
        $(document).ready(function() {
            $('#director').select2();
        });

        let typeDisbursement = {!! json_encode($typeDisbursement) !!}

        if(typeDisbursement == 'xendit') {
            $('#flip_radio').removeAttr("checked")
            $('#xendit_radio').attr("checked", true)
            $('#xendit').css('display', 'block')
            $('#flip').css('display', 'none')
        }
        
        $('input[type=radio]').click(function(){
            if(this.value == 'flip') {
                $('#flip').css('display', 'block')
                $('#xendit').css('display', 'none')
            } else {
                $('#xendit').css('display', 'block')
                $('#flip').css('display', 'none')
            }
        }); 
    </script>
@endsection
@section('content')
    <div class="w-full mt-5">
        <div class="w-full md:w-3/5">
            @include('frontend.layouts.alert-message')
        </div>
        <div class="w-full md:w-3/5 bg-white rounded-sm">
            <form action="{{ isset($division)  ? url('division/update') : url('division') }}" method="post"
                  class="w-full py-5 px-3">
                @if (isset($division))
                    <p class="pb-5 text-sm text-gray-600 font-bold"> Edit Division {{ $division->title }}</p>
                    {{ method_field('POST') }}
                    <input type="hidden" name="division_id" value="{{ $division->id }}">
                @else
                    <p class="pb-5 text-sm text-gray-600 font-bold"> Create Division</p>
                @endif
                {{ csrf_field() }}
                <div class="sm:col-span-3 mb-5">
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        Director <span class="text-red-600 font-bold italic">*) required</span>
                    </label>
                    <select name="director" class="form-control" id="director" required>
                        <option value="">-- Select user as Director --</option>
                                @foreach($users->sortByDesc('id') as $item)
                                    <option value="{{ $item->id }}" {{isset($division) && $item->email == $division->director_email ? 'selected' : ''}}>
                                        {{ $item->email }} | {{ $item->name }}
                                    </option>
                                @endforeach
                    </select>
                    <label for="title" class="block text-xs font-normal text-red-700">
                        The director here who will approve the transaction when it has been approved by the maker
                    </label>
                </div>
                <div class="sm:col-span-3 mb-5">
                    <label for="title" class="block text-sm font-medium text-gray-700">
                        Title <span class="text-red-600 font-bold italic">*) required</span>
                    </label>
                    <div class="mt-1">
                        <input type="text" name="title" autocomplete="title"
                               value="{{ isset($division) ? $division->title : '' }}" required
                               class="border border-gray-300 px-2 py-2 focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                </div>
                <div class="sm:col-span-3 mb-5">
                    <label for="journal_name" class="block text-sm font-medium text-gray-700">
                        Journal name
                    </label>
                    <div class="mt-1">
                        <input type="text" name="journal_name"
                               value="{{ isset($division) ? ($division->division_journal ? $division->division_journal->journal_name : '') : '' }}"
                               autocomplete="phone"
                               class="border border-gray-300 px-2 py-2 focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                </div>
                <div class="sm:col-span-3 mb-5">
                    <label for="journal_key" class="block text-sm font-medium text-gray-700">
                        Journal key
                    </label>
                    <div class="mt-1">
                        <input type="password" name="journal_key"
                               value="{{ isset($division) ? ($division->division_journal ? $division->division_journal->journal_key : '') : '' }}"
                               autocomplete="phone"
                               class="border border-gray-300 px-2 py-2 focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                </div>
                <div class="sm:col-span-3 mb-5">
                    <label for="title" class="block text-sm font-medium text-gray-700">
                        Type Disbursement (Pay Outs) <span class="text-red-600 font-bold italic">*) required</span>
                    </label>
                    <div class="mt-1 flex justify-start">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input form-check-input appearance-none rounded-full h-4 w-4 border border-gray-300 bg-white checked:bg-blue-600 checked:border-blue-600 focus:outline-none transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain float-left mr-2 cursor-pointer" type="radio" name="type_disbursement" id="flip_radio" value="flip" checked>
                            <label class="form-check-label inline-block text-gray-800" for="inlineRadio10">Flip</label>
                        </div>
                        @if(isset($_GET['test']))
                        <div class="form-check form-check-inline">
                            <input class="form-check-input form-check-input appearance-none rounded-full h-4 w-4 border border-gray-300 bg-white checked:bg-blue-600 checked:border-blue-600 focus:outline-none transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain float-left mr-2 cursor-pointer" type="radio" name="type_disbursement" id="xendit_radio" value="xendit">
                            <label class="form-check-label inline-block text-gray-800" for="inlineRadio20">Xendit</label>
                        </div>
                        @endif
                    </div>
                </div>
                
                <div style="display: none;" id="xendit"> 
                    <div class="sm:col-span-3 mb-5">
                        <label for="flip_name" class="block text-sm font-medium text-gray-700">
                            <span class="text-red-600 font-bold italic">Xendit Note</span>
                        </label>
                        <div class="mt-1">
                            <div class="w-full flex">
                                <div class="w-1/2">
                                    <a href="https://d2jnbxtr5v4vqu.cloudfront.net/images/flip-note-2022_02_10_17_25_12.png"
                                       target="_blank">
                                        <img src="https://d2jnbxtr5v4vqu.cloudfront.net/images/flip-note-2022_02_10_17_25_12.png"
                                             alt="" width="200px">
                                    </a>
                                </div>
                            </div>
                        </div>
                        <label for="title" class="block text-xs font-normal text-red-700">
                            Make sure your callback disbursement account is https://app.anggaran.com/api/xendit/disbursement-callback
                        </label>
                    </div>
                    <div class="sm:col-span-3 mb-5">
                        <label for="xendit_name" class="block text-sm font-medium text-gray-700">
                            Xendit Name <span class="text-red-600 font-bold italic">*) required</span>
                        </label>
                        <div class="mt-1">
                            <input type="text" name="xendit_name"
                                   value="{{ isset($division) ? (($division->division_xendit) ? $division->division_xendit->xendit_name : '' ) : '' }}"
                                   autocomplete="xendit_name"
                                   class="border border-gray-300 px-2 py-2 focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <label for="title" class="block text-xs font-normal text-red-700">
                            Make sure <b>Xendit Name</b> field is same with your <b>Xendit Name</b> on your xendit account, please
                            see <i>xendit note</i> above...
                        </label>
                    </div>
                    <div class="sm:col-span-3 mb-5">
                        <label for="xendit_key" class="block text-sm font-medium text-gray-700">
                            Xendit Key <span class="text-red-600 font-bold italic">*) required</span>
                        </label>
                        <div class="mt-1">
                            <input type="text" name="xendit_key"
                                   value="{{ isset($division) ? (($division->division_xendit) ? $division->division_xendit->xendit_key : '' ) : '' }}"
                                   autocomplete="xendit_key"
                                   class="border border-gray-300 px-2 py-2 focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <label for="title" class="block text-xs font-normal text-red-700">
                            Make sure <b>Xendit Keys</b> field is same with your <b>Xendit Keys</b> on your xendit account,
                            please see <i>xendit note</i> above...
                        </label>
                    </div>
                </div>       
        
                <div id="flip">
                    <div class="sm:col-span-3 mb-5">
                        <label for="flip_name" class="block text-sm font-medium text-gray-700">
                            <span class="text-red-600 font-bold italic">Flip Note</span>
                        </label>
                        <div class="mt-1">
                            <div class="w-full flex">
                                <div class="w-1/2">
                                    <a href="https://d2jnbxtr5v4vqu.cloudfront.net/images/note-flip-2021_10_29_21_06_49.png"
                                       target="_blank">
                                        <img src="https://d2jnbxtr5v4vqu.cloudfront.net/images/note-flip-2021_10_29_21_06_49.png"
                                             alt="" width="200px">
                                    </a>
                                </div>
                                <div class="w-1/2">
                                    <a href="https://d2jnbxtr5v4vqu.cloudfront.net/images/callback-url-2021_11_09_10_16_11.png"
                                       target="_blank">
                                        <img src="https://d2jnbxtr5v4vqu.cloudfront.net/images/callback-url-2021_11_09_10_16_11.png"
                                             alt="" width="400px">
                                    </a>
                                </div>
                            </div>
                        </div>
                        <label for="title" class="block text-xs font-normal text-red-700">
                            Make sure the anggaran.com IP is whitelisted in your flip account. Our IP is 165.22.100.223.
                        </label>
                    </div>
                    <div class="sm:col-span-3 mb-5">
                        <label for="flip_name" class="block text-sm font-medium text-gray-700">
                            Flip Name <span class="text-red-600 font-bold italic">*) required</span>
                        </label>
                        <div class="mt-1">
                            <input type="text" name="flip_name"
                                   value="{{ isset($division) ? (($division->division_flip) ? $division->division_flip->flip_name : '' ) : '' }}"
                                   autocomplete="flip_name"
                                   class="border border-gray-300 px-2 py-2 focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <label for="title" class="block text-xs font-normal text-red-700">
                            Make sure <b>Flip Name</b> field is same with your <b>Flip Name</b> on your flip account, please
                            see <i>flip note</i> above...
                        </label>
                    </div>
                    <div class="sm:col-span-3 mb-5">
                        <label for="flip_name" class="block text-sm font-medium text-gray-700">
                            ID Big Flip <span class="text-red-600 font-bold italic">*) required</span>
                        </label>
                        <div class="mt-1">
                            <input type="text" name="id_big_flip"
                                   value="{{ isset($division) ? (($division->division_flip) ? $division->division_flip->id_big_flip : '' ) : '' }}"                                   
                                   autocomplete="flip_name"
                                   class="border border-gray-300 px-2 py-2 focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <label for="title" class="block text-xs font-normal text-red-700">
                            Make sure <b>ID Big Flip</b> field is same with your <b>ID Big Flip</b> on your flip account,
                            please see <i>flip note</i> above...
                        </label>
                    </div>
                    <div class="sm:col-span-3 mb-5">
                        <label for="flip_key" class="block text-sm font-medium text-gray-700">
                            Flip Key <span class="text-red-600 font-bold italic">*) required</span>
                        </label>
                        <div class="mt-1">
                            <input type="password" name="flip_key"
                                   value="{{ isset($division) ? (($division->division_flip) ? $division->division_flip->flip_key : '' ) : '' }}"
                                   autocomplete="flip_key"
                                   class="border border-gray-300 px-2 py-2 focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <label for="title" class="block text-xs font-normal text-red-700">
                            Make sure <b>Flip Key</b> field is same with your <b>Api Secret Key</b> on your flip account,
                            please see <i>flip note</i> above...
                        </label>
                    </div>
                    <div class="sm:col-span-3 mb-5">
                        <label for="flip_token" class="block text-sm font-medium text-gray-700">
                            Flip token <span class="text-red-600 font-bold italic">*) required</span>
                        </label>
                        <div class="mt-1">
                            <input type="password" name="flip_token"
                                   value="{{ isset($division) ? (($division->division_flip) ? $division->division_flip->flip_token : '' ) : '' }}"
                                   autocomplete="flip_token"
                                   class="border border-gray-300 px-2 py-2 focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <label for="title" class="block text-xs font-normal text-red-700">
                            Make sure <b>Flip Token</b> field is same with your <b>Validation Token</b> on your flip
                            account, please see <i>flip note</i> above...
                        </label>
                    </div>
                </div>

                <div class="w-full">
                    <button type="submit"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Submit
                    </button>
                    <a href="{{ url('division') }}" class="btn btn-warning">
                        Back
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
