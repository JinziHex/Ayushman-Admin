<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Staff_Leave;
use App\Models\Mst_Staff;
use App\Models\Mst_Pharmacy;
use Carbon\Carbon;
class AttendanceController extends Controller
{
    public function viewAttendance()
    {
        $pageTitle = "Attendance View";
        $selectedMonthYear = now()->format('Y-m');
        $firstDayOfMonth = Carbon::parse($selectedMonthYear . '-01');
        
       
        
        if(Auth::user()->user_type_id == 18 || Auth::user()->user_type_id == 21 || Auth::user()->user_type_id == 20){
            $branch_id = Auth::user()->staff->branch->branch_id;
            $staffLeaves = Staff_Leave::where('branch_id',$branch_id)->whereYear('from_date', '=', now()->year)
            ->whereMonth('from_date', '=', now()->month)
            ->get();
            $allStaff = Mst_Staff::where('branch_id',$branch_id)->get(); 
        }else{
             if (session()->has('pharmacy_id') && session()->has('pharmacy_name') && session('pharmacy_id') != "all") {
                $pharmacy_id = session('pharmacy_id');
                $pharmacy = Mst_Pharmacy::with('branch')->find($pharmacy_id);
                if ($pharmacy && $pharmacy->branch) {
                    $branch_id = $pharmacy->branch;
                   $staffLeaves = Staff_Leave::where('branch_id',$branch_id)->whereYear('from_date', '=', now()->year)
                    ->whereMonth('from_date', '=', now()->month)
                    ->get();
                    $allStaff = Mst_Staff::where('branch_id',$branch_id)->get(); 
                }
            }else{
                 $staffLeaves = Staff_Leave::whereYear('from_date', '=', now()->year)
                    ->whereMonth('from_date', '=', now()->month)
                    ->get();
                    $allStaff = Mst_Staff::get(); 
            }
        }
        
        
        $daysInMonth = $firstDayOfMonth->daysInMonth;
        
        return view('attendance.view', compact('pageTitle', 'selectedMonthYear','staffLeaves','daysInMonth','allStaff','firstDayOfMonth'));
    }
    public function monthlyAttendance(Request $request)
    {
        $selectedMonthYear = $request->input('month_year', now()->format('Y-m'));
        $firstDayOfMonth = Carbon::parse($selectedMonthYear . '-01');
        $lastDayOfMonth = $firstDayOfMonth->copy()->endOfMonth();
        $daysInMonth = $firstDayOfMonth->daysInMonth;
        if(Auth::user()->user_type_id == 18 || Auth::user()->user_type_id == 21 || Auth::user()->user_type_id == 20){
            $branch_id = Auth::user()->staff->branch->branch_id;
            $allStaff = Mst_Staff::where('branch_id',$branch_id)->get(); 
        // Fetch staff leaves for the selected month and year
        $staffLeaves = Staff_Leave::where('branch_id',$branch_id)->whereYear('from_date', '=', $firstDayOfMonth->year)
            ->whereMonth('from_date', '=', $firstDayOfMonth->month)
            ->get();
    
        // Fetch absent staff IDs during the specified time period
        $absentStaffIds = Staff_Leave::where('branch_id',$branch_id)->where('from_date', '<=', $lastDayOfMonth)
            ->where('to_date', '>=', $firstDayOfMonth)
            ->pluck('staff_id')
            ->toArray();
        }
        else
        {
                if (session()->has('pharmacy_id') && session()->has('pharmacy_name') && session('pharmacy_id') != "all") {
                $pharmacy_id = session('pharmacy_id');
                $pharmacy = Mst_Pharmacy::with('branch')->find($pharmacy_id);
                if ($pharmacy && $pharmacy->branch) {
                    $branch_id = $pharmacy->branch;
                   $staffLeaves = Staff_Leave::where('branch_id',$branch_id)->whereYear('from_date', '=', $firstDayOfMonth->year)
            ->whereMonth('from_date', '=', $firstDayOfMonth->month)
            ->get();
             $absentStaffIds = Staff_Leave::where('branch_id',$branch_id)->where('from_date', '<=', $lastDayOfMonth)
            ->where('to_date', '>=', $firstDayOfMonth)
            ->pluck('staff_id')
            ->toArray();
                    $allStaff = Mst_Staff::where('branch_id',$branch_id)->get(); 
                }
            }else{
                 $staffLeaves = Staff_Leave::whereYear('from_date', '=', now()->year)
                    ->whereMonth('from_date', '=', now()->month)
                    ->get();
                    $allStaff = Mst_Staff::get(); 
                     $absentStaffIds = Staff_Leave::where('from_date', '<=', $lastDayOfMonth)
            ->where('to_date', '>=', $firstDayOfMonth)
            ->pluck('staff_id')
            ->toArray();
            }
        }
        
  
    
        return view('attendance.view', compact('allStaff', 'absentStaffIds', 'selectedMonthYear', 'daysInMonth', 'firstDayOfMonth', 'staffLeaves'));
    }
    
    
    
    
    
}
