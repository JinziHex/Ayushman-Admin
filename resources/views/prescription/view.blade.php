@extends('layouts.app')
@section('content')
    <div class="row" style="min-height: 70vh;">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0 card-title">{{$pageTitle}}</h3>
                </div>
                <div class="col-lg-12" style="background-color:#fff">
                    <form action="{{ route('medicine.dosage.store') }}" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="hidden_id" value="{{ isset($medicine_dosages->medicine_dosage_id) ? $medicine_dosages->medicine_dosage_id : '' }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Patient Name</label>
                                    <input type="text" readonly class="form-control" value="{{$patient_name}}" name="patient_name" placeholder="Patient Name">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Booking ID</label>
                                    <input type="text" readonly class="form-control" value="{{$basic_details->booking_reference_number}}" name="booking_id" placeholder="Booking ID">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Booking Date</label>
                                    <input type="text" readonly class="form-control" value="{{ date('d-m-Y', strtotime($basic_details->booking_date)) }}" name="booking_date" placeholder="Booking Date">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Diagnosis</label>
                                    <textarea class="form-control" readonly name="patient_diagnosis" placeholder="Patient Diagnosis">{{ strip_tags($basic_details->diagnosis)}}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Advice</label>
                                    <textarea class="form-control" readonly name="doctor_advice" placeholder="Doctor Advice Address">{{ strip_tags($basic_details->advice) }}</textarea>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-lg-12">
                                <div class="card">
                                    <div class="table-responsive">
                                        <table class="table card-table table-vcenter text-nowrap" id="productTable">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Medicine Name </th>
                                                    <th>Medicine Dosage</th>
                                                  
                                                    <th>Duration</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                $i = 0;
                                                @endphp
                                                @if($medicineType == 1)
                                                @foreach($medicine_details as $medicine_details)
                                                <tr id="dataRow_{{ $medicine_details->prescription_details_id }}">
                                                    <td>{{ ++$i }}</td>
                                                    <td>{{ $medicine_details->medicine_name}}({{ $medicine_details->medicine_type}})</td>
                                                    <td>{{ $medicine_details->medicine_dosage}}</td>
                                                    
                                                    <td>{{ $medicine_details->duration}}</td>
                                                </tr>
                                                @endforeach
                                                @else
                                                @foreach($medicine_details1 as $medicine_detail)
                                                <tr id="dataRow_{{ $medicine_detail->prescription_details_id }}">
                                                    <td>{{ ++$i }}</td>
                                                    <td>{{ $medicine_detail->medicine_id}}</td>
                                                    <td>{{ $medicine_detail->medicine_dosage}}</td>
                                                    
                                                    <td>{{ $medicine_detail->duration}}</td>
                                                </tr>
                                                @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <center>
                                <a class="btn btn-success" href="{{ route('prescriptions.print', $id) }}">Print</a>
                                <a class="btn btn-danger" href="{{route('prescriptions.index')}}">Cancel</a>
                            </center>
                        </div>
                </div>
            </div>

            </form>

        </div>
    </div>

@endsection
@section('js')
<script>
    function toggleStatus(checkbox) {
        if (checkbox.checked) {
            $("#statusText").text('Active');
            $("input[name=is_active]").val(1); // Set the value to 1 when checked
        } else {
            $("#statusText").text('Inactive');
            $("input[name=is_active]").val(0); // Set the value to 0 when unchecked
        }
    }
</script>


@endsection