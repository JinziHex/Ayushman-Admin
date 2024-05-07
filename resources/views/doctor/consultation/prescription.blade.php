@extends('layouts.app')
@section('content')
    <style>
        .form-control[readonly] {
            background-color: #c7c7c7 !important;
        }

        .page input[type=text][readonly] {
            background-color: #c7c7c7 !important;
        }

        .form-group .last-row {
            border-top: 1px solid #0d97c6;
            padding-top: 15px;
        }
        
   /*dropdown style     */
   .table th {   font-size: 12px;} select.medsearch { display: none !important; } span.current {
    font-size: 10px!important; } .table td {padding: 5px 3px;} .pricecard .form-group label {font-size: 12px;margin:0;} .pricecard .form-group span, .pricecard .form-group input {width: auto;padding: 0;border: unset;line-height: 1;height:auto;} .pricecard .form-group input:focus {outline: unset !important;border: none !important;} .pricecard .form-group {display: flex;align-items: center;gap: 16px;margin: 5px 0;}.pricecard .col-md-4 {margin-left: auto;}.dropdown-select {background-image: linear-gradient(to bottom, rgba(255, 255, 255, 0.25) 0%, rgba(255, 255, 255, 0) 100%);background-repeat: repeat-x;filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#40FFFFFF', endColorstr='#00FFFFFF', GradientType=0);background-color: #fff;border-radius: 6px;box-sizing: border-box;cursor: pointer;display: block;float: left;font-size: 14px;font-weight: normal;outline: none;padding-left: 18px;padding-right: 30px;position: relative;text-align: left !important;transition: all 0.2s ease-in-out;-webkit-user-select: none;-moz-user-select: none; -ms-user-select: none;user-select: none;white-space: nowrap; width: auto;}.dropdown-select:focus {background-color: #fff;}.dropdown-select:hover {background-color: #fff;}.dropdown-select:active,.dropdown-select.open {background-color: #fff !important;border-color: #bbb;box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05) inset;}.dropdown-select:after {height: 0; width: 0; border-left: 4px solid transparent; border-right: 4px solid transparent; border-top: 4px solid #777;-webkit-transform: origin(50% 20%); transform: origin(50% 20%); transition: all 0.125s ease-in-out; content: ''; display: block; margin-top: -2px; pointer-events: none; position: absolute; right: 10px; top: 50%;}.dropdown-select.open:after { -webkit-transform: rotate(-180deg); transform: rotate(-180deg);}.dropdown-select.open .list { -webkit-transform: scale(1); transform: scale(1); opacity: 1; pointer-events: auto;}.dropdown-select.open .option {cursor: pointer;}.dropdown-select.wide {width: 100%;}.dropdown-select .list {box-sizing: border-box; transition: all 0.15s cubic-bezier(0.25, 0, 0.25, 1.75), opacity 0.1s linear; -webkit-transform: scale(0.75); transform: scale(0.75); -webkit-transform-origin: 50% 0; transform-origin: 50% 0; box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.09); background-color: #fff; border-radius: 6px; margin-top: 4px; padding: 3px 0; opacity: 0; overflow: hidden; pointer-events: none; position: absolute; top: 100%; left: 0; z-index: 999; max-height: 250px; overflow: auto; border: 1px solid #ddd;}.dropdown-select .list:hover .option:not(:hover) { background-color: transparent !important;}.dropdown-select .dd-search{overflow:hidden;display:flex;align-items:center;justify-content:center;margin:5px 0;}.dropdown-select .dd-searchbox{width:90%;padding:0.5rem;border:1px solid #999;border-color:#999;border-radius:4px;outline:none;line-height: 1;}.dropdown-select .dd-searchbox:focus{border-color:#12CBC4;}.dropdown-select .list ul { padding: 0;}.dropdown-select .option {cursor: default; font-weight: 400; line-height: 2; outline: none; padding-left: 10px; padding-right: 25px; text-align: left; transition: all 0.2s; list-style: none; font-size: 10px;}.dropdown-select .option:hover,.dropdown-select .option:focus {background-color: #f6f6f6 !important;}.dropdown-select .option.selected { font-weight: 600; color: #12cbc4;}.dropdown-select .option.selected:focus {background: #f6f6f6;}.dropdown-select a {color: #aaa; text-decoration: none; transition: all 0.2s ease-in-out;}.dropdown-select a:hover {
    color: #666;}
    .form-control:disabled, .form-control[readonly] {
    background-color: #c7c7c7;
      }
select.form-control.medsearch.errorSelect + .dropdown-select {
    border-color: red !important;
}
.dropdown-select.wide .list {
    z-index: 999999 !important;
    min-height: 238px !important;
    position: absolute !important;
    max-width: 245px;
}
      .list li.option[disabled] {
    display: none;
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
.medsearch {
    position: relative;
}
/*.dropdown-select.wide .list{*/
/*    left:37px!important;*/
/*}*/
td.medicine-id-td.med_int select.medsearch {
    display: block !important;
    opacity: 0;
    height: 0px !important;
    padding: 0 !important;
    width: 100px !important;
    /* position: absolute !important; */
    top: 35px !important;
}

    </style>
        <div class="row" style="min-height: 70vh;">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="col-md-6">
                        <h3 class="card-title">Add Prescription</h3>
                        </div>
                        <div class="col-md-6 d-flex justify-content-end">
                            <button type="button" class="btn btn-raised btn-warning" data-toggle="modal" data-target="#viewhistory">Patient History - {{ @$bookingInfo->is_for_family_member !== null && @$bookingInfo->is_for_family_member > 0 ? @$bookingInfo->familyMember['family_member_name'] : @$bookingInfo->patient['patient_name'] }}</button>
                         </div>
                    </div>
                    
                    <!-- Previous consultation modal -->
                    <!-- Button to trigger modal -->
                    <div id="viewhistory" class="modal fade" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Patient History </h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    @if($patient_histories->isEmpty())
                                        <p>No previous consultation history for the patient @if($bookingInfo->is_for_family_member !== null && $bookingInfo->is_for_family_member > 0) for {{@$bookingInfo->familyMember['family_member_name']}} @else for {{ @$bookingInfo->patient['patient_name']}} @endif</p>
                                    @else
                                        @foreach($patient_histories as $history)
                                            <div class="card">
                                                <div class="card-header">
                                                    <h3 class="card-title">BOOKING ID:{{ @$history->bookingDetails['booking_reference_number']}}</h3>
                                                    <div class="card-options">
                                                        <a href="#" class="btn btn-primary btn-sm">Status: {{@$history->bookingDetails->bookingStatus['master_value']}}</a>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <span class="form-label">Doctor:  {{@$history->Staff['staff_name']}}</span>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <span class="form-label">Booking Date: {{ \Carbon\Carbon::parse(@$history->bookingDetails['created_at'])->toDateString() }}</span>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <span class="form-label">Timeslot:  {{ (optional(optional(@$history->bookingDetails['staffTimeslot'])->timeSlot)->slot_name ?: 'No timeslot selected') . ': ' . 
           (optional(optional(@$history->bookingDetails['staffTimeslot'])->timeSlot)->time_from ?: '') . '-' . 
           (optional(optional(@$history->bookingDetails['staffTimeslot'])->timeSlot)->time_to ?: '') }}</span>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <span class="form-label">Branch:  {{@$history->bookingDetails->branch['branch_name']}}</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <span class="form-label">Diagnosis:{{@$history->diagnosis}}</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <span class="form-label">Advice::{{@$history->advice}}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h6 style="font-weight: 600; color: #0d97c6;">MEDICATION PRESCRIBED</h6>
                                                            <ul style="list-style: auto;">
                                                                @foreach($history->PrescriptionDetails as $details)
                                                                    <li>
                                                                        {{ $details->medicine['medicine_name'] }} ({{ $details->medicine_dosage }} - {{ $details->duration }})
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h6 style="font-weight: 600; color: #0d97c6;">THERAPIES</h6>
                                                            <ul>
                                                                @if(@$history->bookingDetails['therapyBookings']->isNotEmpty())
                                                                    @foreach(@$history->bookingDetails['therapyBookings'] as $therapy)
                                                                        <li>
                                                                            {{@$therapy->therapy['therapy_name']}}
                                                                        </li>
                                                                    @endforeach
                                                                @else
                                                                    <li>No therapy Added!</li>
                                                                @endif
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Previous consultation modal end -->
                    
                    <div class="col-lg-12 card-background" style="background-color:#fff";>
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <!-- <strong>Whoops!</strong> There were some problems with your input.<br><br> -->
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form action="{{route('doctor.precription.store')}}" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Booking Reference ID*</label>
                                        <input type="hidden" name="booking_id" value="{{$bookingInfo->id}}">
                                        <input type="text" class="form-control" required name="reference_no"
                                            id="reference_no" placeholder="Date" value="{{@$bookingInfo->booking_reference_number}} | TOKEN : {{ @$bookingInfo->token_number ?? '0' }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Consulting Date*</label>
                                        <input type="date" class="form-control" required name="consulting_date"
                                            id="consulting_date" placeholder="Date" value="{{ old('consulting_date') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Booking Date*</label>
                                        <input type="date" class="form-control" required name="booking_date"
                                            id="booking_date" placeholder="Date" value="{{ @$bookingInfo ? \Carbon\Carbon::parse($bookingInfo->created_at)->toDateString() : '' }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Patient Name*</label>
                                        <input type="text" class="form-control" required name="patient_name"
                                            id="patient_name" placeholder="Patient Name" value="{{ @$bookingInfo->is_for_family_member !== null && @$bookingInfo->is_for_family_member > 0 ? @$bookingInfo->familyMember['family_member_name'] : @$bookingInfo->patient['patient_name'] }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Branch*</label>
                                        <input type="text" class="form-control" required name="branch_name"
                                            id="branch_name" placeholder="Date" value="{{@$bookingInfo->branch['branch_name']}}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Timeslot*</label>
                                        <input type="text" class="form-control" required name="timeslot"
                                            id="timeslot" placeholder="Date" value="{{ (optional(optional($bookingInfo->staffTimeslot)->timeSlot)->slot_name ?: 'No timeslot selected') . ': ' . 
                           (optional(optional($bookingInfo->staffTimeslot)->timeSlot)->time_from ?: '') . '-' . 
                           (optional(optional($bookingInfo->staffTimeslot)->timeSlot)->time_to ?: '') }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Diagnosis*</label>
                                        <textarea  class="form-control" required name="diagnosis"
                                            id="diagnosis" placeholder="Diagnosis"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Advice*</label>
                                        <textarea  class="form-control" required name="advice"
                                            id="advice" placeholder="Advice">
                                        </textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-lg-12">
                                    <h6 style="font-weight: 600;
                                    color: #0d97c6;"> MEDICINE PRESCRIPTION </h6>
                                    <div class="card">
                                        <div class="table-responsive" style="min-height: 200px;">
                                            <table class="table card-table table-vcenter text-nowrap" id="productTable">
                                                <thead>
                                                    <tr>
                                                        <th>Medicine Type</th>
                                                        <th class="medicine-name-th">Medicine Name</th>
                                                        <th>Dosage</th>
                                                        <th>Duration</th>
                                                        <th>Actions</th>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr id="productRowTemplate">
                                                        <td>
                                                        <select class="form-control" name="medicine_type[]" id="medId" required>
                                                            <option value="" selected disabled>Please select medicine type</option>
                                                            <option value="1">Internal Medicine</option>
                                                            <option value="2">External Medicine</option>
                                                        </select>
                                                    </td>
                                                    <td class="medicine-id-td med_int" style="display: none;">
                                                        <select class="form-control medicine-name medsearch" name="medicine_id[]" >
                                                            <option value="" default>Please select medicine</option>
                                                            <option value="0" style="display:none;"></option>
                                                            @foreach ($medicines as $id => $medicine)
                                                            <option value="{{ $medicine->id }}" class="medicine-type-{{ $medicine->medicine_type }}">{{ $medicine->medicine_name }}- -
                                                            {{ $medicine->stocks }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                        <td class="medicine-id-tds med_ext" style="display: none;">
                                                        <input type="text" class="form-control"  name="medicine_name[]" placeholder="Enter Medicine Name">
                                                    </td>
                                                        <td class="medicine-batch-no">
                                                            <input type="text" class="form-control" required name="dosage[]" placeholder="Example: 1-0-1">
                                                        </td>
                                                        <td class="medicine-quantity">
                                                            <input type="text" class="form-control" required name="duration[]" placeholder="Example: Twice a Day">
                                                        </td>
                                                        <td><button type="button" onclick="removeFn(this)"
                                                                style="background-color: #007BFF; color: #FFF; padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer;">Remove</button>
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
                            {{-- therapy --}}
                            <div class="row">
                                <div class="col-md-12 col-lg-12">
                                    <h6 style="font-weight: 600;
                                    color: #0d97c6;"> THERAPY PRESCRIPTION </h6>
                                    <div class="card">
                                        <div class="table-responsive" style="min-height: 200px;">
                                            <table class="table card-table table-vcenter text-nowrap" id="productTable2">
                                                <thead>
                                                    <tr>
                                                        <th>Therapy</th>
                                                        <th>Instructions</th>
                                                        <th>Actions</th>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr id="productRowTemplate2" class="product-row">
                                                        <td>
                                                            <select class="form-control therapy_id"  name="therapy_id[]" id="therapy_id">
                                                                <option value="">Please select therapy</option>
                                                                @foreach ($therapies as $id => $therapy)
                                                                <option value="{{ $therapy->id }}">{{ $therapy->therapy_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                         <textarea id="instructions" class="form-control instructions" 
                                                                  name="instructions[]" placeholder="Therapy Instructions"></textarea>
                                                        
                                                             </td>
                                                          <td style="display: none;">
                                                            <input type="hidden" id="booking_fee" class="form-control booking_fee" 
                                                                   name="booking_fee[]" placeholder="Booking Fee" value="" readonly>
                                                        </td>
                                                        <td style="display: none;">
                                                            <select class="form-control timeslots" name="timeslots[]" id="timeslots">
                                                                <option value="">--Select Timeslot--</option>
                                                            </select>
                                                        </td>
                                                        <td><button type="button" onclick="removeFn2(this)"
                                                                style="background-color: #007BFF; color: #FFF; padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer;">Remove</button>
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
                                    <button type="button" class="btn btn-primary" id="addProductBtn2">Add Row</button>
                                </div>
                            </div>
                            <div class="row" style="margin-top:20px;">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <center>
                                            <button type="submit" class="btn btn-raised btn-primary">
                                                <i class="fa fa-check-square-o"></i> Update Status & Save</button>
                                            <button type="reset" class="btn btn-raised btn-success">
                                                <i class="fa fa-refresh"></i> Reset</button>
                                            <a class="btn btn-danger" href="{{route('consultation.index')}}"> <i class="fa fa-times"></i>
                                                Cancel</a>
                                        </center>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
                </form>
            </div>
        </div>
  
@endsection
@section('js')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            var currentDate = new Date().toISOString().slice(0, 10);
            $('#consulting_date').val(currentDate);
        });
       
function removeFn(button) {
    var row = $(button).closest('tr');
    // Check if it's the first row, if yes, do not remove
    if (!row.is(':first-child')) {
        row.remove();
    }
}

function removeFn2(button) {
    var row = $(button).closest('tr');
    if (!row.is(':first-child')) {
        row.remove();
    }
}

        $(document).ready(function() {
            // $('.medicine-select').val(1);
            // $('input[name="medicine_name[]"]').val('0');
           
                //get booking fee

                $(document).on('change', '.therapy_id', function() {
                var row = $(this).closest('.product-row');
                var therapyID = $(this).val();
                var bookingDate = $('#booking_date').val();
        
                if (therapyID && bookingDate) {
                    $.ajax({
                        url: '{{ route('therapy.getTherapyBookingFee') }}',
                        type: "GET",
                        data: {
                            therapy_id: therapyID,
                            booking_date: bookingDate,
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: "json",
                        success: function(data) {
                            var bookingFeeInput = row.find('.booking_fee');
                            var timeslotsSelect = row.find('.timeslots');

                            if (data.error) {
                                $(this).val('');
                                bookingFeeInput.val('');
                                timeslotsSelect.empty();
                            } else {
                                bookingFeeInput.val(data.booking_fee);
                                timeslotsSelect.empty().append('<option value="">--Select Timeslot--</option>');
                                $.each(data.timeslots, function(key, value) {
                                    var optionText = value.therapy_room_name + ' : ' + value.time_from + ' - ' + value.time_to;
                                    timeslotsSelect.append($('<option>', {
                                        value: value.timeslot,
                                        text: optionText
                                    }));
                                });
                            }
                        }
                    });
                } else {
                    var bookingFeeInput = row.find('.booking_fee');
                    var timeslotsSelect = row.find('.timeslots');

                    bookingFeeInput.val('');
                    timeslotsSelect.empty();
                }
            });
        });

        </script>
        <script>
            $(document).ready(function() {
        CKEDITOR.replace('diagnosis', {
            removePlugins: 'image',
        });

        $(document).ready(function() {
            CKEDITOR.replace('advice', {
                removePlugins: 'image',
            });
            
           
        });

    });
 $(document).ready(function() {
     $('select[name="medicine_type[]"]').val(1)
      var z = $('select[name="medicine_type[]"]');
      var y = z.closest('tr')
      changeMedicineType(1,y)
      
  $(document).on('change', 'select[name="medicine_type[]"]', function() {
      const val = $(this).val();
      var row = $(this).closest('tr');
      //alert(val)
      changeMedicineType(val,row)
      
  })
  function changeMedicineType(x,row) {
      if(!x){
          row.find('.medicine-name-th').css('display',"block")
         row.find('.med_int').css("display","none")
         row.find('.med_ext').css("display","none")
      }
      if(x == 1){
          row.find('.medicine-name-th').css('display',"block")
          row.find('.med_int').css("display","block")
          row.find('.med_ext').css("display","none")
         // row.find('.med_ext input[name="medicine_name[]"]').val(0)
         row.find('select[name="medicine_id[]"]').attr('required', true);
          row.find('input[name="medicine_name[]"]').attr('required', false);
         
      }else{
          row.find('.medicine-name-th').css('display',"block")
          row.find('.med_ext').css("display","block")
          row.find('.med_int').css("display","none")
        //   row.find('.med_ext input[name="medicine_name[]"]').val('')
        // row.find('select[name="medicine_id[]"]').val(0);
        row.find('select[name="medicine_id[]"]').attr('required', false);
        row.find('select[name="medicine_id[]"]').val(0);
         row.find('input[name="medicine_name[]"]').attr('required', true);
      }
  }
 })

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
    
 $("#addProductBtn").click(function(event) {
                event.preventDefault();
                // Clone the first row if the table is empty or clone a new empty row if it's not empty
                var newRow = $("#productTable tbody tr").length == 0 ? $("#productRowTemplate").clone().removeAttr("style") : $("#productTable tbody tr:first").clone().removeAttr("style");
                //newRow.find('select').addClass('medicine-select');
                newRow.find('select[name="medicine_type[]"]').val(1);
                //newRow.find('select[name="medicine_type[]"]').val(1);
                newRow.find('.med_int').css("display","block")
                newRow.find('.med_ext').css("display","none")
                // newRow.find('.current').text('Please select medicine');
                 //newRow.find('.medsearch').val('default_value');
                newRow.find('select[name="medicine_id[]"]').attr('required', true);
                
                newRow.find('input').val('').prop('readonly', false);
                // newRow.find('input[name="medicine_name[]"]').val('0')
                newRow.find('input span').remove();
                $("#productTable tbody").append(newRow);
                
                newRow.find('.dropdown-select').remove();
                create_custom_dropdowns();
            });
            
            $("#addProductBtn2").click(function(event) {
                event.preventDefault();
                //alert(2)
                var newRow = $("#productRowTemplate2").clone().removeAttr("style");
                newRow.find('select').addClass('medicine-select');
                //newRow.find('input').val('').prop('readonly', false);
                newRow.find('input').val('');
                newRow.find('input span').remove();
                $("#productTable2 tbody").append(newRow);
              
                //$('#productTable2 tbody tr:first-child button').show();
            });    
    
    
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
            var trWidth = $(this).width();
            var l = trOffset.left 
            var x = trOffset.top - tableOffset.top
            
            console.log("tttt"+ trOffset.left );
            // console.log(trHeight)
            var item = $(this).closest("tr").find('.list');
            item.css({
              display: 'block',
              top: x + trHeight - 5 + 'px',
              left: l   + 'px ',
              right:'unset '
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

function validateForm() {
      var isValid = true;
    
        $("#productTable tbody tr").each(function() {
            //alert("test")
            var valueOFint = $(this).find("select[name='medicine_id[]']").val();
            var valueOFext = $(this).find("input[name='medicine_name[]']").val();
            var typeMed = $(this).find("select[name='medicine_type[]']").val();
            
            
            // if(typeMed && !valueOFint) {
            //     $(this).find("select[name='medicine_id[]']").val(0)
            // }
            if(typeMed && !valueOFext) {
                $(this).find("input[name='medicine_name[]']").val(0);
            }
            
            // alert("select"+ $(this).find("select[name='medicine_id[]']").val())
            // alert("input"+ $(this).find("input[name='medicine_name[]']").val())
            
        })
       
      
      return isValid;
      
   }
</script>
        
@endsection
