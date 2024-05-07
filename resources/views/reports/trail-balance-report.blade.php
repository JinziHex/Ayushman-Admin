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
                <form action="{{ route('trailbalance.report') }}" method="GET" class="card-body">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="posting_from_date" class="form-label">From :</label>
                                <input type="date" class="form-control" name="posting_from_date" id="posting_from_date" value="{{request()->posting_from_date}}">
                            </div>
                            <div class="col-md-6">
                                <label for="posting_to_date" class="form-label">To :</label>
                                <input type="date" class="form-control" name="posting_to_date" id="posting_to_date" value="{{request()->posting_to_date}}">
                            </div>
                          
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <center>
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-filter" aria-hidden="true"></i>
                                            Filter</button>&nbsp;
                                        <a class="btn btn-primary" href="{{ route('trailbalance.report') }}"><i class="fa fa-times"
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
            <h3 class="card-title">Trail Balance Report</h3>
            </div>
            <div class="col-md-6 d-flex justify-content-end">
                
             </div>
        </div>
        <div class="card-body">

            <div class="table-responsive">
             <table id="reports" class="display nowrap table table-striped table-bordered dynamic-height">
            <thead>
                <tr>
                    <th>Account</th>
                    <th>NetDebit</th>
                    <th>NetCredit</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><b>Asset</b></td>
                    <td></td>
                    <td></td>
                </tr>
                @foreach($assetTrialBalance as $row)
                <tr>
                    <td style="color: darkblue">
                        <a>{{ $row->ledgerName }}</a>
                    </td>
                    <td>{{ number_format($row->balance,2) }}</td>
                    <td></td>
                </tr>
                @endforeach
               
                <tr>
                    <td><b>Income</b></td>
                    <td></td>
                    <td></td>
                </tr>
                 @foreach($incomeTrialBalance as $income)
                <tr>
                    <td style="color: darkblue">
                        <a>{{ $income->LedgerName }}</a>
                    </td>
                    <td>{{ number_format(@$income->DebitTotal,2) }}</td>
                    <td>{{ number_format(@$income->CreditTotal,2) }}</td>
                </tr>
              @endforeach
                <tr>
                    <td><b>Expense</b></td>
                    <td></td>
                    <td></td>
                </tr>
                 @foreach($expenseTrialBalance as $expense)
                <tr>
                    <td style="color: darkblue">
                        <a>{{ $expense->LedgerName }}</a>
                    </td>
                    <td>{{ number_format(@$expense->DebitTotal,2) }}</td>
                    <td>{{ number_format(@$expense->CreditTotal,2) }}</td>
                </tr>
              @endforeach
              <tr>
                    <td><b>Liability</b></td>
                    <td></td>
                    <td></td>
                </tr>
                 @foreach($liabilityTrialBalance as $liability)
                <tr>
                    <td style="color: darkblue">
                        <a>{{  $liability->LedgerName }}</a>
                    </td>
                    <td>{{  number_format(@$liability->DebitTotal,2) }}</td>
                    <td>{{  number_format(@$liability->CreditTotal,2) }}</td>
                </tr>
              @endforeach
                
                <tr>
                    <td></td>
                    <td><strong>{{number_format(@$netDebit,2)}}</strong></td>
                    <td><strong>{{number_format(@$netCredit,2)}}</strong></td>
                </tr>
            </tbody>
        </table>


               
            </div>
        </div>
    </div>
    {{-- </div> --}}
    </div>
    <script>
    
        $(document).ready(function() {
             $('#report').DataTable({
            paging: false,
           dom: 'Bfrtip',
            buttons: [
            {
                extend: 'excel',
                text: 'Export to Excel',
                title: 'Trail Balance Report',
                exportOptions: 
                {
                    columns: [0,1,2,3,4]
                }
            },
            {
                extend: 'pdf',
                text: 'Export to PDF',
                title: 'Trail Balance Report',
                footer: true,
                orientation : 'landscape',
                pageSize : 'LEGAL',
                exportOptions: 
                {
                    columns: [0,1,2,3,4],
                    alignment: 'right',
                },
                   
            }
            ]
        });
       
       
    });
    </script>
    
    <script>
    $(document).ready(function() {
        var currentDate = new Date().toISOString().slice(0, 10);
        var invoiceFromDate = "{{ request()->posting_from_date }}";
        var invoiceToDate = "{{ request()->posting_to_date }}";
        if (invoiceFromDate === '') {
            $('#posting_from_date').val(currentDate);
        } else {
            $('#posting_from_date').val(invoiceFromDate);
        }
        if (invoiceToDate === '') {
            $('#posting_to_date').val(currentDate);
        } else {
            $('#posting_to_date').val(invoiceToDate);
        }
    });
    </script>
    
@endsection


