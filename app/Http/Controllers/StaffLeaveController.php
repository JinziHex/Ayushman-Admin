<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Staff_Leave;
use Illuminate\Support\Facades\Auth;
use App\Models\Mst_Branch;
use App\Models\Mst_Staff;
use App\Models\Mst_User;
use App\Models\Mst_Leave_Type;
use App\Models\Trn_Consultation_Booking;
use App\Models\EmployeeAvailableLeave;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Mst_Master_Value;
use App\Models\Mst_Pharmacy;
class StaffLeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pageTitle = "Admin Leave Request";
        $staffleaves = Staff_Leave::select('staff_leave.*', 'mst_staffs.staff_name as staff_name', 'mst_branches.branch_name')
        ->join('mst_staffs', 'staff_leave.staff_id', '=', 'mst_staffs.staff_id')
        ->join('mst_branches', 'mst_staffs.branch_id', '=', 'mst_branches.branch_id')
        ->orderBy('staff_leave.updated_at', 'desc');
    
    // Apply filters if provided
    if ($request->has('staff_name')) {
        $staffleaves->where('mst_staffs.staff_name', 'LIKE', "%{$request->staff_name}%");
    }
    
    if ($request->has('from_date')) {
        $staffleaves->where('staff_leave.from_date', 'LIKE', "%{$request->from_date}%");
    }
    
    if ($request->has('to_date')) {
        $staffleaves->where('staff_leave.to_date', 'LIKE', "%{$request->to_date}%");
    }
    
    if(Auth::user()->user_type_id == 18 || Auth::user()->user_type_id == 21 || Auth::user()->user_type_id == 20){
            $branch_id = Auth::user()->staff->branch->branch_id;
            if ($branch_id) {
                $staffleaves->where('mst_branches.branch_id', $branch_id);
            }
    }else{
        if (session()->has('pharmacy_id') && session()->has('pharmacy_name') && session('pharmacy_id') != "all") {
            $pharmacy_id = session('pharmacy_id');
            $pharmacy = Mst_Pharmacy::with('branch')->find($pharmacy_id);
            if ($pharmacy && $pharmacy->branch) {
                $branch_id = $pharmacy->branch;
                $staffleaves->where('mst_branches.branch_id', $branch_id);
            }
        }
    }
        

    
    $staffleaves = $staffleaves->get();
    
                       

        return view('staffleave.index', compact('pageTitle', 'staffleaves'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pageTitle = "Create Leave Request";
        $branches = DB::table('mst_branches')->where('is_active', 1)->get();
        $leave_types = Mst_Leave_Type::where('is_active', 1)->get();
        $stafftype   = Mst_Master_Value::where('master_id',4)->pluck('master_value','id');
        return view('staffleave.create', compact('pageTitle','branches','leave_types','stafftype'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required',
            'staff_id' => 'required',
            'start_day' => 'required',
            'from_date' => 'required|date|date_format:Y-m-d|after_or_equal:' . today()->format('Y-m-d'),
            'to_date' => 'required|date|after_or_equal:from_date',
            'end_day' => 'required',
            'days' => 'required',
            'leave_type' => 'required',
            'reason' => 'required',
        ], [
            'branch_id.required' => 'The branch field is required.',
            'staff_id.required' => 'The staff field is required.',
            'from_date.required' => 'The from date field is required.',
            'start_day.required' => 'The start day field is required.',
            'to_date.required' => 'The to date field is required.',
            'end_day.required' => 'The end day field is required.',
            'days.required' => 'The days field is required.',
            'leave_type.required' => 'The leave type field is required.',
            'reason.required' => 'The reason field is required.',
        ]);

        // Check if the requested days are not greater than total days
        $staffId = $request->staff_id;
        $requestedDays = $request->days;
        $totalLeaves = EmployeeAvailableLeave::where('staff_id', $staffId)->value('total_leaves');
        
       if($request->leave_type!=5)
       {
            if ($requestedDays > $totalLeaves) {
            return redirect()->back()->withErrors(['days' => 'Requested days cannot be greater than total available days.'])->withInput();
       }
           $updatedTotalLeaves = $totalLeaves -  $requestedDays;
           EmployeeAvailableLeave::where('staff_id', $staffId)
                               ->update(['total_leaves' => $updatedTotalLeaves,
                            ]);
           
       }
        $existingLeaveRequest = Staff_Leave::where('staff_id', $request->staff_id)
            ->where('from_date', $request->from_date)
            ->where('to_date', $request->to_date)
            ->first();
        
        if ($existingLeaveRequest) {
            return redirect()->back()->withErrors(['duplicate' => 'Leave request already exist in this date.'])->withInput();
        }

        $lastInsertedId = Staff_Leave::create([
            'branch_id' => $request->branch_id,
            'staff_id' => $request->staff_id,
            'leave_type' => $request->leave_type,
            'days' => $request->days,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'reason' => $request->reason,
            'start_day' => $request->start_day,
            'end_day' => $request->end_day,
        ]);


        return redirect()->route('staffleave.index')->with('success', 'Leave Request added successfully');
    }
    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pageTitle = "View Leave Request";
        $show = Staff_Leave::select(
            'staff_leave.*',
            'mst_staffs.staff_name as staff_name',
            'mst_branches.branch_name',
            'mst_leave_types.name as leave_type_name'
        )
        ->join('mst_staffs', 'staff_leave.staff_id', '=', 'mst_staffs.staff_id')
        ->join('mst_branches', 'mst_staffs.branch_id', '=', 'mst_branches.branch_id')
        ->leftJoin('mst_leave_types', 'staff_leave.leave_type', '=', 'mst_leave_types.leave_type_id')
        ->where('staff_leave.id', $id)
        ->orderBy('staff_leave.updated_at', 'desc')
        ->first();
    
        return view('staffleave.show', compact('show','pageTitle'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
     
        try {
            $pageTitle = "Edit Leave Request";
            $leave_request = Staff_Leave::select(
                'staff_leave.*',
                'mst_staffs.staff_name as staff_name',
                'mst_staffs.staff_type as staff_type',
                'mst_branches.branch_name',
                'mst_leave_types.name as leave_type_name',
                'employee_available_leaves.total_leaves' // Add this line for total leaves
            )
            ->join('mst_staffs', 'staff_leave.staff_id', '=', 'mst_staffs.staff_id')
            ->join('mst_branches', 'mst_staffs.branch_id', '=', 'mst_branches.branch_id')
            ->leftJoin('mst_leave_types', 'staff_leave.leave_type', '=', 'mst_leave_types.leave_type_id')
            ->leftJoin('employee_available_leaves', function ($join) {
                $join->on('staff_leave.staff_id', '=', 'employee_available_leaves.staff_id')
                    ->where('employee_available_leaves.staff_id', '=', 'staff_leave.staff_id');
            })
            ->where('staff_leave.id', $id)
            ->orderBy('staff_leave.updated_at', 'desc')
            ->first();
            $total_leaves = EmployeeAvailableLeave::where('staff_id', $leave_request->staff_id)->value('total_leaves');
    
            $leave_types = Mst_Leave_Type::where('is_active', 1)->get();
            return view('staffleave.edit', compact('pageTitle', 'leave_request','leave_types','total_leaves'));
        } catch (QueryException $e) {
            return redirect()->route('staffleave.index')->with('error', 'Something went wrong');
        }
    
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $request->validate([
            // Add your validation rules here based on your requirements
            'from_date' => 'required|date|date_format:Y-m-d|after_or_equal:' . today()->format('Y-m-d'),
            'to_date' => 'required|date|after_or_equal:from_date',
            'start_day' => 'required',
            'end_day' => 'required',
            'days' => 'required',
            'leave_type' => 'required',
            'reason' => 'required',
        ]);
        $staffId = $request->staff_id;
        $requestedDays = $request->days;
      
        $totalLeaves = EmployeeAvailableLeave::where('staff_id', $staffId)->value('total_leaves');
        $leaveRequest = Staff_Leave::findOrFail($id);
        $current_days = $leaveRequest->days;
        $t = $totalLeaves + $current_days;
        if ($requestedDays > $t) {
            return redirect()->back()->withErrors(['days' => 'Requested days cannot be greater than total available days.'])->withInput();
        }

        $updatedTotalLeaves = $t -  $requestedDays;
        EmployeeAvailableLeave::where('staff_id', $staffId)
                               ->update(['total_leaves' => $updatedTotalLeaves,
                            ]);
        $leaveRequest->update([
            'from_date' => $request->input('from_date'),
            'to_date' => $request->input('to_date'),
            'start_day' => $request->input('start_day'),
            'end_day' => $request->input('end_day'),
            'days' => $request->input('days'),
            'leave_type' => $request->input('leave_type'),
            'reason' => $request->input('reason'),
            // Add other fields as needed
        ]);

        // Redirect back to the leave request edit page with a success message
        return redirect()->route('staffleave.index')->with('success', 'Leave request updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $leaverequest = Staff_Leave::findOrFail($id);

        // Soft delete the record
        $leaverequest->delete();

        return response()->json([
            'success' => true,
            'message' => 'Leave Request deleted successfully',
        ]);
    }

        public function getStaffNames(Request $request)
        {
            $branchId = $request->input('branch_id');
            $staffTypeId = $request->input('staff_type');
        
            $staffNames = Mst_Staff::where('branch_id', $branchId)
                                   ->where('staff_type', $staffTypeId)
                                   ->pluck('staff_name', 'staff_id');
        
            return response()->json($staffNames);
        }


    public function getTotalLeaves($staffId)
    {
        $userId = Mst_User::where('staff_id', $staffId)->value('user_id');
        // Fetch the total leaves for the given staffId from the database
        $totalLeaves = EmployeeAvailableLeave::where('staff_id', $userId)->first();
    
        return response()->json(['total_leaves' => $totalLeaves]);
    }



    public function checkDoctor($staffId)
    {
        $staff = Mst_Staff::find($staffId);   
        if ($staff) {
            $isDoctor = $staff->staff_type === 20;
            return response()->json(['isDoctor' => $isDoctor]);
        } else {
            return response()->json(['error' => 'Staff member not found'], 404);
        }
    }

    public function bookingCount(Request $request)
    {
        
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $staffId = $request->input('staff_id');
    
        $bookingCount = Trn_Consultation_Booking::where('doctor_id', $staffId)->whereBetween('booking_date', [$fromDate, $toDate])->count();

        return response()->json(['booking_count' => $bookingCount]);
    }
     public function getEmployeeAvaialbleLeaves(Request $request)
    {
            $staff_id = $request->input('staff_id');
            $available_leave =EmployeeAvailableLeave::where('staff_id',$staff_id)->first();
            $data=array();
            if($available_leave)
            {
                
             $datat['leave_count']=$available_leave->total_leaves;
            }
            else
            {
    
             $datat['leave_count']=0.0;
            }
            return response()->json($data);

    }
    
}
