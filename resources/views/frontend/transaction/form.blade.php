@extends('frontend.layouts.app')
@section('title', ' | Create Transaction')
@section('css')
    <style>
         #cover-spin {
            position:fixed;
            width:100%;
            left:0;right:0;top:0;bottom:0;
            background-color: rgba(255,255,255,0.7);
            z-index:9999;
            display:none;
        }

        @-webkit-keyframes spin {
            from {-webkit-transform:rotate(0deg);}
            to {-webkit-transform:rotate(360deg);}
        }

        @keyframes spin {
            from {transform:rotate(0deg);}
            to {transform:rotate(360deg);}
        }

        #cover-spin::after {
            content:'';
            display:block;
            position:absolute;
            left:48%;top:40%;
            width:40px;height:40px;
            border-style:solid;
            border-color:black;
            border-top-color:transparent;
            border-width: 4px;
            border-radius:50%;
            -webkit-animation: spin .8s linear infinite;
            animation: spin .8s linear infinite;
        }

        .select2-container--default .select2-selection--single {
            height: 38px !important;
            width: 100% !important;
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
        var i = 0;
        addFile()
        $("#has_tax_field_id").change(function () {
            var selected = $(this).find(":selected").val();
            var tax = '';
            if (selected != 'no_tax') {
                $("#amount_tax_view_id").show()
                $("#amount_net_view_id").show()
                var tax = '';
                var amountTax = 0;
                var amount = $("#amount_transaction_id").val()
                amount = amount.replaceAll(',', '');
                if (selected == 'pph 21') {
                    tax = 'Tax payable  article 21 (manual)'

                } else if (selected == 'pph 4(2)') {
                    tax = 'Tax Pay Art 4(2) -> 10% dari Amount Transaction'
                    amountTax = 10 * amount / 100;
                } else if (selected == 'pph 23') {
                    tax = 'Tax Pay Art 23 -> 2% dari Amount Transaction'
                    amountTax = 2 * amount / 100;
                } else if (selected == 'pph 26') {
                    tax = 'Tax Pay Art 26 -> 20% dari Amount Transaction'
                    amountTax = 20 * amount / 100;
                }
                $('input[name="amount_tax"]').prop('required', true)
                $('input[name="amount_tax"]').val(amountTax)
                $('input[name="amount_net"]').val(amount-amountTax)
            } else {
                $("#amount_tax_view_id").hide()
                $("#amount_net_view_id").hide()
                $("input[name='amount_tax']").prop('required',false);
            }
            $("#note_tax_id").html('<b>' + tax + '</b>');
        });

        $('#division_field_id').change(function() {
            $("#division_field_id").attr('disabled', true)
            $("#journal_field_id").html('');
            $("#journal_field_id").html('<option selected disabled>Loading</option>');
            $("#journal_view_id").show();
            $.ajax({
                url: "{{ url('transaction/chart-of-accounts/get/') }}" + '/' + $(this).val(),
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
                            $("#journal_field_id").select2({width: 'resolve'})
                        } else {
                            $("#journal_view_id").hide();
                        }
                    } else {
                        $("#journal_view_id").hide();
                    }
                },
                error: function() {
                    $("#journal_field_id").html('');
                    $("#journal_view_id").hide();
                    $("#division_field_id").attr('disabled', false)
                }
            });
        });

        function addFile() {
            var viewAppend = $("#file_view_id");
            viewAppend.append('' +
                '<div class="w-full flex flex-col lg:flex-row mt-5 py-1" id="file_view_'+i+'_id">' +
                '           <input type="hidden" name="file_name_'+i+'" id="file_name_'+i+'_id">' +
                '            <div class="px-2 lg:w-1/4">\n' +
                '                <div class="mt-1 sm:mt-0 col-span-2 sm:mt-2">\n' +
                '                    <input type="file" name="files['+i+']" id="file_'+i+'_id" required onchange="uploadFile('+i+')" ' +
                'class="w-full focus:ring-indigo-500 focus:border-indigo-500 px-2 py-2 sm:text-sm border border-gray-200 rounded-md">\n' +
                '                </div>\n' +
                '            </div>\n' +
                '            <div class="px-2 lg:w-1/4">\n' +
                '                <div class="mt-1 sm:mt-0 col-span-2 sm:mt-2">\n' +
                '                    <input type="text" name="amount_file['+i+']" placeholder="total amount reported" onkeyup="numberFormat2(this)" id="amount_report_id" required class="w-full focus:ring-indigo-500 focus:border-indigo-500  px-2 py-2 sm:text-sm border border-gray-200 rounded-md amount-reported">\n' +
                '                </div>\n' +
                '            </div>\n' +
                '            <div class="px-2 lg:w-1/4">\n' +
                '                <div class="mt-1 sm:mt-0 col-span-2 sm:mt-2">\n' +
                '                    <input type="text" name="note['+i+']" placeholder="note" class="w-full focus:ring-indigo-500 focus:border-indigo-500  px-2 py-2 sm:text-sm border border-gray-200 rounded-md">\n' +
                '                </div>\n' + 
                '            </div>\n' +
                '            <div class="px-2 lg:w-1/4">\n' +
                '                <div class="mt-1 sm:mt-0 col-span-2 sm:mt-2">\n' +
                '                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteFile('+i+')">Delete</button>\n' +
                '                </div>\n' +
                '            </div>' +
                '        </div>');


            i = i + 1;
        }

        function deleteFile(i) {
            var confirmVal = confirm("Are you sure to delete this file?");
            if (confirmVal == true) {
                $("#file_view_" + i + "_id").remove();
            }
        }

        function uploadFile(i) {
            var file_data = $('#file_' + i + '_id').prop('files')[0];
            var form_data = new FormData();
            $('#cover-spin').show(0);
            var warningFail = $('#file_'+ i + '_id')
            form_data.append('file', file_data);
            form_data.append('_token', '{{ csrf_token() }}');
            $.ajax({
                url: 'upload-file', // <-- point to server-side PHP script
                dataType: 'text',  // <-- what to expect back from the PHP script, if anything
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                type: 'post',
                success: function(result) {
                    result = JSON.parse(result)
                    if (result != false) {
                        if(warningFail.hasClass('bg-yellow-400')) {
                            warningFail.removeClass('bg-yellow-400')
                        }
                        $("#file_name_" + i + '_id').val(result.name)
                        $('#cover-spin').hide()
                    } else {
                        $('#cover-spin').hide()
                        warningFail.val(null)
                        warningFail.removeClass('border border-gray-200')
                        warningFail.addClass('border-4 border-yellow-500')
                        alert('Uploading file fail, please check your conection')
                    }
                },
                error: function(result){
                    $('#cover-spin').hide()
                    warningFail.val(null)
                    warningFail.removeClass('border border-gray-200')
                    warningFail.addClass('border-4 border-yellow-500')
                    alert('Uploading file fail, please check your conection')
                }
            });
        }

        function checkAmountTrxAndReport(){
            let getAmountReported   = document.querySelectorAll('.amount-reported');
            let getHasTax           = document.getElementById('has_tax_field_id').value;
            let getAmountNet        = document.getElementsByName('amount_net')[0].value;
            getAmountNet            = parseInt(getAmountNet.replaceAll("," , ""));
            if (getAmountReported.length > 0){
                let getAmountTrx        = document.getElementById('amount_transaction_id').value;
                getAmountTrx = parseInt(getAmountTrx.replaceAll("," , ""));
                let totalReported       = 0;
                getAmountReported.forEach((e,i) =>{
                    let amount = e.value;
                    amount = parseInt(amount.replaceAll("," , ""));
                    totalReported += amount;
                });
                if (getAmountTrx !== totalReported && getHasTax == 'no_tax'){
                    alert('Amount Transaction with Different Total Amount Reported');
                    event.preventDefault();
                }else if (getHasTax !== 'no_tax' && totalReported !== getAmountNet){
                    alert('Amount After Tax with Different Total Amount Reported');
                    event.preventDefault();
                } else if (getHasTax !== 'no_tax' && totalReported == getAmountNet){
                    let resultTax = calculateTax();
                    if(totalReported !== resultTax){
                        alert('Amount After Tax False (Amount - Tax Amount)');
                        event.preventDefault();
                    }
                }
            }

            const numberTransaction = document.querySelector("#amount_transaction_id")
            const numberReported    = document.querySelector("#amount_report_id")
            const numberTax         = document.querySelector("#amount_tax_id")
            const numberNet         = document.querySelector("#amount_net_id")
            numberTransaction.value = numberTransaction.value.toString().replace(/,/g,"")
            numberReported.value    = numberReported.value.toString().replace(/,/g,"")
            numberTax.value         = numberTax.value.toString().replace(/,/g,"")
            numberNet.value         = numberNet.value.toString().replace(/,/g,"")

        }

        $('.number_transaction').keyup(function(event) {
            if(event.which >= 37 && event.which <= 40) return;
            $(this).val(function(index, value) {
            return value
            .replace(/\D/g, "")
            .replace(/\B(?=(\d{3})+(?!\d))/g, ",")
            ;
            });
        });

        function formatRupiah(angka, prefix) {
            var regExp = new RegExp("^[0-9,;]+$");
            var isValid = regExp.test(angka);
            console.log(isValid)
            if(!isValid) return angka.slice(0,angka.length-1)
            const numberTemp=angka.replaceAll(",","")  
           
            return numberTemp.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        function numberFormat2(e){
            let getAmountReportedVal   = document.querySelectorAll('.amount-reported');
            if (getAmountReportedVal.length > 0){
                getAmountReportedVal.forEach((e,i) =>{
                    e.value = formatRupiah(e.value);
                });
            }
        }

        function calculateTax()
        {
            var amount      = $('#amount_transaction_id').val()
            amount          = parseInt(amount.replaceAll("," , ""));
            var amountTax   = $('#amount_tax_id').val()
            amountTax       = parseInt(amountTax.replaceAll("," , ""));
            
            var resultAmountAfterTax   = amount - amountTax
            return resultAmountAfterTax;
        }

        function checkLongerCharacter(){
            var char = document.getElementById("remark_transaction_id")
            var warning = document.getElementById("warning_remark");
            var regExp = new RegExp(('^.*[-!$%^&*()_+|~=`{}[\\]:";\'<>?,.\/]+.*$'));
            if(char.value.length > 18 ) {
                warning.style.display = "block"
            } else if(regExp.test(char.value)) {
                warning.style.display = "block"
            } else {
                warning.style.display = "none"
            }
        }

        $(document).ready(function() {
            $('#inquiry').select2({width : '100%'});
        });

    </script>
@endsection

@section('content')

<div class="w-full mt-5">
  @include('frontend.layouts.alert-message')
</div>

<div class="w-full bg-white w-full px-2 py-3">
    <div id="cover-spin"></div>
    <form action="{{ url('transaction') }}" method="post">
        {{ csrf_field() }}
        <div class="w-full flex flex-col lg:flex-row">
            <div class="lg:w-1/2 px-2">
                <label for="title" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                    Title <span class="text-red-600 font-bold italic">*) required</span>
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                <input type="text" name="title" class="w-full focus:ring-indigo-500 focus:border-indigo-500  px-2 py-2 sm:text-sm border border-gray-200 rounded-md" required>
                </div>
            </div>
            <div class="lg:w-1/2 px-2">
                <label for="division" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                    Division <span class="text-red-600 font-bold italic">*) required</span>
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <select name="division" autocomplete="devision" required id="division_field_id"
                            class="w-full rounded-md focus:ring-indigo-500 focus:border-indigo-500 block sm:text-sm border border-gray-200  py-2.5 px-2 bg-white">
                        <option value="">-- Select Division --</option>
                        @foreach($divisions as $division)
                          <option value="{{ $division->id }}" {{ (isset($filters['devision']) AND ($filters['devision'] == $division->id)) ? 'selected' : '' }}>
                              Division {{ $division->title }}
                          </option>
                      @endforeach
                      </select>
                </div>
            </div>
        </div>
        
        <div class="w-full flex flex-col lg:flex-row mt-5">
            <div class="lg:w-1/2 px-2">
                <label for="inquiry" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                    Inquiry / Account Number <span class="text-red-600 font-bold italic">*) required</span>
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <select name="inquiry" autocomplete="inquiry" id="inquiry" required class="rounded-md focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-200  py-2.5 px-2 bg-white">
                        <option value="" selected>
                            -- Select Inquiry / Account Number --
                        </option>
                        @foreach($inquiries as $inquiry)
                            <option value="{{ $inquiry->id }}" {{ $inquiry->status != 'SUCCESS' ? 'disabled' : '' }}>
                                {{ $inquiry->name_by_input ? $inquiry->name_by_input : $inquiry->name_by_server }} | {{ $inquiry->account_number }} (Bank Code: {{ strtoupper($inquiry->bank_code) }}) | Status: {!! $inquiry->status_label !!}
                            </option>
                        @endforeach
                      </select>
                </div>
                <label for="inquiry" class="block text-sm font-medium text-gray-700 mt-2 sm:pt-2" style="text-align: right;">
                    <a href="{{ url('inquiry/create') }}" target="_blank" class="bg-green-600 hover:bg-blue-700 px-3 py-1 rounded text-white mr-0 ">
                        <i class="fa fa-plus"></i> Inquiry
                    </a>
                </label>
            </div>
            <div class="lg:w-1/2 px-2">
                <div class="label flex justify-between">
                    <label for="remark" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                        Remark <span class="text-red-600 font-bold italic">*) required</span>
                    </label>
                    <p class="block text-md font-medium text-red-800 sm:mt-px sm:pt-2" id="warning_remark" style="display: none;"><i class="fas fa-exclamation-triangle"></i><b>  Remark more than 18 character or there are special characters</b></p>
                </div>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <input type="text" name="remark" id="remark_transaction_id" value="{{ old('remark') }}" required class="w-full focus:border-rose-500 px-2 py-2 sm:text-sm border border-gray-200 rounded-md"
                    onkeyup="checkLongerCharacter()" placeholder="remark not more than 18 character and not allowed special character">
                    {{-- focus:ring-indigo-500 focus:border-indigo-500 --}}
                </div>
            </div>
        </div>

        <div class="w-full flex flex-col lg:flex-row mt-5">
            <div class="lg:w-1/2 px-2">
                <label for="amount" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                    Amount <span class="text-red-600 font-bold italic">*) required</span>
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                <input type="text" name="amount" required id="amount_transaction_id"
                       class="w-full focus:ring-indigo-500 focus:border-indigo-500  px-2 py-2 sm:text-sm border border-gray-200 rounded-md number_transaction">
                </div>
            </div>
            <div class="lg:w-1/2 px-2" id="journal_view_id" hidden>
                <label for="journal" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                    Journal
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <select name="journal" autocomplete="journal" id="journal_field_id"
                            class="w-full rounded-md focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-200  py-2.5 px-2 bg-white" style="width:100%!important">
                        <option value=""> -- Select Account Journal --</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="w-full flex flex-col lg:flex-row mt-5">
            <div class="lg:w-1/2 px-2">
                <label for="journal" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                    Has Tax?
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <select name="exist_tax" id="has_tax_field_id" 
                            class="rounded-md focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-200  py-2.5 px-2 bg-white">
                        <option value="no_tax" selected>No Tax</option>
                        <option value="pph 21">Pph 21</option>
                        <option value="pph 4(2)">Pph 4(2)</option>
                        <option value="pph 23">Pph 23</option>
                        <option value="pph 26">Pph 26</option>
                    </select>
                    <label for="" id="note_tax_id"></label>
                </div>
            </div>
            <div class="lg:w-1/2 px-2" id="amount_tax_view_id" hidden>
                <label for="amount_tax" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                    Tax Amount
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <input type="text" id="amount_tax_id" name="amount_tax" onkeyup="calculateTax()" class="w-full focus:ring-indigo-500 focus:border-indigo-500  px-2 py-2 sm:text-sm border border-gray-200 rounded-md number_transaction">
                </div>
            </div>
        </div>

        <div class="w-full flex mt-5">
            <div class="w-1/2 px-2" id="amount_net_view_id" hidden>
                <label for="amount_net" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                    Amount After Tax<span class="text-red-600 font-bold italic">*) is the amount (Amount - Tax Amount).</span>
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <input type="text" name="amount_net" id="amount_net_id" class="w-full focus:ring-indigo-500 focus:border-indigo-500  px-2 py-2 sm:text-sm border border-gray-200 rounded-md number_transaction">
                </div>
            </div>
        </div>

        <div class="w-full mt-5 ">
            <div class="px-2">
                <label for="description" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                    Description <span class="text-red-600 font-bold italic">*) required</span>
                </label>
                <textarea name="description" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-300 rounded-md" required></textarea>
            </div>
        </div>

        <div class="w-full mt-5 ">
            <div class="px-2">
                <label for="amount" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                    Files <span class="text-red-600 font-bold italic">*) required</span>
                    <button type="button" class="btn btn-primary btn-sm" onclick="addFile()">Add</button>
                </label>
            </div>
        </div>
        <div id="file_view_id"></div>
        <div class="flex justify-end mt-5">
            <div>
                <button type="submit" onclick="checkAmountTrxAndReport()" id="btn_submit_data" class="mt-3 w-full inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Submit
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
