@extends('layouts.app')

@section('content')
@php
use App\Models\Mst_Staff;
@endphp
<style>
    .dt-buttons {
        display: flex;
    align-items: center;
    gap: 10px;
    }
    div.dt-buttons .dt-button {
    background-color: #27c533;
    border: 1px solid #fff;
    border-radius: 5px;
    color: #fff;
}

</style>
<!-- exports starts -->
<link href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css" rel="stylesheet" />
<link href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" rel="stylesheet" />
<!-- exports ends -->
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
                <form action="{{ route('payment.received.report') }}" method="GET" class="card-body">
                    <div class="card-body">
                        <div class="row">
                        <div class="col-md-6">
                            <label for="invoice_from_date" class="form-label">Invoice From:</label>
                            <input type="date" class="form-control" name="invoice_from_date" id="invoice_from_date" value="{{ request('invoice_from_date') }}">
                        </div>
                            <div class="col-md-6">
                                <label for="invoice_to_date" class="form-label">Invoice To:</label>
                                <input type="date" class="form-control" name="invoice_to_date" id="invoice_to_date" value="{{ request('invoice_to_date') }}">
                            </div>
                            <div class="col-md-6">
                                <label for="patient_name" class="form-label">Patient Name:</label>
                                <input type="text" id="patient_name" name="patient_name" class="form-control"
                                value="{{ request('patient_name') }}">
                            </div>
                            <div class="col-md-6">
                                <label for="booking_id" class="form-label">Booking Id:</label>
                                <input type="text" id="booking_id" name="booking_id" class="form-control"
                                value="{{ request('booking_id') }}">
                            </div>
                            <div class="col-md-6">
                                <label for="t_type" class="form-label">Transaction Type:</label>
                                 <select class="form-control" name="transaction_type" id="t_type">
                                                <option value="" >
                                                   Select Transaction Type
                                                </option>
                                                <option value="Consultation Billing" {{request()->input('transaction_type') == 'Consultation Billing' ? 'selected':''}}>
                                                   Consultation Billing
                                                </option>
                                                <option value="Wellness Billing" {{request()->input('transaction_type') == 'Wellness Billing' ? 'selected':''}}>
                                                   Wellness Billing
                                                </option>
                                                <option value="Therapy Billing" {{request()->input('transaction_type') == 'Therapy Billing' ? 'selected':''}} >
                                                   Therapy Billing
                                                </option>
                                                <option value="Medicine Sales" {{request()->input('transaction_type') == 'Medicine Sales' ? 'selected':''}}>
                                                   Medicine Sales
                                                </option>
                                                <option value="Membership" {{request()->input('transaction_type') == 'Membership' ? 'selected':''}} >
                                                   Membership
                                                </option>
                                            
                                        </select>
                                
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Pharmacy</label>
                                    @if(Auth::check() && Auth::user()->user_type_id == 96)
                                   @php
                                    $staff = Mst_Staff::findOrFail(Auth::user()->staff_id);
                                    $mappedpharma = $staff->pharmacies()->pluck('mst_pharmacies.id')->toArray();
                                   @endphp
                                    <select class="form-control" name="pharmacy_id" id="pharmacy_id">
                                        <option value="">Select Pharmacy</option>
                                        @foreach ($pharmacy as $key => $pharmacies)
                                        @if(in_array($pharmacies->id, $mappedpharma))
                                        <option value="{{$pharmacies->id}}" {{request()->input('pharmacy_id') == $pharmacies->id ? 'selected':''}}>{{$pharmacies->pharmacy_name}}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                     @elseif(session()->has('pharmacy_id') && session()->has('pharmacy_name') && session('pharmacy_id') != "all")
                                    @php 
                                        $pharmacy_id = session('pharmacy_id'); 
                                        $pharmacy_name = session('pharmacy_name'); 
                                    @endphp
                                     <select class="form-control" name="pharmacy_id" id="pharmacy_id" readonly>
                                                <option value="{{ $pharmacy_id }}" selected="">
                                                    {{ $pharmacy_name }}
                                                </option>
                                            
                                        </select>
                                    @else
                                    <select class="form-control" name="pharmacy_id" id="pharmacy_id">
                                        <option value="">Select Pharmacy</option>
                                        @foreach ($pharmacy as $key => $pharmacies)
                                        <option value="{{$pharmacies->id}}" {{request()->input('pharmacy_id') == $pharmacies->id ? 'selected':''}}>{{$pharmacies->pharmacy_name}}</option>
                                        @endforeach
                                    </select>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <center>
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-filter" aria-hidden="true"></i>
                                            Filter</button>&nbsp;
                                        <a class="btn btn-primary" href="{{ route('sales.report') }}"><i class="fa fa-times"
                                                aria-hidden="true"></i> Reset</a>
                                    </center>
                                </div>
                            </div>
                        </div>
                    </div>
        </form>
    </div>
    <div class="card">
       <div class="card-header">
            <div class="col-md-6">
            <h3 class="card-title">Payment Received Report</h3>
            </div>
            <div class="col-md-6 d-flex justify-content-end">
                @if(!empty($sumTotalAmount))
                <button class="btn btn-raised btn-warning">Total Sales :</button>
                @endif
             </div>
        </div>
        <div class="card-body">

            <div class="table-responsive">
                <table id="report" class="table table-striped table-bordered text-nowrap w-100">
                    <thead>
                        <tr>
                            <th>SL.NO</th>
                            <th>Transaction Type</th>
                            <th>Invoice<br>Number</th>
                            <th>Booking<br>Id</th>
                            <th>Patient</th>
                            <th>Invoice<br>Date</th>
                            <th>Pharmacy</th>
                            <th>Branch</th>
                            <th>Total<br>Amount</th>
                            <th>Discount</th>
                            <th>Payed Amount</th>
                            <th>Due Amount</th>
                            <th>Details</th>
                            
                            
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $i=1;
                        @endphp
                       @foreach($sales as $key=>$sale)
                       @php
                       if(@$sale->transaction_type=='Membership')
                       {
                         $sale->invoice_number='MEM'.'-'.@$sale->membership_patient_id;
                       }
                       @endphp
                            <tr id="dataRow_{{ $key+1}}">
                                <td>{{ $i++ }}</td>
                                <td>{{@$sale->transaction_type}}</td>
                                <td>{{@$sale->invoice_number}}</td>
                                <td>{{@$sale->booking_reference_number!=""?@$sale->booking_reference_number:"N/A"}}</td>
                                <td>@if($sale->patient_id!=0) {{@$sale->patient_name}} @else Guest Patient @endif</td>
                                <td>{{ \Carbon\Carbon::parse(@$sale->invoice_date)->format('Y-m-d') }} </td>
                                <td>
                                    @if($sale->pharmacy_id!=0)
                                    {{@$sale->pharmacy_name}}
                                    @else
                                    --
                                    
                                     
                                    @endif
                                    </td>
                                 <td>
                                     @if(@$sale->branch!="")
                                     {{@$sale->branch}}
                                     @else
                                     
                                     --
                                     @endif
                                 </td>
                                <td>{{number_format(@$sale->total_amount,2) }}</td>
                                <td>{{number_format(@$sale->discount,2) }}</td>
                                <td>{{number_format(@$sale->payed_amount,2) }}</td>
                                <td>{{number_format(@$sale->due_amount,2) }}</td>
                                <td>@if(@$sale->transaction_type=='Medicine Sales')<a class="btn btn-primary btn-sm" href="{{ route('sales.report.detail', ['id' => $sale->sales_invoice_id]) }}">
                                    Medicine Sale Detail
                                </a>@else -- @endif</td>
                              
                                
                                
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="pagination" style="justify-content:flex-end;margin-top:-10px">
               
            </div>
            </div>
        </div>
    </div>
    {{-- </div> --}}
    </div>
    <script>
    
        $(document).ready(function() {
       
        $('#report').DataTable({
            dom: 'Bfrtip',
            buttons: [
            {
                extend: 'excel',
                text: 'Export to Excel',
                title: 'Payment Received Report',
              
            },
            {
                extend: 'pdf',
                text: 'Export to PDF',
                title: 'Payment Received Report',
                footer: true,
                orientation : 'landscape',
                pageSize : 'LEGAL',
               
                    
                    
            }
            ]
        });
    });
        var invoiceFromDateInput = document.getElementById('invoice_from_date');
        var currentValue = invoiceFromDateInput.value;
        var parts = currentValue.split('-');
        var ddmmyyValue = parts[2] + parts[1] + parts[0].substring(2);
        invoiceFromDateInput.value = ddmmyyValue;
    </script>
    
     <script>
    $(document).ready(function() {
        var currentDate = new Date().toISOString().slice(0, 10);
        var invoiceFromDate = "{{ request()->invoice_from_date }}";
        var invoiceToDate = "{{ request()->invoice_to_date }}";
        if (invoiceFromDate === '') {
            $('#invoice_from_date').val(currentDate);
        } else {
            $('#invoice_from_date').val(invoiceFromDate);
        }
        if (invoiceToDate === '') {
            $('#invoice_to_date').val(currentDate);
        } else {
            $('#invoice_to_date').val(invoiceToDate);
        }
    });
    </script>
    
@endsection


