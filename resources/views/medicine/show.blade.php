@extends('layouts.app')
@section('content')
    
        <div class="row" style="min-height: 70vh;">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0 card-title">Show Medicine</h3>
                    </div>

                    <div class="col-lg-12" style="background-color: #fff;">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Medicine Name</label>
                                    <input type="text" class="form-control" readonly name="medicine_name"
                                        value="{{ $show->medicine_name }}" placeholder="Medicine Name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Medicine Code</label>
                                    <input type="text" class="form-control" readonly name="medicine_name"
                                        value="{{ $show->medicine_code }}" placeholder="Medicine Name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Generic Name</label>
                                    <input type="text" class="form-control" readonly name="generic_name"
                                        value="{{ $show->generic_name }}" placeholder="Generic Name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Item Type</label>
                                    <input type="text" class="form-control" readonly name="item_type"
                                        value="{{ $show->itemType->master_value }}" placeholder="Item Type">
                                </div>
                            </div>
          
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Hsn Code</label>
                                    <input type="text" class="form-control" readonly name="Hsn_code"
                                        value="{{ $show->Hsn_code }}" placeholder="Hsn Code">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="tax_id">Tax</label>
                                    <input type="text" class="form-control" readonly name="tax_id"
                                        value="{{ @$show->tax['tax_group_name'] }}" placeholder=" Tax">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Manufacturer</label>
                                    <input type="text" class="form-control" readonly name="Manufacturer"
                                        value="{{ $show->name }}" placeholder=" Manufacturer">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Unit Price (Including GST)</label>
                                    <input type="text" class="form-control" readonly name="unit_price"
                                        value="{{ $show->unit_price }}" placeholder="Unit Price">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control" readonly name="description" placeholder="Description">{{ $show->description }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="tax_id">Unit</label>
                                    <input type="text" class="form-control" readonly name="unit_id"
                                        value="{{ $show->unit->unit_name }}" placeholder=" Tax">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Reorder Limit</label>
                                    <input type="text" class="form-control" readonly name="reorder_limit"
                                        value="{{ $show->reorder_limit }}" placeholder="Reorder Limit">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Status: @if ($show->is_active)
                                            Active
                                        @else
                                            Inactive
                                        @endif
                                    </label>
                                </div>
                            </div>


                            <div class="col-md-12 col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Stock History</h3>
                                    </div>
                                    <div class="table-responsive">
                                        <table
                                            class="table card-table table-bordered table-vcenter text-nowrap table-gray-dark">
                                            <thead class="bg-gray-dark text-white">
                                                <tr>
                                                    <th class="text-white">ID</th>
                                                    <th class="text-white">Pharmacy</th>
                                                    <th class="text-white">Batch Number</th>
                                                     <th class="text-white">MFD/EXP</th>
                                                      <th class="text-white">Rate</th>
                                                    <th class="text-white">Current Stock</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($medicineStock as $key => $stock)
                                                    <tr>
                                                        <th scope="row">{{ $key + 1 }}</th>
                                                        <td>{{ $stock->pharmacy['pharmacy_name'] ?? '' }}</td>
                                                        <td>{{ $stock->batch_no ?? '' }}</td>
                                                        <td>MFD : {{ $stock->mfd ?? '' }} <br>
                                                            EXP: {{ $stock->expd ?? '' }}
                                                        </td>
                                                        <td>Purchase Rate : {{ $stock->purchase_rate ?? '' }} <br>
                                                            Sales Rate: {{ $stock->sale_rate ?? '' }}
                                                        </td>
                                                        <td>{{ $stock->current_stock ?? '' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- table-responsive -->
                                </div>
                            </div>

                            <!-- ... -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <center>

                                        <a class="btn btn-danger" href="{{ route('medicine.index') }}">Back</a>
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
