@extends('layouts.app')
@section('content')

    <style>
        /* Custom CSS for ash color border */
        .card-ash-border {
            border: 1px solid #b0b0b0;
            /* Adjust the color as needed */
        }

        td.medicine-quantity span {
            color: red;
            font-size: 10px;
            position: absolute;
            bottom: -1px;
            left: 00;
            text-align: center;
            width: 100%;
        }

        td.medicine-quantity {
            position: relative;
        }
    </style>
    <div class="row" style="min-height: 70vh;">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0 card-title">{{ $pageTitle }}</h3>
                </div>
                <div class="card-body">
                    @if ($message = Session::get('status'))
                    <div class="alert alert-success">
                        <p>{{ $message }}</p>
                    </div>
                    @endif
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <form action="{{ route('prescriptions.list') }}" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                        @csrf
                        <div class="row">
                        <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Booking Date</label>
                                    <input type="date" class="form-control" name="booking_date" id="booking_date" placeholder="Booking Date" onchange="getBookingIDs()" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Select Patient*</label>
                                    <select class="form-control" name="patient_id" id="patient_id" required>
                                        <option value="">Select Patient</option>
                             
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Select Booking ID*</label>
                                    <select class="form-control" name="patient_booking_id" id="patient_booking_id" required>
                                        <option value="">--Select Booking ID--</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="justify-content-center mt-5" style="margin-top: 12px;">
                                        <button type="submit" class="btn btn-raised btn-primary"><i class="fa fa-check-square-o"></i> Select</button>
                                        <a class="btn btn-danger ml-2" href="{{ url('/prescriptions') }}">Reset</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    @if(isset($prescriptions) && count($prescriptions) > 0)
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <div class="card">
                                <div class="table-responsive">
                                    <table class="table card-table table-vcenter text-nowrap" id="productTable">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Booking ID</th>
                                                <th>Doctor Name</th>
                                                <th>Prescritpions</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                            $i = 0;
                                            @endphp
                                            @foreach($prescriptions as $prescription)
                                            <tr id="dataRow_{{ $prescription->prescription_id }}">
                                                <td>{{ ++$i }}</td>
                                                <td>{{ $bookingInfo->booking_reference_number}}</td>
                                                <td>{{ $prescription->staff->staff_name}}</td>
                                                <td>{{ $prescription->staff->staff_code.'-'.$prescription->created_at->format('d-m-Y h:i A')}}</td>
                                                <td>
                                                   <a class="btn btn-primary btn-sm edit-custom" href="{{ route('prescriptions.print', $prescription->prescription_id) }}" target="_blank"><i class="fa fa-print" aria-hidden="true"></i> Print</a>

                                                    <a class="btn btn-primary btn-sm" href="{{ route('prescriptions.show', $prescription->prescription_id) }}">
                                                        <i class="fa fa-eye" aria-hidden="true"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
<!-- <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script> -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // searchable dropdown
        $('#patient_id').select2();
     $('#patient_booking_id').select2();
    });

    // In your Blade view, generate the route URL
    var getBookingIdsRoute = "{{ route('get.booking.ids', '') }}";
    
    $(document).on('change', '#patient_id', function() {
        var selected_patient_id = $(this).val();
        $.ajax({
            url: getBookingIdsRoute + "/" + selected_patient_id,
            method: "get",
            data: {
                _token: "{{ csrf_token() }}",
            },
            success: function(data) {
                console.log(data);
                var booking_id = '{{ $booking_id ?? '' }}'; 
    
                $('#patient_booking_id').empty().append('<option value="">Choose Booking ID</option>');
    
                $.each(data, function(key, value) {
                    $('#patient_booking_id').append('<option value="' + key + '">' + value + '</option>');
                });
    
                $('#patient_booking_id').val(booking_id).change();
            },
            error: function(xhr, status, error) {
                console.log('Error fetching booking id: ' + error);
            }
        });
    });


function getBookingIDs() {
    var bookingDate = document.getElementById('booking_date').value;

    if (!bookingDate) {
        return; // Exit early if booking date is empty
    }

    var url = '{{ route("get.patient.ids") }}'; // Assuming this is the route that returns patients based on the booking date

    // Make an AJAX request
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            booking_date: bookingDate
        })
    })
    .then(response => response.json())
    .then(data => {
        // Update the patient select element with the fetched data
        var patientSelect = document.getElementById('patient_id');
        patientSelect.innerHTML = '<option value="">Select Patient</option>';
        data.forEach(patient => {
            patientSelect.innerHTML += '<option value="' + patient.id + '">' + patient.patient_name + ' (' + patient.patient_code + ')</option>';
        });
    })
    .catch(error => {
        console.error('Error:', error);
    });
}


</script>
@endsection