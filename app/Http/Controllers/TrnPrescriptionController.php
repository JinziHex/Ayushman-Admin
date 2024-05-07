<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trn_Prescription;
use App\Models\Trn_Prescription_Details;
use App\Models\Mst_Patient;
use App\Models\Mst_Staff;
use App\Models\Trn_Patient_Family_Member;
use Dompdf\Dompdf;
use View;
use Dompdf\Options;
use Carbon\Carbon;
use App\Models\Trn_Consultation_Booking;
use App\Models\Mst_Branch;
use App\Models\Mst_Setting;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use DB;


class TrnPrescriptionController extends Controller
{
    public function index()
    {
        try {
            $pageTitle = "Prescriptions";
            $patients = Mst_Patient::where('is_active', 1)->get();
            return view('prescription.index', compact('pageTitle', 'patients'));
        } catch (QueryException $e) {
            dd('Something went wrong.');
        }
    }

    public function list(Request $request)
    {
        try {
            $pageTitle = "Prescriptions";
            $patients = Mst_Patient::where('is_active', 1)->get();
            $prescriptions = [];
            $bookingInfo = null;
            $patient_id = $request->patient_id;
            $booking_id = $request->patient_booking_id;
            if ($request->has('patient_booking_id')) {
                $prescriptions = Trn_Prescription::where('booking_id', $request->patient_booking_id)->with('Staff')->orderBy('created_at', 'desc')->get();
                $bookingInfo = Trn_Consultation_Booking::where('id', $request->patient_booking_id)->first();
            }
            return view('prescription.index', compact('pageTitle', 'prescriptions', 'patients', 'patient_id', 'booking_id','bookingInfo'));
        } catch (QueryException $e) {
            dd('Something went wrong.');
        }
    }

    public function show($id)
    {
   
        try {
            $pageTitle = "Prescription Details";
            $basic_details = Trn_Prescription::join('trn_consultation_bookings', 'trn__prescriptions.Booking_Id', '=', 'trn_consultation_bookings.id')
                ->where('trn__prescriptions.prescription_id', $id)
                ->first();
                
            if ($basic_details->is_for_family_member == 0) {
                $patient_name = Mst_Patient::where('id', $basic_details->patient_id)->value('patient_name');
            } else {
                $patient_name = Trn_Patient_Family_Member::where('id', $basic_details->family_member_id)->value('family_member_name');
            }
        $medicineType = Trn_Prescription_Details::where('priscription_id', $id)->value('medicine_type');
            $medicine_details = Trn_Prescription_Details::where('trn__prescription__details.priscription_id', $id)
                //->join('mst_medicines', 'trn__prescription__details.medicine_id', '=', 'mst_medicines.id')
                //->join('mst_master_values as med_type_medicine', 'mst_medicines.medicine_type', '=', 'med_type_medicine.id')
               // ->join('mst_medicine_dosages', 'trn__prescription__details.medicine_dosage', '=', 'mst_medicine_dosages.medicine_dosage_id')
               // ->join('mst_master_values as master_values', 'mst_medicines.medicine_type', '=', 'master_values.id')
                ->leftJoin('mst_medicines', function ($join) {
                    $join->on('trn__prescription__details.medicine_id', '=', 'mst_medicines.id')
                        ->where('trn__prescription__details.medicine_type', '=', 1);
                })
                ->select(
                    'mst_medicines.medicine_name',
                    DB::raw("CASE 
                        WHEN trn__prescription__details.medicine_type = 1 THEN mst_medicines.medicine_name
                        ELSE trn__prescription__details.medicine_id
                    END as medicine_name"),
                    'trn__prescription__details.medicine_type as medicine_type',
                    'trn__prescription__details.medicine_dosage as medicine_dosage',
                    'trn__prescription__details.remarks',
                    'trn__prescription__details.duration',
                    'trn__prescription__details.priscription_id as id',
                    'trn__prescription__details.prescription_details_id',
                )
                ->get();
         //   dd($medicine_details);
            $medicine_details1 = Trn_Prescription_Details::where('trn__prescription__details.priscription_id', $id)
                 ->select(
                'trn__prescription__details.remarks',
                'trn__prescription__details.duration',
                'trn__prescription__details.priscription_id as id',
                'trn__prescription__details.prescription_details_id',
                'trn__prescription__details.medicine_id',
                'trn__prescription__details.medicine_dosage'
            )
            ->get();
            return view('prescription.view', compact('pageTitle', 'basic_details', 'id', 'patient_name', 'medicine_details','medicineType','medicine_details1'));
        } catch (QueryException $e) {
            dd($e->getMessage());
            dd('Something went wrong.');
        }
    }

    public function print($id)
    {
        try {
            $pageTitle = "Prescription Details";
            $basic_details = Trn_Prescription::join('trn_consultation_bookings', 'trn__prescriptions.Booking_Id', '=', 'trn_consultation_bookings.id')
                ->where('trn__prescriptions.prescription_id', $id)
                ->first();
            if ($basic_details->is_for_family_member == 0) {
                $patient_details = Mst_Patient::where('id', $basic_details->patient_id)->first();
                // Get the current year for age calculation
                $currentYear = Carbon::now()->year;
                $carbonDate = Carbon::parse($patient_details->patient_dob);
                $year = $carbonDate->year;
                $age = $currentYear - $year;
                $patient_personal_details = [
                    'patient_name' => $patient_details->patient_name ?? "N/A",
                    'patient_mobile' => $patient_details->patient_mobile ?? "N/A",
                    'patient_address' => $patient_details->patient_address ?? "N/A",
                    'patient_age' => $age ?? "N/A",
                ];
            } else {
                $patient_details = Trn_Patient_Family_Member::where('id', $basic_details->family_member_id)->first();
                // Get the current year for age calculation
                $currentYear = Carbon::now()->year;
                $carbonDate = Carbon::parse($patient_details->date_of_birth);
                $year = $carbonDate->year;
                $age = $currentYear - $year;
                $patient_personal_details = [
                    'patient_name' => $patient_details->family_member_name ?? "N/A",
                    'patient_mobile' => $patient_details->mobile_number ?? "N/A",
                    'patient_address' => $patient_details->address ?? "N/A",
                    'patient_age' => $age ?? "N/A",
                ];
            }
            $getDoctorDetails = Mst_Staff::where('staff_id', $basic_details->doctor_id)->first();
            $getBranchID = Trn_Consultation_Booking::where('id', $basic_details->Booking_Id)->first();
            if ($getBranchID && $getBranchID->branch_id) {
                $branch_details = Mst_Branch::where('branch_id', $getBranchID->branch_id)->select('branch_name','branch_address','branch_contact_number','branch_email')->first();
            }else{
                $branch_details = NULL;
            }
            $AppkicationSettings = Mst_Setting::where('id',1)->first();
            $medicineType = Trn_Prescription_Details::where('priscription_id', $id)->value('medicine_type');
            $medicine_details = Trn_Prescription_Details::where('trn__prescription__details.priscription_id', $id)
                //->join('mst_medicines', 'trn__prescription__details.medicine_id', '=', 'mst_medicines.id')
                //->join('mst_master_values as med_type_medicine', 'mst_medicines.medicine_type', '=', 'med_type_medicine.id')
               // ->join('mst_medicine_dosages', 'trn__prescription__details.medicine_dosage', '=', 'mst_medicine_dosages.medicine_dosage_id')
               // ->join('mst_master_values as master_values', 'mst_medicines.medicine_type', '=', 'master_values.id')
                ->leftJoin('mst_medicines', function ($join) {
                    $join->on('trn__prescription__details.medicine_id', '=', 'mst_medicines.id')
                        ->where('trn__prescription__details.medicine_type', '=', 1);
                })
                ->select(
                    'mst_medicines.medicine_name',
                    DB::raw("CASE 
                        WHEN trn__prescription__details.medicine_type = 1 THEN mst_medicines.medicine_name
                        ELSE trn__prescription__details.medicine_id
                    END as medicine_name"),
                    'trn__prescription__details.medicine_type as medicine_type',
                    'trn__prescription__details.medicine_dosage as medicine_dosage',
                    'trn__prescription__details.remarks',
                    'trn__prescription__details.duration',
                    'trn__prescription__details.priscription_id as id',
                    'trn__prescription__details.prescription_details_id',
                )
                ->get();
         //   dd($medicine_details);
            $medicine_details1 = Trn_Prescription_Details::where('trn__prescription__details.priscription_id', $id)
                 ->select(
                'trn__prescription__details.remarks',
                'trn__prescription__details.duration',
                'trn__prescription__details.priscription_id as id',
                'trn__prescription__details.prescription_details_id',
                'trn__prescription__details.medicine_id',
                'trn__prescription__details.medicine_dosage'
            )
            ->get();
                
            $dompdf = new Dompdf();
            $view = View::make('prescription.print_prescription', ['doctorDetails' => $getDoctorDetails, 'pageTitle' => $pageTitle,'branch_details' => $branch_details, 'settings' =>$AppkicationSettings, 'basic_details' => $basic_details, 'patient_personal_details' => $patient_personal_details, 'medicine_details' => $medicine_details, 'medicine_details1' => $medicine_details1,'medicineType' => $medicineType]);
            $html = $view->render();
            // Load HTML content from a template or dynamically generate it based on $data
            // $html = '<html>HIKSLQW OIDJQ WOIJ D UHWEN</html>'; // You can generate HTML content here based on $data

            // Set PDF options if needed
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', true); // Enable PHP code within the HTML (optional)
            $dompdf->setOptions($options);

            // Load HTML into Dompdf
            $dompdf->loadHtml($html);

            // Set paper size and orientation
            $dompdf->setPaper('A4', 'portrait');

            // Render the HTML as PDF
            $dompdf->render();

            // Return the PDF content
            $pdfContent = $dompdf->output();
            // Pass your data as needed

            // You can also save the PDF to a file or store it in the database for future reference
            // For example, to save it to a file
            $pdfFilename = 'invoice.pdf';
            file_put_contents($pdfFilename, $pdfContent);

            // Return a response to the user for immediate viewing or downloading
            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="' . $pdfFilename . '"');
        } catch (QueryException $e) {
            dd($e->getMessage());
            dd('Something went wrong.');
        }
    }

    public function getPatientBookingIds($id)
    {
       
     
            $allBookings = Trn_Consultation_Booking::where('patient_id', $id)
                        ->where('booking_type_id','=',84)  // consultation bookingonly
                        ->where('booking_status_id','88')  //completed bookings only . a booking will be marked as completed once the doctor enters prescription and mark consultation done.
                        ->select('booking_reference_number', 'id')->get();
                     
                        $data = [];
            foreach ($allBookings as $bookings) {
                $data[$bookings->id] = $bookings->booking_reference_number;
            }
         
            return response()->json($data);
    }
    public function getPatientIds(Request $request)
    {
        $bookingDate = $request->input('booking_date');
    
        // Fetch booking IDs based on the booking date
        $bookingIds = Trn_Consultation_Booking::whereDate('booking_date', $bookingDate)
                            ->pluck('patient_id')
                            ->toArray();
    
        // Fetch patients for the booking IDs
        $patients = Mst_Patient::whereIn('id', $bookingIds)->get();
    
        return response()->json($patients);
    }
    
}
