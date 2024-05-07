@extends('layouts.app')
@section('content')
    <style>
        .fa-eye:before {
            color: #fff !important;
        }
    </style>
    <div class="row">
        <div class="col-md-12 col-lg-12">
            <button class="btn btn-blue displayfilter"><i class="fa fa-filter" aria-hidden="true"></i>
                <span>Show Filters</span></button>
             <div class="card displaycard ShowFilterBox">
                <div class="card-header">
                    <h3 class="card-title">Search Reports</h3>
                </div>
                <form action="{{route('bookings.wellness.index')}}" method="GET" class="card-body">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                <label for="appointment_from_date" class="form-label">Appointment  From:</label>
                                <input type="date" class="form-control" name="appointment_from_date" id="appointment_from_date" value="{{request()->appointment_from_date}}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                <label for="appointment_to_date" class="form-label">Appointment To:</label>
                                <input type="date" class="form-control" name="appointment_to_date" id="appointment_to_date" value="{{request()->appointment_to_date}}">
                                </div>
                            </div>
                           
                           
                          
                            <div class="col-md-12">
                                <div class="form-group">
                                    <center>
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-filter" aria-hidden="true"></i>
                                            Filter</button>&nbsp;
                                        <a class="btn btn-primary" href="#"><i class="fa fa-times"
                                                aria-hidden="true"></i> Reset</a>
                                    </center>
                                </div>
                            </div>
                        </div>
                    </div>
        </form>
    </div>
            <div class="card">
                @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        <p>{{ $message }}</p>
                    </div>
                @endif
                @if ($message = Session::get('error'))
                    <div class="alert alert-danger">
                        <p>{{ $message }}</p>
                    </div>
                @endif
                <div class="card-header">
                    <h3 class="card-title">Wellness Bookings</h3>
                </div>
                <div class="card-body">
                    <a href="{{route('create.wellness.booking')}}" class="btn btn-block btn-info">
                        <i class="fa fa-plus"></i>
                        Add Wellness Booking
                    </a>
                    <div class="table-responsive">
                        <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th class="wd-15p">SL.NO</th>
                                    <th class="wd-15p">Reference No.</th>
                                    <th class="wd-15p">Patient</th>
                                    <th class="wd-15p">Booked Date</th>
                                    <th class="wd-15p">Branch</th>
                                    <th class="wd-20p">Status</th>
                                    <th class="wd-15p">Action</th>
                                </tr>
                            </thead>
                        <tbody>
                                @php
                                    $i = 0;
                                @endphp
                                @foreach ($bookings as $key => $booking)
                                    <tr id="">
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{$booking->booking_reference_number}}</td>
                                        <td>{{$booking->patient_name}}</td>
                                        <td>{{$booking->booking_date}}</td>
                                         <td>{{$booking->branch_name}}</td>
                                        <td>{{$booking->master_value}}</td>
                                        <td>
                                        <a class="btn btn-primary" href="{{ route('view.wellness.booking', ['id' => $booking->id]) }}">
                                            <i class="fa fa-pencil-square-o" aria-hidden="true"></i> View
                                        </a>
                                        @if($booking->master_value == "Approved" || $booking->master_value == "Completed")
                                            <a class="btn btn-primary btn-sm edit-custom" href="{{ route('wellness.booking.invoices.print', $booking->id) }}" target="_blank">
                                                <i class="fa fa-print" aria-hidden="true"></i> Print
                                            </a>
                                        @endif
                                        <form style="display: inline-block"
                                            action="{{ route('delete.wellness.booking', $booking->id) }}" method="post">
                                            @csrf
                                            @method('delete')
                                            <button type="button" onclick="deleteData({{ $booking->id }})"class="btn-danger btn-sm">
                                                <i class="fa fa-trash" aria-hidden="true"></i> Delete
                                            </button>
                                        </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- TABLE WRAPPER -->
                </div>
                <!-- SECTION WRAPPER -->
            </div>
        </div>
    </div>
    <!-- ROW-1 CLOSED -->
@endsection
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function deleteData(dataId) {
    Swal.fire({
        title: "Delete selected data?",
        text: "Are you sure you want to delete this data",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes",
        cancelButtonText: "No",
        closeOnConfirm: true,
        closeOnCancel: true
    }).then(function(result) {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ route('delete.wellness.booking', '') }}/" + dataId,
                type: "DELETE",
                data: {
                    _token: "{{ csrf_token() }}",
                },
                success: function(response) {
                    // Handle the success response, e.g., remove the row from the table
                    if (response == '1') {
                        $("#dataRow_" + dataId).remove();
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Data deleted successfully',
                        }).then(function() {
                            // Reload the page
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred! Please try again later.',
                        });
                    }
                },
                error: function() {
                    alert('An error occurred while deleting the staff.');
                },
            });
        } else {
            // Handle the cancel action if needed
            return;
        }
    });
}
</script>
<script>
    $(document).ready(function() {
        var currentDate = new Date().toISOString().slice(0, 10);
        var appointmentFromDate = "{{ request()->appointment_from_date }}";
        var appointmentToDate = "{{ request()->appointment_to_date }}";
        if (appointmentFromDate === '') {
            $('#appointment_from_date').val(currentDate);
        } else {
            $('#appointment_from_date').val(appointmentFromDate);
        }
        if (appointmentToDate === '') {
            $('#appointment_to_date').val(currentDate);
        } else {
            $('#appointment_to_date').val(appointmentToDate);
        }
    });
    </script>