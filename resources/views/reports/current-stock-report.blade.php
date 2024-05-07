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
                <form action="{{ route('current.stock.report') }}" method="GET" class="card-body">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="medicine_code" class="form-label">Medicine Code:</label>
                                <input type="text" id="medicine_code" name="medicine_code" class="form-control"
                                value="{{ request()->input('medicine_code') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="medicine_name" class="form-label">Medicine Name:</label>
                                <input type="text" id="medicine_name" name="medicine_name" class="form-control"
                                value="{{ request()->input('medicine_name') }}">
                            </div>
                            <div class="col-md-4">
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
                                        <a class="btn btn-primary" href="{{ route('current.stock.report') }}"><i class="fa fa-times"
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
            <h3 class="card-title">Current Stock Report</h3>
            </div>
            <div class="col-md-6 d-flex justify-content-end">
                @php
                    $totalSaleRate = 0;
                    $totalPurchaseRate = 0;
                    try {
                        if ($current_stocks) {
                            $totalSaleRate = $current_stocks->sum(function ($item) {
                                return $item->sale_rate_with_tax * $item->current_stock;
                            });
                            $totalPurchaseRate = $current_stocks->sum(function ($item) {
                                return $item->purchase_rate * $item->current_stock;
                            });
                        }
                    } catch (\Exception $e) {
                 
                        $totalSaleRate = 0;
                       $totalPurchaseRate = 0;
                        \Log::error('Error calculating total due amount: ' . $e->getMessage());
                    }
                @endphp
               
               @if(!empty($totalSaleRate))
                <button class="btn btn-raised btn-outline-success">Total Sale Rate:{{$totalSaleRate}}</button>
               @endif
               @if(!empty($totalPurchaseRate))
               <br>
                <button class="btn btn-raised btn-outline-info ml-6">Total Purchase Rate:{{$totalPurchaseRate}}</button>
               @endif
           
                
             </div>
        </div>
         
        <div class="card-body">

            <div class="table-responsive">
                <table id="report" class="table table-striped table-bordered text-nowrap w-100">
                    <thead>
                        <tr>
                            <th>SL.NO</th>
                            <th>Medicine<br>Code</th>
                            <th>Medicine<br>Name</th>
                            <th>Batch</th>
                            <th>Sales Rate</th>
                            <th>Sale Value</th>
                            <th>Purchase Rate</th>
                            <th>Purchase value</th>
                            <th>Pharmacy</th>
                            <th>Current<br>Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $i = 0;
                        @endphp
                        @foreach ($current_stocks as $key=>$current_stock)
                        <tr id="dataRow_{{ $current_stock->stock_id  }}">
                            <td>{{ $key+1 }}</td>
                            <td>{{@$current_stock->medicines['medicine_code']}}</td>
                            <td>{{@$current_stock->medicines['medicine_name']}}</td>
                            <td>Batch: {{$current_stock->batch_no }} <br>
                                MFD: {{$current_stock->mfd }} <br>
                                EXP: {{$current_stock->expd }}
                            </td>
                            <td>{{$current_stock->sale_rate_with_tax }}</td>
                            <td>{{number_format(@$current_stock->sale_rate_with_tax * @$current_stock->current_stock,2) }}</td>
                            <td>{{$current_stock->purchase_rate }}</td>
                            <td>{{number_format(@$current_stock->purchase_rate * @$current_stock->current_stock,2)}}</td>
                            <td>{{@$current_stock->pharmacy['pharmacy_name']}}</td>
                            <td>{{$current_stock->current_stock }}</td>
                        </tr>
                    @endforeach
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
            dom: 'Bfrtip',
            buttons: [
            {
                extend: 'excel',
                text: 'Export to Excel',
                title: 'Current Stocks Report',
                exportOptions: 
                {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                }
            },
            {
                extend: 'pdf',
                text: 'Export to PDF',
                title: 'Current Stocks Report',
                footer: true,
                orientation: 'landscape',
                pageSize: 'LEGAL',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                },
                customize: function(doc) {
                    doc.content[1].margin = [20, 0, 20, 0]; //left, top, right, bottom
                    doc.content.forEach(function(item) {
                        if (item.table) {
                            item.table.widths = ['auto', 'auto', '*', 'auto', 'auto', 'auto', '*', 'auto']; // Set width to auto for all columns
                        }
                    });
                }
            }
            ]
        });
    });
    </script>
    
@endsection


