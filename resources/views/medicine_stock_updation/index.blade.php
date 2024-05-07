@extends('layouts.app')
@section('content')
@php
use App\Models\Mst_Staff;
@endphp
<style>
    .readinput {
    background-color: #e9ecef !important;
}

.table th {   font-size: 12px;} select.medsearch { display: none !important; } span.current {
    font-size: 10px!important; } .table td {padding: 5px 3px;} .pricecard .form-group label {font-size: 12px;margin:0;} .pricecard .form-group span, .pricecard .form-group input {width: auto;padding: 0;border: unset;line-height: 1;height:auto;} .pricecard .form-group input:focus {outline: unset !important;border: none !important;} .pricecard .form-group {display: flex;align-items: center;gap: 16px;margin: 5px 0;}.pricecard .col-md-4 {margin-left: auto;}.dropdown-select {background-image: linear-gradient(to bottom, rgba(255, 255, 255, 0.25) 0%, rgba(255, 255, 255, 0) 100%);background-repeat: repeat-x;filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#40FFFFFF', endColorstr='#00FFFFFF', GradientType=0);background-color: #fff;border-radius: 6px;box-sizing: border-box;cursor: pointer;display: block;float: left;font-size: 14px;font-weight: normal;outline: none;padding-left: 18px;padding-right: 30px;position: relative;text-align: left !important;transition: all 0.2s ease-in-out;-webkit-user-select: none;-moz-user-select: none; -ms-user-select: none;user-select: none;white-space: nowrap; width: auto;}.dropdown-select:focus {background-color: #fff;}.dropdown-select:hover {background-color: #fff;}.dropdown-select:active,.dropdown-select.open {background-color: #fff !important;border-color: #bbb;box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05) inset;}.dropdown-select:after {height: 0; width: 0; border-left: 4px solid transparent; border-right: 4px solid transparent; border-top: 4px solid #777;-webkit-transform: origin(50% 20%); transform: origin(50% 20%); transition: all 0.125s ease-in-out; content: ''; display: block; margin-top: -2px; pointer-events: none; position: absolute; right: 10px; top: 50%;}.dropdown-select.open:after { -webkit-transform: rotate(-180deg); transform: rotate(-180deg);}.dropdown-select.open .list { -webkit-transform: scale(1); transform: scale(1); opacity: 1; pointer-events: auto;}.dropdown-select.open .option {cursor: pointer;}.dropdown-select.wide {width: 100%;}.dropdown-select.wide .list {left: 0 !important;right: 0 !important;}.dropdown-select .list {box-sizing: border-box; transition: all 0.15s cubic-bezier(0.25, 0, 0.25, 1.75), opacity 0.1s linear; -webkit-transform: scale(0.75); transform: scale(0.75); -webkit-transform-origin: 50% 0; transform-origin: 50% 0; box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.09); background-color: #fff; border-radius: 6px; margin-top: 4px; padding: 3px 0; opacity: 0; overflow: hidden; pointer-events: none; position: absolute; top: 100%; left: 0; z-index: 999; max-height: 250px; overflow: auto; border: 1px solid #ddd;}.dropdown-select .list:hover .option:not(:hover) { background-color: transparent !important;}.dropdown-select .dd-search{overflow:hidden;display:flex;align-items:center;justify-content:center;margin:5px 0;}.dropdown-select .dd-searchbox{width:90%;padding:0.5rem;border:1px solid #999;border-color:#999;border-radius:4px;outline:none;line-height: 1;}.dropdown-select .dd-searchbox:focus{border-color:#12CBC4;}.dropdown-select .list ul { padding: 0;}.dropdown-select .option {cursor: default; font-weight: 400; line-height: 2; outline: none; padding-left: 10px; padding-right: 25px; text-align: left; transition: all 0.2s; list-style: none; font-size: 10px;}.dropdown-select .option:hover,.dropdown-select .option:focus {background-color: #f6f6f6 !important;}.dropdown-select .option.selected { font-weight: 600; color: #12cbc4;}.dropdown-select .option.selected:focus {background: #f6f6f6;}.dropdown-select a {color: #aaa; text-decoration: none; transition: all 0.2s ease-in-out;}.dropdown-select a:hover {
    color: #666;}
    select.form-control.medsearch.errorSelect + .dropdown-select {
    border-color: red !important;
}
.dropdown-select.wide .list {
    z-index: 999999 !important;
    min-height: 238px !important;
    position: absolute !important;
    /*max-width: 245px;*/
}
.dropdown-select .list ul {
    height: auto!important;
}
.dropdown-select .option {
    line-height: 1.8!important;
}
.dropdown-select .option {
    font-size: 12px!important;
}
/*.dropdown-select.wide .list {*/
/*    left: 29px !important;*/
/*}*/

   

</style>
<!--<div class="container">-->
   <div class="row" style="min-height: 70vh;">
      <div class="col-md-12">
         <div class="card">
            @if ($message = Session::get('success'))
            <div class="alert alert-success">
               <p>{{$message}}</p>
            </div>
            @endif
            @if ($message1 = Session::get('msg'))
            <div class="alert alert-danger">
               <p>{{$message1}}</p>
            </div>
            @endif
            <div class="card-header">
               <h3 class="mb-0 card-title">Medicine Stock Correction</h3>
            </div>
            <div class="col-lg-12" style="background-color: #fff;">
               @if ($errors->any())
               <div class="alert alert-danger">
                  <strong>Whoops!</strong> There were some problems with your input.<br><br>
                  <ul>
                     @foreach ($errors->all() as $error)
                     <li>{{ $error }}</li>
                     @endforeach
                  </ul>
               </div>
               @endif
               <form action="{{ route('update.medicine.stocks') }}" method="POST" id="form" enctype="multipart/form-data" onsubmit="return validateAdjustments()">
                @csrf
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
                     <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Medicine *</label>
                            <select class="form-control medsearch" required name="medicine" id="medicine">
                                <option value="">Choose Medicine</option>
                                @foreach($medicines as $id => $value)
                                <option value="{{ $id }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="form-group">
                           <label class="form-label">Generic Name</label>
                           <input type="text" class="form-control"  name="generic_name" id="generic_name"  placeholder="Generic Name" readonly style="background-color: #e9ecef !important;">
                           <input type="hidden" class="form-control" required name="unit_id" id="unit_id">
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Batch No*</label>
                            <select class="form-control"  name="batch_no" id="batch_no">
                                <option value="">Choose Batch No</option>
                                <!-- Batch numbers will be dynamically added here -->
                            </select>
                        </div>
                    </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label" for="mfd">Manufacture Date</label>
                        <input type="date" class="form-control readinput" required name="mfd" id="mfd" placeholder="Manufacture Date">
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label" for="expd">Expiry Date</label>
                        <input type="date" class="form-control readinput" required name="expd" id="expd" placeholder="Expiry Date">
                    </div>
                </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Current Stock</label>
                            <input type="text" class="form-control" required name="current_stock" id="current_stock" placeholder="Current Stock" readonly style="background-color: #e9ecef !important;">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group col-auto">
                            <label class="form-label">Adjustments*</label>
                            <label class="selectgroup-item" style="width: 80px">
                                <input type="radio" name="stock-updt-val" value="1" class="selectgroup-input" onchange="handleAdjustmentChange(this)">
                                <span class="selectgroup-button"> + </span>
                            </label>
                            <label class="selectgroup-item" style="width: 80px">
                                <input type="radio" name="stock-updt-val" value="2" class="selectgroup-input" onchange="handleAdjustmentChange(this)">
                                <span class="selectgroup-button"> - </span>
                            </label>
                            <span id="adjustment-error" class="text-danger" style="display: none;">Please select either an increment or a decrement.</span>
                        </div>
                    </div>
               <div class="col-md-3">
                        <div class="form-group">
                           <label class="form-label">Adjustment Stock*</label>
                           <input type="number" class="form-control" id="numericInputs"  min="1" name="adjustment_stock" placeholder="Adjustmemt Stock"  required>
                        </div>
                     </div>

                     <div class="col-md-3">
                        <div class="form-group">
                           <label class="form-label">New Stock</label>
                           <input type="number" class="form-control numericInput readinput"  min="0" name="new_stock" placeholder="New Stock" readonly>
                        </div>
                     </div>

                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Purchase Rate</label>
                           <input type="text" class="form-control"  pattern="\d+(\.\d{0,2})?" id="numericInput1"  required id="purchase_rate" name="purchase_rate" placeholder="Purchase Rate" min="1" readonly="" style="background-color: #e9ecef !important;">

                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">MRP (Tax Inclusive)</label>
                           <input type="text" class="form-control" readonly pattern="\d+(\.\d{0,2})?"  id="numericInput2" required name="sale_rate" id="sale_rate" placeholder="MRP (Tax Inclusive)" style="background-color: #e9ecef !important;">
                        </div>
                     </div>

                     <div class="col-md-12">
                        <div class="form-group">
                           <label class="form-label">Remarks *</label>
                           <textarea class="form-control" name="remarks"></textarea>
                        </div>
                     </div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-md-12">
                       <!-- An empty column that spans the entire width -->
                    </div>
                 </div>
                 <div class="row mb-3">
                    <div class="col-md-12">
                       <!-- An empty column that spans the entire width -->
                    </div>
                 </div>
                  <!-- ... -->
                  <div class="form-group">
                     <center>
                        <button type="submit" class="btn btn-raised btn-primary">
                        <i class="fa fa-check-square-o"></i> Update</button>
                        <button type="reset" class="btn btn-raised btn-success">
                        Reset</button>
                     </center>
                  </div>
            </div>
         </div>
         </form>
      </div>
   </div>
<!--</div>-->
@endsection
@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>


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
    
     $('#productTable tbody tr').not(':first').hide();

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

<script>

    var incrementCheckbox = document.querySelector('input[name="increment_current_stock"]');
    var decrementCheckbox = document.querySelector('input[name="decrement_current_stock"]');



    // Fetch generic name, batch numbers, and current stock based on selected medicine
        $(document).on('change', '#medicine', function (event) {
       // alert("test")
        //var medicineId = this.value;
        var medicineId = $(this).val();
        if (medicineId === '') {
            // Empty the batch number dropdown
            $('#batch_no').html('<option value="">Choose Batch No</option>');
            $('#generic_name').val('');
            $('#current_stock').val('');
            $('#numericInput1').val('');
            $('#numericInput2').val('');
            var currDate = new Date().toISOString().slice(0, 10);
            $('#mfd').val(currDate);
            $('#expd').val(currDate);
            return; // Exit the function early
        }

        // Make an AJAX request to get the generic name
        $.ajax({
            url: '/get-generic-name/' + medicineId,
            type: 'GET',
            success: function (data) {
                // Update the value of the 'generic_name' input field
                document.getElementById('generic_name').value = data.generic_name;
            },
            error: function (error) {
                console.error('Error fetching generic name:', error);
            }
        });

        $.ajax({
                url: '/get-unit-ids/' + medicineId,
                type: 'GET',
                success: function (data) {
                    // Update the unit_id input field with the fetched unit ID
                    $('#unit_id').val(data.unit_id);
                },
                error: function (error) {
                    console.error('Error fetching unit ID:', error);
                }
            });


        // Make an AJAX request to get batch numbers
        $.ajax({
            url: '/get-batch-numbers/' + medicineId,
            type: 'GET',
            success: function (data) {
                console.log(data)
                // Update the options of the 'batch_no' dropdown
                var batchNoDropdown = document.getElementById('batch_no');
                 batchNoDropdown.innerHTML = '<option value="">Choose Batch No</option>';
                data.batch_numbers.forEach(function (batchData) {
                var option = document.createElement('option');
                var text = batchData.batch_no + ' (';
                for (var key in batchData) {
                    if (key !== 'batch_no') {
                        text += key + ': ' + batchData[key] + ', ';
                        option.setAttribute('data-' + key, batchData[key]);
                    }
                }
                text = text.slice(0, -2);
                text += ')';
                option.value = batchData.batch_no;
                option.text = text;
                batchNoDropdown.appendChild(option);
            });
            },
            error: function (error) {
                console.error('Error fetching batch numbers:', error);
            }
        });
    });

    $('#batch_no').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        var batchNumber = selectedOption.val();
        var mfd = selectedOption.data('mfd');
        var expd = selectedOption.data('expd');
        var purchaseRate = selectedOption.data('purchase-rate');
        var saleRate = selectedOption.data('sale-rate');
        
        $("input[name='mfd']").val(mfd);
        $("input[name='expd']").val(expd);
        $("input[name='purchase_rate']").val(purchaseRate);
        $("input[name='sale_rate']").val(saleRate);
        
    
    });
        
     $('input[name="adjustment_stock"]').on('input', function() {
        var cStock = parseFloat($('input[name="current_stock"]').val());
        var change = parseFloat($(this).val());
    
        if ($('input[type="radio"][name="stock-updt-val"]').is(':checked')) {
            
            var x = $('input[type="radio"][name="stock-updt-val"]:checked').val();
            if (x == 1) {
                var newStock = cStock + change;
                $('input[name="new_stock"]').val(newStock);
            } else {
                //alert(cStock)
                if(cStock >= change ) {
                   
                  $('input[name="new_stock"]').val(cStock - change);  
                }
                else {
                    $('input[name="new_stock"]').val('');  
                      Swal.fire({
                        icon: 'error',
                        title: 'Adjustment stock is not greater than current stock',
                        text: 'Please adjust the stock accordingly.'
                    });
    $(this).val('');
           
                }
                
            }
        }
    });
    $('input[type="radio"][name="stock-updt-val"]').on('change', function() {
         $('input[name="new_stock"]').val('');
         $('input[name="adjustment_stock"]').val('');
    });

    // Make an AJAX request to get current stock based on selected batch number
    document.getElementById('batch_no').addEventListener('change', function () {
    var medicineId = document.getElementById('medicine').value;
    var batchNo = this.value;

    // Make an AJAX request to get current stock
    $.ajax({
        url: '/get-current-stock/' + medicineId + '/' + batchNo,
        type: 'GET',
        success: function (data) {
            // Update the value of the 'current_stock', 'purchase_rate', and 'sales_rate' input fields
            document.getElementById('current_stock').value = data.current_stock;
            document.getElementById('numericInput1').value = data.purchase_rate;
            document.getElementById('numericInput2').value = data.sale_rate;
            document.getElementById('mfd').value = data.mfD;
            document.getElementById('expd').value = data.expD;
        
        },
        error: function (error) {
            console.error('Error fetching current stock:', error);
        }
    });
});


    $(document).ready(function () {
        // Get the current date in the format "YYYY-MM-DD"
        var currentDate = new Date().toISOString().slice(0, 10);
        
        // Set the current date as the default value for the 'mfd' input field
        $("#mfd").val(currentDate);
        $("#expd").val(currentDate);
    });

    // incrementCheckbox.addEventListener("change",e=>{
    //     const currentStock = document.querySelector('input[name="current_stock"]').value
    //     const newStock = document.querySelector('input[name="new_stock"]').value
    //     const remarks = document.querySelector('input[name="remarks"]')

    // })

     
    // Function to validate adjustment selection
    function validateAdjustments() {
    var adjustmentError = document.getElementById('adjustment-error');

    // Check if either increment or decrement is selected
    if (!incrementCheckbox.checked && !decrementCheckbox.checked) {
        adjustmentError.style.display = 'block'; // Show error message
        return false; // Prevent form submission
    } else {
        adjustmentError.style.display = 'none'; // Hide error message
        return true; // Allow form submission
    }
}


document.querySelectorAll('.numericInput').forEach(function(input) {

    input.addEventListener('keyup', function(event) {
        let inputValue = event.target.value;
        // Replace any non-numeric characters (except dot) with an empty string
        inputValue = inputValue.replace(/[^0-9.]/g, '');
        // Replace any dot followed by more than two digits with a dot followed by only two digits
        inputValue = inputValue.replace(/(\..*)\./g, '$1');
        // Ensure the value is not negative
        if (parseFloat(inputValue) < 0) {
            inputValue = '';
        }
        event.target.value = inputValue;
    });
});
   document.getElementById('numericInput1').addEventListener('input', function(event) {
        let inputValue = event.target.value;
        inputValue = inputValue.replace(/[^0-9.]/g, '');
        inputValue = inputValue.replace(/(\..*)\./g, '$1');
        event.target.value = inputValue;
    });
    document.getElementById('numericInput2').addEventListener('input', function(event) {
        let inputValue = event.target.value;
        inputValue = inputValue.replace(/[^0-9.]/g, '');
        inputValue = inputValue.replace(/(\..*)\./g, '$1');
        event.target.value = inputValue;
    });
    
    document.getElementById('numericInputs').addEventListener('input', function(event) {
        let inputValue = event.target.value;
        inputValue = inputValue.replace(/[^0-9.]/g, '');
        inputValue = inputValue.replace(/(\..*)\./g, '$1');
        event.target.value = inputValue;
    });
  function handleAdjustmentChange(checkbox) {
        var checkboxes = document.querySelectorAll('input[name="' + checkbox.name + '"]');
        checkboxes.forEach(function (cb) {
            if (cb !== checkbox && cb.checked) {
                cb.checked = false;
            }
        });
    }
</script>
@endsection
