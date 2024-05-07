@extends('layouts.app')
@section('content')
@php
use App\Helpers\AdminHelper;
use App\Models\Mst_Staff;
// dd(AdminHelper::getProductId($value->medicine_code));
@endphp

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
               @if ($message = Session::get('error'))
               <div class="alert alert-danger">
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
               <form action="{{ route('medicine.sales.return.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                  @csrf
                  <input type="hidden" name="discount_percentage" value="3" id="discount_percentage">
                  <div class="row">
                     <div class="col-md-3">
                        <div class="form-group">
                           <label class="form-label">Select Invoice ID*</label>
                           <select class="form-control" name="patient_invoice_id" id="patient_invoice_id" required="">
                              <option value="">Choose Invoice ID</option>
                              @foreach ($invoices as $invoice)
                              <option value="{{ $invoice->sales_invoice_id }}">{{ $invoice->sales_invoice_number }}</option>
                              @endforeach
                           </select>

                        </div>
                     </div>

                     <div class="col-md-3">
                        <div class="form-group">

                           <label class="form-label">Patient*</label>
                           <input type="text" class="form-control" name="patient_id" id="patient_id" required readonly placeholder="Patient">
                           <input type="hidden" id="patient_id_hidden" name="patient_id_hidden" />
                        </div>
                     </div>


                     <div class="col-md-3">
                        <div class="form-group">
                           <label class="form-label">Return Date</label>
                           <input type="date" class="form-control" readonly name="due_date" id="date" placeholder="Date">
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
                               
                                   <select class="form-control" name="pharmacy_id" id="pharmacy_id" required>
                                       <option value="">Select Pharmacy</option>
                                       @foreach ($pharmacies as $pharmacy)
                                           @if(in_array($pharmacy->id, $mappedpharma))
                                               <option value="{{ $pharmacy->id }}">{{ $pharmacy->pharmacy_name }}</option>
                                           @endif
                                       @endforeach
                                </select>
                          
                       @else
                            @if(session()->has('pharmacy_id') && session()->has('pharmacy_name') && session('pharmacy_id') != "all")
                               <select class="form-control" name="pharmacy_id" id="pharmacy_id" required readonly>
                                   <option value="{{ session('pharmacy_id') }}">{{ session('pharmacy_name') }}</option>
                               </select>
                           @else
                               <select class="form-control" name="pharmacy_id" id="pharmacy_id" required>
                                   <option value="">Select Pharmacy</option>
                                   @foreach ($pharmacies as $pharmacy)
                                       <option value="{{ $pharmacy->id }}">{{ $pharmacy->pharmacy_name }}</option>
                                   @endforeach
                               </select>
                            @endif
                       @endif
                         
                        </div>
                     </div>

                     <!-- <div class="col-md-3">
                        <div class="form-group">
                           <div class="form-label">Print Invoice</div>
                           <label class="custom-switch">
                              <input type="hidden" name="is_print" value="0">
                              <input type="checkbox" id="is_print" name="is_print" value="1" checked="checked" onchange="toggleStatus(this)" class="custom-switch-input">
                              <span id="statusLabel" class="custom-switch-indicator"></span>
                              <span id="statusText" class="custom-switch-description">
                                 Print Invoice
                              </span>
                           </label>
                        </div>
                     </div> -->
                  </div>
                  <div class="row">
                     <div class="col-md-12 col-lg-12">
                        <div class="card">
                           <div class="table-responsive">
                              <table class="table card-table table-vcenter text-nowrap" id="productTable">
                                 <thead>
                                    <tr>
                                       <th>Medicine Name</th>
                                       <th>Batch No</th>
                                       <th>Quantity</th>
                                       <th>Return Quantity</th>
                                       <th>Unit</th>
                                       <th>Rate</th>
                                       <th>Amount</th>
                                       <th>Actions</th>
                                       <!-- <th>Stock ID</th>
                                       <th>Current stock</th>
                                       <th>Order limit</th>
                                       <th>Tax Rate</th>
                                       <th>Tax Amount</th>
                                       <th>Manufacture Date</th>
                                       <th>Expiry Date</th> -->
                                    </tr>
                                    <tr id="productRowTemplate" style="display: none">
                                       <td>
                                          <input type="text" class="form-control medicine-select" name="medicine_id[]" readonly>
                                          <input type="hidden" id="medicine_id_hidden" name="medicine_id_hidden[]" />
                                       </td>
                                       <td class="medicine-batch-no"><input type="text" class="form-control" name="batch_no[]" readonly></td>
                                       <td class="medicine-qty"><input type="text" class="form-control" name="qty[]" readonly></td>
                                       <td class="medicine-quantity"><input type="number" min="1" class="form-control" name="quantity[]" oninput="calculateAmount(this)"></td>
                                       <td style="width: 15%;" class="medicine-unit-id">
                                           <input type="text" class="form-control" name="unit_id[]" readonly>
                                           <input type="hidden" id="unit_id_hidden" name="unit_id_hidden[]" />
                                        </td>
                                       <td style="width: 15%;" class="medicine-rate"><input type="text" class="form-control" name="rate[]" readonly></td>
                                       <td style="width: 15%;" class="medicine-amount"><input type="text" class="form-control" name="amount[]" readonly></td>
                                       <td><button type="button" onclick="myClickFunction(this)" style="background-color: #007BFF; color: #FFF; padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer;">Remove</button></td>
                                       <td class="display-med-row medicine-stock-id"><input type="hidden" class="form-control" name="med_stock_id[]" readonly></td>

                                       <td class="display-med-row medicine-current-stock"><input type="hidden" class="form-control" name="current-stock[]" readonly></td>
                                       <td class="display-med-row medicine-reorder-limit"><input type="hidden" class="form-control" name="limit[]" readonly></td>

                                       <td class="display-med-row medicine-tax-rate"><input type="hidden" class="form-control" name="tax_rate[]"></td>
                                       <td class="display-med-row medicine-tax-amount"><input type="hidden" class="form-control" name="single_tax_amount[]" readonly></td>

                                       <td class="display-med-row medicine-mfd"><input type="hidden" class="form-control" name="mfd[]" readonly></td>
                                       <td class="display-med-row medicine-expd"><input type="hidden" class="form-control" name="expd[]" readonly></td>
                                    </tr>
                                 </thead>
                                 <tbody>

                                 </tbody>
                              </table>
                           </div>
                        </div>
                     </div>
                  </div>
                  <!-- popup starts  -->
                  <!-- Modal for displaying medicine batch details -->
                  <div class="modal" id="medicineBatchModal" tabindex="-1" role="dialog" aria-labelledby="medicineBatchModalLabel" aria-hidden="true" data-backdrop="static">
                     <div class="modal-dialog" role="document" style="max-width: 90%; ">
                        <div class="modal-content">
                           <div class="modal-header">
                              <h5 class="modal-title" id="medicineBatchModalLabel">Medicine Batch Details</h5>
                              <button type="button" class="close modal-close no-selected-item" data-dismiss="modal" aria-label="Close">
                                 <span aria-hidden="true">&times;</span>
                              </button>
                           </div>
                           <div class="modal-body">
                              <!-- Display medicine batch details here -->
                              <table class="table">
                                 <thead>
                                    <tr>
                                       <th>#</th>
                                       <th>Stock ID</th>
                                       <th>Batch Number</th>
                                       <th>Type</th>
                                       <th>MFD</th>
                                       <th>EXPD</th>
                                       <th>Current Stock</th>
                                       <th>Reorder Limit</th>
                                       <th>Unit</th>
                                       <th>Unit Price</th>
                                       <th>Tax Rate</th>
                                       <th>Select</th>
                                    </tr>
                                 </thead>
                                 <tbody id="medicineBatchDetails">
                                    <!-- Data will be displayed here -->
                                 </tbody>
                              </table>
                           </div>
                           <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" id="close-modal" data-dismiss="modal">Select</button>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-6">
                        <!-- Left Div - terms_condition -->
                        <div id="terms_condition" class="custom-margin">
                           <div class="form-group">
                              <label class="form-label">Notes:*</label>
                              <textarea class="form-control" name="notes" placeholder="Notes" required=""></textarea>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <!-- Right Div - discount_amount -->
                        <div id="discount_amount" class="custom-margin">
                           <div class="card card-ash-border">
                              <div class="card-body">
                                 <table style="width: 100%;">
                                    <tr>
                                       <td><strong>Sub Total</strong></td>
                                       <td style="text-align: right;"><strong class="tot">0</strong><input type="hidden" id="sub-total-input" name="sub_total_amount" value="0"></td>
                                    </tr>
                                    <tr>
                                       <td><strong>Tax Amount</strong></td>
                                       <td style="text-align: right;"><strong class="tax-amount">0</strong><input type="hidden" id="tax-amount-input-1" name="total_tax_amount" value="0"></td>
                                    </tr>
                                    <tr>
                                       <td><strong>Total Amount</strong></td>
                                       <td style="text-align: right;"><strong class="total-amount">0</strong><input type="hidden" id="total-amount-input" name="total_amount" value="0"></td>
                                    </tr>
                                    <!-- <tr>
                                       <td><strong>Discount Amount</strong></td>
                                       <td style="text-align: right;"><strong class="discount-amount">0</strong><input type="hidden" id="discount-amount-input" name="discount_amount" value="0"></td>
                                    </tr> -->
                                 </table>
                                 <hr>
                                 <div class="form-group mb-2"> <!-- Decreased margin height -->
                                    <label class="form-label payable-amount">Payable Amount : <b>0</b></label>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="form-group">
                     <center>
                        <button type="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Save</button>
                        <a class="btn btn-danger" href="{{ url('/medicine-sales-return') }}">Cancel</a>
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
<!-- <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script> -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>


<script>
   // total amount 
   // Get the current date
   var currentDate = new Date();
   // Format the date as "YYYY-MM-DD" (required by input type="date)
   var formattedDate = currentDate.toISOString().split('T')[0];
   // Set the value of the input field to today's date
   document.getElementById("date").value = formattedDate;

   $(document).ready(function() {
      function updateTotalAmount() {
         const subTotal = parseFloat($('.tot').val()) || 0;
         const taxAmount = parseFloat($('.tax-amount').val()) || 0;
         const totalAmount = subTotal + taxAmount;
         var x = totalAmount.toFixed(2);
          if (x - Math.floor(x) >= 0.5) {
            x = Math.ceil(x);
        } else {
            x= Math.floor(x);
        }
         
         $('.total-amount').text('' + x);
         $('#sub-total-input').val(subTotal);
         $('#tax-amount-input').val(taxAmount);
         $('#total-amount-input').val(x);
      }

      // Listen for changes in the Sub Total and Tax Amount input fields
      $('#sub-total, #tax-amount').on('input', updateTotalAmount);

      // Initial update of the Total Amount
      updateTotalAmount();


      // searchable dropdown
      $('#patient_id').select2(); // Initialize Select2 for the patient dropdown
      $('#patient_invoice_id').select2();
      // Handle change event on the payment mode dropdown
      $('#payment_mode').change(function() {
         // Get the selected value
         var selectedPaymentMode = $(this).val();
         // alert(selectedPaymentMode);
         // Make an AJAX request to fetch the ledger names based on the selected payment mode
         $.ajax({
            url: '{{ route("getLedgerNames") }}',
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

      $('select[name="product_id[]"]').each(function() {
         var select = $(this);

         // Fetch product IDs based on medicine code
         var medicineCode = select.closest('tr').find('input[name="medicine_code[]"]').val();
         var productUrl = '/get-product-id/' + encodeURIComponent(medicineCode);

         $.get(productUrl, function(productResponse) {
            // Populate the dropdown with product IDs
            productResponse.forEach(function(product) {
               var option = $('<option>', {
                  value: product.product_id,
                  text: product.product_id
               });

               select.append(option);
            });
         });
      });

      calculateTotals();

      $("#productTable tbody").on("input", 'input[name="amount[]"], input[name="discount[]"]', function() {
         calculateTotals();
      });

      $("#addProductBtn").click(function(event) {
         event.preventDefault();
         // Clone the product row template
         var newRow = $("#productRowTemplate").clone();
         // Remove the "style" attribute to make the row visible
         newRow.removeAttr("style");
         newRow.find('select').addClass('medicine-select');
         newRow.find('input[type="text"]').val('');
         newRow.find('input[type="number"]').val('');
         newRow.find('.medicine-quantity input').prop("readonly", true);
         newRow.find('.medicine-name').val('');
         newRow.find('input').removeAttr("disabled")
         // newRow.removeAttr('style')
         newRow.find('input span').remove()
         // Append the new row to the table
         $("#productTable tbody").append(newRow);
      });

   });

   function validateForm() {
      var isValid = true;

      // Loop through each row in the table
      $("#productTable tbody tr").each(function() {
         // Get the values from the input fields in the current row
         var calculatedAmount = parseFloat($(this).find('input[name="quantity[]"]').val() * $(this).find('input[name="rate[]"]').val()) - (parseFloat($(this).find('input[name="discount[]"]').val()) + parseFloat($(this).find('input[name="tax[]"]').val()));
         var amount = parseFloat($(this).find('input[name="amount[]"]').val()) || 0;
         // Compare the calculated amount with the entered amount
         var epsilon = 0.0001; // A small positive number to account for floating-point precision
         if (Math.abs(calculatedAmount - amount) > epsilon) {
            alert('Invalid Amount in row ' + ($(this).index() + 1));
            isValid = false;
            return false; // Exit the loop early if an invalid amount is found
         }
      });

      return isValid;
   }

   function calculateTotals() {
      var subTotal = 0;
      var itemDiscount = 0;
      var billDiscount = parseFloat($("#bill_discount").val()) || 0;
      var totalTax = 0;
      var roundOff = parseFloat($("#round_off").val()) || 0;

      // Loop through each row in the table
      $("#productTable tbody tr").each(function() {
         // Get the value from the "Amount" input field in the current row
         var amount = parseFloat($(this).find('input[name="amount[]"]').val()) || 0;


         subTotal += amount;

         // Get the value from the "Discount" input field in the current row
         var discount = parseFloat($(this).find('input[name="discount[]"]').val()) || 0;


         itemDiscount += discount;

         var tax = parseFloat($(this).find('input[name="tax[]"]').val()) || 0;

         totalTax += tax;
      });

      // Update the "Sub Total" input field with the calculated subtotal
      $("#sub_total").val(subTotal.toFixed(2));

      // Update the "Item-wise Discount" input field with the calculated item discount
      $("#item_discount").val(itemDiscount.toFixed(2));

      //tax field
      $("#total_tax").val(totalTax.toFixed(2));

      var total = subTotal - itemDiscount + billDiscount + totalTax + roundOff;

      // Update the "Total" input field with the calculated total
      $("#total").val(total.toFixed(2));
   }


   $('#patient_invoice_id').on('change', function() {
      var selected_patient_invoice_id = $(this).val();


      $.ajax({
         url: "{{ route('get.patient.invoice.ids', '') }}/" + selected_patient_invoice_id,
         method: "patch",
         data: {
            _token: "{{ csrf_token() }}",
            selected_patient_invoice_id: selected_patient_invoice_id // Include selected_patient_invoice_id in the data
         },
         success: function(data) {

            $('#patient_id').empty();
            $.each(data, function(key, value) {
               $('#patient_id').val(`${value.patient_name}`);
               $('#patient_id_hidden').val(value.patient_id);
            });
         },
         error: function() {
            console.log('Error fetching patient IDs.');
         }
      });

   });


   function toggleStatus(checkbox) {
      if (checkbox.checked) {
         $("#statusText").text('Print Invoice');
         $("input[name=is_print]").val(1); // Set the value to 1 when checked
      } else {
         $("#statusText").text('Do Not Print');
         $("input[name=is_print]").val(0); // Set the value to 0 when unchecked
      }
   }

   $(document).on('change', '.medicine-select', function() {


      // $("#medicineBatchModal").show()
      var selected_medicine_id = $(this).val();
      var selct = $(this);
      $(".selectedCls").removeClass("selectedCls")
      $(this).parents('tr').addClass("selectedCls");

      var field1 = $(this).parents('tr').find(".medicine-batch-no input")
      $.ajax({
         url: "{{ route('get.medicine.batches', '') }}/" + selected_medicine_id,
         method: "patch",
         data: {
            _token: "{{ csrf_token() }}",
         },
         success: function(response) {
            console.log(response.data);
            // Clear previous data in the modal
            $('#medicineBatchDetails').empty();

            // Populate the modal with the received data
            response.data.forEach(function(item, index) {
               //alert(item.id)
               var row = `<tr>
                    <td class="batch-index">${index+1}</td>
                    <td class="medicine-stock-id">${item.id}</td>
                    <td class="batch-medicine-batch-number">${item.medicine_batch_number}</td>
                    <td class="batch-medicine-type">${item.medicine_type}</td>
                    <td class="batch-medicine-mfd">${item.medicine_mfd}</td>
                    <td class="batch-medicine-expd">${item.medicine_expd}</td>
                    <td class="batch-current-stock">${item.medicine_current_stock}</td>
                    <td class="batch-medicine-reorder-limit">${item.medicine_reorder_limit}</td>
                    <td class="batch-medicine-unit">${item.medicine_unit}</td>
                    <td class="batch-medicine-unit-price">${item.medicine_unit_price}</td>
                    <td class="batch-medicine-tax-rate">${item.medicine_tax_rate}</td>
                    <td class="radio-batch-btn"><input type="radio" value="${item.id}" name="selected_batch"></td>
                </tr>`;
               $('#medicineBatchDetails').append(row);
            });
            // Show the modal
            $('#medicineBatchModal').modal('show');

         },
         error: function() {
            console.log(0);
            console.log('Error fetching medicine batches.');
         }
      });



      $(document).on('change', '.radio-batch-btn', function() {
         // $(document).on('click', '.modal-close', function() {

         //var selectedValue = $("input[name='selected_batch']:checked")
         //select.closest(".medicine-batch-no").find("input").val()
         var selectedValue = $("input[name='selected_batch']:checked")
         var id = selectedValue.closest('tr').find('.medicine-stock-id').text();
         var stock = 0
         // alert(id)


         $(".selectedCls").find(".medicine-stock-id input").val(id)

         var v1 = selectedValue.closest('tr').find('.batch-medicine-batch-number').text();
         $(".selectedCls").find(".medicine-batch-no input").val(v1)
         // field1.val(v1)

         var max = selectedValue.closest('tr').find('.batch-current-stock').text();
         $(".selectedCls").find(".medicine-quantity input").val(1)
         $(".selectedCls").find(".medicine-quantity input").attr("max", max);
         $(".selectedCls").find(".medicine-quantity input").attr("min", 0);



         var v1 = selectedValue.closest('tr').find('.batch-medicine-unit').text();
         $(".selectedCls").find(".medicine-unit-id input").val(v1)

         var v1 = selectedValue.closest('tr').find('.batch-medicine-unit-price').text();
         $(".selectedCls").find(".medicine-rate input").val(v1)

         var v2 = selectedValue.closest('tr').find('.batch-medicine-unit-price').text();
         $(".selectedCls").find(".medicine-amount input").val(v2)

         var v1 = selectedValue.closest('tr').find('.batch-medicine-mfd').text();
         $(".selectedCls").find(".medicine-mfd input").val(v1)

         var v1 = selectedValue.closest('tr').find('.batch-medicine-expd').text();
         $(".selectedCls").find(".medicine-expd input").val(v1)

         var v1 = selectedValue.closest('tr').find('.batch-medicine-tax-rate').text();
         $(".selectedCls").find(".medicine-tax-rate input").val(v1)

         var v1 = selectedValue.closest('tr').find('.medicine-stock-id').text();
         $(".selectedCls").find(".medicine-stock-id").val(v1)

         // msg 
         var v1 = selectedValue.closest('tr').find('.batch-current-stock').text();
         $(".selectedCls").find(".medicine-current-stock input").val(v1)
         var v1 = selectedValue.closest('tr').find('.batch-medicine-reorder-limit').text();
         $(".selectedCls").find(".medicine-reorder-limit input").val(v1)


         // med_stock_id 
         // taxCalculation() 


      });
   });

   // function taxCalculation() {
   //    var tax = $('input[name="tax_rate[]"]');
   //    var sum1 = 0;
   //    var totalTax = 0
   //    inputElements.each(function() {
   //       sum1 = parseFloat($(this).val()) || 0;
   //       var x = $(this).parent("td").siblings(".medicine-tax-rate").find('input').val();
   //       // alert(sum1);
   //       // alert(x)
   //       x = parseFloat(x) || 0;
   //       var tax = (sum1 * x) / 100;
   //       //alert(tax)
   //       totalTax += tax
   //    });

   // }

   function myClickFunction(bt) {
      var x = bt.parentNode.parentNode
      var subtotal = parseFloat($('.tot').text())
      var totaltax = parseFloat($('.tax-amount').text())

      var totalRemove = x.querySelector('input[name="amount[]"]').value;
      var taxRemove = x.querySelector('input[name="single_tax_amount[]"]').value;
      // alert(subtotal)
      // alert(totaltax)
      // alert(totalRemove)
      // alert(taxRemove)

      var subtotal = subtotal - totalRemove
      $('.tot').text(subtotal.toFixed(2))
      var tax = totaltax - taxRemove
      $('.tax-amount').text(tax)
      var total = subtotal + tax
      total = total.toFixed(2);
        if (total - Math.floor(total) >= 0.5) {
          total = Math.ceil(total);
        } else {
          total= Math.floor(total);
        }
      
      $('.total-amount').text(total)

      var discount = $("#discount_percentage").val()
      var discountT = (total * discount) / 100
      //alert(discountT)
      $("#discount-amount-input").val(discountT)
      $(".discount-amount").text('' + discountT)
      var payable = total - discountT

      $(".payable-amount b").text('' + payable)
      $(".paid-amount").val(payable)

      x.remove()
   }

   $(document).on('click', '#close-modal', function() {
      // ******************
      var selectedValue = $("input[name='selected_batch']:checked")
      // console.log("test"+ selectedValue)
      if (selectedValue.length != 0) {
         var id = selectedValue.closest('tr').find('.medicine-stock-id').text();
         var ids = $('input[name="med_stock_id[]"]');
         var j = 0
         var max = parseFloat(selectedValue.closest('tr').find('.batch-current-stock').text());
         // var v2 = selectedValue.closest('tr').find('.batch-medicine-unit-price').text();
         //    $(".selectedCls").find(".medicine-amount input").val(v2)
         var selected = null
         selected = ids.filter(function() {
            return $(this).val() === id;
         });
         j = selected.length
         var amt = 0
         if (j > 1) {
            selected.each(function(index) {
               if (index == j - 1) {
                  $(this).closest('tr').find(".medicine-quantity input").val(j)
                  amt = $(this).closest('tr').find(".medicine-amount input").val()
                  $(this).closest('tr').find(".medicine-amount input").val(j * amt)

               } else {
                  $(this).closest('tr').remove()
               }

            });

         }
         var lmt = parseFloat(selectedValue.closest('tr').find('.batch-medicine-reorder-limit').text());
         var stck = parseFloat(selectedValue.closest('tr').find('.batch-current-stock').text());
         var quantity = parseFloat($(".selectedCls").find(".medicine-quantity input").val());
         //$(".selectedCls").find(".medicine-quantity").append('<span>Limited Stock</span>')
         var checkVal = 0
         if (stck > lmt) {
            checkVal = stck - lmt
            $(".selectedCls").find(".medicine-quantity span").remove()
         } else {
            $(".selectedCls").find(".medicine-quantity").append('<span>Limited Stock</span>')
         }
         if (checkVal != 0 && checkVal <= quantity) {
            $(".selectedCls").find(".medicine-quantity").append('<span>Limited Stock</span>')
         }
         if (checkVal > quantity) {
            $(".selectedCls").find(".medicine-quantity span").remove()
         }
         // ****************
         var inputElements = $('input[name="amount[]"]');
         var sum = 0;
         inputElements.each(function() {
            sum += parseFloat($(this).val()) || 0;
         });

         $(".tot").text(sum.toFixed(2));
         $('#sub-total-input').val(sum);

         //   tax 
         // var inputElements = $('input[name="rate[]"]');
         var tax = $('input[name="tax_rate[]"]');
         var sum1 = 0;
         var totalTax = 0
         inputElements.each(function() {
            sum1 = parseFloat($(this).val()) || 0;
            var x = $(this).parent("td").siblings(".medicine-tax-rate").find('input').val();
            x = parseFloat(x) || 0;
            var tax = (sum1 * x) / 100;
            var y = $(this).parent("td").siblings(".medicine-tax-amount").find('input');
            y.val(tax)
            totalTax += tax
         });
         
         var x = sum + totalTax;
         if (x - Math.floor(x) >= 0.5) {
          x = Math.ceil(x);
        } else {
          x= Math.floor(x);
        }
         $(".tax-amount").text(totalTax);
         $('#tax-amount-input').val(totalTax);
         $(".total-amount").text(x);
         $('#total-amount-input').val(x);

         var totalA = parseFloat($(".total-amount").text())
         var discount = $("#discount_percentage").val()
         var discountT = (totalA * discount) / 100
         //alert(discountT)
         $("#discount-amount-input").val(discountT)
         $(".discount-amount").text('' + discountT)
         var payable = totalA - discountT

         $(".payable-amount b").text('' + payable)
         $(".paid-amount").val(payable)
      }
      var disable = $('input[name="batch_no[]"]');

      disable.each(function() {
         if ($(this).val() == '') {
            $(this).parent("td").siblings(".medicine-quantity").find('input').prop("readonly", true);
         } else {
            $(this).parent("td").siblings(".medicine-quantity").find('input').prop("readonly", false);
         }

      });

   });
   // calculate amount 
   function calculateAmount(input) {
       
        //      if (input.value < 1) {
        //     input.value = 1;
        // }
        input.value = input.value.replace(/^0+|[\-.]/g, '');

      var quantityInput = input.value;
      var qty = parseInt($(input).closest('tr').find('.medicine-qty input').val());
      if (quantityInput > qty) {
         swal("Error", "Quantity cannot be greater than available quantity", "error");
         input.value = qty;
      } else {
         // Your existing calculation logic here
         // For example, updating the total amount based on the quantity
      }
      // Get the parent row
      var row = input.closest('tr');
      var inputElements = $('input[name="amount[]"]');
      // Find the rate input field in the same row
      var rateInput = row.querySelector('.medicine-rate input');

      // Find the amount input field in the same row
      var amountInput = row.querySelector('.medicine-amount input');



      // Calculate the amount based on the quantity and rate
      var quantity = parseFloat(input.value);
      var rate = parseFloat(rateInput.value);

      if (!isNaN(quantity) && !isNaN(rate)) {
         var sum = 0;
         var amount = quantity * rate;
         amountInput.value = amount.toFixed(2); // Set the value with 2 decimal places

         inputElements.each(function() {
            sum += parseFloat($(this).val()) || 0;
         });
         // var current_sub_total = $(".tot").val();
         // var updated_sub_total = current_sub_total + amount;
         $(".tot").text(sum.toFixed(2));
         $('#sub-total-input').val(sum);
         var amount = $('input[name="amount[]"]');
         var sum1 = 0;
         var totalTax = 0
         amount.each(function() {
            sum1 = parseFloat($(this).val()) || 0;
            var x = $(this).closest("tr").find('.medicine-tax-rate input').val();
            x = parseFloat(x) || 0;
            var tax = (sum1 * x) / 100;
            var taxField = $(this).closest("tr").find('.medicine-tax-amount input');
            taxField.val(tax.toFixed(2)); // Update tax input field
            totalTax += tax;
         });
        var x = sum + totalTax;
         if (x - Math.floor(x) >= 0.5) {
          x = Math.ceil(x);
        } else {
          x= Math.floor(x);
        }

         $(".tax-amount").text(totalTax.toFixed(2));
         $('#tax-amount-input-1').val(totalTax.toFixed(2));
         $(".total-amount").text(x);
         $('#total-amount-input').val(x);

         // Discount
         var totalA = parseFloat($(".total-amount").text())
         var discount = $("#discount_percentage").val()
         var discountT = (totalA * discount) / 100
         //alert(discountT)
         //$("#discount-amount-input").val(discountT)
         $(".tax-amount").text('' + discountT)
         $("#tax-amount-input-1").val(discountT)
         var payable = totalA + discountT
            if (payable - Math.floor(payable) >= 0.5) {
          payable = Math.ceil(payable);
        } else {
          payable= Math.floor(payable);
        }
                    
         //alert(payable)
         $(".payable-amount b").text('' + payable)
         $(".paid-amount").val(payable)

        
         $(".total-amount").text(payable)
         $("#total-amount-input").val(payable)



      } else {
         amountInput.value = '';
      }

   }
   $(document).on('click', '.no-selected-item', function() {
      // var selectedValue = $("input[name='selected_batch']:checked")
      $("input[name='selected_batch']:checked").prop("checked", false);

      // Remove the "style" attribute to make the row visible
      var newRow = $('.selectedCls');
      newRow.removeAttr("style");
      // newRow.find('select').addClass('medicine-select');
      newRow.find('input[type="text"]').val('');
      newRow.find('input[type="number"]').val('');
      newRow.find('input').removeAttr("disabled")
      // newRow.removeAttr('style')
      newRow.find('input span').remove()

   });

   $(document).on('change', '.medicine-quantity input', function() {
      var stck = parseFloat($(this).closest('tr').find('.medicine-current-stock input').val());
      var lmt = parseFloat($(this).closest('tr').find('.medicine-reorder-limit input').val());
      var quantity = parseFloat($(this).val());

      var checkVal = 0
      if (stck > lmt) {
         checkVal = stck - lmt
         //alert(checkVal)
         $(this).closest('tr').find(".medicine-quantity span").remove()
      } else {
         $(this).closest('tr').find(".medicine-quantity");
      }
      if (checkVal != 0 && checkVal <= quantity) {
         $(this).closest('tr').find(".medicine-quantity");
      }
      if (checkVal > quantity) {
         $(this).closest('tr').find(".medicine-quantity span").remove()
      }
   })


   $(document).ready(function() {
      $('#patient_invoice_id').change(function() {
         var purchaseInvoiceId = $(this).val();
         $.ajax({
            url: '/get-sale-invoice-details',
            method: 'GET',
            data: {
               patient_invoice_id: purchaseInvoiceId
            },
            success: function(response) {
               console.log(response);
               if (response.length > 0) {
                  $('#productTable tbody').empty()
                  for (var i = 0; i < response.length; i++) {
                     var newRow = $("#productRowTemplate").clone();
                     newRow.removeAttr("style");
                     newRow.find('input[name="medicine_id[]"]').val(response[i].medicine_name);
                     newRow.find('input[name="medicine_id_hidden[]"]').val(response[i].medicine_id);
                     newRow.find('input[name="quantity[]"]').val('0');
                     newRow.find('input[name="amount[]"]').val(response[i].amount * response[i].quantity);
                     newRow.find('input[name="batch_no[]"]').val(response[i].batch_id);
                     var x = parseInt(response[i].quantity);
                     newRow.find('input[name="qty[]"]').val(x);
                     newRow.find('input[name="unit_id[]"]').val(response[i].unit_name);
                     newRow.find('input[name="unit_id_hidden[]"]').val(response[i].medicine_unit_id);
                     newRow.find('input[name="rate[]"]').val(response[i].rate);
                     newRow.find('input[name="free_quantity[]"]').val(response[i].free_quantity);
                     $('#productTable tbody').append(newRow);
                  }
               } else {}
            },
            error: function() {
               alert('Error fetching purchase invoice details.');
            }
         });
      });
   });
</script>
@endsection