<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Trn_Consultation_Booking;
use App\Models\Mst_Staff;
use App\Models\Mst_Medicine;
use App\Models\Mst_Therapy;
use App\Models\Trn_Prescription;
use App\Models\Trn_Prescription_Details;
use App\Models\Trn_Booking_Therapy_detail;
use App\Models\Trn_Medicine_Stock;

class ConsultationController extends Controller
{
    
    public function ConsultIndex(Request $request)
    {
        $userType = Auth::user()->user_type_id;
        if($userType == 20) //a doctor
        {
            $staff = Mst_Staff::findOrFail(Auth::user()->staff_id);
            if ($staff) {
            $booking = Trn_Consultation_Booking::with('patient','familyMember','branch','bookingStatus')->where('doctor_id',$staff->staff_id)->where('booking_type_id',84)->where('booking_status_id',88)->orderBy('created_at','DESC')->get();
            }
        }else{
            $booking = Trn_Consultation_Booking::with('patient','familyMember','branch','bookingStatus')->where('booking_type_id',84)->where('booking_status_id',88)->orderBy('created_at','DESC')->get(); //confirmed bookings only.
        }
        return view('doctor.consultation.index', [
            'bookings' => $booking,
            'pageTitle' => 'Consultation Bookings'
        ]);
    }

    public function PrescriptionAdd($id, Request $request)
    {
        $bookingInfo = Trn_Consultation_Booking::findOrFail($id);
        $bookingInfoPatientHistory = Trn_Consultation_Booking::with('patient','familyMember','branch','bookingStatus')->findOrFail($id);
        
        if($bookingInfoPatientHistory->is_for_family_member !== null && $bookingInfoPatientHistory->is_for_family_member > 0)
        {
            $booked_for=$bookingInfoPatientHistory->familyMember['id'];
           
            $bookingPreviousIds = Trn_Consultation_Booking::where('family_member_id',$booked_for)->where('booking_type_id',84)->where('booking_status_id',89)->pluck('id');
           
        }
        else
        {
            $booked_for=$bookingInfoPatientHistory->patient['id'];
            $bookingPreviousIds = Trn_Consultation_Booking::where('patient_id',$booked_for)->where('booking_type_id',84)->where('booking_status_id',89)->pluck('id');
        }
        
        $patient_histories=Trn_Prescription::with('Staff','BookingDetails','BookingDetails.bookingStatus','BookingDetails.timeSlot','BookingDetails.therapyBookings','PrescriptionDetails','PrescriptionDetails.medicine')->whereIn('Booking_id', $bookingPreviousIds)->orderBy('created_at','DESC')->get();
        $medicines = Mst_Medicine::where('is_active', 1)
            ->orderBy('created_at', 'DESC')
            ->get();
        
        foreach ($medicines as $medicine) {
            $stocks = Trn_Medicine_Stock::where('medicine_id', $medicine->id)->sum('current_stock');
            $medicine->stocks = $stocks; // Add a 'stocks' property to each medicine object
        }
        
        return view('doctor.consultation.prescription', [
            'pageTitle' => 'Add Prescriptions',
            'medicines' => $medicines,
            'therapies' => Mst_Therapy::where('is_active', 1)
                ->select('id', 'therapy_name')
                ->get(),
            'patient_histories' => $patient_histories,
            'bookingInfo' => $bookingInfo,
        ]);
    }

     public function prescriptionStore(Request $request)
    { 

            $validator = Validator::make($request->all(), [
                'diagnosis' => 'required',
                'advice' => 'required',
            ]);
    
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
    
            DB::beginTransaction();
    
            $prescription = new Trn_Prescription();
            $prescription->Booking_id = $request->booking_id;
            $prescription->doctor_id = Auth::user()->staff_id;
            $prescription->diagnosis = $request->diagnosis;
            $prescription->advice = $request->advice;
            $prescription->duration =  0;
            $prescription->save();
    
            $prescriptionId = DB::getPdo()->lastInsertId();
            $medicineIds = $request->input('medicine_id');
            $medicineNames = $request->input('medicine_name');
            $medicineDosages = $request->input('dosage');
            $medicineDurations = $request->input('duration');
            $medicineTypes = $request->input('medicine_type');
 
            array_shift($medicineNames);
            
            //dd($medicineIds,$medicineDosages,$medicineDurations);
                
            foreach ($medicineIds as $key => $medicineId) {
            if($medicineTypes[$key] == 1)
             {
                $detail = new Trn_Prescription_Details();
                $detail->priscription_id = $prescriptionId;
                $detail->medicine_type = 1;
                $detail->medicine_id = $medicineId;
                $detail->duration = $medicineDurations[$key];
                $detail->medicine_dosage = $medicineDosages[$key];
                $detail->remarks = '';
                $detail->save();
             }
           
            else
            {
               
                foreach ($medicineNames as $key => $medicineName) {
                if ($medicineName != 0) {
                $detail = new Trn_Prescription_Details();
                $detail->priscription_id = $prescriptionId;
                $detail->medicine_type = 2;
                $detail->medicine_id = $medicineName;
                $detail->duration = $medicineDurations[$key];
                $detail->medicine_dosage = $medicineDosages[$key];
                $detail->remarks = '';
                $detail->save();
            }
                }
            }
            }
            $booking=Trn_Consultation_Booking::find($request->booking_id);
            $booking->booking_status_id=89;
            $booking->update();
            
            $therapyIds = $request->input('therapy_id');
            $bookingFees = $request->input('booking_fee');
            $timeSlots = $request->input('timeslots');
            $instructions = $request->input('instructions');

                foreach ($therapyIds as $key => $therapyId) {
                                
                    $detail = new Trn_Booking_Therapy_detail();
                    $detail->therapy_id = $therapyId;
                    $detail->booking_id = $request->booking_id;
                    $detail->therapy_fee = $bookingFees[$key];
                    $detail->instructions = $instructions[$key];
                    $detail->booking_timeslot = $timeSlots[$key];
                    $detail->save();
         
                    }

            return redirect()->route('consultation.index')->with('success', 'Prescription Added Successfully!');
    }
    
    public function PatientHistory($id, Request $request)
    {
        $bookingInfo = Trn_Consultation_Booking::with('patient','familyMember','branch','bookingStatus')->findOrFail($id);
        
        if($bookingInfo->is_for_family_member !== null && $bookingInfo->is_for_family_member > 0)
        {
            $booked_for=$bookingInfo->familyMember['id'];
           
            $bookingPreviousIds = Trn_Consultation_Booking::where('family_member_id',$booked_for)->where('booking_type_id',84)->where('booking_status_id',89)->pluck('id');
           
        }
        else
        {
            $booked_for=$bookingInfo->patient['id'];
            $bookingPreviousIds = Trn_Consultation_Booking::where('patient_id',$booked_for)->where('booking_type_id',84)->where('booking_status_id',89)->pluck('id');
        }
        
        $patient_histories=Trn_Prescription::with('Staff','BookingDetails','BookingDetails.bookingStatus','BookingDetails.timeSlot','BookingDetails.therapyBookings','PrescriptionDetails','PrescriptionDetails.medicine')->whereIn('Booking_id', $bookingPreviousIds)->orderBy('created_at','DESC')->get();
        
        return view('doctor.consultation.patient-history', [
            'pageTitle' => 'Patient History',
            'patient_histories'=>$patient_histories,
            'booking_info' => $bookingInfo
        ]);
    }
     public function ConsultHistory(Request $request)
    {
        $userType = Auth::user()->user_type_id;
        if($userType == 20) //a doctor
        {
            $staff = Mst_Staff::findOrFail(Auth::user()->staff_id);
            $doctorId = $staff->staff_id;
            if ($staff) {
            $booking = Trn_Consultation_Booking::with('patient','familyMember','branch','bookingStatus')
            ->where('doctor_id', $doctorId)
            ->where('booking_type_id',84)
            ->whereIn('booking_status_id',[89,90])
            ->orderBy('created_at','DESC')
            ->get();
          
            }
        }else{
            $booking = Trn_Consultation_Booking::with('patient','familyMember','branch','bookingStatus')
            ->where('booking_type_id',84)
            ->whereIn('booking_status_id',[89,90])
            ->orderBy('created_at','DESC')
            ->get(); //confirmed bookings only.
        }
        return view('doctor.consultation.consultation-history', [
            'bookings' => $booking,
            'pageTitle' => 'Consultation History'
        ]);
    }
    public function viewConsultation($id, Request $request)
    {
        //dd(1);
        $history=Trn_Prescription::with('Staff','BookingDetails','BookingDetails.bookingStatus','BookingDetails.timeSlot','BookingDetails.therapyBookings','PrescriptionDetails','PrescriptionDetails.medicine')->where('Booking_id', $id)->orderBy('created_at','DESC')->first();
        //dd($patient_histories);
        return view('doctor.consultation.view-consultation', [
            'pageTitle' => 'View Consultation',
            'history'=>$history
        ]);
        
    }

}
