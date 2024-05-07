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
                <form action="{{ route('profitloss.report') }}" method="GET" class="card-body">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="posting_from_date" class="form-label">From:</label>
                                <input type="date" class="form-control" name="posting_from_date" id="posting_from_date" value="{{request()->posting_from_date}}">
                            </div>
                            <div class="col-md-6">
                                <label for="posting_to_date" class="form-label">To:</label>
                                <input type="date" class="form-control" name="posting_to_date" id="posting_to_date" value="{{request()->posting_to_date}}">
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
        <div class="card-header">
            <div class="col-md-6">
            <h3 class="card-title">Profit And Loss Report</h3>
            </div>
            <div class="col-md-6 d-flex justify-content-end">
                
             </div>
        </div>
        <div class="card-body">

            <div class="table-responsive">
                <table id="reports" class="table table-striped table-bordered text-nowrap w-100">
                    <thead>
                        <tr>
                          
                            
                            <th>Particulars</th>
                            <th>INR</th>
                            <th>INR</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                         <tr>
                          
                            <td><i><u>Trading Account</u></i></td>
                            <td></td>
                            <td></td>
                           
                        </tr>
                       
                        <tr>
                          
                            <td><b>Sales Account:</b></td>
                            <td></td>
                            <td>{{number_format(@$tradingAccountSales[0]->total_amount,2)}}</td>
                           
                        </tr>
                        <tr>
                          
                            <td>{{@$tradingAccountSales[0]->ledger_name}}</td>
                            <td>{{number_format(@$tradingAccountSales[0]->total_amount,2)}}</td>
                            <td></td>
                           
                        </tr>
                        <tr>
                          
                            <td></td>
                            <td></td>
                            <td></td>
                           
                        </tr>
                         <tr>
                          
                            <td><b>Cost of sales</b></td>
                            <td></td>
                            <td>0.00</td>
                           
                        </tr>
                        <tr>
                          
                            <td>Less closing stock</td>
                            <td>0.00</td>
                            <td></td>
                           
                        </tr>
                        <tr>
                          
                            <td></td>
                            <td></td>
                            <td></td>
                           
                        </tr>
                        <tr>
                          
                            <td><b>Gross Profit</b></td>
                            <td></td>
                            <td>{{number_format(@$tradingAccountSales[0]->total_amount,2)}}</td>
                           
                        </tr>
                         <tr>
                          
                            <td><i><u>Income Statement</u></i></td>
                            <td></td>
                            <td></td>
                           
                        </tr>
                        <tr>
                          
                            <td><b>Indirect Income</b></td>
                            <td></td>
                            <td>{{number_format(@$indirectIncomeStatement[0]->total_amount??0,2)}}</td>
                           
                        </tr>
                         <tr>
                          
                            <td></td>
                            <td></td>
                            <td>{{number_format(@$tradingAccountSales[0]->total_amount,2)}}</td>
                           
                        </tr>
                         <tr>
                          
                            <td><b>Indirect Expense</b></td>
                            <td></td>
                            <td>{{number_format(@$indirectExpenseStatement[0]->total_amount??0,2)}}</td>
                           
                        </tr>
                        
                        <tr>
                          
                            <td></td>
                            <td></td>
                            <td></td>
                           
                        </tr>
                        <tr>
                          
                            <td><b>Net Profit</b></td>
                            <td></td>
                            <td>{{number_format(@$tradingAccountSales[0]->total_amount,2)}}</td>
                           
                        </tr>
                    
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
            paging: false,
            dom: 'Bfrtip<"pagination"lp>',
            buttons: [
            {
                extend: 'excel',
                text: 'Export to Excel',
                title: 'Payable Report',
                exportOptions: 
                {
                    columns: [0,1,2,3,4]
                }
            },
            {
                extend: 'pdf',
                text: 'Export to PDF',
                title: 'Payable Report',
                footer: true,
                orientation : 'landscape',
                pageSize : 'LEGAL',
                exportOptions: 
                {
                    columns: [0,1,2,3,4],
                    alignment: 'right',
                },
                    customize: function(doc) {
                    doc.content[1].margin = [ 100, 0, 100, 0 ]; //left, top, right, bottom
                    doc.content.forEach(function(item) {
                    if (item.table) {
                        item.table.widths = [40, '*','*','*','*','*','*','*']
                    }
                    })
                    }
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


