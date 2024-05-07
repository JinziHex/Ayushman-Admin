<?php

namespace App\Http\Controllers;
use Illuminate\Database\QueryException;
use App\Models\Sys_Account_Group;
use App\Models\Mst_Account_Sub_Head;
use App\Models\Mst_Account_Ledger;
use App\Models\Trn_income_expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Mst_Master_Value;
use App\Models\Mst_Branch;
use App\Models\Mst_Pharmacy;
use App\Models\Trn_Ledger_Posting;

class IncomeExpenseController extends Controller
{
    public function index(Request $request)
    {
        
       $query =  Trn_income_expense::orderBy('trn_income_expenses.created_at', 'DESC')
            ->join('mst__account__ledgers', 'trn_income_expenses.income_expense_ledger_id', '=', 'mst__account__ledgers.id')
            ->join('mst_branches', 'mst_branches.branch_id', '=', 'trn_income_expenses.branch_id')
            ->select('trn_income_expenses.*', 'mst__account__ledgers.ledger_name as income_expense_ledger_id');
        if (Auth::user()->user_type_id == 18 || Auth::user()->user_type_id == 21 || Auth::user()->user_type_id == 20) {
            $branch_id = Auth::user()->staff->branch_id;
            if ($branch_id) {
                $query->where('trn_income_expenses.branch_id', $branch_id);
            }
        } else {
            if (session()->has('pharmacy_id') && session()->has('pharmacy_name') && session('pharmacy_id') != "all") {
                $pharmacy_id = session('pharmacy_id');
                $pharmacy = Mst_Pharmacy::with('branch')->find($pharmacy_id);
                if ($pharmacy && $pharmacy->branch) {
                    $branch_id = $pharmacy->branch;
                    $query->where('trn_income_expenses.branch_id', $branch_id);
                }
            }
        }
        
         $expdata = $query->get();
            
    return view('income-expense.index', [
        'incexpdata' => $expdata,
        'pageTitle' => 'Income-Expense'
    ]);

    }
    
    public function create(Request $request)
    {
        return view('income-expense.create', [
            'ledgerList' => Mst_Account_Ledger::orderBy('ledger_name','ASC')->get(),
            'payment_type' => Mst_Master_Value::where('master_id', 25)->pluck('master_value', 'id'),
            'branchs' => Mst_Branch::get(),
            'pageTitle' => 'Add Miscellaneous Income-Expense'
        ]);
    }
    
    public function store(Request $request)
    {

            $request->validate([
            'income_expense_type_id' => 'required',
            'income_expense_date' => 'required|date',
            'income_expense_ledger_id' => 'required',
            'income_expense_amount' => 'required|numeric',
            'transaction_mode_id' => 'required',
            'transaction_account_id' => 'required',
            'branch' => 'required',
            'reference_file' => 'nullable|mimes:pdf,doc|max:2048' //2mb
    
        ]);
            $incomeExpense = new Trn_income_expense;
            $incomeExpense->income_expense_type_id = $request->input('income_expense_type_id');
            $incomeExpense->income_expense_date = $request->input('income_expense_date');
            $incomeExpense->income_expense_ledger_id = $request->input('income_expense_ledger_id');
            $incomeExpense->income_expense_amount = $request->input('income_expense_amount');
            $incomeExpense->transaction_mode_id = $request->input('transaction_mode_id');
            $incomeExpense->transaction_account_id = $request->input('transaction_account_id');
            $incomeExpense->branch_id = $request->input('branch');
            $incomeExpense->reference = $request->input('reference');
            $incomeExpense->notes = $request->input('notes');
            $refFile = $request->file('reference_file');
            
            if (!empty($refFile)) {
                if (isset($refFile)) {
                    $filename = uniqid('income_expense_') . '.' . $refFile->getClientOriginalExtension();
                    if(isset($refFile) && $refFile->isValid() ) 
                    {
                       $path2 = $refFile->move(public_path('assets/uploads/miscelleneous/documents'), $filename);
                    }
            }
            } else {
                $filename =null;
            }
            $incomeExpense->reference_file = $filename;
        
            $incomeExpense->save();
            
            $ledgerPosting1 = new Trn_Ledger_Posting;
            $ledgerPosting2 = new Trn_Ledger_Posting;
        
            $ledgerPosting1->posting_date = $request->input('income_expense_date');
            $ledgerPosting1->master_id = 'IE' . $incomeExpense->id;
            $ledgerPosting1->account_ledger_id =$request->input('income_expense_ledger_id');
            $ledgerPosting1->debit =0;
            $ledgerPosting1->credit = $request->input('income_expense_amount');
            $ledgerPosting1->pharmacy_id = $request->input('branch');
            $ledgerPosting1->transaction_amount = $request->input('income_expense_amount');
        
            $ledgerPosting2->posting_date = $request->input('income_expense_date');
            $ledgerPosting2->master_id = 'IE' . $incomeExpense->id;
            $ledgerPosting2->account_ledger_id = $request->input('income_expense_ledger_id');
            $ledgerPosting2->debit = $request->input('income_expense_amount');
            $ledgerPosting2->credit = 0;
            $ledgerPosting2->pharmacy_id = $request->input('branch');
             $ledgerPosting2->transaction_amount = $request->input('income_expense_amount');
        
            // Save the models to the database
            $ledgerPosting1->save();
            $ledgerPosting2->save();
            return redirect()->route('income-expense.index')->with('success', 'Income/Expense added successfully');
    }
    
        public function destroy($id)
        {
            $incomeExpense = Trn_income_expense::find($id);
        
            if (!$incomeExpense) {
                return redirect()->route('income-expense.index')->with('error', 'Record not found.');
            }
        
            Trn_Ledger_Posting::where('master_id', 'IE' . $id)->delete();
            $incomeExpense->delete();
        
            return redirect()->route('income-expense.index')->with('success', 'Record deleted successfully.');
        }


}
