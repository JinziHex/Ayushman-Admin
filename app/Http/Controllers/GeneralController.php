<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Trn_Consultation_Booking;
use App\Models\Mst_Branch;

class GeneralController extends Controller
{
    public function generalIndex(Request $request)
    {
        $currentDate = Carbon::now()->toDateString();
        $branch = Mst_Branch::where('branch_code','=',$request->branch_code)->select('branch_id','branch_name','branch_code','branch_address','branch_contact_number')->first();

        if($branch)
        {
            $branchId = $branch->branch_id;
            $todaysConsultationBooking = Trn_Consultation_Booking::with('bookingType', 'patient', 'bookingStatus','doctor')
                ->where('branch_id', $branchId)
                ->where('booking_type_id', 84) //consultation
                ->whereDate('booking_date', $currentDate)
                ->orderBy('created_at', 'DESC')
                ->limit(30)
                ->get();
            $todaysWellnessBooking = Trn_Consultation_Booking::with('bookingType', 'patient', 'bookingStatus','wellnessBookings')
                    ->where('branch_id', $branchId)
                    ->where('booking_type_id', 85) //wellness
                    ->whereDate('booking_date', $currentDate)
                    ->orderBy('created_at', 'DESC')
                    ->limit(30)
                    ->get();
            $todaysTherapyBooking = Trn_Consultation_Booking::with('bookingType', 'patient', 'bookingStatus','therapyBookings')
                    ->where('branch_id', $branchId)
                    ->where('booking_type_id', 86) //therapy
                    ->whereDate('booking_date', $currentDate)
                    ->orderBy('created_at', 'DESC')
                    ->limit(30)
                    ->get();
        }

        return view('general.index', [
            'branch' => $branch,
            'consultations' => $todaysConsultationBooking,
            'wellnesses' => $todaysWellnessBooking,
            'therapies' => $todaysTherapyBooking,
            'pageTitle' => 'Ayushman - General'
        ]);
    }
}
