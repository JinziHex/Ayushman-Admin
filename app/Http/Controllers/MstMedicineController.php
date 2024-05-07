<?php

namespace App\Http\Controllers;
use App\Models\Mst_Medicine;
use App\Models\Mst_Tax;
use Illuminate\Support\Facades\Auth;
use App\Models\Mst_Master_Value;
use App\Models\Mst_Branch;
use App\Models\Mst_Unit;
use App\Models\Trn_Medicine_Stock;
use App\Models\Mst_Manufacturer;
use App\Models\Mst_Pharmacy;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Trn_Ledger_Posting;
use App\Models\Mst_Tax_Group;
use Carbon\Carbon;
use App\Models\Mst_Tax_Group_Included_Taxes;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MstMedicineController extends Controller
{
    public function index(Request $request)
    {
        $pageTitle = "Medicines";
        $medicineType =  Mst_Master_Value::where('master_id',14)->pluck('master_value','id');
        $query = Mst_Medicine::query();

        if($request->has('medicine_name')){
            $query->where('medicine_name','LIKE',"%{$request->medicine_name}%");
        }
       
        if($request->has('generic_name')){
            $query->where('generic_name','LIKE',"%{$request->generic_name}%");
        }
        if($request->has('medicine_type')){
            $query->where('medicine_type','LIKE',"%{$request->medicine_type}%");
        }
        
        // if ($request->filled('branch')) {
        //     $query->whereHas('branch', function ($q) use ($request) {
        //         $q->where('branch_name', 'like', '%' . $request->input('branch') . '%');
        //     });
        // }
        if($request->has('contact_number')){
            $query->where('staff_contact_number','LIKE',"%{$request->contact_number}%");
        }
        // if ($request->filled('manufacturer')) {
        //     $query->whereHas('Manufacturer', function ($q) use ($request) {
        //         $q->where('master_value', 'like', '%' . $request->input('manufacturer') . '%');
        //     });
        // }
        $medicines = $query->orderBy('created_at', 'desc')->get();
        return view('medicine.index', compact('pageTitle', 'medicines','medicineType'));
    }

    public function create()
    {
        $pageTitle = "Create Medicine";
        $itemType = Mst_Master_Value::where('master_id',13)->pluck('master_value','id');
        $medicineType =  Mst_Master_Value::where('master_id',14)->pluck('master_value','id');
        // $dosageForm =  Mst_Master_Value::where('master_id',15)->pluck('master_value','id');
        $Manufacturer = Mst_Manufacturer::where('is_active',  1)
        ->whereNull('deleted_at') 
        ->get();
    // $branches = Mst_Branch::pluck('branch_name','branch_id'); 
    $taxes = Mst_Tax_Group::pluck('tax_group_name','id');

        $units = Mst_Unit::pluck('unit_name','id');
        $randomMedicineCode = 'MED_' . Str::random(8);
        return view('medicine.create', compact('pageTitle','taxes','itemType','medicineType','Manufacturer','units','randomMedicineCode'));
    }

    public function store(Request $request)
    {
  
        $request->validate([
            'medicine_name' => 'required',
            'generic_name' => 'required',
            'item_type' => 'required',
            'medicine_type' => 'required',
            'tax_id' => 'required|exists:mst__tax__groups,id',
            'unit_price' => 'required',
            'unit_id' => 'required|exists:mst_units,id',
            'is_active' => 'required',
            'medicine_code' => 'unique:mst_medicines,medicine_code|required',
        ]);
    
        $medicines = new Mst_Medicine();
        $is_active = $request->input('is_active') ? 1 : 0;
        $medicines->medicine_code = $request->input('medicine_code');
        $medicines->medicine_name = $request->input('medicine_name');
        $medicines->generic_name = $request->input('generic_name');
        $medicines->item_type = $request->input('item_type');
        $medicines->medicine_type = $request->input('medicine_type');
        $medicines->Hsn_code = $request->input('Hsn_code');
        $medicines->tax_id = $request->input('tax_id');
        $medicines->manufacturer = $request->input('manufacturer');
        $medicines->unit_price = $request->input('unit_price');
        $medicines->description = $request->input('description');
        $medicines->unit_id = $request->input('unit_id');
        $medicines->is_active =  $is_active ;
        $medicines->reorder_limit = $request->input('reorder_limit');
        $medicines->created_by = Auth::id();
        $medicines->save();


        return redirect()->route('medicine.index')->with('success','Medicine added successfully');
    }
    

    public function edit($id)
    {
        $pageTitle = "Edit Medicine";
        $medicine = Mst_Medicine::findOrFail($id);
        $itemType = Mst_Master_Value::where('master_id',13)->pluck('master_value','id');
        $medicineType =  Mst_Master_Value::where('master_id',14)->pluck('master_value','id');
        $Manufacturer = Mst_Manufacturer::where('is_active',  1)
                        ->whereNull('deleted_at') 
                        ->get();
        $taxes = Mst_Tax_Group::pluck('tax_group_name','id');
        $units = Mst_Unit::pluck('unit_name','id');

        return view('medicine.edit', compact('pageTitle','medicine','taxes','itemType','medicineType','Manufacturer','units'));
    }

    public function update(Request $request,$id)
    {
        $medicine = Mst_Medicine::findOrFail($id);
        $request->validate([
        
            'medicine_name' => 'required',
            'generic_name' => 'required',
            'item_type' => 'required',
            'medicine_type' => 'required',
            'tax_id' => 'required|exists:mst__tax__groups,id',      
            'unit_price' => 'required',
            'unit_id' => 'required|exists:mst_units,id',
            'medicine_code' => 'required|unique:mst_medicines,medicine_code,' . $medicine->id,
            //'Hsn_code' =>  'required|exists:mst_branches,branch_id',
           
           
            ]);
        $is_active = $request->input('is_active') ? 1 : 0;
    
       
        
        $medicine->medicine_name = $request->input('medicine_name');
        $medicine->generic_name = $request->input('generic_name');
        $medicine->item_type = $request->input('item_type');
        $medicine->medicine_type = $request->input('medicine_type');
        $medicine->Hsn_code = $request->input('Hsn_code');
        $medicine->tax_id = $request->input('tax_id');
        $medicine->manufacturer = $request->input('manufacturer');
        $medicine->unit_price = $request->input('unit_price');
        $medicine->description = $request->input('description');
        $medicine->unit_id = $request->input('unit_id');
        $medicine->is_active =  $is_active ;
        $medicine->reorder_limit = $request->input('reorder_limit');
        $medicine->save();
    
        return redirect()->route('medicine.index')->with('success','Medicine updated successfully'); 
    }

    public function show($id)
    {
        $pageTitle = "View medicine details";
        $show = Mst_Medicine::where('id', $id)
        ->join('mst__manufacturers', 'mst__manufacturers.manufacturer_id', '=', 'mst_medicines.manufacturer')
            ->select('mst_medicines.*', 'mst__manufacturers.name')
            ->first();
 
        $medicineStock = Trn_Medicine_Stock::where('medicine_id', $id)->get();
        return view('medicine.show',compact('pageTitle','show','medicineStock'));

    }
     
    public function destroy($id)
    {
        $medicine = Mst_Medicine::findOrFail($id);
        $medicine->delete();
        return 1;

        return redirect()->route('medicine.index')->with('success','Medicine deleted successfully');
    }

    public function updateStatus($medicineId)
    {
        $medicine = Mst_Medicine::find($medicineId);
        if (!$medicine) {
            return response()->json(['success' => false]);
        }
    
        // Toggle the is_active value
        $medicine->is_active = !$medicine->is_active;
        $medicine->save();
    
        return response()->json(['success' => true, 'status' => $medicine->is_active]);
    }

    public function viewStockUpdation()
    {
     
        $pageTitle = "Medicine Initial Stock Updation";
        $stock = Trn_Medicine_Stock::join('mst_medicines', 'trn_medicine_stocks.medicine_id', '=', 'mst_medicines.id')
                    ->select('trn_medicine_stocks.*')->first();
        $pharmacies = Mst_Pharmacy::get();
        $branchs = Mst_Branch::get();
        $meds = Mst_Medicine::get();
        return view('medicine.stockupdation',compact('pageTitle','branchs','meds','pharmacies','stock'));
    }
        public function getBatchNumbers(Request $request)
    {
        // Retrieve batch numbers based on the selected medicine ID
        $medicineId = $request->input('medicine_id');
        $batchNumbers = Trn_Medicine_Stock::where('medicine_id', $medicineId)->pluck('batch_no');
    
        return response()->json(['batchNumbers' => $batchNumbers]);
    }
    public function getCurrentStockOld($medicineId, $batchNo)
    {
    
        // Fetch current stock based on the provided parameters
        $currentStock = Trn_Medicine_Stock::where('medicine_id', $medicineId)
            ->where('batch_no', $batchNo)
            ->value('current_stock');

        // Return the current stock as JSON
        return response()->json(['current_stock' => $currentStock]);
    }
    public function getCurrentStock($medicineId, $batchNo)
    {
        // Fetch required data based on the provided parameters
        $data = Trn_Medicine_Stock::where('medicine_id', $medicineId)
            ->where('batch_no', $batchNo)
            ->select('current_stock', 'purchase_rate', 'sale_rate')
            ->first();
    
        // Return the data as JSON
        return response()->json([
            'current_stock' => $data->current_stock,
            'purchase_rate' => $data->purchase_rate,
            'sale_rate' => $data->sale_rate
        ]);
    }

//old initial stock update code - modified on 29/02/2024 due to empty row php error issue
// public function updateStockMedicine(Request $request)
//     {

//             $validatedData = $request->validate([
//                 'pharmacy_id' => 'required',
//                 'medicine_id' => 'required',
//                 'batch_no' => 'required',
//                 'mfd' => 'required',
//                 'expd' => 'required',
//                 'new_stock' => 'required',
//                 'purchase_rate' => 'required',
//                 'sale_rate' => 'required',
//             ]);
    
//             $pharmacyId = $request->input('pharmacy_id');
//             $medicineIds = $request->input('medicine_id');
//             $batchNos = $request->input('batch_no');
//             $mfdDates = $request->input('mfd');
//             $expdDates = $request->input('expd');
//             $newStocks = $request->input('new_stock');
//             $purchaseRates = $request->input('purchase_rate');
//             $saleRates = $request->input('sale_rate');

//             // Remove the first element from  array
//             array_shift($medicineIds);
//             array_shift($batchNos);
//             array_shift($mfdDates);
//             array_shift($expdDates);
//             array_shift($newStocks);
//             array_shift($purchaseRates);
//             array_shift($saleRates);
    
//             $existingRecordsMsg = [];

// foreach ($medicineIds as $key => $medicineId) {
//     $existingRecord = Trn_Medicine_Stock::leftjoin('mst_medicines','mst_medicines.id','=','trn_medicine_stocks.medicine_id')->where([
//         'pharmacy_id' => $pharmacyId,
//         'medicine_id' => $medicineId,
//         'batch_no' => $batchNos[$key],
//         'mfd' => $mfdDates[$key],
//         'expd' => $expdDates[$key],
//         'opening_stock' => 0,
//         'current_stock' => $newStocks[$key],
//         'purchase_rate' => $purchaseRates[$key],
//         'sale_rate' => $saleRates[$key],
//     ])->select('trn_medicine_stocks.*','mst_medicines.medicine_name')->first();

//     if (!$existingRecord) {
//         $newStockRecord = Trn_Medicine_Stock::create([
//             'pharmacy_id' => $pharmacyId,
//             'medicine_id' => $medicineId,
//             'batch_no' => $batchNos[$key],
//             'mfd' => $mfdDates[$key],
//             'expd' => $expdDates[$key],
//             'opening_stock' => 0,
//             'current_stock' => $newStocks[$key],
//             'purchase_rate' => $purchaseRates[$key],
//             'sale_rate' => $saleRates[$key],
//         ]);

//         // Update the stock_code
//         $stockCode = 'STK' . $newStockRecord->stock_id;
//         $newStockRecord->update(['stock_code' => $stockCode]);
//         $branchId = Mst_Pharmacy::where('id', $request->pharmacy_id)->value('branch');
        
//         //Accounts Receivable
//         Trn_Ledger_Posting::create([
//             'posting_date' => Carbon::now(),
//             'master_id' => 'ISU' . $newStockRecord->stock_id,
//             'account_ledger_id' => 3,
//             'entity_id' => 0,
//             'debit' => array_sum($purchaseRates),
//             'credit' => 0,
//             'branch_id' => $branchId,
//             'transaction_id' => $newStockRecord->stock_id,
//             'narration' => 'Initial Stock Updation Payment'
//         ]);

//         //Accounts Payable
//         Trn_Ledger_Posting::create([
//             'posting_date' => Carbon::now(),
//             'master_id' => 'ISU' . $newStockRecord->stock_id,
//             'account_ledger_id' => 4,
//             'entity_id' => 0,
//             'debit' => 0,
//             'credit' => array_sum($purchaseRates),
//             'branch_id' => $branchId,
//             'transaction_id' => $newStockRecord->stock_id,
//             'narration' => 'Initial Stock Updation Payment'
//         ]);
//     } else {
//         $existingRecordsMsg[] = $existingRecord->medicine_name;
//     }
// }

//     if (!empty($existingRecordsMsg)) {
//         $medicineNames=implode(",",$existingRecordsMsg);
//         $errorMessage = "Records for medicine $existingRecord->medicine_name already exists.";
//         return redirect()->back()->with('errors', $errorMessage);
//     } else {
//         return redirect()->back()->with('success', 'Stock updated/created successfully');
//     }
//     }  


    public function updateStockMedicine(Request $request)
    {
        $validatedData = $request->validate([
            'medicine_id' => 'required|array',
            'batch_no' => 'required|array',
            'mfd' => 'required|array',
            'expd' => 'required|array',
            'new_stock' => 'required|array',
            'purchase_rate' => 'required|array',
            'sale_rate' => 'required|array',
        ]);
    
        try {
            DB::beginTransaction();
    
            $pharmacyId = $request->input('pharmacy_id');
            $branchId = Mst_Pharmacy::where('id', $pharmacyId)->value('branch');
            
            $medicines = $request->medicine_id;
            $batches = $request->batch_no;
            $expds = $request->expd;
            $mfds = $request->mfd;
            $newStock = $request->new_stock;
            $tax = $request->tax;
            $purchaseRate = $request->purchase_rate;
            $saleRateOld = $request->sale_rate_Old;
            $saleRate = $request->sale_rate;
            
            array_shift($medicines);
            array_shift($batches);
            array_shift($expds);
            array_shift($newStock);
            array_shift($saleRateOld);
            array_shift($mfds);
            array_shift($purchaseRate);
            array_shift($tax);
            array_shift($saleRate);
    
            $errors = [];
            $insertedMedicines = [];
    
            foreach ($medicines as $key => $medicine) {
                // Check if a record already exists for the given pharmacy and medicine ID
                $existingRecord = Trn_Medicine_Stock::where('pharmacy_id', $pharmacyId)
                    ->where('medicine_id', $medicine)
                    ->where('batch_no',$batches[$key])
                    ->where('mfd', $mfds[$key])
                    ->where('expd', $expds[$key])
                    ->where('purchase_rate', $purchaseRate[$key])
                    ->where('sale_rate_with_tax', $saleRate[$key])
                    ->count();
               $med = Mst_Medicine::where('id', $medicine)->pluck('medicine_name'); 
                if ($existingRecord === 0) {
                    // Create a new record if it doesn't already exist
                    $newStockRecord = Trn_Medicine_Stock::create([
                        'pharmacy_id' => $pharmacyId,
                        'medicine_id' => $medicine,
                        'batch_no' => $batches[$key],
                        'mfd' => $mfds[$key],
                        'expd' => $expds[$key],
                        'opening_stock' => 0,
                        'current_stock' => $newStock[$key],
                        'purchase_rate' => $purchaseRate[$key],
                        'sale_rate' => $saleRateOld[$key],
                        'sale_rate_with_tax' => $saleRate[$key],
                        'branch_id' => $branchId,
                    ]);
    
                    $stockCode = 'STK' . $newStockRecord->stock_id;
                    $newStockRecord->update(['stock_code' => $stockCode]);
                    $subAmount = $newStock[$key] *  $purchaseRate[$key];
    
                    Trn_Ledger_Posting::create([
                        'posting_date' => Carbon::now(),
                        'master_id' => 'ISU' . $newStockRecord->stock_id,
                        'account_ledger_id' => 3,
                        'entity_id' => 0,
                        'debit' => $subAmount,
                        'credit' => 0,
                        'branch_id' => $branchId,
                        'transaction_id' => $newStockRecord->stock_id,
                        'narration' => 'Initial Stock Updation Payment'
                    ]);
    
                    Trn_Ledger_Posting::create([
                        'posting_date' => Carbon::now(),
                        'master_id' => 'ISU' . $newStockRecord->stock_id,
                        'account_ledger_id' => 4,
                        'entity_id' => 0,
                        'debit' => 0,
                        'credit' => $subAmount,
                        'branch_id' => $branchId,
                        'transaction_id' => $newStockRecord->stock_id,
                        'narration' => 'Initial Stock Updation Payment'
                    ]);
                   $insertedMedicines[] = $med;
     
                } else {
                    
                     $errors[] = $med;
                }
            }
    
            DB::commit();
    
            if (!empty($errors)) {
                $errorMedicines = implode(', ', $errors);
                return redirect()->back()->with('errors', 'Stock already exists for medicines: ' . $errorMedicines);
            } else {
                return redirect()->back()->with('success', 'Stock created successfully for all medicines except existing ones')->with('insertedMedicines', $insertedMedicines);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update stock: ' . $e->getMessage());
        }
    }


    
    public function getUnitPrice(Request $request, $medicineId)
    {
        $medicine = Mst_Medicine::join('mst__tax__groups', 'mst_medicines.tax_id', '=', 'mst__tax__groups.id')
                                  ->select('mst_medicines.*','mst__tax__groups.tax_group_name')
                                  ->where('mst_medicines.id', $medicineId)
                                  ->first();
    
        if (!$medicine) {
            return response()->json(['success' => false]);
        }
                              // Step 3: Get the tax_group_id based on the tax_group_name
                    $taxGroupId = Mst_Tax_Group::where('id', $medicine->tax_id)->value('id');
            
                    // Step 4: Get the included_tax ids based on the tax_group_id
                    $includedTaxIds = Mst_Tax_Group_Included_Taxes::where('tax_group_id', $taxGroupId)->pluck('included_tax')->toArray();
                    
                    // Step 5: Get the tax_rate values based on the included_tax ids
                     $taxRates = Mst_Tax::whereIn('id', $includedTaxIds)->pluck('tax_rate', 'tax_name')->toArray();
                    
                    // Step 6: Calculate the total_tax_rate
                    // $totalTaxRate = array_sum($taxRates);
                   $cgstAndSgstRates = array_filter($taxRates, function ($taxName) {
                        return strpos(strtoupper($taxName), 'CGST') !== false || strpos(strtoupper($taxName), 'SGST') !== false;
                    }, ARRAY_FILTER_USE_KEY);
                    $totalTaxRate = array_sum($cgstAndSgstRates);
                    
                            // // Step 3: Get the tax_group_id based on the tax_group_name
                            // $taxGroupId = Mst_Tax_Group::where('id', $medicine->tax_id)->value('id');
                            // $includedTaxIds = Mst_Tax_Group_Included_Taxes::where('tax_group_id', $taxGroupId)->pluck('included_tax')->toArray();
                            // $taxRates = Mst_Tax::whereIn('id', $includedTaxIds)->pluck('tax_rate')->toArray();
                            // $totalTaxRate = array_sum($taxRates);
        $unitPrice = $medicine->unit_price;
    
        return response()->json(['success' => true, 'unitPrice' => $unitPrice , 'medicine_tax_rate' =>isset($totalTaxRate) ? $totalTaxRate : 0,]);
    }

}