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
                <form action="{{ route('ledger.report.detail', ['id' => $account_ledger_id]) }}" method="GET" class="card-body">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="posting_from_date" class="form-label">Invoice From:</label>
                                <input type="date" class="form-control" name="posting_from_date" id="invoice_from_date" value="{{request()->posting_from_date}}">
                            </div>
                            <div class="col-md-6">
                                <label for="invoice_to_date" class="form-label">Invoice To:</label>
                                <input type="date" class="form-control" name="posting_to_date" id="invoice_to_date" value="{{request()->posting_to_date}}">
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Branches</label>
                                    <select class="form-control" name="branch_id" id="branch_id">
                                        <option value="">Select Branch</option>
                                        @foreach ($branches as $key => $branch)
                                        <option value="{{$branch->branch_id}}" {{request()->input('branch_id') == $branch->branch_id ? 'selected':''}}>{{$branch->branch_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                           
                          
                            <div class="col-md-12">
                                <div class="form-group">
                                    <center>
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-filter" aria-hidden="true"></i>
                                            Filter</button>&nbsp;
                                        <a class="btn btn-primary" href="{{ route('ledger.report') }}"><i class="fa fa-times"
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
            <h3 class="card-title">Ledger Detailed Report</h3>
            </div>
            <div class="col-md-6 d-flex justify-content-end">
                
             </div>
        </div>
        <div class="card-body">

            <div class="table-responsive">
                <table id="report" class="table table-striped table-bordered text-nowrap w-100">
                    <thead>
                        <tr>
                            <th>SL.NO</th>
                            <th>Date</th>
                            {{--<th>Branch</th>--}}
                            <th>Account</th>
                            <th>Transaction details<br>(Narration)</th>
                             <th>Transaction Type</th>
                            <th>Debit</th>
                            <th>Credit</th>
                             <th>Amount</th>
                           
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $i = 0;
                        @endphp
                       
                        @foreach ($ledgerDetails as $key=>$detail)
                        
                        <tr id="dataRow_{{ @$detail->ledger_posting_id  }}">
                            <td>{{ $key+1 }}</td>
                            <td>{{ @$detail->posting_date  }}</td>
                           {{-- <td>{{ @$detail->branch['branch_name'] }}</td>--}}
                            <td>{{ @$detail->ledger->subGroups['account_sub_group_name']  }}</td>
                            <td>{{ @$detail->narration  }}</td>
                            <td>{{ @$detail->master_id  }}</td>
                            <td>{{ number_format(@$detail->debit,2)  }}</td>
                            <td>{{ number_format(@$detail->credit,2)  }}</td>
                            <td>{{ number_format(@$detail->debit-@$details->credit,2)  }}</td>
                            
                          
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="pagination" style="justify-content:flex-end;margin-top:-10px">
                   {{-- // --}}
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
                    buttons: [{
                            extend: 'excel',
                            text: 'Export to Excel',
                            title: 'Ledgere Report Detailed',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6,7]
                            }
                        },
                        {
                            extend: 'pdf',
                            text: 'Export to PDF',
                            title: 'Ledger Report Detailed',
                            footer: true,
                            orientation: 'landscape',
                            pageSize: 'LEGAL',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6, 7],
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


