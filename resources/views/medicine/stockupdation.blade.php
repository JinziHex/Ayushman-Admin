@extends('layouts.app')
@section('content')
@php
use App\Models\Mst_Staff;
@endphp
    <style>
    
     .table th {   font-size: 12px;} select.medsearch { display: none !important; } span.current {
    font-size: 10px!important; } .table td {padding: 5px 3px;} .pricecard .form-group label {font-size: 12px;margin:0;} .pricecard .form-group span, .pricecard .form-group input {width: auto;padding: 0;border: unset;line-height: 1;height:auto;} .pricecard .form-group input:focus {outline: unset !important;border: none !important;} .pricecard .form-group {display: flex;align-items: center;gap: 16px;margin: 5px 0;}.pricecard .col-md-4 {margin-left: auto;}.dropdown-select {background-image: linear-gradient(to bottom, rgba(255, 255, 255, 0.25) 0%, rgba(255, 255, 255, 0) 100%);background-repeat: repeat-x;filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#40FFFFFF', endColorstr='#00FFFFFF', GradientType=0);background-color: #fff;border-radius: 6px;box-sizing: border-box;cursor: pointer;display: block;float: left;font-size: 14px;font-weight: normal;outline: none;padding-left: 18px;padding-right: 30px;position: relative;text-align: left !important;transition: all 0.2s ease-in-out;-webkit-user-select: none;-moz-user-select: none; -ms-user-select: none;user-select: none;white-space: nowrap; width: auto;}.dropdown-select:focus {background-color: #fff;}.dropdown-select:hover {background-color: #fff;}.dropdown-select:active,.dropdown-select.open {background-color: #fff !important;border-color: #bbb;box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05) inset;}.dropdown-select:after {height: 0; width: 0; border-left: 4px solid transparent; border-right: 4px solid transparent; border-top: 4px solid #777;-webkit-transform: origin(50% 20%); transform: origin(50% 20%); transition: all 0.125s ease-in-out; content: ''; display: block; margin-top: -2px; pointer-events: none; position: absolute; right: 10px; top: 50%;}.dropdown-select.open:after { -webkit-transform: rotate(-180deg); transform: rotate(-180deg);}.dropdown-select.open .list { -webkit-transform: scale(1); transform: scale(1); opacity: 1; pointer-events: auto;}.dropdown-select.open .option {cursor: pointer;}.dropdown-select.wide {width: 100%;}.dropdown-select.wide .list {left: 0 !important;right: 0 !important;}.dropdown-select .list {box-sizing: border-box; transition: all 0.15s cubic-bezier(0.25, 0, 0.25, 1.75), opacity 0.1s linear; -webkit-transform: scale(0.75); transform: scale(0.75); -webkit-transform-origin: 50% 0; transform-origin: 50% 0; box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.09); background-color: #fff; border-radius: 6px; margin-top: 4px; padding: 3px 0; opacity: 0; overflow: hidden; pointer-events: none; position: absolute; top: 100%; left: 0; z-index: 999; max-height: 250px; overflow: auto; border: 1px solid #ddd;}.dropdown-select .list:hover .option:not(:hover) { background-color: transparent !important;}.dropdown-select .dd-search{overflow:hidden;display:flex;align-items:center;justify-content:center;margin:5px 0;}.dropdown-select .dd-searchbox{width:90%;padding:0.5rem;border:1px solid #999;border-color:#999;border-radius:4px;outline:none;line-height: 1;}.dropdown-select .dd-searchbox:focus{border-color:#12CBC4;}.dropdown-select .list ul { padding: 0;}.dropdown-select .option {cursor: default; font-weight: 400; line-height: 2; outline: none; padding-left: 10px; padding-right: 25px; text-align: left; transition: all 0.2s; list-style: none; font-size: 10px;}.dropdown-select .option:hover,.dropdown-select .option:focus {background-color: #f6f6f6 !important;}.dropdown-select .option.selected { font-weight: 600; color: #12cbc4;}.dropdown-select .option.selected:focus {background: #f6f6f6;}.dropdown-select a {color: #aaa; text-decoration: none; transition: all 0.2s ease-in-out;}.dropdown-select a:hover {
    color: #666;}
    
    
        .card-header {
            display: flex;
            justify-content: space-between;
        }

        .card-title {
            margin-top: 0;
            /* Optional: Adjust margin if needed */
        }

    .equal-width-td {
        width: 14.28%;
    }
.list li.option[disabled] {
    display: none;
}
select.form-control.medsearch.errorSelect + .dropdown-select {
    border-color: red !important;
}
.dropdown-select .list ul {
    padding: 0;
    height: auto;
    overflow: auto;
}
.dropdown-select .list
{
    top:150px !important;
    left:20px !important;
   
}
.display-med-row {
      display: none;
   }
   .dropdown-select.wide .list {
    z-index: 999999 !important;
    min-height: 238px !important;
    position: absolute !important;
    max-width: 245px;
}
   table#productTable tbody tr:nth-child(2) .no-click {
    pointer-events: none;
}
    </style>
 
        <div class="row" style="min-height: 70vh;">
            <div class="col-md-12">
                <div class="card">
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success">
                            <p>{{ $message }}</p>
                        </div>
                    @endif
                    @if ($message = Session::get('errors'))
                        <div class="alert alert-danger">
                            <p>{{ $errors }}</p>
                        </div>
                    @endif
                    <div class="card-header">
                        <div class="col-md-6">
                            <h3 class="mb-0 card-title">Medicine Initial Stock Update</h3>
                        </div>
                    </div>
                    <div class="col-lg-12" style="background-color: #fff;">
   
                        <form action="{{ route('updatestockmedicine') }}" method="POST" id="myForm" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="_method" value="PUT">
                            <input type="hidden" name="_method" value="PUT">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Pharmacy*</label>
                                       @if(Auth::check() && Auth::user()->user_type_id == 96)
                                       @php
                                        $staff = Mst_Staff::findOrFail(Auth::user()->staff_id);
                                        $mappedpharma = $staff->pharmacies()->pluck('mst_pharmacies.id')->toArray();
                                       @endphp
                                      
                                           <select class="form-control" name="pharmacy_id" id="pharmacy_id" required>
                                               <option value="">Select Pharmacy</option>
                                               @foreach ($pharmacies as $pharmacy)
                                                   @if(in_array($pharmacy->id, $mappedpharma))
                                                       <option value="{{ $pharmacy->id }}">{{ $pharmacy->pharmacy_name }}</option>
                                                   @endif
                                               @endforeach
                                           </select>
                                       
                                   @else
                                    @if(session()->has('pharmacy_id') && session()->has('pharmacy_name') && session('pharmacy_id') != "all")
                                           <select class="form-control" name="pharmacy_id" id="pharmacy_id" required readonly>
                                               <option value="{{ session('pharmacy_id') }}">{{ session('pharmacy_name') }}</option>
                                           </select>
                                       @else
                                       <select class="form-control" name="pharmacy_id" id="pharmacy_id" required>
                                           <option value="">Select Pharmacy</option>
                                           @foreach ($pharmacies as $pharmacy)
                                               <option value="{{ $pharmacy->id }}">{{ $pharmacy->pharmacy_name }}</option>
                                           @endforeach
                                       </select>
                                       @endif
                                   @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-lg-12">
                                    <div class="card">
                                        <div class="table-responsive">
                                            <table class="table card-table table-vcenter text-nowrap" id="productTable" style="min-height: 200px;">
                                                <thead>
                                                    <tr>
                                                        <th style="min-width:250px;">Medicine</th>
                                                        <th>Batch No</th>
                                                        <th>MFD / EXP</th>
                                                        <th>Stock</th>
                                                        <th>Tax %</th>
                                                        <th>Purchase<br>rate<br><span style="text-transform: none;font-size: 8px !important;">(Including GST)</span></th>
                                                        <th>Sale<br>Rate<br><span style="text-transform: none;font-size: 8px !important;">(Excluding GST)</span></th>
                                                        <th>Sale<br>Rate<br><span style="text-transform: none;font-size: 8px !important;">(Including GST)</span></th>
                                                        <th>Actions</th>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr id="productRowTemplate" style="display: none">
                                                        <td class="equal-width-td">
                                                            <select class="form-control medicine-name medsearch" name="medicine_id[]"
                                                                dis>
                                                                <option value="">Please select medicine</option>
                                                                
                                                                @foreach ($meds as $id => $medicine)
                                                                <option value="{{ $medicine->id }}">{{ $medicine->medicine_name }} - {{$medicine->medicine_code}}</option>
                                                                @endforeach

                                                            </select>
                                                        </td>
                                                        <td class="medicine-batch-no equal-width-td">
                                                            <input type="text" class="form-control"  name="batch_no[]" id="batch_no">
                                                    </td>
                                                        <td class="medicine-stock-mfd equal-width-td">
                                                            <input type="date" class="form-control"  name="mfd[]" id="mfd"><br>
                                                            <input type="date" class="form-control"  name="expd[]" id="expd">
                                                        </td>
                                                        <td class="medicine-stock equal-width-td">
                                                            <input type="number" class="form-control" min="0"  name="new_stock[]" placeholder="New Stock"></td>
                                                            
                                                        <td class="medicine-tax equal-width-td">
                                                            <input type="number" class="form-control" min="0"  name="tax[]" placeholder="Tax%"></td>
                                                        <td class="medicine-purchase-rate equal-width-td">
                                                            <input type="text" class="form-control"  name="purchase_rate[]" placeholder="Purchase Rate" oninput="if(this.value.length === 1 && this.value === '0') { this.value = ''; } else { this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1'); }"></td>
                                                        <td class="medicine-sale-rate equal-width-td">
                                                            
                                                            <input type="text" class="form-control sale-rate"  name="sale_rate_Old[]" placeholder="Sale Rate" ></td>
                                                        <td class="medicine-sale-rate equal-width-td">
                                                            <input type="text" class="form-control sale-rate"  name="sale_rate[]" placeholder="Sale Rate" oninput="if(this.value.length === 1 && this.value === '0') { this.value = ''; } else { this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1'); }">
                                                            
                                                        <td class="equal-width-td"><button type="button" onclick="removeFn(this)"
                                                                style="background-color: #007BFF; color: #FFF; padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer;" class="no-click">Remove</button>
                                                        </td>
                                                        <td class="display-med-row medicine-stock-id">
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-primary" id="addProductBtn">Add Row</button>
                                </div>
                            </div>
                            <div class="form-group">
                                <center>
                                    <button type="submit" class="btn btn-raised btn-primary"> Update</button>
                                    <button type="reset" class="btn btn-raised btn-success">
                                        Reset</button>
                                </center>
                            </div>
                    </div>
                </div>
                </form>
            </div>
        </div>
 
@endsection
@section('js')
    <!-- <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script> -->
    <script src="https://cdn.ckeditor.com/ckeditor5/34.0.1/classic/ckeditor.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>



    <!-- Add the correct path to the CKEditor script -->
    <script src="https://cdn.ckeditor.com/ckeditor5/34.0.1/classic/ckeditor.js"></script>

    <script>
 
        function removeFn(parm) {
            var currentRow = $(parm).closest('tr');
            currentRow.remove();
        }
    $(document).ready(function() {
        $("#addProductBtn").click(function(event) {
            event.preventDefault();
            var newRow = $("#productRowTemplate").clone().removeAttr("style");
            
            newRow.find('td').addClass('equal-width-td');
            newRow.find('select.medicine-name').addClass('medicine-select');
            newRow.find('input').val('').prop('readonly', false);
            newRow.find('input').siblings('span').remove(); 
            
           
            newRow.find('input[name="mfd[]"]').val('');
            newRow.find('input[name="expd[]"]').val('')
            
            $("#productTable tbody").append(newRow);
            //fetchBatchDetailsForRow(newRow);
            
           initializeValidation();
        });
        
    });

    </script>
<script>
     $(document).ready(function() {
       //  alert("test")
        $("#addProductBtn").click();
    });
</script>
<script>
jQuery(document).ready(function($) {
    var validationRules = {
    pharmacy_id: 'required',
    'medicine_id[]': 'required',
    'batch_no[]': 'required',
    'mfd[]': 'required',
    'expd[]': 'required',
    'new_stock[]': {
        required: true,
        min: 0
    },
    'purchase_rate[]': 'required',
    'sale_rate[]': 'required'
};

var validationMessages = {
    pharmacy_id: 'Please select a pharmacy',
    'medicine_id[]': 'Please select a medicine',
    'batch_no[]': 'Please enter a batch number',
    'mfd[]': 'Please enter a manufacturing date',
    'expd[]': 'Please enter an expiry date',
    'new_stock[]': {
        required: 'Please enter the new stock',
        min: 'Stock cannot be negative'
    },
    'purchase_rate[]': 'Please enter the purchase rate',
    'sale_rate[]': 'Please enter the sale rate'
};
var validationGroups = {
    'medicine_id[]': 'medicine',
    'batch_no[]': 'batch',
    'mfd[]': 'date',
    'expd[]': 'date',
    'new_stock[]': 'stock',
    'purchase_rate[]': 'rate',
    'sale_rate[]': 'rate'
};

function initializeValidation() {
    $('#myForm').validate({
        rules: validationRules,
        messages: validationMessages,
        groups: validationGroups,
        ignore: ":hidden:not(#myForm tr:first)"
    });
}

initializeValidation();
});
$(document).ready(function() {
    // Set min and initial value for expd[] inputs based on mfd[] inputs
    $('input[name="mfd[]"]').each(function() {
        var mfdValue = $(this).val();
        if (mfdValue) {
            var mfdDate = new Date(mfdValue);
            mfdDate.setDate(mfdDate.getDate() + 1);
            var minExpDate = mfdDate.toISOString().split('T')[0];
            var expInput = $(this).closest('tr').find('input[name="expd[]"]');
            expInput.attr('min', minExpDate);
            expInput.val(minExpDate);
        }
    });

    // Update min attribute of expd[] inputs when mfd[] inputs change
    $(document).on('change', 'input[name="mfd[]"]', function() {
        var mfdValue = $(this).val();
        if (mfdValue) {
            var expInput = $(this).closest('tr').find('input[name="expd[]"]');
            expInput.attr('min', mfdValue);
            // If you want to clear the expd[] value when mfd[] is changed, uncomment the next line
            expInput.val('');
        }
    });
});

</script>


<script>
function filter(input) {
    var valThis = $(input).val();
    $(input).closest('.dropdown-select').find('ul > li').each(function () {
        var text = $(this).text();
        (text.toLowerCase().indexOf(valThis.toLowerCase()) > -1) ? $(this).show() : $(this).hide();
    });
}

// Ensure the DOM is ready before executing the script
$(document).ready(function () {
    // Function to create custom dropdowns
    function create_custom_dropdowns() {
        $('select.medsearch').each(function (i, select) {
            if (!$(this).next().hasClass('dropdown-select')) {
                $(this).after('<div class="dropdown-select wide ' + ($(this).attr('class') || '') + '" tabindex="0"><span class="current"></span><div class="list"><ul></ul></div></div>');
                var dropdown = $(this).next();
                var options = $(select).find('option');
                var selected = $(this).find('option:selected');
                dropdown.find('.current').html(selected.data('display-text') || selected.text());
                options.each(function (j, o) {
                    var display = $(o).data('display-text') || '';
                    var disabledAttribute = $(o).is(':disabled') ? 'disabled' : '';
                    dropdown.find('ul').append('<li class="option ' + ($(o).is(':selected') ? 'selected' : '') + '" data-value="' + $(o).val() + '" data-display-text="' + display + '" ' + disabledAttribute + '>' + $(o).text() + '</li>');
                });
            }
            $(this).next().find('ul').before('<div class="dd-search"><input id="" autocomplete="off" onkeyup="filter(this)" class="dd-searchbox txtSearchValue" type="text"></div>');
            
            
        });
    }

    // Call the function to create custom dropdowns
    create_custom_dropdowns();
    
    $(document).on('change', 'select.medsearch', function() {
        var selectedMedicineId = $(this).val();
        var passThis = $(this);
        //alert(selectedMedicineId);
        var saleRateField = $(this).closest('tr').find('input[name="sale_rate[]"]');
        var saleRateFieldOld = $(this).closest('tr').find('input[name="sale_rate_Old[]"]');
        var taxRateField = $(this).closest('tr').find('input[name="tax[]"]');
        
    if(selectedMedicineId) {
        // AJAX request to fetch unit price
        $.ajax({
            type: 'GET',
            url: '/initialstock/getUnitPrice/' + selectedMedicineId,
            success: function(response) {
                if (response.success) {
                    saleRateField.val(response.unitPrice);
                    taxRateField.val(response.medicine_tax_rate);
                    saleRateFieldOld.val(response.unitPrice);
                    
                    changeSalesprice(passThis)
                    
                } else {
                    saleRateField.val('');
                    alert('Failed to fetch unit price for the selected medicine.');
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                alert('An error occurred while fetching unit price.');
            }
        });
    }
        
    });
     $(document).on('input', 'input[name="sale_rate_Old[]"]', function() {
         var passThis = $(this);
         changeSalesprice(passThis);
     });
     
    
    function changeSalesprice(passThis) {
        var saleRateField = parseFloat (passThis.closest('tr').find('input[name="sale_rate[]"]').val());
        var saleRateFieldOld = parseFloat(passThis.closest('tr').find('input[name="sale_rate_Old[]"]').val());
        var taxRateField = parseFloat(passThis.closest('tr').find('input[name="tax[]"]').val());
       // alert(taxRateField)
       
        var totalC = 0;
        var taxAmount = (saleRateFieldOld / 100) * taxRateField ;
        totalC = saleRateFieldOld + taxAmount
        passThis.closest('tr').find('input[name="sale_rate[]"]').val(totalC.toFixed(2));
        
    }
    $(document).on('input', 'input[name="sale_rate[]"]', function() {
          var passThis = $(this);
        // console.log("tets")
         var saleRateField = parseFloat (passThis.closest('tr').find('input[name="sale_rate[]"]').val());
       // var saleRateFieldOld = parseFloat(passThis.closest('tr').find('input[name="sale_rate_Old[]"]').val());
        var taxRateField = parseFloat(passThis.closest('tr').find('input[name="tax[]"]').val());
       // alert(taxRateField)
       
       var totalC = 0;
        var taxAmount = saleRateField * taxRateField / 100;
        // console.log(saleRateField);
        // console.log(taxRateField);
        totalC = saleRateField / (1 + taxRateField / 100);
        
        //console.log(totalC);
        passThis.closest('tr').find('input[name="sale_rate_Old[]"]').val(totalC.toFixed(2));
     });
    
    //  $('#productTable tbody tr').not(':first').hide();

    // Event listeners

// Open/close
    $(document).on('click', '.dropdown-select', function (event) {
        if($(event.target).hasClass('dd-searchbox')){
            return;
        }
        $('.dropdown-select').not($(this)).removeClass('open');
        $(this).toggleClass('open');
        if ($(this).hasClass('open')) {
            $(this).find('.option').attr('tabindex', 0);
            $(this).find('.selected').focus();
            //alert("test")
            
            $('.table-responsive').css('position', 'unset');
            $('.table-responsive *').css('position', 'unset');
            
            var tableOffset = $(this).closest('.card').offset();
             var trOffset = $(this).closest("tr").offset();
            var trHeight = $(this).closest("tr").height();
            // var trWidth = $(this).width();
            var x = trOffset.top - tableOffset.top
            
            console.log("tttt"+ trOffset);
            console.log(trHeight)
            var item = $(this).closest("tr").find('.list');
            item.css({
              display: 'block',
              top: x + trHeight - 5 + 'px',
              //left: trOffset.left + trWidth + 'px'
            });
            
            
        } else {
            $(this).find('.option').removeAttr('tabindex');
            $(this).focus();
            
            $('.table-responsive').css('position', 'relative');
            $('.table-responsive *').css('position', 'relative');
        }
    });

    // Close when clicking outside
    $(document).on('click', function (event) {
        if ($(event.target).closest('.dropdown-select').length === 0) {
            $('.dropdown-select').removeClass('open');
            $('.dropdown-select .option').removeAttr('tabindex');
            
            $('.table-responsive').css('position', 'relative');
            $('.table-responsive *').css('position', 'relative');
        }
        event.stopPropagation();
    });

    // Option click
    $(document).on('click', '.dropdown-select .option', function (event) {
        $(this).closest('.list').find('.selected').removeClass('selected');
        $(this).addClass('selected');
        var text = $(this).data('display-text') || $(this).text();
        $(this).closest('.dropdown-select').find('.current').text(text);
        $(this).closest('.dropdown-select').prev('select').val($(this).data('value')).trigger('change');
    });

    // Keyboard events
    $(document).on('keydown', '.dropdown-select', function (event) {
        var focused_option = $($(this).find('.list .option:focus')[0] || $(this).find('.list .option.selected')[0]);
        // Space or Enter
        //if (event.keyCode == 32 || event.keyCode == 13) {
        if (event.keyCode == 13) {
            if ($(this).hasClass('open')) {
                focused_option.trigger('click');
            } else {
                $(this).trigger('click');
            }
            return false;
            // Down
        } else if (event.keyCode == 40) {
            if (!$(this).hasClass('open')) {
                $(this).trigger('click');
            } else {
                focused_option.next().focus();
            }
            return false;
            // Up
        } else if (event.keyCode == 38) {
            if (!$(this).hasClass('open')) {
                $(this).trigger('click');
            } else {
                var focused_option = $($(this).find('.list .option:focus')[0] || $(this).find('.list .option.selected')[0]);
                focused_option.prev().focus();
            }
            return false;
            // Esc
        } else if (event.keyCode == 27) {
            if ($(this).hasClass('open')) {
                $(this).trigger('click');
            }
            return false;
        }
    });
});
</script>


