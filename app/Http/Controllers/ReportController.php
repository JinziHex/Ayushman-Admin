<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Trn_Medicine_Sales_Invoice;
use App\Models\Trn_Medicine_Sales_Invoice_Details;
use App\Models\Mst_Pharmacy;
use App\Models\Trn_Medicine_Purchase_Invoice;
use App\Models\Trn_Medicine_Purchase_Invoice_Detail;
use App\Models\Mst_Supplier;
use App\Models\Trn_Medicine_Purchase_Return;
use App\Models\Trn_Medicine_Purchase_Return_Detail;
use App\Models\Trn_Medicine_Sales_Return;
use App\Models\Trn_Medicine_Sales_Return_Details;
use App\Models\Trn_branch_stock_transfer;
use App\Models\Trn_branch_stock_transfer_detail;
use App\Models\Trn_Medicine_Stock;
use App\Models\Mst_Master_Value;
use App\Models\Mst_Staff;
use App\Models\Trn_Ledger_Posting;
use App\Models\Mst_Account_Ledger;
use App\Models\Mst_Branch;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    
    public function SalesReport(Request $request)
    {

        $pageTitle = "Sales Report";
        
        $saleQuery = Trn_Medicine_Sales_Invoice::select(
            'sales_invoice_id',
            'sales_invoice_number',
            'invoice_date',
            'pharmacy_id',
            'branch_id',
            'total_amount',
        )
        ->with('pharmacy')
        ->withCount([
            'salesInvoiceDetails as sales_invoice_details_count' => function ($query) {
                $query->select(DB::raw('count(*)'));
            }
        ]);
        
        if(Auth::check() && Auth::user()->user_type_id == 96) {
            $staff = Mst_Staff::findOrFail(Auth::user()->staff_id);
            $mappedPharmacies = $staff->pharmacies()->pluck('mst_pharmacies.id')->toArray();
            $saleQuery->whereIn('pharmacy_id', $mappedPharmacies);
        }
        
        if (Auth::user()->user_type_id == 18 || Auth::user()->user_type_id == 21 || Auth::user()->user_type_id == 20) {
                    $branch_id = Auth::user()->staff->branch_id;
                    if ($branch_id) {
                        $saleQuery->where('branch_id', $branch_id);
                    }
                } else {
                    if (session()->has('pharmacy_id') && session()->has('pharmacy_name') && session('pharmacy_id') != "all") {
                        $pharmacy_id = session('pharmacy_id');
                        $pharmacy = Mst_Pharmacy::with('branch')->find($pharmacy_id);
                        if ($pharmacy && $pharmacy->branch) {
                            $branch_id = $pharmacy->branch;
                            $saleQuery->where('branch_id', $branch_id);
                        }
                    }
                }
        

            if ($request->filled('pharmacy_id')) {
                $saleQuery->where('pharmacy_id', $request->input('pharmacy_id'));
            }
    
            if ($request->filled('sales_invoice_number')) {
                $saleQuery->where('sales_invoice_number', $request->input('sales_invoice_number'));
            }
            $saleQuery->where(function ($query) use ($request) {
                if ($request->filled('invoice_from_date') && $request->filled('invoice_to_date')) {
                    $query->whereBetween('invoice_date', [$request->input('invoice_from_date'), $request->input('invoice_to_date')]);
                } elseif ($request->filled('invoice_from_date')) {
                    $query->where('invoice_date', '>=', $request->input('invoice_from_date'));
                } elseif ($request->filled('invoice_to_date')) {
                    $query->where('invoice_date', '<=', $request->input('invoice_to_date'));
                } else {
                    $query->whereDate('invoice_date', Carbon::today());
                }
            });

        $sumTotalAmount = $saleQuery->sum('total_amount');
        $salesInvoices = $saleQuery->orderBy('invoice_date','DESC')->get();

        return view('reports.sales-report', [
            'pageTitle' => 'Sales Reports',
            'pharmacy' => Mst_Pharmacy::orderBy('created_at','DESC')->get(),
            'sales' => $salesInvoices,
            'sumTotalAmount' => $sumTotalAmount
        ]);
    }

    
    public function SalaryReportDetail(Request $request, $id)
    {
        $salesQuery = Trn_Medicine_Sales_Invoice_Details::join('trn__medicine__sales__invoices', 'trn__medicine__sales__invoice__details.sales_invoice_id', '=', 'trn__medicine__sales__invoices.sales_invoice_id')
            ->join('mst_medicines', 'trn__medicine__sales__invoice__details.medicine_id', '=', 'mst_medicines.id')
            ->where('trn__medicine__sales__invoice__details.sales_invoice_id', $request->id);
    
        if ($request->filled('medicine_name')) {
            $salesQuery->where('medicine_name', 'like', '%' . $request->input('medicine_name') . '%');
        }
    
        $SalesDetail = $salesQuery->get();
     
        return view('reports.sales-report-detail', [
            'pageTitle' => 'Sales Report Detail',
            'pharmacy' => Mst_Pharmacy::orderBy('created_at','DESC')->get(),
            'invoice_id' => $id,
            'sales_details' => $SalesDetail,
            'id' => $id
        ]);
    }
    
    public function PurchaseReport(Request $request)
    {
        $purchaseQuery = Trn_Medicine_Purchase_Invoice::select(
            'purchase_invoice_id',
            'purchase_invoice_no',
            'supplier_id',
            'invoice_date',
            'pharmacy_id',
            'branch_id',
            'total_amount',
            'payment_mode'
        )
        ->with(['Pharmacy', 'Supplier','paymentMode'])
        ->withCount([
            'purchaseInvoiceDetails as purchase_invoice_details_count' => function ($query) {
                $query->select(DB::raw('count(*)'));
            }
        ]);
        
        if(Auth::check() && Auth::user()->user_type_id == 96) {
            $staff = Mst_Staff::findOrFail(Auth::user()->staff_id);
            $mappedPharmacies = $staff->pharmacies()->pluck('mst_pharmacies.id')->toArray();
            $purchaseQuery->whereIn('pharmacy_id', $mappedPharmacies);
        }
        
        if (Auth::user()->user_type_id == 18 || Auth::user()->user_type_id == 21 || Auth::user()->user_type_id == 20) {
                    $branch_id = Auth::user()->staff->branch_id;
                    if ($branch_id) {
                        $purchaseQuery->where('branch_id', $branch_id);
                    }
                } else {
                    if (session()->has('pharmacy_id') && session()->has('pharmacy_name') && session('pharmacy_id') != "all") {
                        $pharmacy_id = session('pharmacy_id');
                        $pharmacy = Mst_Pharmacy::with('branch')->find($pharmacy_id);
                        if ($pharmacy && $pharmacy->branch) {
                            $branch_id = $pharmacy->branch;
                            $purchaseQuery->where('branch_id', $branch_id);
                        }
                    }
                }

        if ($request->filled('pharmacy_id')) {
            $purchaseQuery->where('pharmacy_id', $request->input('pharmacy_id'));
        }

        if ($request->filled('supplier_id')) {
            $purchaseQuery->where('supplier_id', $request->input('supplier_id'));
        }

        if ($request->filled('purchase_invoice_no')) {
            $purchaseQuery->where('purchase_invoice_no', $request->input('purchase_invoice_no'));
        }
        $purchaseQuery->where(function ($query) use ($request) {
            if ($request->filled('invoice_from_date') && $request->filled('invoice_to_date')) {
                $query->whereBetween('invoice_date', [$request->input('invoice_from_date'), $request->input('invoice_to_date')]);
            } elseif ($request->filled('invoice_from_date')) {
                $query->where('invoice_date', '>=', $request->input('invoice_from_date'));
            } elseif ($request->filled('invoice_to_date')) {
                $query->where('invoice_date', '<=', $request->input('invoice_to_date'));
            } else {
                    $query->whereDate('invoice_date', Carbon::today());
            }
        });

        $Invoices= $purchaseQuery->orderBy('invoice_date','DESC')->get();
        return view('reports.purchase-report', [
            'pageTitle' => 'Purchase Report',
            'pharmacy' => Mst_Pharmacy::orderBy('created_at','DESC')->get(),
            'suppliers' => Mst_Supplier::orderBy('created_at','DESC')->get(),
            'purchase' => $Invoices,
        ]);
    }

    public function PurchaseReportDetail(Request $request, $id)
    {

        $purchaseQuery = Trn_Medicine_Purchase_Invoice_Detail::join('trn_medicine_purchase_invoices', 'trn_medicine_purchase_invoices.purchase_invoice_id', '=', 'trn_medicine_purchase_invoice_details.invoice_id')
            ->join('mst_medicines', 'trn_medicine_purchase_invoice_details.product_id', '=', 'mst_medicines.id')
            ->where('trn_medicine_purchase_invoice_details.invoice_id', $request->id);
    
        if ($request->filled('medicine_name')) {
            $purchaseQuery->where('medicine_name', 'like', '%' . $request->input('medicine_name') . '%');
        }
    
        $PurchaseDetail = $purchaseQuery->get();

        return view('reports.purchase-report-detail', [
            'pageTitle' => 'Purchase Report Detail',
            'invoice_id' => $id,
            'purchase_details' => $PurchaseDetail,
        ]);
    }
    
    public function PurchaseReturnReport(Request $request)
    {
        $purchaseReturnQuery = Trn_Medicine_Purchase_Return::select(
            'purchase_return_id',
            'purchase_return_no',
            'purchase_invoice_id',
            'supplier_id',
            'return_date',
            'pharmacy_id',
            'branch_id',
            'sub_total'
        )
        ->with(['pharmacy', 'supplier','PurchaseInvoice'])
        ->withCount([
            'PurchaseReturnDetails as purchase_invoice_return_details_count' => function ($query) {
                $query->select(DB::raw('count(*)'));
            }
        ]);
        
        if(Auth::check() && Auth::user()->user_type_id == 96) {
            $staff = Mst_Staff::findOrFail(Auth::user()->staff_id);
            $mappedPharmacies = $staff->pharmacies()->pluck('mst_pharmacies.id')->toArray();
            $purchaseReturnQuery->whereIn('pharmacy_id', $mappedPharmacies);
        }
        
         if (Auth::user()->user_type_id == 18 || Auth::user()->user_type_id == 21 || Auth::user()->user_type_id == 20) {
                    $branch_id = Auth::user()->staff->branch_id;
                    $pharmaId = Mst_Pharmacy::find($branch_id);
                    $pharma = $pharmaId->id;
                    if ($branch_id) {
                        $purchaseReturnQuery->where('pharmacy_id', $pharma);
                    }
                } else {
                    if (session()->has('pharmacy_id') && session()->has('pharmacy_name') && session('pharmacy_id') != "all") {
                        $pharmacy_id = session('pharmacy_id');
                            $purchaseReturnQuery->where('pharmacy_id', $pharmacy_id);
                    }
                }

        if ($request->filled('pharmacy_id')) {
            $purchaseReturnQuery->where('pharmacy_id', $request->input('pharmacy_id'));
        }

        if ($request->filled('supplier_id')) {
            $purchaseReturnQuery->where('supplier_id', $request->input('supplier_id'));
        }

        if ($request->filled('purchase_return_no')) {
            $purchaseReturnQuery->where('purchase_return_no', $request->input('purchase_return_no'));
        }
        $purchaseReturnQuery->where(function ($query) use ($request) {
            if ($request->filled('return_from_date') && $request->filled('return_to_date')) {
                $query->whereBetween('return_date', [$request->input('return_from_date'), $request->input('return_to_date')]);
            } elseif ($request->filled('return_from_date')) {
                $query->where('return_date', '>=', $request->input('return_from_date'));
            } elseif ($request->filled('return_to_date')) {
                $query->where('return_date', '<=', $request->input('return_to_date'));
            }
        });

        $Invoices= $purchaseReturnQuery->paginate(10);
        return view('reports.return.purchase-return-report', [
            'pageTitle' => 'Purchase Return Report',
            'pharmacy' => Mst_Pharmacy::orderBy('created_at','DESC')->get(),
            'suppliers' => Mst_Supplier::orderBy('created_at','DESC')->get(),
            'purchase_returns' => $Invoices,
        ]);
    }

    
    public function PurchaseReturnReportDetail(Request $request, $id)
    {

        $purchaseReturnQuery = Trn_Medicine_Purchase_Return_Detail::join('trn_medicine_purchase_return', 'trn_medicine_purchase_return.purchase_return_id', '=', 'trn_medicine_purchase_return_details.purchase_return_id')
            ->join('mst_medicines', 'trn_medicine_purchase_return_details.product_id', '=', 'mst_medicines.id')
            ->where('trn_medicine_purchase_return_details.purchase_return_id', $request->id);
    
        if ($request->filled('medicine_name')) {
            $purchaseReturnQuery->where('medicine_name', 'like', '%' . $request->input('medicine_name') . '%');
        }
    
        $PurchaseReturnDetail = $purchaseReturnQuery->get();

        return view('reports.return.purchase-return-detail', [
            'pageTitle' => 'Purchase Return Report Detail',
            'return_id' => $id,
            'purchase_return_details' => $PurchaseReturnDetail,
        ]);
    }

    
    public function SalesReturnReport(Request $request)
    {
        $saleReturnQuery = Trn_Medicine_Sales_Return::select(
            'sales_return_id',
            'sales_return_no',
            'sales_invoice_id',
            'patient_id',
            'pharmacy_id',
            'return_date',
            'total_amount'
        )
        ->with(['Pharmacy', 'Patient','Invoice'])
        ->withCount([
            'salesReturnDetails as sales_return_detail_count' => function ($query) {
                $query->select(DB::raw('count(*)'));
            }
        ]);
        
        if(Auth::check() && Auth::user()->user_type_id == 96) {
            $staff = Mst_Staff::findOrFail(Auth::user()->staff_id);
            $mappedPharmacies = $staff->pharmacies()->pluck('mst_pharmacies.id')->toArray();
            $saleReturnQuery->whereIn('pharmacy_id', $mappedPharmacies);
        }
        
        if (Auth::user()->user_type_id == 18 || Auth::user()->user_type_id == 21 || Auth::user()->user_type_id == 20) {
                    $branch_id = Auth::user()->staff->branch_id;
                    $pharmaId = Mst_Pharmacy::find($branch_id);
                    $pharma = $pharmaId->id;
                    if ($branch_id) {
                        $saleReturnQuery->where('pharmacy_id', $pharma);
                    }
                } else {
                    if (session()->has('pharmacy_id') && session()->has('pharmacy_name') && session('pharmacy_id') != "all") {
                        $pharmacy_id = session('pharmacy_id');
                            $saleReturnQuery->where('pharmacy_id', $pharmacy_id);
                    }
                }

        if ($request->filled('pharmacy_id')) {
            $saleReturnQuery->where('pharmacy_id', $request->input('pharmacy_id'));
        }

        if ($request->filled('sales_return_no')) {
            $saleReturnQuery->where('sales_return_no', $request->input('sales_return_no'));
        }
        $saleReturnQuery->where(function ($query) use ($request) {
            if ($request->filled('return_from_date') && $request->filled('return_to_date')) {
                $query->whereBetween('return_date', [$request->input('return_from_date'), $request->input('return_to_date')]);
            } elseif ($request->filled('return_from_date')) {
                $query->where('return_date', '>=', $request->input('return_from_date'));
            } elseif ($request->filled('return_to_date')) {
                $query->where('return_date', '<=', $request->input('return_to_date'));
            }
        });

        $Invoices= $saleReturnQuery->paginate(10);
        
        return view('reports.return.sale-return-report', [
            'pageTitle' => 'Sales Return Report',
            'pharmacy' => Mst_Pharmacy::orderBy('created_at','DESC')->get(),
            'sales_returns' => $Invoices,
        ]);
    }


    public function SalesReturnReportDetail(Request $request, $id)
    {

        $salesReturnQuery = Trn_Medicine_Sales_Return_Details::join('trn__medicine__sales__returns', 'trn__medicine__sales__returns.sales_return_id', '=', 'trn__medicine__sales__return__details.sales_return_id')
            ->join('mst_medicines', 'trn__medicine__sales__return__details.medicine_id', '=', 'mst_medicines.id')
            ->where('trn__medicine__sales__return__details.sales_return_id', $request->id);
    
        if ($request->filled('medicine_name')) {
            $salesReturnQuery->where('medicine_name', 'like', '%' . $request->input('medicine_name') . '%');
        }
    
        $saleReturnDetail = $salesReturnQuery->get();

        return view('reports.return.sale-return-detail', [
            'pageTitle' => 'Sale Return Report Detail',
            'return_id' => $id,
            'sale_return_detail' => $saleReturnDetail,
        ]);
    }
    
    //stock transfer report
    
    public function StockTransferReport(Request $request)
    {
        $transferQuery = Trn_branch_stock_transfer::select(
            'id',
            'transfer_code',
            'transfer_date',
            'from_pharmacy_id',
            'to_pharmacy_id',
        )
        ->with(['pharmacy','pharmacys'])
        ->withCount([
            'stockTransferDetails as transfer_item_count' => function ($query) {
                $query->select(DB::raw('count(*)'));
            }
        ]);
        
        if(Auth::check() && Auth::user()->user_type_id == 96) {
            $staff = Mst_Staff::findOrFail(Auth::user()->staff_id);
            $mappedPharmacies = $staff->pharmacies()->pluck('mst_pharmacies.id')->toArray();
            $transferQuery->whereIn('from_pharmacy_id', $mappedPharmacies);
        }

        if ($request->filled('from_pharmacy_id')) {
            $transferQuery->where('from_pharmacy_id', $request->input('from_pharmacy_id'));
        }

        if ($request->filled('to_pharmacy_id')) {
            $transferQuery->where('to_pharmacy_id', $request->input('to_pharmacy_id'));
        }

        if ($request->filled('transfer_code')) {
            $transferQuery->where('transfer_code', $request->input('transfer_code'));
        }
        $transferQuery->where(function ($query) use ($request) {
            if ($request->filled('transfer_from_date') && $request->filled('transfer_to_date')) {
                $query->whereBetween('transfer_date', [$request->input('transfer_from_date'), $request->input('transfer_to_date')]);
            } elseif ($request->filled('transfer_from_date')) {
                $query->where('transfer_date', '>=', $request->input('transfer_from_date'));
            } elseif ($request->filled('transfer_to_date')) {
                $query->where('transfer_date', '<=', $request->input('transfer_to_date'));
            }
        });

        $queryData = $transferQuery->orderBy('created_at','DESC')->get();
        return view('reports.stock-transfer-report', [
            'pageTitle' => 'Stock Transfer Report',
            'pharmacy' => Mst_Pharmacy::orderBy('created_at','DESC')->get(),
            'stock_transfers' => $queryData,
        ]);
    }

    public function StockTransferReportDetail(Request $request, $id)
    {

        $stocktransferQuery = Trn_branch_stock_transfer_detail::join('trn_branch_stock_transfers', 'trn_branch_stock_transfers.id', '=', 'trn_branch_stock_transfer_details.stock_transfer_id')
            ->join('mst_medicines', 'trn_branch_stock_transfer_details.medicine_id', '=', 'mst_medicines.id')
            ->where('trn_branch_stock_transfer_details.stock_transfer_id', $request->id);
    
        if ($request->filled('medicine_name')) {
            $stocktransferQuery->where('medicine_name', 'like', '%' . $request->input('medicine_name') . '%');
        }
    
        $stocktransferDetail = $stocktransferQuery->get();

        return view('reports.stock-transfer-report-detail', [
            'pageTitle' => 'Stock Transfer Report Detail',
            'transfer_id' => $id,
            'stock_transfer_detail' => $stocktransferDetail,
        ]);
    }
    
    //current stock report

    
    public function CurrentStockReport(Request $request)
    {
        $currentStock = Trn_Medicine_Stock::select(
            'stock_id',
            'stock_code',
            'medicine_id',
            'pharmacy_id',
            'batch_no',
            'mfd',
            'expd',
            'purchase_rate',
            'sale_rate',
            'sale_rate_with_tax',
            'current_stock',
        )
        ->with(['medicines','pharmacy']);
        
         if (Auth::user()->user_type_id == 18 || Auth::user()->user_type_id == 21 || Auth::user()->user_type_id == 20) {
                    $branch_id = Auth::user()->staff->branch_id;
                    if ($branch_id) {
                        $currentStock->where('branch_id', $branch_id);
                    }
                } else {
                    if (session()->has('pharmacy_id') && session()->has('pharmacy_name') && session('pharmacy_id') != "all") {
                        $pharmacy_id = session('pharmacy_id');
                        $pharmacy = Mst_Pharmacy::with('branch')->find($pharmacy_id);
                        if ($pharmacy && $pharmacy->branch) {
                            $branch_id = $pharmacy->branch;
                            $currentStock->where('branch_id', $branch_id);
                        }
                    }
                }
        
        if(Auth::check() && Auth::user()->user_type_id == 96) {
            $staff = Mst_Staff::findOrFail(Auth::user()->staff_id);
            $mappedPharmacies = $staff->pharmacies()->pluck('mst_pharmacies.id')->toArray();
            $currentStock->whereIn('pharmacy_id', $mappedPharmacies);
        }

        if ($request->filled('pharmacy_id')) {
            $currentStock->where('pharmacy_id', $request->input('pharmacy_id'));
        }

        if ($request->filled('medicine_name')) {
            $currentStock->whereHas('medicines', function ($query) use ($request) {
                $query->where('medicine_name', 'like', '%' . $request->input('medicine_name') . '%');
            });
        }

        if ($request->filled('medicine_code')) {
            $currentStock->whereHas('medicines', function ($query) use ($request) {
                $query->where('medicine_code', 'like', '%' . $request->input('medicine_code') . '%');
            });
        }

        $queryData = $currentStock->orderBy('created_at','DESC')->get();
        return view('reports.current-stock-report', [
            'pageTitle' => 'Current Stock Report',
            'pharmacy' => Mst_Pharmacy::orderBy('created_at','DESC')->get(),
            'current_stocks' => $queryData,
        ]);
    }
    
    public function PaymentMadeReport(Request $request)
    {
            $purchaseQuery = Trn_Medicine_Purchase_Invoice::select(
            'purchase_invoice_id',
            'purchase_invoice_no',
            'supplier_id',
            'invoice_date',
            'pharmacy_id',
            'total_amount',
            'paid_amount',
            'payment_mode',
            'is_paid',
        )
        ->with(['Supplier'])
        ->withCount([
            'purchaseInvoiceDetails as purchase_invoice_details_count' => function ($query) {
                $query->select(DB::raw('count(*)'));
            }
        ]);
        
        if(Auth::check() && Auth::user()->user_type_id == 96) {
            $staff = Mst_Staff::findOrFail(Auth::user()->staff_id);
            $mappedPharmacies = $staff->pharmacies()->pluck('mst_pharmacies.id')->toArray();
            $purchaseQuery->whereIn('pharmacy_id', $mappedPharmacies);
        }

        if ($request->filled('pharmacy_id')) {
            $purchaseQuery->where('pharmacy_id', $request->input('pharmacy_id'));
        }

        if ($request->filled('supplier_id')) {
            $purchaseQuery->where('supplier_id', $request->input('supplier_id'));
        }

        if ($request->filled('purchase_invoice_no')) {
            $purchaseQuery->where('purchase_invoice_no', $request->input('purchase_invoice_no'));
        }
        $purchaseQuery->where(function ($query) use ($request) {
            if ($request->filled('invoice_from_date') && $request->filled('invoice_to_date')) {
                $query->whereBetween('invoice_date', [$request->input('invoice_from_date'), $request->input('invoice_to_date')]);
            } elseif ($request->filled('invoice_from_date')) {
                $query->where('invoice_date', '>=', $request->input('invoice_from_date'));
            } elseif ($request->filled('invoice_to_date')) {
                $query->where('invoice_date', '<=', $request->input('invoice_to_date'));
            } else {
                    $query->whereDate('invoice_date', Carbon::today());
            }
        });

        $Invoices= $purchaseQuery->orderBy('invoice_date','DESC')->get();
     
        return view('reports.payment-made-report', [
            'pageTitle' => 'Payment Made Report',
            'pharmacy' => Mst_Pharmacy::orderBy('created_at','DESC')->get(),
            'suppliers' => Mst_Supplier::orderBy('created_at','DESC')->get(),
            'purchase' => $Invoices,
        ]);
    }
    
        public function PaymentMadeReportDetail(Request $request, $id)
    {

        $purchaseQuery = Trn_Medicine_Purchase_Invoice_Detail::join('trn_medicine_purchase_invoices', 'trn_medicine_purchase_invoices.purchase_invoice_id', '=', 'trn_medicine_purchase_invoice_details.invoice_id')
            ->join('mst_medicines', 'trn_medicine_purchase_invoice_details.product_id', '=', 'mst_medicines.id')
            ->where('trn_medicine_purchase_invoice_details.invoice_id', $request->id);
    
        if ($request->filled('medicine_name')) {
            $purchaseQuery->where('medicine_name', 'like', '%' . $request->input('medicine_name') . '%');
        }
    
        $PurchaseDetail = $purchaseQuery->get();

        return view('reports.patment-made-report-detail', [
            'pageTitle' => 'Payment Made Report Detail',
            'invoice_id' => $id,
            'purchase_details' => $PurchaseDetail,
        ]);
    }
    
    public function PayableReport(Request $request)
    {
            $purchaseQuery = Trn_Medicine_Purchase_Invoice::with('Supplier')
        ->select(
            'trn_medicine_purchase_invoices.supplier_id',
            DB::raw('COALESCE(SUM(trn_medicine_purchase_invoices.total_amount), 0) as total_payable_amount'),
            DB::raw('COALESCE(SUM(trn_medicine_purchase_invoices.paid_amount), 0) as total_paid_amount'),
            DB::raw('(COALESCE(SUM(trn_medicine_purchase_invoices.total_amount), 0) - COALESCE(SUM(trn_medicine_purchase_invoices.paid_amount), 0)) as balance_due')
        );
    
        if ($request->filled('supplier_id')) {
            $purchaseQuery->where('supplier_id', $request->input('supplier_id'));
        }
        
        if ($request->filled('invoice_from_date') && $request->filled('invoice_to_date')) {
            $purchaseQuery->whereBetween('invoice_date', [$request->input('invoice_from_date'), $request->input('invoice_to_date')]);
        } elseif ($request->filled('invoice_from_date')) {
            $purchaseQuery->where('invoice_date', '>=', $request->input('invoice_from_date'));
        } elseif ($request->filled('invoice_to_date')) {
            $purchaseQuery->where('invoice_date', '<=', $request->input('invoice_to_date'));
        } else {
            // No date range provided, default to today
            $purchaseQuery->whereDate('invoice_date', Carbon::today());
        }
        $purchaseQuery->where('is_paid', 0);
    
    $Invoices = $purchaseQuery->groupBy('trn_medicine_purchase_invoices.supplier_id')
        ->havingRaw('balance_due > 0')
        ->get();
    
    //dd($Invoices);
    
        return view('reports.payable-report', [
            'pageTitle' => 'Payable Report',
            'suppliers' => Mst_Supplier::orderBy('created_at','DESC')->get(),
            'purchase' => $Invoices,
        ]);
    }

    public function PayableReportDetail(Request $request, $id)
    {

        $purchaseQuery = Trn_Medicine_Purchase_Invoice::query()->with('Supplier','Pharmacy');
         if ($request->filled('pharmacy_id')) {
            $purchaseQuery->where('pharmacy_id', $request->input('pharmacy_id'));
        }


        if ($request->filled('purchase_invoice_no')) {
            $purchaseQuery->where('purchase_invoice_no','like', '%' . $request->input('purchase_invoice_no') . '%');
        }
        

    
      
        if ($request->filled('invoice_detail_from_date') && $request->filled('invoice_detail_to_date')) {
            $purchaseQuery->whereBetween('invoice_date', [$request->input('invoice_detail_from_date'), $request->input('invoice_detail_to_date')]);
        } elseif ($request->filled('invoice_detail_from_date')) {
            $purchaseQuery->where('invoice_date', '>=', $request->input('invoice_detail_from_date'));
        } elseif ($request->filled('invoice_detail_to_date')) {
            $purchaseQuery->where('invoice_date', '<=', $request->input('invoice_detail_to_date'));
        } 
        
        $purchaseQuery->where('is_paid', 0)->whereRaw('total_amount - paid_amount > 0');
    
    $Invoices = $purchaseQuery->where('is_paid', 0)->where('supplier_id',$id)->get();
  //dd($Invoices);
        return view('reports.payable-report-detail', [
            'pageTitle' => 'Payable Report Detail',
            'supplier_id' => $id,
            'purchase_details' => $Invoices ,
             'pharmacy' => Mst_Pharmacy::orderBy('created_at','DESC')->get(),
        ]);
    }
    public function ledgerReport(Request $request)
    {
        $ledgerQuery = Trn_Ledger_Posting::query()->with('ledger')
                    ->select(
                        'account_ledger_id',
                        DB::raw('SUM(debit) as debit'),
                        DB::raw('SUM(credit) as credit'),
                        DB::raw('SUM(debit) - SUM(credit) as balance')
                    );
         if ($request->filled('account_ledger_id')) {
            $ledgerQuery->where('account_ledger_id', $request->input('account_ledger_id'));
        }
        
        if ($request->filled('posting_from_date') && $request->filled('posting_to_date')) {
            $ledgerQuery->whereBetween('posting_date', [$request->input('posting_from_date'), $request->input('posting_to_date')]);
        } elseif ($request->filled('posting_from_date')) {
            $purchaseQuery->where('posting_date', '>=', $request->input('posting_from_date'));
        } elseif ($request->filled('posting_to_date')) {
            $ledgerQuery->where('posting_date', '<=', $request->input('posting_to_date'));
        } else {
            // No date range provided, default to today
            $ledgerQuery->whereDate('posting_date', Carbon::today());
        }
                    //->where('account_ledger_id', $accountLedgerId)
                    //->whereBetween('posting_date', [$fromDate, $toDate])
        $ledgerQuery =$ledgerQuery->groupBy('account_ledger_id')
                    ->get();
          return view('reports.ledger-report', [
            'pageTitle' => 'Ledger Report',
            'ledgers' => Mst_Account_Ledger::orderBy('ledger_name','ASC')->get(),
            'ledgerSummary' => $ledgerQuery,
        ]);

        
    }
    public function ledgerReportDetails(Request $request,$id)
    {
         $ledgerQuery = Trn_Ledger_Posting::query();
        
         if ($request->filled('branch_id')) {
            $ledgerQuery->where('branch_id', $request->input('branch_id'));
        }
        
        if ($request->filled('posting_from_date') && $request->filled('posting_to_date')) {
            $ledgerQuery->whereBetween('posting_date', [$request->input('posting_from_date'), $request->input('posting_to_date')]);
        } elseif ($request->filled('posting_from_date')) {
            $purchaseQuery->where('posting_date', '>=', $request->input('posting_from_date'));
        } elseif ($request->filled('posting_to_date')) {
            $ledgerQuery->where('posting_date', '<=', $request->input('posting_to_date'));
        } else {
            // No date range provided, default to today
            $ledgerQuery->whereDate('posting_date', Carbon::today());
        }
                    //->where('account_ledger_id', $accountLedgerId)
                    //->whereBetween('posting_date', [$fromDate, $toDate])
        $ledgerQuery =$ledgerQuery ->where('account_ledger_id',$id)->orderBy('posting_date','DESC')->get();
          return view('reports.ledger-report-detail', [
            'pageTitle' => 'Ledger Detailed Report',
            'branches'=>Mst_Branch::query()->where('is_active',1)->get(),
            'ledgerName' => Mst_Account_Ledger::findOrFail($id)->ledger_name,
            'ledgerDetails' => $ledgerQuery,
            'account_ledger_id'=>$id
        ]);
        
    }
   public function TrailBalanceReport(Request $request)
{
    $assetTrialBalance = DB::table('mst__account__ledgers')
        ->join('mst_account_sub_head', 'mst__account__ledgers.account_sub_group_id', '=', 'mst_account_sub_head.id')
        ->join('sys__account__groups', 'mst_account_sub_head.account_group_id', '=', 'sys__account__groups.id')
        ->leftJoin('trn_ledger_postings', 'mst__account__ledgers.id', '=', 'trn_ledger_postings.account_ledger_id')
        ->leftJoin('trn_medicine_purchase_invoices', 'mst__account__ledgers.id', '=', 'trn_medicine_purchase_invoices.deposit_to')
        ->leftJoin('trn_medicine_purchase_return', 'trn_medicine_purchase_invoices.purchase_invoice_id', '=', 'trn_medicine_purchase_return.purchase_invoice_id')
        ->select(
            'sys__account__groups.account_group_name AS AccountGroupName',
            'mst__account__ledgers.ledger_name as ledgerName',
            DB::raw('SUM(
                CASE
                    WHEN mst__account__ledgers.id = 1 THEN trn_ledger_postings.debit - trn_ledger_postings.credit
                    WHEN mst__account__ledgers.id = 27 THEN trn_medicine_purchase_invoices.total_amount - trn_medicine_purchase_invoices.paid_amount - trn_medicine_purchase_return.sub_total
                    ELSE trn_ledger_postings.debit
                END
            ) AS balance')
        )
        ->where(function ($query) use ($request) {
            $query->where('sys__account__groups.account_group_name', '=', 'Asset')
                ->orWhereIn('mst_account_sub_head.account_sub_group_name', ['Fixed Assets', 'Bank Account', 'Other Current Asset']);
        })
        ->where('mst_account_sub_head.account_sub_group_name', '!=', 'Stock In Hand')
        ->where('mst__account__ledgers.ledger_name', '!=', 'Purchase Account')
        ->when($request->filled('posting_from_date') && $request->filled('posting_to_date'), function ($query) use ($request) {
            $query->whereBetween('posting_date', [$request->input('posting_from_date'), $request->input('posting_to_date')]);
        })
        ->when($request->filled('posting_from_date'), function ($query) use ($request) {
            $query->where('posting_date', '>=', $request->input('posting_from_date'));
        })
        ->when($request->filled('posting_to_date'), function ($query) use ($request) {
            $query->where('posting_date', '<=', $request->input('posting_to_date'));
        })
        ->when(!$request->filled('posting_from_date') && !$request->filled('posting_to_date'), function ($query) {
            $query->whereDate('posting_date', Carbon::today());
        })
        ->groupBy('AccountGroupName', 'ledgerName')
        ->get();

    $liabilityTrialBalance = DB::table('mst__account__ledgers')
        ->select(
            'mst__account__ledgers.ledger_name AS LedgerName',
            DB::raw('SUM(trn_ledger_postings.debit) AS DebitTotal'),
            DB::raw('SUM(trn_ledger_postings.credit) AS CreditTotal'))
        ->join('mst_account_sub_head', 'mst__account__ledgers.account_sub_group_id', '=', 'mst_account_sub_head.id')
        ->join('sys__account__groups', 'mst_account_sub_head.account_group_id', '=', 'sys__account__groups.id')
        ->leftJoin('trn_ledger_postings', 'mst__account__ledgers.id', '=', 'trn_ledger_postings.account_ledger_id')
        ->where('sys__account__groups.account_group_name', '=', 'Liability')
        ->where('mst__account__ledgers.ledger_name', '!=', 'Purchase Account')
        ->when($request->filled('posting_from_date') && $request->filled('posting_to_date'), function ($query) use ($request) {
            $query->whereBetween('posting_date', [$request->input('posting_from_date'), $request->input('posting_to_date')]);
        })
        ->when($request->filled('posting_from_date'), function ($query) use ($request) {
            $query->where('posting_date', '>=', $request->input('posting_from_date'));
        })
        ->when($request->filled('posting_to_date'), function ($query) use ($request) {
            $query->where('posting_date', '<=', $request->input('posting_to_date'));
        })
        ->when(!$request->filled('posting_from_date') && !$request->filled('posting_to_date'), function ($query) {
            $query->whereDate('posting_date', Carbon::today());
        })
        ->groupBy('LedgerName')
        ->get();

    $incomeTrialBalance = DB::table('mst__account__ledgers')
        ->select(
            'mst__account__ledgers.ledger_name AS LedgerName',
            DB::raw('SUM(trn_ledger_postings.debit) AS DebitTotal'),
            DB::raw('SUM(trn_ledger_postings.credit) AS CreditTotal'))
        ->join('mst_account_sub_head', 'mst__account__ledgers.account_sub_group_id', '=', 'mst_account_sub_head.id')
        ->join('sys__account__groups', 'mst_account_sub_head.account_group_id', '=', 'sys__account__groups.id')
        ->leftJoin('trn_ledger_postings', 'mst__account__ledgers.id', '=', 'trn_ledger_postings.account_ledger_id')
        ->where('sys__account__groups.account_group_name', 'Income')
        ->where('mst__account__ledgers.ledger_name', '!=', 'Purchase Account')
        ->when($request->filled('posting_from_date') && $request->filled('posting_to_date'), function ($query) use ($request) {
            $query->whereBetween('posting_date', [$request->input('posting_from_date'), $request->input('posting_to_date')]);
        })
        ->when($request->filled('posting_from_date'), function ($query) use ($request) {
            $query->where('posting_date', '>=', $request->input('posting_from_date'));
        })
        ->when($request->filled('posting_to_date'), function ($query) use ($request) {
            $query->where('posting_date', '<=', $request->input('posting_to_date'));
        })
        ->when(!$request->filled('posting_from_date') && !$request->filled('posting_to_date'), function ($query) {
            $query->whereDate('posting_date', Carbon::today());
        })
        ->groupBy('LedgerName')
        ->get();

    $expenseTrialBalance = DB::table('mst__account__ledgers')
        ->select(
            'mst__account__ledgers.ledger_name AS LedgerName',
            DB::raw('SUM(trn_ledger_postings.debit) AS DebitTotal'),
            DB::raw('SUM(trn_ledger_postings.credit) AS CreditTotal'))
        ->join('mst_account_sub_head', 'mst__account__ledgers.account_sub_group_id', '=', 'mst_account_sub_head.id')
        ->join('sys__account__groups', 'mst_account_sub_head.account_group_id', '=', 'sys__account__groups.id')
        ->leftJoin('trn_ledger_postings', 'mst__account__ledgers.id', '=', 'trn_ledger_postings.account_ledger_id')
        ->where('sys__account__groups.account_group_name', 'Expense')
        ->where('mst_account_sub_head.account_sub_group_name', '!=', 'Direct Expenses')
        ->when($request->filled('posting_from_date') && $request->filled('posting_to_date'), function ($query) use ($request) {
            $query->whereBetween('posting_date', [$request->input('posting_from_date'), $request->input('posting_to_date')]);
        })
        ->when($request->filled('posting_from_date'), function ($query) use ($request) {
            $query->where('posting_date', '>=', $request->input('posting_from_date'));
        })
        ->when($request->filled('posting_to_date'), function ($query) use ($request) {
            $query->where('posting_date', '<=', $request->input('posting_to_date'));
        })
        ->when(!$request->filled('posting_from_date') && !$request->filled('posting_to_date'), function ($query) {
            $query->whereDate('posting_date', Carbon::today());
        })
        ->groupBy('LedgerName')
        ->get();

    // Calculate net credit and net debit
    $netCredit = $liabilityTrialBalance->sum('CreditTotal') + $incomeTrialBalance->sum('CreditTotal');
    $netDebit = $assetTrialBalance->sum('balance') + $expenseTrialBalance->sum('DebitTotal');

    return view('reports.trail-balance-report', [
        'pageTitle' => 'Trial Balance Report',
        'assetTrialBalance' => $assetTrialBalance,
        'incomeTrialBalance' => $incomeTrialBalance,
        'expenseTrialBalance' => $expenseTrialBalance,
        'liabilityTrialBalance' => $liabilityTrialBalance,
        'netCredit' => $netCredit,
        'netDebit' => $netDebit
    ]);
}


    
    public function TrailBalanceReportLegacy(Request $request)
    {
        // $assetTrialBalance = DB::select("
        //     SELECT
        //         sys__account__groups.account_group_name AS AccountGroupName,
        //         mst_account_sub_head.account_sub_group_name As subheadName
        //         SUM(IF(mst_account_sub_head.id =1, trn_ledger_postings.debit - trn_ledger_postings.credit, trn_ledger_postings.debit)) AS balance
        //     FROM
        //         mst__account__ledgers
        //     JOIN
        //         mst_account_sub_head ON mst__account__ledgers.account_sub_group_id = mst_account_sub_head.id
        //     JOIN
        //         sys__account__groups ON mst_account_sub_head.account_group_id = sys__account__groups.id
        //     LEFT JOIN
        //         trn_ledger_postings ON  mst__account__ledgers.id = trn_ledger_postings.account_ledger_id
        //     WHERE
        //         (sys__account__groups.account_group_name = 'Asset' OR mst_account_sub_head.account_sub_group_name IN ('Fixed Assets', 'Bank Account', 'Other Current Asset'))
        //         AND mst_account_sub_head.account_sub_group_name != 'Stock In Hand'
        //         AND mst_account_sub_head.id !=1
        //     GROUP BY
        //         AccountGroupName,subheadName
        // ");
    $assetTrialBalance = DB::table('mst__account__ledgers')
    ->join('mst_account_sub_head', 'mst__account__ledgers.account_sub_group_id', '=', 'mst_account_sub_head.id')
    ->join('sys__account__groups', 'mst_account_sub_head.account_group_id', '=', 'sys__account__groups.id')
    ->leftJoin('trn_ledger_postings', 'mst__account__ledgers.id', '=', 'trn_ledger_postings.account_ledger_id')
    ->leftJoin('trn_medicine_purchase_invoices','mst__account__ledgers.id','=','trn_medicine_purchase_invoices.deposit_to')
     ->leftJoin('trn_medicine_purchase_return','trn_medicine_purchase_invoices.purchase_invoice_id','=','trn_medicine_purchase_return.purchase_invoice_id')
    ->select(
        'sys__account__groups.account_group_name AS AccountGroupName',
       //'mst_account_sub_head.account_sub_group_name AS subheadName',
        'mst__account__ledgers.ledger_name as ledgerName',
        DB::raw('SUM(
            CASE
                WHEN mst__account__ledgers.id = 1 THEN trn_ledger_postings.debit - trn_ledger_postings.credit
                WHEN mst__account__ledgers.id = 27 THEN trn_medicine_purchase_invoices.total_amount - trn_medicine_purchase_invoices.paid_amount - trn_medicine_purchase_return.sub_total
                ELSE trn_ledger_postings.debit
            END
        ) AS balance')
    )
    ->where(function($query) {
        $query->where('sys__account__groups.account_group_name', '=', 'Asset')
            ->orWhereIn('mst_account_sub_head.account_sub_group_name', ['Fixed Assets', 'Bank Account', 'Other Current Asset']);
    })
    ->where('mst_account_sub_head.account_sub_group_name', '!=', 'Stock In Hand')
    ->where('mst__account__ledgers.ledger_name', '!=', 'Purchase Account')
    ->groupBy('AccountGroupName','ledgerName')
    ->get();
    
    $liabilityTrialBalance = DB::table('mst__account__ledgers')
    ->select(
             'mst__account__ledgers.ledger_name AS LedgerName',
             DB::raw('SUM(trn_ledger_postings.debit) AS DebitTotal'),
             DB::raw('SUM(trn_ledger_postings.credit) AS CreditTotal'))
    ->join('mst_account_sub_head', 'mst__account__ledgers.account_sub_group_id', '=', 'mst_account_sub_head.id')
    ->join('sys__account__groups', 'mst_account_sub_head.account_group_id', '=', 'sys__account__groups.id')
    ->leftJoin('trn_ledger_postings', 'mst__account__ledgers.id', '=', 'trn_ledger_postings.account_ledger_id')
    ->where('sys__account__groups.account_group_name', '=', 'Liability')
    //->where('mst_account_sub_head.account_sub_group_name', '=', 'Capital Account')
    ->where('mst__account__ledgers.ledger_name', '!=', 'Purchase Account')
    ->groupBy('LedgerName')
    ->get();



        // // Liability Trial Balance Query
        // $liabilityTrialBalance = DB::select("
        //     SELECT
        //         sys__account__groups.account_group_name AS AccountGroupName,
        //         mst__account__ledgers.ledger_name AS LedgerName,
        //         SUM(trn_ledger_postings.debit) AS DebitTotal,
        //         SUM(trn_ledger_postings.credit) AS CreditTotal
        //     FROM
        //         mst__account__ledgers
        //     JOIN
        //         mst_account_sub_head ON mst__account__ledgers.account_sub_group_id = mst_account_sub_head.id
        //     JOIN
        //         sys__account__groups ON mst_account_sub_head.account_group_id = sys__account__groups.id
        //     LEFT JOIN
        //         trn_ledger_postings ON mst_account_ledgers.id = trn_ledger_postings.account_ledger_id
        //     WHERE
        //         sys__account__groups.account_group_name = 'Liability'
        //         AND mst_account_sub_head.id = 24
        //         AND mst__account__ledgers.id != 12
        //     GROUP BY
        //         mst__account__ledgers.id
        // ");

        // // Income Trial Balance Query
        // $incomeTrialBalance = DB::select("
        //     SELECT
        //         mst_account_groups.account_group_name AS AccountGroupName,
        //         mst_account_ledgers.ledger_name AS LedgerName,
        //         SUM(trn_ledger_postings.debit) AS DebitTotal,
        //         SUM(trn_ledger_postings.credit) AS CreditTotal
        //     FROM
        //         mst_account_ledgers
        //     JOIN
        //         mst_account_groups ON mst_account_ledgers.account_group_id = mst_account_groups.id
        //     LEFT JOIN
        //         trn_ledger_postings ON mst_account_ledgers.id = trn_ledger_postings.account_ledger_id
        //     WHERE
        //         mst_account_groups.account_group_name = 'Income'
        //         AND mst_account_ledgers.ledger_name != 'Purchase Account'
        //     GROUP BY
        //         mst_account_ledgers.id
        // ");
        $incomeTrialBalance = DB::table('mst__account__ledgers')
                            ->select(
                                   'mst__account__ledgers.ledger_name AS LedgerName',
                                     DB::raw('SUM(trn_ledger_postings.debit) AS DebitTotal'),
                                     DB::raw('SUM(trn_ledger_postings.credit) AS CreditTotal'))
                            ->join('mst_account_sub_head', 'mst__account__ledgers.account_sub_group_id', '=', 'mst_account_sub_head.id')
                            ->join('sys__account__groups', 'mst_account_sub_head.account_group_id', '=', 'sys__account__groups.id')
                            ->leftJoin('trn_ledger_postings', 'mst__account__ledgers.id', '=', 'trn_ledger_postings.account_ledger_id')
                            ->where('sys__account__groups.account_group_name', 'Income')
                            ->where('mst__account__ledgers.ledger_name', '!=', 'Purchase Account')
                            ->groupBy('LedgerName')
                            ->get();
         $expenseTrialBalance = DB::table('mst__account__ledgers')
                            ->select(
                                   'mst__account__ledgers.ledger_name AS LedgerName',
                                     DB::raw('SUM(trn_ledger_postings.debit) AS DebitTotal'),
                                     DB::raw('SUM(trn_ledger_postings.credit) AS CreditTotal'))
                            ->join('mst_account_sub_head', 'mst__account__ledgers.account_sub_group_id', '=', 'mst_account_sub_head.id')
                            ->join('sys__account__groups', 'mst_account_sub_head.account_group_id', '=', 'sys__account__groups.id')
                            ->leftJoin('trn_ledger_postings', 'mst__account__ledgers.id', '=', 'trn_ledger_postings.account_ledger_id')
                            ->where('sys__account__groups.account_group_name', 'Expense')
                            ->where('mst_account_sub_head.account_sub_group_name', '!=', 'Direct Expenses')
                            ->groupBy('LedgerName')
                            ->get();



        // // Expense Trial Balance Query
        // $expenseTrialBalance = DB::select("
        //     SELECT
        //         mst_account_groups.account_group_name AS AccountGroupName,
        //         mst_account_ledgers.ledger_name AS LedgerName,
        //         SUM(trn_ledger_postings.debit) AS DebitTotal,
        //         SUM(trn_ledger_postings.credit) AS CreditTotal
        //     FROM
        //         mst_account_ledgers
        //     JOIN
        //         mst_account_groups ON mst_account_ledgers.account_group_id = mst_account_groups.id
        //     LEFT JOIN
        //         trn_ledger_postings ON mst_account_ledgers.id = trn_ledger_postings.account_ledger_id
        //     WHERE
        //         mst_account_groups.account_group_name = 'Expense'
        //         AND mst_account_ledgers.id != 12
        //     GROUP BY
        //         mst_account_ledgers.id
        // ");

        
    //dd($assetTrialBalance);
    // Calculate net credit and net debit
    $netCredit = $liabilityTrialBalance->sum('CreditTotal') + $incomeTrialBalance->sum('CreditTotal');
    $netDebit = $assetTrialBalance->sum('balance') + $expenseTrialBalance->sum('DebitTotal');
      return view('reports.trail-balance-report', [
            'pageTitle' => 'Trial Balance Report',
            'assetTrialBalance'=>$assetTrialBalance,
            'incomeTrialBalance'=>$incomeTrialBalance,
            'expenseTrialBalance'=>$expenseTrialBalance,
            'liabilityTrialBalance'=>$liabilityTrialBalance,
            'netDebit'=>$netDebit,
            'netCredit'=>$netCredit,
            
            
        ]);
        
    }
    
    public function profitLossReport(Request $request)
    {
        $tradingAccountSales = DB::table('trn_ledger_postings')
            ->join('mst__account__ledgers', 'trn_ledger_postings.account_ledger_id', '=', 'mst__account__ledgers.id')
            ->join('mst_account_sub_head', 'mst__account__ledgers.account_sub_group_id', '=', 'mst_account_sub_head.id')
            ->join('sys__account__groups', 'mst_account_sub_head.account_group_id', '=', 'sys__account__groups.id')
             ->whereIn('mst__account__ledgers.ledger_name', ['Sales Revenue','Sales Accounts'])
            ->select('mst__account__ledgers.ledger_name', DB::raw('SUM(trn_ledger_postings.credit) as total_amount'))
            ->groupBy('mst__account__ledgers.ledger_name');
           
        if ($request->has('branch_id')) {
            $tradingAccountSales->where('trn_ledger_postings.branch_id',request('branch_id'));
        }
        if ($request->has('postng_from_date') && $request->has('posting_to_date')) {
            $start_date = $request->input('posting_from_date');
            $end_date = $request->input('posting_to_date');

            $tradingAccountSales->whereBetween('trn_ledger_postings.posting_date', [$start_date, $end_date]);
        }
        if(!$request->filled('posting_from_date') && !$request->filled('posting_to_date'))
        {
             $tradingAccountSales->whereDate('trn_ledger_postings.posting_date', Carbon::now());
            
        }
        $tradingAccountSales=$tradingAccountSales->get();
        $tradingAccountSales = $tradingAccountSales->map(function ($item) {
            $item->total_amount = doubleval($item->total_amount);
            return $item;
        });
        
        

       $tradingAccountCostOfSales = DB::table('trn_ledger_postings')
            ->join('mst__account__ledgers', 'trn_ledger_postings.account_ledger_id', '=', 'mst__account__ledgers.id')
            ->join('mst_account_sub_head', 'mst__account__ledgers.account_sub_group_id', '=', 'mst_account_sub_head.id')
            ->join('sys__account__groups', 'mst_account_sub_head.account_group_id', '=', 'sys__account__groups.id')
            ->whereIn('mst__account__ledgers.ledger_name', ['Purchase Account'])
            ->select('mst__account__ledgers.ledger_name', DB::raw('SUM(trn_ledger_postings.debit) as total_amount'))
            ->groupBy('mst__account__ledgers.ledger_name');
            if ($request->has('branch_id')) {
                $tradingAccountCostOfSales ->where('trn_ledger_postings.branch_id',request('branch_id'));
            }
             if ($request->has('postng_from_date') && $request->has('posting_to_date')) {
            $start_date = $request->input('posting_from_date');
            $end_date = $request->input('posting_to_date');
    
                $tradingAccountCostOfSales ->whereBetween('trn_ledger_postings.posting_date', [$start_date, $end_date]);
            }
            if(!$request->filled('posting_from_date') && !$request->filled('posting_to_date'))
            {
                 $tradingAccountCostOfSales->whereDate('trn_ledger_postings.posting_date', Carbon::now());
                
            }
            $tradingAccountCostOfSales=$tradingAccountCostOfSales->get();
            $tradingAccountCostOfSales = $tradingAccountCostOfSales->map(function ($item) {
                $item->total_amount = doubleval($item->total_amount);
                return $item;
            });
            

        $grossProfit = $tradingAccountSales->sum('total_amount')- $tradingAccountCostOfSales->sum('total_amount');

        $indirectIncomeStatement = DB::table('trn_ledger_postings')
            ->join('mst__account__ledgers', 'trn_ledger_postings.account_ledger_id', '=', 'mst__account__ledgers.id')
            ->join('mst_account_sub_head', 'mst__account__ledgers.account_sub_group_id', '=', 'mst_account_sub_head.id')
            ->join('sys__account__groups', 'mst_account_sub_head.account_group_id', '=', 'sys__account__groups.id')
            ->whereIn('mst_account_sub_head.account_sub_group_name', ['Indirect Incomes'])
            ->select('mst_account_sub_head.account_sub_group_name', DB::raw('SUM(trn_ledger_postings.transaction_amount) as total_amount'))
            ->groupBy('mst_account_sub_head.account_sub_group_name');
            if ($request->has('branch_id')) {
                $indirectIncomeStatement->where('trn_ledger_postings.branch_id',request('branch_id'));
            }
             if ($request->has('postng_from_date') && $request->has('posting_to_date')) {
            $start_date = $request->input('posting_from_date');
            $end_date = $request->input('posting_to_date');
    
                $indirectIncomeStatement->whereBetween('trn_ledger_postings.posting_date', [$start_date, $end_date]);
            }
            if(!$request->filled('posting_from_date') && !$request->filled('posting_to_date'))
            {
                 $indirectIncomeStatement->whereDate('trn_ledger_postings.posting_date', Carbon::now());
                
            }
            $indirectIncomeStatement=$indirectIncomeStatement->get();
        //conversion to double
        $indirectIncomeStatement = $indirectIncomeStatement->map(function ($item) {
            $item->total_amount = doubleval($item->total_amount);
            return $item;
        });
        
         $indirectExpenseStatement = DB::table('trn_ledger_postings')
            ->join('mst__account__ledgers', 'trn_ledger_postings.account_ledger_id', '=', 'mst__account__ledgers.id')
            ->join('mst_account_sub_head', 'mst__account__ledgers.account_sub_group_id', '=', 'mst_account_sub_head.id')
            ->join('sys__account__groups', 'mst_account_sub_head.account_group_id', '=', 'sys__account__groups.id')
            ->whereIn('mst_account_sub_head.account_sub_group_name', ['Indirect Expenses'])
            ->select('mst_account_sub_head.account_sub_group_name', DB::raw('SUM(trn_ledger_postings.transaction_amount) as total_amount'))
            ->groupBy('mst_account_sub_head.account_sub_group_name');
            if ($request->has('branch_id')) {
                $indirectExpenseStatement->where('trn_ledger_postings.branch_id',request('branch_id'));
            }
             if ($request->has('postng_from_date') && $request->has('posting_to_date')) {
            $start_date = $request->input('posting_from_date');
            $end_date = $request->input('posting_to_date');
    
                $indirectExpenseStatement->whereBetween('trn_ledger_postings.posting_date', [$start_date, $end_date]);
            }
            if(!$request->filled('posting_from_date') && !$request->filled('posting_to_date'))
            {
                 $indirectExpenseStatement->whereDate('trn_ledger_postings.posting_date', Carbon::now());
                
            }
            $indirectExpenseStatement=$indirectExpenseStatement->get();
        //conversion to double
        $indirectExpenseStatement = $indirectExpenseStatement->map(function ($item) {
            $item->total_amount = doubleval($item->total_amount);
            return $item;
        });
        
        
           //return  $incomeStatement;
        // $netProfit = $incomeStatement->where('account_sub_group_name', 'Indirect Incomes')->sum('total_amount') - $incomeStatement->where('account_sub_group_name', 'Indirect Expenses')->sum('total_amount');
        
        //dd($tradingAccountSales,$tradingAccountCostOfSales,$indirectIncomeStatement, $indirectExpenseStatement);
         return view('reports.profit-loss-report', [
            'pageTitle' => 'Profit and loss Report',
            'tradingAccountSales'=>$tradingAccountSales,
            'tradingAccountCostOfSales'=>$tradingAccountCostOfSales,
            'indirectIncomeStatement'=>$indirectIncomeStatement,
            'indirectExpenseStatement'=>$indirectExpenseStatement,
           
            
            
        ]);
    }
  public function paymentReceivedReport(Request $request)
    {
        //use Illuminate\Support\Facades\DB;

        $paymentReceivableReport = DB::table('trn_consultation_booking_invoices')
            ->select(
                DB::raw('CASE 
                    WHEN trn_consultation_bookings.booking_type_id = 84 THEN "Consultation Billing"
                    WHEN trn_consultation_bookings.booking_type_id = 85 THEN "Wellness Billing"
                    WHEN trn_consultation_bookings.booking_type_id = 86 THEN "Therapy Billing"
                    ELSE "Unknown"
                END as transaction_type'), 
                'booking_invoice_number AS invoice_number',
                'booking_id AS booking_id',
                'patient_id AS patient_id',
                'patient_name AS patient_name',
                'mst_pharmacies.id as pharmacy_id',
                'mst_pharmacies.pharmacy_name',
                //'trn_consultation_booking_invoices.branch_id AS branch_id',
                'invoice_date AS invoice_date',
                'mst_branches.branch_name AS branch',
                'amount AS total_amount',
                'trn_consultation_booking_invoices.discount AS discount',
                'trn_consultation_bookings.booking_type_id',
                'trn_consultation_bookings.booking_reference_number',
                'paid_amount AS payed_amount',
                DB::raw('(amount - paid_amount) AS due_amount')
            )
            ->leftJoin('mst_branches', 'trn_consultation_booking_invoices.branch_id', '=', 'mst_branches.branch_id')
            ->leftJoin('trn_consultation_bookings', 'trn_consultation_booking_invoices.booking_id', '=', 'trn_consultation_bookings.id')
            ->leftJoin('mst_patients', 'trn_consultation_bookings.patient_id', '=', 'mst_patients.id')
            ->leftJoin('mst_pharmacies', 'mst_pharmacies.branch', '=', 'trn_consultation_booking_invoices.branch_id')
            //->leftJoin('mst_pharmacies', 'mst_branches.pharmacy_id', '=', 'mst_pharmacies.id')
            //->where('trn_consultation_booking_invoices.booking_type_id', '=', 85) // Assuming booking_type_id for Consultation Billing is 85
            ->get();
        //return $paymentReceivableReport;
        
        $salesInvoiceReport = DB::table('trn__medicine__sales__invoices')
            ->select(
                DB::raw('"Medicine Sales" AS transaction_type'), 
                'sales_invoice_number AS invoice_number',
                'booking_id AS booking_id',
                'sales_invoice_id',
               // DB::raw('0 AS patient_id'),
               'trn__medicine__sales__invoices.pharmacy_id',
               'mst_pharmacies.pharmacy_name',
                'mst_branches.branch_id AS branch_id',
                'trn__medicine__sales__invoices.patient_id AS patient_id',
                'trn__medicine__sales__invoices.pharmacy_id as pharmacy_id',
                'patient_name AS patient_name',
                'invoice_date AS invoice_date',
                'mst_branches.branch_name AS branch',
                'mst_pharmacies.pharmacy_name',
                'trn_consultation_bookings.booking_reference_number',
                'total_amount',
                DB::raw('0.00 AS discount'),
                'trn__medicine__sales__invoices.payable_amount AS payed_amount',
                DB::raw('(total_amount - trn__medicine__sales__invoices.payable_amount) AS due_amount')
            )
            ->leftJoin('mst_branches', 'trn__medicine__sales__invoices.branch_id', '=', 'mst_branches.branch_id')
             ->leftJoin('trn_consultation_bookings', 'trn_consultation_bookings.id', '=', 'trn__medicine__sales__invoices.booking_id')
              ->leftJoin('mst_patients', 'trn__medicine__sales__invoices.patient_id', '=', 'mst_patients.id')
              ->leftJoin('mst_pharmacies', 'trn__medicine__sales__invoices.pharmacy_id', '=', 'mst_pharmacies.id')
            //->leftJoin('mst_pharmacy', 'trn_medicine_sales_invoices.branch_id', '=', 'mst_pharmacy.branch_id')
            ->get();
        
        $membershipReport = DB::table('mst__patient__membership__bookings')
            ->select(
                DB::raw('"Membership" AS transaction_type'), 
                DB::raw('"" AS invoice_number'),
                DB::raw('"" AS booking_id'),
                DB::raw('"" AS booking_reference_number'),
                DB::raw('0 AS pharmacy_id'),
                'membership_patient_id',
                'patient_id',
                'patient_name',
                //'branch_id',
                DB::raw('"" AS invoice_date'),
                DB::raw('"N/A" AS pharmacy_name'),
               // 'mst_branches.branch_name AS branch',
                'payment_amount AS total_amount',
                DB::raw('0.00 AS discount'),
                'payment_amount AS payed_amount',
                DB::raw('(payment_amount) AS due_amount')
            )
           // ->leftJoin('mst_branches', 'mst__patient__membership__bookings.branch_id', '=', 'mst_branch.branch_id')
            ->leftJoin('mst_patients', 'mst__patient__membership__bookings.patient_id', '=', 'mst_patients.id')
            //->leftJoin('mst_pharmacies', 'mst_pharmacies.id', '=', 'mst_patients.id')
            ->where('mst__patient__membership__bookings.is_active', '=', 1)
            ->orderBy('membership_patient_id', 'desc')
            ->get();
        
        // Combine the results from all three queries into a single collection
        $paymentReceivableReport = $paymentReceivableReport->concat($salesInvoiceReport)->concat($membershipReport);
        
        // Sort the combined results by invoice_date in descending order
        $paymentReceivableReport = $paymentReceivableReport->sortByDesc('invoice_date')->values();
        if ($request->filled('patient_name')) {
                $patientName=$request->get('patient_name');
            $paymentReceivableReport = $paymentReceivableReport->filter(function ($item) use ($patientName) {
                return stripos($item->patient_name, $patientName) !== false;
            });
        }
        if ($request->filled('booking_id')) {
                $bookingId=$request->get('booking_id');
            $paymentReceivableReport = $paymentReceivableReport->filter(function ($item) use ($bookingId) {
                return stripos($item->booking_reference_number, $bookingId) !== false;
            });
        }
        if ($request->filled('pharmacy_id')) {
            $pharmacyId=$request->get('pharmacy_id');
               $paymentReceivableReport = $paymentReceivableReport->filter(function ($item) use ($pharmacyId) {
            return $item->pharmacy_id == $pharmacyId;
        });
            }
        if($request->filled('transaction_type'))
        {
            $transactionType=$request->get('transaction_type');
            $paymentReceivableReport = $paymentReceivableReport->filter(function ($item) use ($transactionType) {
            return $item->transaction_type === $transactionType;
        });
            
        }
        if ($request->filled('invoice_from_date') && $request->filled('invoice_to_date')) 
        {
            $fromDate = $request->input('invoice_from_date');
            $toDate = $request->input('invoice_to_date');
            $paymentReceivableReport = $paymentReceivableReport->filter(function ($item) use ($fromDate, $toDate) {
                return Carbon::parse($item->invoice_date)->between($fromDate, $toDate);
            });
        }
        else
        {
            $fromDate = Carbon::now()->startOfDay();
            $toDate = Carbon::now()->endOfDay();
            $paymentReceivableReport = $paymentReceivableReport->filter(function ($item) use ($fromDate, $toDate) {
                return Carbon::parse($item->invoice_date)->between($fromDate, $toDate);
            });
            
        }

    // If no dates provided in the request, use current date
    if (empty($fromDate) || empty($toDate)) {
        $fromDate = Carbon::now()->startOfDay();
        $toDate = Carbon::now()->endOfDay();
    }
         

// Return the filtered report
//return $filteredReport;
        
        // Return the final report
    //return $paymentReceivableReport;
         return view('reports.payment-received-report', [
            'pageTitle' => 'Payment Received Report',
            'pharmacy' => Mst_Pharmacy::orderBy('created_at','DESC')->get(),
            'sales' => $paymentReceivableReport,
           // 'sumTotalAmount' => $sumTotalAmount
        ]);

    }



   
    
    
}
