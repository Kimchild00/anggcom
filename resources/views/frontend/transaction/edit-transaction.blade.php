@extends('frontend.layouts.app')
@section('title', ' | Edit Transaction')
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
        const cdnUrl = "https://d2jnbxtr5v4vqu.cloudfront.net/images/";
        let dataReport = {!! json_encode($transactionReport) !!}
        let i = dataReport.length;
        if (dataReport.length > 0) {
            $.each(dataReport, function (i) {
                let currentReport = dataReport[i];
                let viewAppend = $("#file_view_id");
                viewAppend.append('' +
                    '<div class="w-full flex flex-col ct-app lg:flex-row mt-5" id="file_view_'+i+'_id">' +
                    '           <input type="hidden" name="transaction_files['+i+'][id]" value="'+currentReport.id+'" id="file_name_'+i+'_id">' +
                    '           <input type="hidden" name="transaction_files['+i+'][file_name]" data-old="'+i+'" value="'+currentReport.file_name+'" id="file_name_'+i+'_id">' +
                    '           <input type="hidden" name="transaction_files['+i+'][new_file]" data-new="'+i+'" id="file_name_'+i+'_id">' +
                    '            <div class="px-2 lg:w-1/4">\n' +
                    '                <div class="mt-1 sm:mt-0 col-span-2 sm:mt-2">\n' +
                    '                    <input type="file" name="transaction_files['+i+'][files]" value="'+currentReport.file_name+'" data-val="old" id="file_'+i+'_id" data-img="'+i+'" data-val="old" onchange="uploadFile('+i+')" ' +
                    '                       class="w-full focus:ring-indigo-500 focus:border-indigo-500  px-2 py-2 sm:te#xt-sm border border-gray-200 rounded-md">\n' +
                    '                </div>\n' + ' *&nbsp;<a href="'+cdnUrl+currentReport.file_name+'" target="_blank" download="" id="preview-'+i+'" title="Download Server Receipt"><i class="fa fa-file" style="color:blue"></i>&nbsp;'+currentReport.file_name+'</a></span>\n' + 
                    '            </div>\n' +
                    '            <div class="px-2 lg:w-1/4">\n' +
                    '                <div class="mt-1 sm:mt-0 col-span-2 sm:mt-2">\n' +
                    '                    <input type="text" name="transaction_files['+i+'][amount_file]" value="'+ new Intl.NumberFormat().format(currentReport.amount) +'" placeholder="total amount reported" onkeyup="numberFormat2(this)" id="amount_report_id" required class="w-full focus:ring-indigo-500 focus:border-indigo-500  px-2 py-2 sm:text-sm border border-gray-200 rounded-md amount-reported">\n' +
                    '                </div>\n' +
                    '            </div>\n' +
                    '            <div class="px-2 lg:w-1/4">\n' +
                    '                <div class="mt-1 sm:mt-0 col-span-2 sm:mt-2">\n' +
                    '                    <input type="text" name="transaction_files['+i+'][note]" value="'+currentReport.note+'" placeholder="note" class="w-full focus:ring-indigo-500 focus:border-indigo-500  px-2 py-2 sm:text-sm border border-gray-200 rounded-md">\n' +
                    '                </div>\n' + 
                    '            </div>\n' +
                    '            <div class="px-2 lg:w-1/4">\n' +
                    '                <div class="mt-1 sm:mt-0 col-span-2 sm:mt-2">\n' +
                    '                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteFile('+i+',' +currentReport.id + ' )">Delete</button>\n' +
                    '                </div>\n' +
                    '            </div>' +
                    '        </div>');

                i = i + 1;
            });
        }

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
                    amountTax = new Intl.NumberFormat().format(amountTax)
                } else if (selected == 'pph 23') {
                    tax = 'Tax Pay Art 23 -> 2% dari Amount Transaction'
                    amountTax = 2 * amount / 100;
                    amountTax = new Intl.NumberFormat().format(amountTax)
                } else if (selected == 'pph 26') {
                    tax = 'Tax Pay Art 26 -> 20% dari Amount Transaction'
                    amountTax = 20 * amount / 100;
                    amountTax = new Intl.NumberFormat().format(amountTax)
                }
                $('input[name="amount_tax"]').prop('required', true)
                $('input[name="amount_tax"]').val(amountTax)
                amountTax = amountTax.replaceAll(',', '')
                $('input[name="amount_net"]').val(new Intl.NumberFormat().format(amount-amountTax))
            } else {
                $("#amount_tax_view_id").hide()
                $("#amount_net_view_id").hide()
                $("input[name='amount_tax']").prop('required',false);
            }
            $("#note_tax_id").html('<b>' + tax + '</b>');
        });

        $(document).ready(function(){
            let data = {!! json_encode($transaction) !!}
            getJournal(data.division_id)

            let dataTransaction = {!! json_encode($transactionTax) !!}
            var selected = $("#has_tax_field_id").find(":selected").val();
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
                    amountTax = new Intl.NumberFormat().format(dataTransaction)
                } else if (selected == 'pph 4(2)') {
                    tax = 'Tax Pay Art 4(2) -> 10% dari Amount Transaction'
                    amountTax = new Intl.NumberFormat().format(dataTransaction)
                } else if (selected == 'pph 23') {
                    tax = 'Tax Pay Art 23 -> 2% dari Amount Transaction'
                    amountTax = new Intl.NumberFormat().format(dataTransaction)
                } else if (selected == 'pph 26') {
                    tax = 'Tax Pay Art 26 -> 20% dari Amount Transaction'
                    amountTax = new Intl.NumberFormat().format(dataTransaction)
                }
                $('input[name="amount_tax"]').prop('required', true)
                $('input[name="amount_tax"]').val(amountTax)
                amountTax = amountTax.replaceAll(',', '')
                $('input[name="amount_net"]').val(new Intl.NumberFormat().format(amount-amountTax))
            } else {
                $("#amount_tax_view_id").hide()
                $("#amount_net_view_id").hide()
                $("input[name='amount_tax']").prop('required',false);
            }
            $("#note_tax_id").html('<b>' + tax + '</b>');
        });

        
        function addFile() {
            let i = $(".ct-app").length;
            let viewAppend = $("#file_view_id");
                viewAppend.append('' +
                    '<div class="w-full flex flex-col ct-app lg:flex-row mt-5" id="file_view_'+i+'_id">' +
                    '           <input type="hidden" name="transaction_files['+i+'][new_file]" data-new="'+i+'" id="file_name_'+i+'_id">' +
                    '            <div class="px-2 lg:w-1/4">\n' +
                    '                <div class="mt-1 sm:mt-0 col-span-2 sm:mt-2">\n' +
                    '                    <input type="file" name="transaction_files['+i+'][files]" required id="file_'+i+'_id" data-img="'+i+'" data-val="new" data-type="new" onchange="uploadFile('+i+')" ' +
                    'class="w-full focus:ring-indigo-500 focus:border-indigo-500  px-2 py-2 sm:text-sm border border-gray-200 rounded-md">\n' +
                    '                </div>\n' +
                    '            </div>\n' +
                    '            <div class="px-2 lg:w-1/4">\n' +
                    '                <div class="mt-1 sm:mt-0 col-span-2 sm:mt-2">\n' +
                    '                    <input type="text" name="transaction_files['+i+'][amount_file]" required placeholder="total amount reported" onkeyup="numberFormat2(this)" id="amount_report_id" required class="w-full focus:ring-indigo-500 focus:border-indigo-500  px-2 py-2 sm:text-sm border border-gray-200 rounded-md amount-reported">\n' +
                    '                </div>\n' +
                    '            </div>\n' +
                    '            <div class="px-2 lg:w-1/4">\n' +
                    '                <div class="mt-1 sm:mt-0 col-span-2 sm:mt-2">\n' +
                    '                    <input type="text" name="transaction_files['+i+'][note]" required placeholder="note" class="w-full focus:ring-indigo-500 focus:border-indigo-500  px-2 py-2 sm:text-sm border border-gray-200 rounded-md">\n' +
                    '                </div>\n' + 
                    '            </div>\n' +
                    '            <div class="px-2 lg:w-1/4">\n' +
                    '                <div class="mt-1 sm:mt-0 col-span-2 sm:mt-2">\n' +
                    '                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteFile('+i+')">Delete</button>\n' +
                    '                </div>\n' +
                    '            </div>' +
                    '        </div>');

        }

        function getJournal(id) {
            $.ajax({
               url: "{{ url('transaction/chart-of-accounts/get/') }}" + '/' + id,
                type: "get",
                success: function (response) {
                    const journal=$("#journal_field_id").data("journal")
                    if (response.status == true) {
                        var data = response.message
                        if (data.length > 0) {
                            $("#journal_field_id").append('' +
                                    `<option value=''>-- Select Account --</option>`);
                            for (let i = 0; i < data.length; ++i) {
                              if(journal===Number(data[i].id.split(",")[0])) {
                                $("#journal_field_id").append('' +
                                    `<option value='${data[i].id}' selected>${data[i].text}</option>`);
                              }
                                $("#journal_field_id").append('' +
                                    `<option value='${data[i].id}'>${data[i].text}</option>`);
                            }
                            $("#journal_field_id").select2();
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
        }

        function deleteFile(i,id) {
            var confirmVal = confirm("Are you sure to delete this file?");
            console.log(id);
            if (confirmVal == true) {
                    $("#file_view_" + i + "_id").remove();
                    var inputHiddenAppend = $('#input-hidden-delete-file');
                    var incre = 0;
                    if (id) {
                        inputHiddenAppend.append('' +
                        '<input type="hidden" name="delete-file[]" value="'+ id +'">' +
                        '        </div>');

                        incre = incre + 1;
                    }
            }else{
                    $("#file_view_" + i + "_id").remove();
            }
        }
        
        function uploadFile(i) {
            var data = $('*[data-img="'+i+'"]');
            let file_data = data.prop('files')[0];
            let type = data.data('val');
            let dataName = "data-"+type+'="'+i+'"'
            let preview = "#preview-"+i
            var form_data = new FormData();
            let warningFail = $('#file_'+ i + '_id')
            $('#cover-spin').show(0);
            form_data.append('file', file_data);
            form_data.append('_token', '{{ csrf_token() }}');
            $.ajax({
                url: window.origin+'/transaction/upload-file', // <-- point to server-side PHP script
                dataType: 'text',  // <-- what to expect back from the PHP script, if anything
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                type: 'post',
                success: function(result){
                    result = JSON.parse(result)
                    if (result != false) {
                        $('*['+dataName+']').val(result.name)
                        $(preview).attr("href", cdnUrl+result.name)
                        $(preview).text(result.name)
                        if(warningFail.hasClass('bg-yellow-400')) {
                            warningFail.removeClass('bg-yellow-400')
                        }
                        $('#cover-spin').hide()
                    } else {
                        $('#cover-spin').hide()  
                        warningFail.val(null)
                        warningFail.removeClass('border border-gray-200')
                        warningFail.addClass('border-4 border-yellow-500')
                        alert('Uploading file fail, please check your conection')
                    }
                },
                error: function(result) {
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

    </script>
@endsection

@section('content')

<div class="w-full mt-5">
  @include('frontend.layouts.alert-message')
</div>

<div class="w-full bg-white w-full px-2 py-3">
    <div id="cover-spin"></div>
    <form action="{{ url('transaction/update/' . $transaction->id) }}" method="post">
        {{ csrf_field() }}
        <div class="w-full flex flex-col lg:flex-row">
            <div class="lg:w-1/2 px-2">
                <label for="title" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                    Title <span class="text-red-600 font-bold italic">*) required</span>
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <input type="text" name="title" value="{{ isset($transaction) ? $transaction->title : old('title') }}" class="w-full focus:ring-indigo-500 focus:border-indigo-500  px-2 py-2 sm:text-sm border border-gray-200 rounded-md" required>
                </div>
            </div>
            <div class="lg:w-1/2 px-2">
                <label for="division" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                    Division <span class="text-red-600 font-bold italic">*) required</span>
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <select name="division" autocomplete="devision" required id="division_field_id"
                            class="rounded-md focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-200  py-2.5 px-2 bg-white">
                            <option value="">-- Select Division --</option>
                            @foreach($divisions as $division)
                            <option value="{{ $division->id }}" {{ (isset($transaction) AND $transaction->division_id == $division->id) ? "selected" : "" }}>
                                Division {{ $division->title }}
                            </option>
                            @endforeach
                    </select>
                </div>
            </div>
        </div>
        
        <div class="w-full flex flex-col lg:flex-row">
            <div class="lg:w-1/2 px-2">
                <label for="inquiry" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                    Inquiry / Account Number <span class="text-red-600 font-bold italic">*) required</span>
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <select name="inquiry" autocomplete="inquiry" required class="rounded-md focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-200  py-2.5 px-2 bg-white">
                            <option value="" selected>
                                -- Select Inquiry / Account Number --
                            </option>
                            @foreach($inquiries as $inquiry)
                                <option value="{{ $inquiry->id }}" {{ $inquiry->status != 'SUCCESS' ? 'disabled' : '' }} {{ (isset($transaction) AND $transaction->inquiry_id == $inquiry->id) ? "selected" : "" }}>
                                    {{ $inquiry->name_by_input ? $inquiry->name_by_input : $inquiry->name_by_server }} | {{ $inquiry->account_number }} | Status: {!! $inquiry->status_label !!}
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
                    <input type="text" name="remark" id="remark_transaction_id" value="{{ isset($transaction) ? $transaction->remark : old('remark') }}" required class="w-full focus:border-rose-500 px-2 py-2 sm:text-sm border border-gray-200 rounded-md"
                    onkeyup="checkLongerCharacter()" placeholder="remark not more than 18 character and not allowed special character">
                </div>
            </div>
        </div>

        <div class="w-full flex flex-col lg:flex-row">
            <div class="lg:w-1/2 px-2">
                <label for="amount" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                    Amount <span class="text-red-600 font-bold italic">*) required</span>
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                <input type="text" name="amount" required id="amount_transaction_id" value="{{ isset($transaction) ? number_format($transaction->amount + $transactionTax) : old('amount') }}" 
                       class="w-full focus:ring-indigo-500 focus:border-indigo-500  px-2 py-2 sm:text-sm border border-gray-200 rounded-md number_transaction">
                </div>
            </div>
            <div class="lg:w-1/2 px-2" id="journal_view_id">
                <label for="journal" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                    Journal
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <select name="journal" autocomplete="journal" id="journal_field_id"
                        class="rounded-md focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-200  py-2.5 px-2 bg-white" data-journal="{{ $transaction->ott_code }}">
                       
                    </select>
                </div>
            </div>
        </div>

        <div class="w-full flex flex-col lg:flex-row">
            <div class="lg:w-1/2 px-2">
                <label for="journal" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                    Has Tax?
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <select name="exist_tax" id="has_tax_field_id" 
                            class="rounded-md focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-200  py-2.5 px-2 bg-white">
                        <option value="no_tax" {{ isset($transactionTypeTax) && $transactionTypeTax == 'no_tax' ? 'selected' : '' }}>No Tax</option>
                        <option value="pph 21" {{ isset($transactionTypeTax) && $transactionTypeTax == 'pph 21' ? 'selected' : '' }}>Pph 21</option>
                        <option value="pph 4(2)" {{ isset($transactionTypeTax) && $transactionTypeTax == 'pph 4(2)' ? 'selected' : '' }}>Pph 4(2)</option>
                        <option value="pph 23" {{ isset($transactionTypeTax) && $transactionTypeTax == 'pph 23' ? 'selected' : '' }}>Pph 23</option>
                        <option value="pph 26" {{ isset($transactionTypeTax) && $transactionTypeTax == 'pph 26' ? 'selected' : '' }}>Pph 26</option>
                    </select>
                    <label for="" id="note_tax_id"></label>
                </div>
            </div>
            <div class="lg:w-1/2 px-2" id="amount_tax_view_id" hidden>
                <label for="amount_tax" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                    Tax Amount
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <input type="text" id="amount_tax_id" name="amount_tax" value="{{ isset($transactionTax) ? number_format($transactionTax) : '' }}"  onkeyup="calculateTax()" class="w-full focus:ring-indigo-500 focus:border-indigo-500  px-2 py-2 sm:text-sm border border-gray-200 rounded-md number_transaction">
                </div>
            </div>
        </div>

        <div class="w-full flex flex-col lg:flex-row">
            <div class="lg:w-1/2 px-2" id="amount_net_view_id" hidden>
                <label for="amount_net" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                    Amount After Tax<span class="text-red-600 font-bold italic">*) is the amount (Amount - Tax Amount).</span>
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <input type="text" name="amount_net" id="amount_net_id" value="{{ isset($transaction) ? number_format($transaction->amount_net) : '' }} : '' }}"  class="w-full focus:ring-indigo-500 focus:border-indigo-500  px-2 py-2 sm:text-sm border border-gray-200 rounded-md number_transaction">
                </div>
            </div>
        </div>

        <div class="w-full mt-5 ">
            <div class="px-2">
                <label for="description" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                    Description <span class="text-red-600 font-bold italic">*) required</span>
                </label>
                <textarea name="description" value="{{ isset($transaction) ? $transaction->description : old('description') }}"  rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-300 rounded-md">{{ $transaction->description }}</textarea>
            </div>
            <div id="input-hidden-delete-file">
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