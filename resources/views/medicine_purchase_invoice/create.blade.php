@extends('layouts.app')
@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
@php
use App\Helpers\AdminHelper;
use App\Models\Mst_Staff;
use App\Models\Mst_Pharmacy;
// dd(AdminHelper::getProductId($value->medicine_code));
@endphp

<style>
   .table th {   font-size: 12px;} select.medsearch { display: none !important; } span.current {
    font-size: 10px!important; } .table td {padding: 5px 3px;} .pricecard .form-group label {font-size: 12px;margin:0;} .pricecard .form-group span, .pricecard .form-group input {width: auto;padding: 0;border: unset;line-height: 1;height:auto;} .pricecard .form-group input:focus {outline: unset !important;border: none !important;} .pricecard .form-group {display: flex;align-items: center;gap: 16px;margin: 5px 0;}.pricecard .col-md-4 {margin-left: auto;}.dropdown-select {background-image: linear-gradient(to bottom, rgba(255, 255, 255, 0.25) 0%, rgba(255, 255, 255, 0) 100%);background-repeat: repeat-x;filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#40FFFFFF', endColorstr='#00FFFFFF', GradientType=0);background-color: #fff;border-radius: 6px;box-sizing: border-box;cursor: pointer;display: block;float: left;font-size: 14px;font-weight: normal;outline: none;padding-left: 18px;padding-right: 30px;position: relative;text-align: left !important;transition: all 0.2s ease-in-out;-webkit-user-select: none;-moz-user-select: none; -ms-user-select: none;user-select: none;white-space: nowrap; width: auto;}.dropdown-select:focus {background-color: #fff;}.dropdown-select:hover {background-color: #fff;}.dropdown-select:active,.dropdown-select.open {background-color: #fff !important;border-color: #bbb;box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05) inset;}.dropdown-select:after {height: 0; width: 0; border-left: 4px solid transparent; border-right: 4px solid transparent; border-top: 4px solid #777;-webkit-transform: origin(50% 20%); transform: origin(50% 20%); transition: all 0.125s ease-in-out; content: ''; display: block; margin-top: -2px; pointer-events: none; position: absolute; right: 10px; top: 50%;}.dropdown-select.open:after { -webkit-transform: rotate(-180deg); transform: rotate(-180deg);}.dropdown-select.open .list { -webkit-transform: scale(1); transform: scale(1); opacity: 1; pointer-events: auto;}.dropdown-select.open .option {cursor: pointer;}.dropdown-select.wide {width: 100%;}.dropdown-select.wide .list {left: 0 !important;right: 0 !important;}.dropdown-select .list {box-sizing: border-box; transition: all 0.15s cubic-bezier(0.25, 0, 0.25, 1.75), opacity 0.1s linear; -webkit-transform: scale(0.75); transform: scale(0.75); -webkit-transform-origin: 50% 0; transform-origin: 50% 0; box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.09); background-color: #fff; border-radius: 6px; margin-top: 4px; padding: 3px 0; opacity: 0; overflow: hidden; pointer-events: none; position: absolute; top: 100%; left: 0; z-index: 999; max-height: 250px; overflow: auto; border: 1px solid #ddd;}.dropdown-select .list:hover .option:not(:hover) { background-color: transparent !important;}.dropdown-select .dd-search{overflow:hidden;display:flex;align-items:center;justify-content:center;margin:5px 0;}.dropdown-select .dd-searchbox{width:90%;padding:0.5rem;border:1px solid #999;border-color:#999;border-radius:4px;outline:none;line-height: 1;}.dropdown-select .dd-searchbox:focus{border-color:#12CBC4;}.dropdown-select .list ul { padding: 0;}.dropdown-select .option {cursor: default; font-weight: 400; line-height: 2; outline: none; padding-left: 10px; padding-right: 25px; text-align: left; transition: all 0.2s; list-style: none; font-size: 10px;}.dropdown-select .option:hover,.dropdown-select .option:focus {background-color: #f6f6f6 !important;}.dropdown-select .option.selected { font-weight: 600; color: #12cbc4;}.dropdown-select .option.selected:focus {background: #f6f6f6;}.dropdown-select a {color: #aaa; text-decoration: none; transition: all 0.2s ease-in-out;}.dropdown-select a:hover {
    color: #666;}
    .form-control:disabled, .form-control[readonly] {
    background-color: #c7c7c7;
      }
      .list li.option[disabled] {
    display: none;
}
select.form-control.medsearch.errorSelect + .dropdown-select {
    border-color: red !important;
}
.dropdown-select .list ul {
    padding: 0;
    height: 50px;
    overflow: auto;
}
.flex-cl label {
    display: flex;
    align-items: center;
    gap: 5px;
    margin-top: 10px;
}
.flex-cl label {
    display: flex;
    align-items: center;
    gap: 5px;
    margin-top: 10px;
    position: absolute;
    right: -80px;
    top: 0;
    z-index: 999;
}
/*.dropdown-select.wide .list {*/
/*    z-index: 999999!important;*/
/*    min-height: 238px!important;*/
/*    position: relative!important;*/
/*}*/
.dropdown-select.wide .list {
    z-index: 999999 !important;
    min-height: 238px !important;
    position: absolute !important;
    max-width: 245px;
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
.dropdown-select.wide .list{
    left:37px!important;
}
.dd-search:nth-child(2) {
    display: none;
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
                  <!-- <strong>Whoops!</strong> There were some problems with your input.<br><br> -->
                  <ul>
                     @foreach ($errors->all() as $error)
                     <li>{{ $error }}</li>
                     @endforeach
                  </ul>
               </div>
               @endif
               <div class="card-body border" style="background-color: #13b75229 !important;  padding: 10px 10px;">
               <form id="productForm" action="{{ route('excel.import') }}" method="POST" enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                           <div class="col-md-12">
                              <div class="form-group">
                                 <a class="btn btn-raised btn-blue" href="{{ route('download.products.sample') }}" style="padding:5px;float:right;">
                                    <i class="fa fa-file-excel-o"></i> Download Sample Excel
                                 </a> <br>
                                  <label class="form-label">Import Product Excel* (Supported Format: .xlsx, .xls)</label>
                                  <input type="file" class="form-control custom-file-input" name="products_file" style="opacity:1; background-color: #29c7315e;" accept=".xlsx, .xls">
                              </div>
                          </div>
                          <div class="col-md-12">
                           <div class="form-group">
                              <center>
                              <button type="submit" class="btn btn-primary">Import</button>
                              <center>
                           </div>
                           </div> 
                     </form></br> </br>
                   </div>
               </div>

              
               <form action="{{ route('medicinePurchaseInvoice.store') }}" method="POST" enctype="multipart/form-data" id="myForm"  onsubmit="return validateForm()">
                  @csrf
                  <div class="row">
                     <div class="col-md-3">
                        <div class="form-group">
                           <label class="form-label">Supplier*</label>
                           <select class="form-control" name="supplier_id" id="supplier_id" required onchange="updateCreditSection();">
                              <option value="">Select Supplier</option>
                              @foreach ($suppliers as $id => $supplier)
                              <option value="{{ $supplier->supplier_id }}" data-credit-limit="{{ $supplier->credit_limit }}" data-current-credit="{{ $supplier->current_credit }}">
                                 {{ $supplier->supplier_name }}
                              </option>
                              @endforeach
                           </select>
                        </div>
                     </div>
                     <div class="col-md-2">
                        <div class="form-group">
                           <label class="form-label">Invoice No*</label>
                           <input type="text" class="form-control" required name="invoice_no" id="invoice_no" maxlength="16" value="{{ old('invoice_no') }}" placeholder="Invoice No">
                        </div>
                     </div>
                     <div class="col-md-2">
                        <div class="form-group">
                           <label class="form-label">Invoice Date*</label>
                           <input type="date" class="form-control" required name="invoice_date" id="invoice_date" onchange="updateDueDate()" maxlength="16" value="{{ old('invoice_date') ?: now()->format('Y-m-d') }}" placeholder="Invoice Date">

                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="form-group">
                           <label class="form-label">Pharmacy*</label>
                             @if(Auth::check() && Auth::user()->user_type_id == 96)
                           @php
                            $staff = Mst_Staff::findOrFail(Auth::user()->staff_id);
                            $mappedpharma = $staff->pharmacies()->pluck('mst_pharmacies.id')->toArray();
                           @endphp
                            <select class="form-control" name="pharmacy_id" id="pharmacy_id">
                                <option value="" {{ !request('id') ? 'selected' : '' }}>Choose Pharmacy</option>
                                @foreach ($pharmacies as $pharmacy)
                                       @if(in_array($pharmacy->id, $mappedpharma))
                                           <option value="{{ $pharmacy->id }}" {{request()->input('pharmacy_id') == $pharmacy->id ? 'selected':''}}>{{ $pharmacy->pharmacy_name }}</option>
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
                        @elseif(Auth::user()->user_type_id == 18 || Auth::user()->user_type_id == 21 || Auth::user()->user_type_id == 20)
                            @php
                                $branch_id = Auth::user()->staff->branch_id;
                                $pharmacydet = Mst_Pharmacy::where('branch', $branch_id)->first();
                            @endphp
                             <select class="form-control" name="pharmacy_id" id="pharmacy_id" readonly>
                                    <option value="{{ $pharmacydet->id }}" selected="">
                                        {{ $pharmacydet->pharmacy_name }}
                                    </option>
                                
                            </select>
                        @else
                        <select class="form-control" name="pharmacy_id" id="pharmacy_id">
                                <option value="" {{ !request('id') ? 'selected' : '' }}>Choose Pharmacy</option>
                                @foreach($pharmacies as  $data)
                                    <option value="{{ $data->id }}"{{ old('id') == $data->id ? 'selected' : '' }}>
                                        {{ $data->pharmacy_name }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                        </div>
                     </div>
                     <div class="col-md-2">
                        <div class="form-group">
                           <label class="form-label">Due Date</label>
                           <input type="date" class="form-control"  name="due_date" id="due_date" placeholder="Due Date" value="{{ old('due_date') ?: now()->format('Y-m-d') }}">
                        </div>
                     </div>
                  </div>
                  <div class="row align-items-center" id="creditSection">
                     <div class="col-6">
                        <p><span><strong>Credit Limit:</strong></span><span id="creditLimitDisplay" name="credit_limit" style="color: green;"></span></p>
                        <p><span><strong>Credit Period:</strong></span> <span id="currentCreditDisplay" name="current_credit" style="color: green;">0%</span></p>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-12 col-lg-12">
                        <div class="card">
                           <!--<div class="table-responsive" style="min-height: 375px;">-->
                           <div class="table-responsive" id="table-tr">
                              <table class="table card-table table-vcenter text-nowrap" id="productTable">
                                 <thead>
                                    <tr>
                                       <th>Product Name</th>
                                       <th>Product Code</th>
                                       <th>Quantity</th>
                                       <th>Product Unit</th>
                                       <th>Sale Rate(Excluding Tax)</th>
                                    <th>Sale Rate(Including Tax)</th>
                                       <th>Purchase Rate</th>
                                       <th>Purchase MRP</th>
                                       <th>Free Quantity</th>
                                       <th>Batch No</th>
                                       <th>Manufacture Date</th>
                                       <th>Expiry Date</th>
                                       <th>Discount</th>         
                                       <th>Tax%</th>
                                       <th>Tax Amount</th>
                                       <th>Amount</th>
                                       <th>Action</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <tr id="productRowTemplate" >
                                       <td style="width: 100%;min-width: 300px;">
                                      <select class="form-control medsearch" name="product_id[]" onchange="fetchMedicineDetails(this);">
                                          <option value="" selected disabled>Select medicine</option>
                                          @foreach($products as $product)
                                             <option value="{{ $product->id }}">{{ $product->medicine_name }} - {{ $product->medicine_code}}</option>
                                          @endforeach
                                       </select>
                                       </td>
                                       <td><input type="text" class="form-control" name="medicine_code[]" readonly></td>
                                    <td>
                                            <input type="text" class="form-control" name="quantity[]" oninput="this.value = Math.max(1, parseInt(this.value) || 0)" />
                                        </td>

                                       <td><input type="text" class="form-control" name="unit_id[]" readonly></td>
                                       <td><input type="text" class="form-control" name="sales_rate[]" readonly>
                                       
                                       </td>
                                       <td><input type="text" class="form-control" name="rateIncluding[]" value="" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/^0+/, '');"></td>
                                      <td><input type="text" class="form-control" name="rate[]" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"></td>
                                    <td>
                                        <input type="text" class="form-control" name="purchase_mrp[]" placeholder="0" oninput="validateFreeQuantity(this)" readonly />
                                    </td>
                                       <td>
                                        <input type="text" class="form-control" name="free_quantity[]" placeholder="0" oninput="validateFreeQuantity(this)" />
                                    </td>
                                       <td><input type="text" class="form-control" name="batch_no[]"></td>
                                       <td><input type="date" class="form-control" name="mfd[]" value="{{ now()->toDateString() }}"></td>
                                       <td><input type="date" class="form-control" name="expd[]" value="{{ now()->toDateString() }}"></td>
                                        <td><input type="text" class="form-control" name="discount[]" oninput="this.value = this.value.replace(/[^0-9]/g, '')"></td>
                                         <td><input type="text" class="form-control" name="tax[]" readonly></td>
                                         <td><input type="text" class="form-control" name="tax_amount[]" readonly></td>
                                         <td><input type="text" class="form-control" name="amount[]" readonly >
                                         <input type="hidden" class="form-control" name="amount1[]" readonly ></td>
                                       <td><button class="btn btn-success remove-button" onclick="removeRow(this)" type="button">Remove</button> </td>
           
                                     </tr>
                                     @isset($processedData)
                                     @foreach($processedData[0] as $row)
                                    <tr id="productRowTemplate">
                                        <td>
                                            <select class="form-control medsearch impoSel" name="product_id[]" onchange="fetchMedicineDetails(this);">
                                                <option value="" selected disabled>Select medicine</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}" {{$product->id==$row['medicine_id']?'selected':''}}>{{ $product->medicine_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="text" class="form-control" name="medicine_code[]" value="{{ $row['medicine_code']??null }}" readonly></td>
                                        <td><input type="text" class="form-control" name="quantity[]" value="{{ $row['qty'] }}" onchange="calculateTotals();" oninput="this.value = Math.max(1, parseInt(this.value) || 0);"></td>
                                        <td><input type="text" class="form-control" name="unit_id[]" value="{{ $row['unit_id']??null }}" readonly></td>
                                        <td><input type="text" class="form-control" name="sales_rate[]" value="{{ $row['sales_rate']??null }}"></td>
                                        <td><input type="text" class="form-control" name="rateIncluding[]" value="{{ $row['sales_rate']??null }}"></td>
                                        <td><input type="text" class="form-control" name="rate[]" value="{{ $row['purchase_rate']??null }}"></td>
                                         <td>
                                        <input type="text" class="form-control" name="purchase_mrp[]" value="{{ $row['purchase_mrp']??null }}"  readonly />
                                        </td>
                                        <td><input type="text" class="form-control" name="free_quantity[]" value="{{ $row['free_qty']??null }}" placeholder="0" oninput="validateFreeQuantity(this)"></td>
                                        <td><input type="text" class="form-control" name="batch_no[]" value="{{ $row['batch_no']??null }}"></td>
                                        <td><input type="date" class="form-control" name="mfd[]" value="{{ $row['mdd']??null }}"></td>
                                        <td><input type="date" class="form-control" name="expd[]" value="{{ $row['expd']??null }}"></td>
                                        <td><input type="text" class="form-control" name="discount[]" value="{{ $row['discount']??null }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')"></td>
                                        <td><input type="text" class="form-control" name="tax[]" value="{{ $row['tax']??null }}" readonly></td>
                                        <td><input type="text" class="form-control" name="tax_amount[]" value="{{ $row['tax_amount']??null }}" readonly></td>
                                         <td><input type="text" class="form-control" name="amount[]" value="{{ $row['amount']??null }}" readonly>
                                          <input type="hidden" class="form-control" name="amount1[]" value="{{ $row['amount']??null }}" readonly ></td>
                                        <td><button class="btn btn-success remove-button" onclick="removeRow(this)" type="button">Remove</button></td>
                                    </tr>
                                     @endforeach
                                     @endisset

                                 </tbody>
                              </table>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="row">
                  <div class="col-md-12">
                @isset($processedData)
                  <button class="btn btn-primary" id="addProductBtnImported">Add Product</button>
                @else
                <button class="btn btn-primary" id="addProductBtn">Add Product</button>
                @endif
               </div>
                  </div>
                  <!-- ROW-1 CLOSED -->
                  <div class="col-md-4">
                     <div class="form-group">
                        <label class="form-label">Sub Total:</label>
                        <span><input type="text" class="form-control" readonly name="sub_total" id="sub_total" placeholder="Sub Total"></span>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label class="form-label">Item-wise discount:</label>
                        <input type="text" class="form-control" readonly name="item_wise_discount" id="item_discount" placeholder="Item Wise Discount"  oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label class="form-label">Bill discount:</label>
                        <input type="text" class="form-control" name="bill_discount" id="bill_discount" placeholder="Bill Discount"  oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label class="form-label">Total Tax Amount:</label>
                        <div class="flex-cl" style="position:relative;">
                            <input type="text" class="form-control" readonly name="total_tax" id="total_tax" placeholder="Tax">
                            <label>
                                
                                <input type="checkbox" name="isigst" value="1">
                                <strong> IS IGST </strong> 
                            </label>
                        </div>
                        
                        <input type="hidden" class="form-control" readonly name="total_tax_percentage" id="total_tax1" placeholder="Tax">
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label class="form-label">CGST:</label>
                        <input type="text" class="form-control" readonly name="cgst" id="cgst" placeholder="CGST">
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label class="form-label">SGST:</label>
                        <input type="text" class="form-control" readonly name="sgst" id="sgst" placeholder="SGST">
                     </div>
                  </div>

                  <div class="col-md-4">
                     <div class="form-group">
                        <label class="form-label">IGST:</label>
                        <input type="text" class="form-control" readonly name="igst" id="igst" placeholder="IGST">
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label class="form-label">Adjustment :</label>
                        <input type="text" class="form-control" name="round_off" id="round_off" placeholder="Round Off" oninput="this.value = this.value.replace(/[^\d.+-]|(?<=\.).*\.|(?<=\..*)\.|(?<=\d)[+-]|(?<=^[+-])|[+-](?=[+-])/, '')
" >
                        <!--<input type="text" class="form-control" name="round_off" id="round_off" placeholder="Round Off"  oninput="this.value = this.value.replace(/[^0-9]/g, '')">-->
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label class="form-label">Total:</label>
                        <input type="text" class="form-control" readonly name="total_amount" id="total" placeholder="Total">
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <div class="form-label">Amount Paid</div>
                           <label class="custom-switch">
                              <!-- Hidden field for false value -->
                              <input type="hidden" name="is_paid" value="0">
                              <input type="checkbox" id="is_paid" name="is_paid" onchange="toggleStatus(this)" class="custom-switch-input" checked value="1">
                              <span id="statusLabel" class="custom-switch-indicator"></span>
                              <span id="statusText" class="custom-switch-description">paid</span>
                           </label>
                        </div>
                     </div>
                  </div>
                  <div id="payment_fields" style="display: none;">
                     <div class="row">
                        <div class="col-md-3">
                           <div class="form-group">
                              <label class="form-label">Paid Amount</label>
                                <input type="text" class="form-control" name="paid_amount" maxlength="16" value="{{ old('paid_amount') }}" placeholder="Paid Amount" oninput="if(this.value.length === 1 && this.value === '0') { this.value = ''; } else { this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1'); }">
                                

                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group">
                              <label for="payment-type" class="form-label">Payment Mode</label>
                              <select class="form-control"  name="payment_mode" placeholder="Payment Mode" id="payment_mode"  >
                                 <option value="">--Select--</option>
                                 @foreach($paymentType as $id => $value)
                                 <option value="{{ $id }}">{{ $value }}</option>
                                 @endforeach
                              </select>
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group">
                              <label class="form-label">Deposit To</label>
                              <select class="form-control" name="deposit_to" id="deposit_to">
                                 <option value="">Deposit To</option>
                              </select>
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group">
                              <label class="form-label">Reference Code</label>
                              <input type="text" class="form-control" name="reference_code" maxlength="16" value="{{ old('reference_code') }}" placeholder="Reference Code">
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="form-group">
                     <center>
                        <button type="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Save</button>
                        <a class="btn btn-danger" href="{{ route ('medicinePurchaseInvoice.index') }}">Close</a>
                     </center>
                  </div>
            </div>
         </div>
      </div>
      </form>
   </div>
</div>
</div>
</div>
</div>
</div>

@endsection

@section('js')

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>

   $(document).ready(function() {
        $('.searchable').select2();
        $('.impoSel').each(function() {
        //fetchMedicineDetails(this);
    });
         
    });
</script>




<script>


function validateForm() {
      var isValid = true;
    var totalRows = $("#productTable tbody tr").length - 1;
    if(totalRows > 1) {
         $("#productTable tbody tr").each(function() {
               var select = parseFloat($(this).find('select[name="product_id[]"]').val());  
               
               if(!select) {
                   $(this).remove()
               }
         
         });
    }
     //var remainingCredit=document.getElementById('remainingCreditDisplay').textContent;
       var paid_amount=$('input[name="paid_amount"]').val();
       var total_amount=$('input[name="total_amount"]').val();
       var remaining_amount=total_amount-paid_amount;
       //alert(remaining_amount)
       if(remaining_amount != 0) {
           Swal.fire({
                      icon: 'error',
                      title: 'Error',
                      text: 'Paid Amount should be equal to Total Amount',
                      timer: 2000,
                      showConfirmButton: false
                    });
           var isValid = false;
           return isValid
       }
       
       
       
      
      return isValid;
      
   }
</script>


<script>
   $(document).ready(function() {
      // Handle change event on the payment mode dropdown
      $('#payment_mode').change(function() {
         // Get the selected value
         var selectedPaymentMode = $(this).val();

         // Make an AJAX request to fetch the ledger names based on the selected payment mode
         $.ajax({
            url: '{{ route("getLedgerNames1") }}',
            type: 'GET',
            data: {
               payment_mode: selectedPaymentMode
            },
            success: function(data) {
               // Clear existing options
               $('#deposit_to').empty();

               // Add default option
               $('#deposit_to').append('<option value="">Deposit To</option>');

               // Add options based on the response
               $.each(data, function(key, value) {
                  $('#deposit_to').append('<option value="' + key + '">' + value + '</option>');
               });
            },
            error: function(error) {
               console.log(error);
            }
         });
      });
   });
</script>

<script>
   $(document).ready(function() {
      $('input[readonly][data-medicine-code]').each(function() {
         var input = $(this);
         var medicineCode = input.data('medicine-code');

         // Fetch product_id and unit_id
         var productUrl = '/get-product-id/' + encodeURIComponent(medicineCode);
         var unitUrl = '/get-unit-id/' + encodeURIComponent(medicineCode);

         $.get(productUrl, function(productResponse) {
            // Set the value of the corresponding input field in the same row
            input.closest('tr').find('input[name="product_id[]"]').val(productResponse.product_id);
         });

         $.get(unitUrl, function(unitResponse) {
            // Set the value of the corresponding input field in the same row
            input.closest('tr').find('input[name="unit_id[]"]').val(unitResponse.unit_id);
         });
      });
   });

   $(document).ready(function() {
   $('select[name="product_id[]"]').each(function() {
      var select = $(this);
      var medicineCode = select.closest('tr').find('input[name="medicine_code[]"]').val();
      var productUrl = '/get-product-id/' + encodeURIComponent(medicineCode);

      $.get(productUrl, function(productResponse) {
         productResponse.forEach(function(product) {
            var option = $('<option>', {
               value: product.product_id,
               text: product.product_id
            });

            select.append(option); // Fix: append the 'option' variable
         });
      });
   });
});

</script>

<script>
   function updateCreditLimit() {
      // Get the selected supplier_id
      var selectedSupplierId = $("#supplier_id").val();

      // Make an AJAX request to fetch the credit limit based on the selected supplier_id
      $.ajax({
         url: '/get-credit-details/' + encodeURIComponent(selectedSupplierId),
         type: 'GET',
         success: function(creditDetails) {
             //console.log(creditDetails)
            // Update the content of the credit limit display element
            $("#creditLimitDisplay").text(creditDetails.creditLimit);
            $("#currentCreditDisplay").text('Current Credit: ' + creditDetails.currentCredit);
            $("#due_date").val(creditDetails.dueDate);
            console.log('Current Credit: ' + creditDetails.currentCredit);

            // Call the emptyOthers function
            emptyOthers();
         },
         error: function(error) {
            console.log(error);
         }
      });
   }

   function emptyOthers() {
      document.getElementById("invoice_date").value = '';
    //   document.getElementById("due_date").value = '';
   }
</script>

<script>
   function emptyOthers() {
      document.getElementById("invoice_date").value = '';
    //   document.getElementById("due_date").value = '';
   }

    // $(document).ready(function() {
        
    // });
    
     $("#addProductBtnImported").on("click", function(event) {
            event.preventDefault();
            var newRow = $("#productRowTemplate").clone();
            newRow.removeAttr("style");
            newRow.find('input[type="text"]').val('');
            newRow.find('input[type="number"]').val('');
            // Append the new row to the table
            newRow.find('input[name="mfd[]"]').val(new Date().toISOString().split('T')[0])
            var mfdValue =  newRow.find('input[name="mfd[]"]').val();
            
              var mfdDate = new Date(mfdValue);
               mfdDate.setDate(mfdDate.getDate() + 1);
                var minExpDate = mfdDate.toISOString().split('T')[0];
            var expInput = newRow.find('input[name="expd[]"]');
            expInput.attr('min', minExpDate);
            expInput.val(minExpDate);
            
            $("#productTable tbody").append(newRow);
        });



   $(document).ready(function() {
      // Call the function initially to set the visibility based on the initial state
      togglePaymentFields();

      // Bind the function to the change event of the "is_paid" checkbox
      $("#is_paid").change(function() {
         togglePaymentFields();
      });


   });

   function togglePaymentFields() {
      var isPaidToggle = $("#is_paid");
      var paymentFields = $("#payment_fields");

      if (isPaidToggle.prop("checked")) {
         paymentFields.show(); // Show the payment fields
         
          $('#payment_fields select').prop('required', true);
      } else {
         paymentFields.hide(); // Hide the payment fields
         $('#payment_fields select').prop('required', false);
      }
   }

   function toggleStatus(checkbox) {
      if (checkbox.checked) {
         $("#statusText").text('Paid');
         $("#statusLabel").removeClass("custom-switch-indicator-danger");
         $("input[name=is_paid]").val(1); // Set the value to 1 when checked
      } else {
         $("#statusText").text('Not Paid');
         $("#statusLabel").addClass("custom-switch-indicator-danger");
         $("input[name=is_paid]").val(0); // Set the value to 0 when unchecked
      }
   }
</script>

<script>
   // Function to update credit section visibility and content
   function updateCreditSection() {
      var selectedSupplier = document.getElementById('supplier_id');
      var creditSection = document.getElementById('creditSection');
      var creditLimitDisplay = document.getElementById('creditLimitDisplay');
      var currentCreditDisplay = document.getElementById('currentCreditDisplay');

      // Check if a supplier is selected
      if (selectedSupplier.value !== "") {
         // Show the credit section
         creditSection.style.display = 'block';

         // Get the selected supplier's ID
         var supplierId = selectedSupplier.value;

         // Make an AJAX request to fetch credit-related data
         $.ajax({
            url: '{{ route("medicinePurchaseInvoice.getcreditinfo", ["supplierId" => "_supplierId_"]) }}'.replace('_supplierId_', supplierId),
            method: 'GET',
            success: function(data) {
               // console.log(data)
               creditLimitDisplay.innerText = data.creditLimit;
               currentCreditDisplay.innerText = data.currentCredit;
                $("#due_date").val(data.dueDate);
            },
            error: function(error) {
               console.error('Error fetching data:', error);
            }
         });
      } else {
         creditSection.style.display = 'none';
      }
   }
</script>

<script>
$("#addProductBtn").on("click", function(event) {
            event.preventDefault();
            var newRow = $("#productRowTemplate").clone();
            newRow.removeAttr("style");
            newRow.find('input[type="text"]').val('');
            newRow.find('input[type="number"]').val('');
            // Append the new row to the table
            newRow.find('input[name="mfd[]"]').val(new Date().toISOString().split('T')[0])
            var mfdValue =  newRow.find('input[name="mfd[]"]').val();
            
              var mfdDate = new Date(mfdValue);
               mfdDate.setDate(mfdDate.getDate() + 1);
                var minExpDate = mfdDate.toISOString().split('T')[0];
            var expInput = newRow.find('input[name="expd[]"]');
            expInput.attr('min', minExpDate);
            expInput.val(minExpDate);
            
            
            $("#productTable tbody").append(newRow);
            newRow.find('.dropdown-select').remove();
           create_custom_dropdowns();
        });


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

function filter(i){
    var valThis = $(i).val();
    $(i).closest('.dropdown-select').find('ul > li').each(function(){
     var text = $(this).text();
        (text.toLowerCase().indexOf(valThis.toLowerCase()) > -1) ? $(this).show() : $(this).hide();         
   });
};
// Search

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

$(document).ready(function () {
    create_custom_dropdowns();
    // $("#addProductBtn").click();
});


    $(document).ready(function() {
        $('input[name="free_quantity[]"]').on('input', function() {
            var purchaseQuantity = parseInt($(this).closest('tr').find('input[name="quantity[]"]').val()) || 0;
            var freeQuantity = parseInt($(this).val()) || 0;
            
            if (freeQuantity > purchaseQuantity) {
                swal("Free Quantity cannot be greater than Purchase Quantity");
                $(this).val(purchaseQuantity);
            }
        });
    });
        $(document).ready(function() {
        // $('input[name="expd[]"]').on('change', function() {
        //     var expdInput = $(this);
        //     var mfdInput = expdInput.closest('tr').find('input[name="mfd[]"]');
        //     var expd = new Date(expdInput.val());
        //     var mfd = new Date(mfdInput.val());

        //     if (expd < mfd) {
        //         expdInput.val(mfdInput.val());
        //         swal("Expiry date cannot be less than Manufacturing date");
        //     }
        // });
         $('input[name="mfd[]"]').each(function() {
              var mfdValue = $(this).val();
              var mfdDate = new Date(mfdValue);
               mfdDate.setDate(mfdDate.getDate() + 1);
                var minExpDate = mfdDate.toISOString().split('T')[0];
            var expInput = $(this).closest('tr').find('input[name="expd[]"]');
            expInput.attr('min', minExpDate);
            expInput.val(minExpDate);
         });
        $(document).on('change', 'input[name="mfd[]"]', function() { 
            //alert($(this).val())
            var mfdValue = $(this).val();
            var expInput = $(this).closest('tr').find('input[name="expd[]"]');
            var x = expInput.val('');
            expInput.attr('min', mfdValue);
            
        })
    });

</script>

<script>

  function removeRow(button) {
   var row = $(button).closest('tr');
   row.remove();
   calculateMainTotals()

  }
  $(document).on('input', 'input[name="quantity[]"]', function () {

    var enteredValue = $(this).val();
    var parsedValue = parseFloat(enteredValue);
    if (isNaN(parsedValue) || parsedValue < 0) {
        $(this).val('');
    }


    var row = $(this).closest('tr');
    var purchaserate = row.find("input[name='rate[]']").val();
    if(purchaserate) {
      calculateRowAmounts(row)
    }

  });
  $(document).on('input', 'input[name="rate[]"]', function () {
    var row = $(this).closest('tr');
    var purchasequantity = row.find("input[name='quantity[]']").val();
    if(purchasequantity) {
      calculateRowAmounts(row)
    }

  });
  $(document).on('input', 'input[name="discount[]"]', function () {

       var totalTax = 0;
        var row = $(this).closest('tr');
        var discountVal =  $(this).val();
        
        //row.find('input[name="tax_amount[]"]').val('');
        
        var amountValue = parseFloat(row.find('input[name="amount1[]"]').val()) || 0;
        //alert(amountValue)
        if (discountVal >= amountValue) {
             Swal.fire({
                          icon: 'error',
                          title: 'Error',
                          text: 'Discount cannot be greater than total amount',
                          timer: 2000,
                          showConfirmButton: false
                        });

            $(this).val('');
            calculateRowAmounts(row)  
        }
      else
      {
          calculateRowAmounts(row)  
      }

    //var row = $(this).closest('tr');
       
  });

  // $(document).on('input', 'input[name="discount[]"]', function () {
  //   var row = $(this).closest('tr');
  //    calculateRowAmounts(row)    
  // }
        $(document).on('input', 'input[name="rateIncluding[]"]', function () {
            
            if($(this).val()) {
                calculateSales($(this));
            }
            else {
                $(this).val('0.1')
                calculateSales($(this));
            }
        });
        
        $(document).on('blur', 'input[name="rateIncluding[]"]', function () {
            if ($(this).val() == '') {
                $(this).val('0.1');
                calculateSales($(this));
            }
        });
        
        function calculateSales(input) {
            var tax = parseFloat(input.closest('tr').find("input[name='tax[]']").val()) || 0;
            var sale = parseFloat(input.val()) || 0;
            if (sale > 0) {
                var taxRateDecimal = tax / 100;
                var originalValue = sale / (1 + taxRateDecimal);
                input.closest('tr').find("input[name='sales_rate[]']").val(originalValue.toFixed(2));
            } 
        }

  // select change

  function fetchMedicineDetails(select) {
        // var rowMedicine = $(select).closest('tr');
        // rowMedicine.find('input').val('');
        // rowMedicine.find('select').not(select).val('');
        
        var selectedProductId = $(select).val();
        $.ajax({
            url: '{{ route("getMedicineDetails", ["productId" => "_productId_"]) }}'.replace('_productId_', selectedProductId),
            method: 'GET',
            success: function (data) {
                var row = $(select).closest('tr');
                row.find('[name="medicine_code[]"]').val(data.medicine_code);
                row.find('[name="unit_id[]"]').val(data.unit_name);
                row.find('[name="sales_rate[]"]').val(data.unit_price);
                 var x = (data.unit_price*data.tax_rate)/100;
                x = parseFloat(x) + parseFloat(data.unit_price);
                //x = parseFloat(x);
               // alert(x)
                row.find('[name="rateIncluding[]"]').val(x.toFixed(2));
                
                var currentDate = new Date().toISOString().split('T')[0];
              if (row.find('[name="mfd[]"]').val() === '') {
                row.find('[name="mfd[]"]').val(currentDate);
            }
            if (row.find('[name="expd[]"]').val() === '') {
                row.find('[name="expd[]"]').val(currentDate);
            }
                row.find('[name="tax[]"]').val(data.tax_rate);
                
            var quantityInput = row.find('[name="quantity[]"]');
            //alert(quantityInput.val())
            var rateInput = row.find('[name="rate[]"]');
            var amountInput = row.find('[name="amount[]"]');
            // Event handler for quantity field
            quantityInput.on('keyup', function () {
                calculateRowAmounts();
            });
            rateInput.on('keyup', function () {
                calculateRowAmounts(row);
            });
            calculateRowAmounts(row);
            
           
            
            
            },
            error: function (error) {
                console.error('Error fetching data:', error);
            }
        });

         var medicine = $(select).val();
        console.log("medi"+medicine);
        var row = $(select).closest('tr');
        if (medicine && medicine != null) {
            row.find("input[name='batch_no[]']").prop('required', true);
            row.find("input[name='quantity[]']").prop('required', true);
            //alert( row.find("input[name='quantity[]']").val());
            row.find("input[name='rate[]']").prop('required', true);
        }
        else
        {
            row.find("input[name='batch_no[]']").prop('required', false);
            row.find("input[name='quantity[]']").prop('required', false);
            row.find("input[name='rate[]']").prop('required', false);
        }
  }



 $('#round_off').on('input', function() {   
           //var totalA= parseFloat($('input[name="total_amount"]').val()) || 0;
            var v =  $(this).val();
            var totaldis= parseFloat($('input[name="item_wise_discount"]').val()) || 0;
              var totalBilldis= parseFloat($('input[name="bill_discount"]').val()) || 0;
              var sub= parseFloat($('input[name="sub_total"]').val()) || 0;
              var totalTax= parseFloat($('input[name="total_tax"]').val()) || 0;
              var roundoff = parseFloat($('input[name="round_off"]').val()) || 0;
              
              var check = sub - totalBilldis + totalTax - totaldis ;
              if(roundoff <= -check){
                         Swal.fire({
                      icon: 'error',
                      title: 'Error',
                      text:  'We cannot substract  an amount greater than or equal to total',
                    });
                   
                  $('input[name="round_off"]').val(0)
              }
              else {
              var total = sub - totalBilldis + totalTax - totaldis + roundoff ;
              
            // //   if (total - Math.floor(total) >= 0.5) {
            // //         total = Math.ceil(total);
            // //     } else {
            // //         total= Math.floor(total);
            // //     }
             $('input[name="total_amount"]').val(total.toFixed(2))
             $('input[name="paid_amount"]').val(total.toFixed(2))
            calculateMainTotals()
              }
    });


 $('#bill_discount').on('input', function () {
    // calculateItemWiseDiscount();
    
    var itemDis = parseFloat($("#item_discount").val()) || 0;
    var subTotal =parseFloat( $("#sub_total").val() ) || 0;
  
    var x = parseFloat($(this).val())
    if( x >= subTotal ) {
        Swal.fire({
                      icon: 'error',
                      title: 'Error',
                      text: 'Bill Discount cannot be greater than or equal to subtotal',
                    });
                    $(this).val('')
    }
    
    if(itemDis == 0 && x < subTotal) {
        calculateMainTotals();
    }
    
    
    if($(this).val()  ) {
         $('input[name="discount[]"]').each(function () {
           $(this).prop('readonly', true);
        });
    }
    else {
        $('input[name="discount[]"]').each(function () {
           $(this).prop('readonly', false);
           $(this).val('')
           
        });
         calculateMainTotals();
    }
      if(itemDis > 0) {
        Swal.fire({
                      icon: 'error',
                      title: 'Error',
                      text: 'Bill Discount cant be added while item discount is applied',
                    });
                    $(this).val('')

    }
});

// if ($('#bill_discount').val()) {
//     calculateMainTotals();
// }



// **************************************************************************
// ********************************************

// Main calculate functions

  function calculateRowAmounts(row) {
      var quantityValue = parseFloat(row.find('input[name="quantity[]"]').val())|| 0;
      var rateValue = parseFloat(row.find('[name="rate[]"]').val()) || 0;
      var discount =  parseFloat(row.find('[name="discount[]"]').val()) || 0;
      var taxpercen = parseFloat(row.find('input[name="tax[]"]').val()) || 0;


      var amountValue = quantityValue * rateValue;
      // amount without tax
      row.find('[name="amount1[]"]').val(amountValue);
      //console.log(amountValue);
      row.find('[name="amount[]"]').val(amountValue.toFixed(2));
      // minus discount
      amountValue = amountValue - discount;
      //console.log(amountValue)
      
      // calculate tax amount 
      var taxAmount = (amountValue / 100) * taxpercen;
      //console.log(taxAmount)
      row.find('input[name="tax_amount[]"]').val(taxAmount.toFixed(2));

      // row total with tax added
      var amount1 = amountValue + taxAmount;
    //   row.find('[name="amount[]"]').val(amount1.toFixed(2));
      
      
    //   single rate
      var taxwithoutDiscount = (rateValue / 100) * taxpercen;
      var mrp = rateValue + taxwithoutDiscount;
      row.find('[name="purchase_mrp[]"]').val(mrp.toFixed(2));

      setTimeout(function() {
        calculateMainTotals()
      }, 500);

  }

  function calculateMainTotals() {    
            var sub =0;
            var totalTax =0;
            var itemDiscountT =0;
            $("#productTable tbody tr").each(function() {
               // alert("t")
               var amount = parseFloat($(this).find('input[name="amount1[]"]').val()) || 0 ;
               sub += amount;
               
               var tax = parseFloat($(this).find('input[name="tax_amount[]"]').val()) || 0;
               totalTax += tax;

               var disc = parseFloat($(this).find('input[name="discount[]"]').val()) || 0;
               itemDiscountT += disc;


              
            });

            $('input[name="item_wise_discount"]').val(itemDiscountT.toFixed(2));
            
           // console.log("mainsub" + sub)
             $("#sub_total").val(sub.toFixed(2));
             $("#total_tax").val(totalTax.toFixed(2));
             
             var cgst = totalTax / 2;
            var sgst = totalTax / 2;
        
            $("#cgst").val(cgst.toFixed(2));
            $("#sgst").val(sgst.toFixed(2));
            //$("#igst").val(igst.toFixed(2));
             
             
              var totaldis= parseFloat($('input[name="item_wise_discount"]').val()) || 0;
              var totalBilldis= parseFloat($('input[name="bill_discount"]').val()) || 0;
              var itemDiscount= parseFloat($('input[name="item_wise_discount"]').val()) || 0;
              var totalTax= parseFloat($('input[name="total_tax"]').val()) || 0;
              var round = parseFloat($("#round_off").val()) || 0;
              
            //   var total = sub + totalBilldis + totalTax - totaldis - itemDiscount ;
            var total = sub + totalTax - itemDiscount - totalBilldis + round
            //console.log("maintotal"+total)
       
              $('input[name="total_amount"]').val(total.toFixed(2))
              $('input[name="paid_amount"]').attr('max', total.toFixed(2));
              $('input[name="paid_amount"]').val(total.toFixed(2));
               
        
  }




// invoice


    $('#supplier_id, #invoice_no').on('change', function() {
        checkInvoice();
    });

    function checkInvoice() {
        var supplierId = $('#supplier_id').val();
        var invoiceNo = $('#invoice_no').val();

        if (!supplierId || !invoiceNo) {
            return;
        }

        $.ajax({
            url: "{{ route('purchase.checkInvoice') }}",
            type: "GET",
            data: {
                supplier_id: supplierId,
                invoice_no: invoiceNo,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                if (response.exists) {
                   Swal.fire({
                      icon: 'error',
                      title: 'Error',
                      text: 'The invoice ID is already taken for the supplier.',
                      timer: 2000,
                      showConfirmButton: false
                    });

                    $('#invoice_no').val('');
                }
            }
        });
    }

  $(document).ready(function() {
    
     $('input[name="paid_amount"]').on('change', function() {
         //alert("test")
        var x = parseFloat($(this).val());
        var y = parseFloat($('input[name="total_amount"]').val());
      
      // alert("Total Amount: " + y);
      // alert("Paid Amount: " + x);
      
        if (x > y) {
            swal("Error", "Paid Amount cannot be greater than Total amount", "error");
            $(this).val('');
        }
    });

      
     $('input[name="isigst"]').change(function() {
         //alert("test")
      if ($(this).is(':checked')) {
        var totTax = parseFloat($("#total_tax").val());
        $("#cgst").val('');
        $("#sgst").val('');
        $("#igst").val(totTax);
      } else {
        var totTax = parseFloat($("#total_tax").val());
        var cgst = totTax / 2;
        var sgst = totTax / 2;
        
        $("#cgst").val(cgst.toFixed(2));
        $("#sgst").val(sgst.toFixed(2));
        $("#igst").val('');
      }
    });


     // ?

     function checkAndCalculateTotal() {
        var allValuesFilled = true;
        $('input[name="amount1[]"]').not(':first').each(function() { // Excluding the first input field
            if (!$(this).val()) {
                allValuesFilled = false;
                return false; // Exit the loop early if any input is empty
            }
        });
        if (allValuesFilled) {
            calculateMainTotals();
        } else {
            setTimeout(checkAndCalculateTotal, 300); // Check again after 1 second if not all values are filled
        }
    }
    
    checkAndCalculateTotal();
    //setTimeout(calculateMainTotals, 200000);


    

   });
   if(window.location.href.indexOf("/import/excel") > -1) {
       // alert("test")
         $('table#productTable tbody tr:first-child').css('display', 'none');
    }
</script>

@endsection