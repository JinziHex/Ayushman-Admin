@php
    $userType = Auth::user()->user_type_id;
@endphp
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar">
  <div class="side-header">
    <a class="header-brand1" href="#">
      <img src="{{asset('assets/images/ayushman-logo.jpeg')}}" class="header-brand-img desktop-logo" alt="logo">
      <img src="{{asset('assets/images/ayushman-logo.jpeg')}}" class="header-brand-img toggle-logo" alt="logo">
      <img src="{{asset('assets/images/ayushman-logo.jpeg')}}" class="header-brand-img light-logo" alt="logo">
      <img src="{{ asset('assets/images/logo.png') }}" class="header-brand-img light-logo1" alt="logo"> </a><!-- LOGO -->
    <a aria-label="Hide Sidebar" class="app-sidebar__toggle ml-auto" data-toggle="sidebar" href="#"></a><!-- sidebar-toggle-->
  </div>
  <div class="app-sidebar__user">
    <div class="dropdown user-pro-body text-center">
      <div class="user-pic">
        <img src="{{ asset('assets/images/avatar.png') }}" alt="user-img" class="avatar-xl rounded-circle">
      </div>
      <div class="user-info">
        @switch($userType)
                    @case(1)
                        <span class="text-muted app-sidebar__user-name text-sm">Administrator</span>
                    @break

                    @case(96)
                        <span class="text-muted app-sidebar__user-name text-sm">Pharmacist</span>
                    @break

                    @case(18)
                        <span class="text-muted app-sidebar__user-name text-sm">Receptionist</span>
                        
                    @break
                    @case(20)
                        <span class="text-muted app-sidebar__user-name text-sm">Doctor</span>
                    @break
                    @case(21)
                        <span class="text-muted app-sidebar__user-name text-sm">Accountant</span>
                    @break

                    @default
                        <span class="text-muted app-sidebar__user-name text-sm">Administrator</span>
                    @break
                @endswitch
      </div>
    </div>
  </div>
  

  <ul class="side-menu">
    <li class="slide">
         @if (Auth::user()->user_type_id == 1)
                <a class="side-menu__item {{ Request::is('home') ? 'active' : '' }}" href="{{ route('home') }}">
                @elseif(Auth::user()->user_type_id == 96)
                    <a class="side-menu__item {{ Request::is('pharmacy-home') ? 'active' : '' }}"
                        href="{{ route('pharmacy.home') }}">
                    @elseif(Auth::user()->user_type_id == 18)
                        <a class="side-menu__item {{ Request::is('pharmacy-home') ? 'active' : '' }}"
                            href="{{ route('reception.home') }}">
                 @elseif(Auth::user()->user_type_id == 20)
                    <a class="side-menu__item {{ Request::is('doctor-home') ? 'active' : '' }}" href="{{ route('doctor.home') }}">
            @elseif(Auth::user()->user_type_id == 21)
                    <a class="side-menu__item {{ Request::is('accountant-home') ? 'active' : '' }}" href="{{ route('accountant.home') }}">
            @endif
        <i class="side-menu__icon ti-home"></i>
        <span class="side-menu__label">Dashboard</span>
      </a>
    </li>
    <!-- Other menu items -->
  </ul>

  <div class="container">
      @if(Auth::user()->user_type_id == 96)
      <li class="slide">
          <a class="side-menu__item" data-toggle="slide" href="#"><i
                  class="side-menu__icon ti-crown"></i><span
                  class="side-menu__label">{{ __('Masters') }}</span><i class="angle fa fa-angle-right"></i></a>
          <ul class="slide-menu">
              <li><a class="slide-item" href="{{ url('/medicine/index') }}">{{ __('Medicines') }}</a></li>
              <li><a class="slide-item" href="{{ route('supplier.index') }}">{{ __('Suppliers') }}</a></li>
          </li>
        </ul>
      <li class="slide">
      <a class="side-menu__item" data-toggle="slide" href="#">
        <i class="side-menu__icon ti-bar-chart"></i>
        <span class="side-menu__label"> {{ __('Inventory') }}</span><i class="angle fa fa-angle-right"></i>
      </a>
      <ul class="slide-menu">
       
      <li><a class="slide-item" href="{{ url('/medicine-purchase-invoice/index')}}">{{ __('Medicine Purchase Invoice') }}</a></li>
        <li><a class="slide-item" href="{{ url('/medicine-purchase-return/index')}}">{{ __('Medicine Purchase Return') }}</a></li>
        <li><a class="slide-item" href="{{ url('/medicine-stock-updation/index')}}">{{ __('Medicine Stock Correction') }}</a></li>
        <li><a class="slide-item" href="{{ url('/medicine-stock-updations') }}">{{ __('Initial Stock Update') }}</a></li> 
         <!--<li><a class="slide-item" href="{{ route('therapy-stock-transfers.index')}}">{{ __('Stock Transfer To Therapy') }}</a></li>-->
        <li><a class="slide-item" href="{{ url('/medicine-sales-invoices') }}">{{__('Medicine Sales Invoice')}}</a></li>
        <li><a class="slide-item" href="{{ url('/medicine-sales-return') }}">{{__('Medicine Sales Return')}}</a></li>
        <li><a class="slide-item" href="{{ url('/branch/stock-transfer') }}">{{ __('Stock Transfer To Pharmacy') }}</a></li> 
        </ul>
        </li>
            <li class="slide">
              <a class="side-menu__item {{ Request::is('prescriptions.index') ? 'active' : '' }}"
                  href="{{ route('prescriptions.index') }}">
                  <i class="fa-solid ti ti-file"></i>
                  <span class="side-menu__label">Prescriptions</span>
              </a>
          </li>
          <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <i class="side-menu__icon ti-bar-chart"></i>
                    <span class="side-menu__label"> {{ __('Reports') }}</span><i class="angle fa fa-angle-right"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ url('/sales-report') }}">{{ __('Sales Report') }}</a>
                    <li><a class="slide-item" href="{{ url('/sales-return-report') }}">{{ __('Sales Return Report') }}</a>
                    </li>
                    <li><a class="slide-item" href="{{ url('/purchase-report') }}">{{ __('Purchase Report') }}</a>
                    </li>
                    <li><a class="slide-item" href="{{ url('/purchase-return-report') }}">{{ __('Purchase Return Report') }}</a>
                    </li>
                     <li><a class="slide-item" href="{{ url('/stock-transfer-report') }}">{{ __('Stock Transfer Report') }}</a>
                    </li>
                    <li><a class="slide-item" href="{{ url('/current-stocks-report') }}">{{ __('Current Stocks Report') }}</a>
                    </li>
                </ul>
            </li>
          <li class="slide">
            <a class="side-menu__item" data-toggle="slide" href="#">
                <i class="side-menu__icon ti-settings"></i>
                <span class="side-menu__label"> {{ __('Settings') }}</span><i class="angle fa fa-angle-right"></i>
            </a>
            <ul class="slide-menu">
                <li><a class="slide-item" href="{{url('/profile')}}">{{ __('Profile') }}</a>
                </li>
                <li><a class="slide-item" href="{{url('/change-password')}}">{{ __('Change Password') }}</a>
                </li>
            </ul>
        </li>
        @elseif (Auth::user()->user_type_id == 18)
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#"><i
                        class="side-menu__icon ti-crown"></i><span
                        class="side-menu__label">{{ __('Masters') }}</span><i class="angle fa fa-angle-right"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ url('/patients/index') }}">{{ __('Patients') }}</a></li>
            </li>
            </ul>
            <!-- HRMS  -->

            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <i class="side-menu__icon fa fa-users"></i>
                    <span class="side-menu__label"> {{ __('HRMS') }}</span><i class="angle fa fa-angle-right"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item"
                            href="{{ route('staffleave.index') }}">{{ __('Staff Leave Marking') }}</a></li>
                    <li><a class="slide-item" href="{{ route('attendance.view') }}">{{ __('Attendance') }}</a></li>
            </li>
            </ul>
             <li class="slide">
            <a class="side-menu__item" data-toggle="slide" href="#">
                <i class="side-menu__icon fa fa-search"></i>
                <span class="side-menu__label">{{ __('Search') }}</span><i class="angle fa fa-angle-right"></i>
            </a>
            <ul class="slide-menu">
                <li><a class="slide-item" href="{{ url('/booking-search/index') }}">{{ __('Booking Search') }}</a></li>
                <li><a class="slide-item" href="{{ url('/patient-search/index') }}">{{ __('Patient Search') }}</a></li>
     
 
            </ul>
        </li>
             <li class="slide">
            <a class="side-menu__item" data-toggle="slide" href="#">
                <i class="side-menu__icon fa fa-edit"></i>
                <span class="side-menu__label">{{ __('Billing') }}</span><i class="angle fa fa-angle-right"></i>
            </a>
            <ul class="slide-menu">
                <li><a class="slide-item" href="{{ url('/consultation-bill/index') }}">{{ __('Consultation Billing') }}</a></li>
                <li><a class="slide-item" href="{{ url('/wellness-bill/index') }}">{{ __('Wellness Billing') }}</a></li>
                <li><a class="slide-item" href="{{ url('/therapy-bill/index') }}">{{ __('Therapy Billing') }}</a></li>
                <li><a class="slide-item" href="{{ url('/invoice-settlemnt/index') }}">{{ __('Invoice Settlement') }}</a></li>
     
 
            </ul>
            </li>
              <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <i class="side-menu__icon ti-layout"></i>
                    <span class="side-menu__label"> {{ __('Bookings') }}</span><i
                        class="angle fa fa-angle-right"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ url('/booking/consultation-booking') }}">{{ __('Consultation Booking') }}</a>
                    </li>
                    <li><a class="slide-item" href="{{ url('/booking/wellness-booking') }}">{{ __('Wellness Booking') }}</a>
                    </li>
                    <li><a class="slide-item" href="{{ url('/booking/therapy-booking') }}">{{ __('Therapy Booking') }}</a>
                    </li>
                    <li><a class="slide-item" href="{{ url('/booking/therapy-refund') }}">{{ __('Therapy Refund') }}</a>
                    </li>
                </ul>
            </li>
    
            
            <li class="slide">
                <a class="side-menu__item {{ Request::is('prescriptions.index') ? 'active' : '' }}"
                    href="{{ route('prescriptions.index') }}">
                    <i class="fa-solid ti ti-file"></i>
                    <span class="side-menu__label">Prescriptions</span>
                </a>
            </li>
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <i class="side-menu__icon ti-settings"></i>
                    <span class="side-menu__label"> {{ __('Settings') }}</span><i class="angle fa fa-angle-right"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ url('/profile') }}">{{ __('Profile') }}</a>
                    </li>
                    <li><a class="slide-item" href="{{ url('/change-password') }}">{{ __('Change Password') }}</a>
                    </li>
                </ul>
            </li>
            @elseif (Auth::user()->user_type_id == 20)  
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <i class="side-menu__icon ti-file"></i>
                    <span class="side-menu__label"> {{ __('Consultations') }}</span><i class="angle fa fa-angle-right"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ url('/doctor/consultation/index') }}">{{ __('Consultation Bookings') }}</a>
                    </li>
                    <li><a class="slide-item" href="{{ url('/doctor/consultation/history') }}">{{ __('Consultation History') }}</a>
                    </li>
                </ul>
            </li>
                <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <i class="side-menu__icon fa fa-users"></i>
                    <span class="side-menu__label"> {{ __('Employee') }}</span><i class="angle fa fa-angle-right"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ url('/doctor-leave/index') }}">{{ __('Leave Request') }}</a>
                    </li>
                    <li><a class="slide-item" href="{{ url('/doctor-attendance') }}">{{ __('Attendance View') }}</a>
                    </li>
                </ul>
            </li>
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <i class="side-menu__icon ti-settings"></i>
                    <span class="side-menu__label"> {{ __('Settings') }}</span><i class="angle fa fa-angle-right"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ url('/profile') }}">{{ __('Profile') }}</a>
                    </li>
                    <li><a class="slide-item" href="{{ url('/change-password') }}">{{ __('Change Password') }}</a>
                    </li>
                </ul>
            </li>

            
            @elseif (Auth::user()->user_type_id == 21)   <!--accountant  -->
                <li class="slide">
                  <a class="side-menu__item" data-toggle="slide" href="#"><i
                          class="side-menu__icon ti-crown"></i><span
                          class="side-menu__label">{{ __('Masters') }}</span><i class="angle fa fa-angle-right"></i></a>
                  <ul class="slide-menu">
                      <li><a class="slide-item" href="{{ route('supplier.index') }}">{{ __('Suppliers') }}</a></li>
                  </li>
                </ul>
                <li class="slide">
                  <a class="side-menu__item" data-toggle="slide" href="#">
                    <i class="side-menu__icon ti-bar-chart"></i>
                    <span class="side-menu__label"> {{ __('Inventory') }}</span><i class="angle fa fa-angle-right"></i>
                  </a>
                  <ul class="slide-menu">
                   
                  <li><a class="slide-item" href="{{ url('/medicine-purchase-invoice/index')}}">{{ __('Medicine Purchase Invoice') }}</a></li>
                    <li><a class="slide-item" href="{{ url('/medicine-purchase-return/index')}}">{{ __('Medicine Purchase Return') }}</a></li>
                    </ul>
                </li>
                <li class="slide">
                  <a class="side-menu__item" data-toggle="slide" href="#">
                    <i class="side-menu__icon ti-wallet"></i>
                    <span class="side-menu__label"> {{ __('Accounts') }}</span><i class="angle fa fa-angle-right"></i>
                  </a>
                  <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ url('/account-sub-group/index') }}">{{__('Account Sub Groups')}}</a></li>
                    <li><a class="slide-item" href="{{ url('/account-ledger/index') }}">{{__('Account ledger ')}}</a></li>
                    <li><a class="slide-item" href="{{ url('/journel-entry')}}">{{__('Journel Entry')}}</a></li>
                    <li><a class="slide-item" href="{{url('/income-expense/index')}}">{{__('Income/Expense')}}</a></li> 
                    <li><a class="slide-item" href="{{ url('/staff-cash-deposit/index') }}">{{ __('Staff Cash Deposit') }}</a></li>
                </li>
                </ul>
                 <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="#">
                        <i class="side-menu__icon fa fa-edit"></i>
                        <span class="side-menu__label">{{ __('Billing') }}</span><i class="angle fa fa-angle-right"></i>
                    </a>
                    <ul class="slide-menu">
                        <li><a class="slide-item" href="{{ url('/consultation-bill/index') }}">{{ __('Consultation Billing') }}</a></li>
                        <li><a class="slide-item" href="{{ url('/wellness-bill/index') }}">{{ __('Wellness Billing') }}</a></li>
                        <li><a class="slide-item" href="{{ url('/therapy-bill/index') }}">{{ __('Therapy Billing') }}</a></li>
                        <li><a class="slide-item" href="{{ url('/invoice-settlemnt/index') }}">{{ __('Invoice Settlement') }}</a></li>
             
         
                    </ul>
                </li>
                 <li class="slide">
                  <a class="side-menu__item" data-toggle="slide" href="#">
                    <i class="side-menu__icon fa fa-users"></i>
                    <span class="side-menu__label"> {{ __('HRMS') }}</span><i class="angle fa fa-angle-right"></i>
                  </a>
                  <ul class="slide-menu">
                   <li><a class="slide-item" href="{{ route('staffleave.index') }}">{{ __('Staff Leave Marking') }}</a></li>
                     <li><a class="slide-item" href="{{ route('attendance.view') }}">{{ __('Attendance') }}</a></li>
                    <li><a class="slide-item" href="{{ route('salary-processing.index') }}">{{ __('Salary Processing') }}</a></li>
                     <li><a class="slide-item" href="{{ route('advance-salary.index') }}">{{ __('Advance Salary') }}</a></li>
                     <li><a class="slide-item" href="{{route('branchTransfer.index')}}">{{__('Employee Branch Transfer')}}</a></li>
                     </ul>
                </li>
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <i class="side-menu__icon ti-settings"></i>
                    <span class="side-menu__label"> {{ __('Settings') }}</span><i class="angle fa fa-angle-right"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ url('/profile') }}">{{ __('Profile') }}</a>
                    </li>
                    <li><a class="slide-item" href="{{ url('/change-password') }}">{{ __('Change Password') }}</a>
                    </li>
                </ul>
            </li>
    @else
    
    <li class="slide">
      <a class="side-menu__item" data-toggle="slide" href="#"><i class="side-menu__icon ti-crown"></i><span class="side-menu__label">{{ __('Masters') }}</span><i class="angle fa fa-angle-right"></i></a>
      <ul class="slide-menu">
      <li><a class="slide-item" href="{{ url('/unit/index')}}">{{ __('Units') }}</a></li>
      <li><a class="slide-item" href="{{ route('medicine.dosage.index') }}">{{ __('Medicine Dosage') }}</a></li>
      <li><a class="slide-item" href="{{ url('/medicine/index') }}">{{ __('Medicines') }}</a></li>
      <li><a class="slide-item" href="{{ url('/timeslot')}}">{{ __('Timeslots') }}</a></li>
      <li><a class="slide-item" href="{{ route('leave.type.index') }}">{{ __('Leave Types') }}</a></li>
      <li><a class="slide-item" href="{{ url('/therapies/index')}}">{{ __('Therapy') }}</a></li>
      <li><a class="slide-item" href="{{ url('/therapyrooms/index') }}">{{ __('Therapy Rooms') }}</a></li>
      <li><a class="slide-item" href="{{ route('manufacturer.index') }}">{{ __('Manufacturers') }}</a></li>
      <li><a class="slide-item" href="{{ route('supplier.index')}}">{{ __('Suppliers') }}</a></li>
      <li><a class="slide-item" href="{{ url('/branches') }}">{{ __('Branches') }}</a></li>
      <li><a class="slide-item" href="{{ url('wellness/index')}}">{{ __('Wellness') }}</a></li>
      <li><a class="slide-item" href="{{ url('membership/index')}}">{{ __('Membership Packages') }}</a></li>
      <li><a class="slide-item" href="{{ url('/patients/index')}}">{{ __('Patients') }}</a></li>
      <li><a class="slide-item" href="{{ url('/pharmacy/index')}}">{{ __('Pharmacy') }}</a></li>
    <!-- <li><a class="slide-item" href="{{ url('/masters') }}">{{ __('Master Values') }}</a></li> -->
    <!-- <li><a class="slide-item" href="{{ route('leave.type.index') }}">{{ __('Leave Types') }}</a></li> --> 
    <!-- <li><a class="slide-item" href="{{ url('/staffs/index')}}">{{ __('Staffs') }}</a></li> -->
    {{-- <li><a class="slide-item" href="{{ url('/qualifications') }}">{{ __('Qualifications') }}</a></li> --}}
    <!-- <li><a class="slide-item" href="{{ url('/therapyrooms/index') }}">{{ __('Therapy Rooms') }}</a></li> -->
    <li><a class="slide-item" href="{{ url('/externaldoctors/index')}}">{{ __('External Doctors') }}</a></li>
    <!--<li><a class="slide-item" href="{{ url('/therapyroom-assigning/index')}}">{{ __('Therapy Room Assigning') }}</a></li>-->
    <!-- <li><a class="slide-item" href="{{ url('/user/index')}}">{{ __('Users') }}</a></li> -->
    <!-- <li><a class="slide-item" href="{{ url('/package/index')}}">{{ __('Salary Package') }}</a></li> -->
    </li>
    </ul>

    <!-- HRMS  -->

    <li class="slide">
      <a class="side-menu__item" data-toggle="slide" href="#">
        <i class="side-menu__icon fa fa-users"></i>
        <span class="side-menu__label"> {{ __('HRMS') }}</span><i class="angle fa fa-angle-right"></i>
      </a>
      <ul class="slide-menu">
      <!-- <a class="side-menu__item " href="{{route('branchTransfer.index')}}"><i class="side-menu__icon fa fa-users"></i></i><span class="side-menu__label">Employee Branch Transfer</span></a> -->
        <li><a class="slide-item" href="{{ url('/staffs/index')}}">{{ __('Staffs') }}</a></li>
        <li><a class="slide-item" href="{{ url('/salary/index')}}">{{ __('Salary Head') }}</a></li>
       <li><a class="slide-item" href="{{ route('staffleave.index') }}">{{ __('Staff Leave Marking') }}</a></li>
        <li><a class="slide-item" href="{{ route('holidays.index') }}">{{ __('Holidays') }}</a></li>
         <li><a class="slide-item" href="{{ route('attendance.view') }}">{{ __('Attendance') }}</a></li>
        <li><a class="slide-item" href="{{route('branchTransfer.index')}}">{{__('Employee Branch Transfer')}}</a></li>
        <li><a class="slide-item" href="{{route('availableleaves.index')}}">{{__('Employee Available Leaves')}}</a></li>
        <li><a class="slide-item"
                            href="{{ route('salary-processing.index') }}">{{ __('Salary Processing') }}</a>
                    </li>
        <li><a class="slide-item" href="{{ route('advance-salary.index') }}">{{ __('Advance Salary') }}</a></li>
         </ul>
    </li>


        <li class="slide">
            <a class="side-menu__item" data-toggle="slide" href="#">
                <i class="side-menu__icon fa fa-search"></i>
                <span class="side-menu__label">{{ __('Search') }}</span><i class="angle fa fa-angle-right"></i>
            </a>
            <ul class="slide-menu">
                <li><a class="slide-item" href="{{ url('/booking-search/index') }}">{{ __('Booking Search') }}</a></li>
                <li><a class="slide-item" href="{{ url('/patient-search/index') }}">{{ __('Patient Search') }}</a></li>
     
 
            </ul>
        </li>

<!-- Accounts  -->
    <li class="slide">
      <a class="side-menu__item" data-toggle="slide" href="#">
        <i class="side-menu__icon ti-wallet"></i>
        <span class="side-menu__label"> {{ __('Accounts') }}</span><i class="angle fa fa-angle-right"></i>
      </a>
      <ul class="slide-menu">
        <li><a class="slide-item" href="{{ url('/account-sub-group/index') }}">{{__('Account Sub Groups')}}</a></li>
        <li><a class="slide-item" href="{{ url('/account-ledger/index') }}">{{__('Account ledger ')}}</a></li>
        <!-- <li><a class="slide-item" href="{{ url('/tax/index')}}">{{ __('Taxes_') }}</a></li> -->
        <li><a class="slide-item" href="{{ url('/tax-group/index')}}">{{ __('Tax Groups') }}</a></li>
        <li><a class="slide-item" href="{{ url('/journel-entry')}}">{{__('Journel Entry')}}</a></li>
        <!-- <li><a class="slide-item" href="#">{{__('Attendence View-Biometric')}}</a></li> -->
        <li><a class="slide-item" href="{{url('/income-expense/index')}}">{{__('Income/Expense')}}</a></li> 
         <li><a class="slide-item" href="{{ url('/staff-cash-deposit/index') }}">{{ __('Staff Cash Deposit') }}</a></li>
    </li>
    </ul>
    <!-- <li class="slide">
      <a class="side-menu__item " href="{{route('branchTransfer.index')}}"><i class="side-menu__icon fa fa-users"></i></i><span class="side-menu__label">Employee Branch Transfer</span></a>
    </li> -->
        <li class="slide">
            <a class="side-menu__item" data-toggle="slide" href="#">
                <i class="side-menu__icon fa fa-edit"></i>
                <span class="side-menu__label">{{ __('Billing') }}</span><i class="angle fa fa-angle-right"></i>
            </a>
            <ul class="slide-menu">
                <li><a class="slide-item" href="{{ url('/consultation-bill/index') }}">{{ __('Consultation Billing') }}</a></li>
                <li><a class="slide-item" href="{{ url('/wellness-bill/index') }}">{{ __('Wellness Billing') }}</a></li>
                <li><a class="slide-item" href="{{ url('/therapy-bill/index') }}">{{ __('Therapy Billing') }}</a></li>
                <li><a class="slide-item" href="{{ url('/invoice-settlemnt/index') }}">{{ __('Invoice Settlement') }}</a></li>
     
 
            </ul>
        </li>

    {{-- <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">
          <i class="side-menu__icon ti-check-box"></i>
          <span class="side-menu__label"> {{ __('Settlements') }}</span><i class="angle fa fa-angle-right"></i>
    </a>
    <ul class="slide-menu">
      <li><a class="slide-item" href="#">{{__('Invoice Settlement')}}</a></li>
      </li>
    </ul> --}}


    {{-- <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">
          <i class="side-menu__icon ti-crown"></i>
          <span class="side-menu__label"> {{ __('Membership') }}</span><i class="angle fa fa-angle-right"></i>
    </a>
    <ul class="slide-menu">
      <li><a class="slide-item" href="#">{{__('Manage Patient Memberships')}}</a></li>
      </li>
    </ul> --}}


    {{-- <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">
          <i class="side-menu__icon ti-comment"></i>
          <span class="side-menu__label"> {{ __('Message Center') }}</span>
    </a>
    </li>
    --}}
    {{-- <li class="slide">
             <a class="side-menu__item " href="{{route('booking_type.index')}}"><i class="side-menu__icon fa fa-users"></i></i><span class="side-menu__label">Consultation</span></a>
    </li>


    <li class="slide">
      <a class="side-menu__item " href="{{route('booking_type.wellnessIndex')}}"><i class="side-menu__icon ti-heart"></i></i><span class="side-menu__label">Wellness</span></a>
    </li> --}}

    {{-- <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">
          <i class="side-menu__icon fa fa-heart"></i>
          <span class="side-menu__label"> {{ __('Therapy') }}</span><i class="angle fa fa-angle-right"></i>
    </a>
    <ul class="slide-menu">
      <li><a class="slide-item" href="{{route('booking_type.therapyIndex')}}">{{__('Manage Therapy')}}</a></li>
      <li><a class="slide-item" href="#">{{__('Therapy Refund')}}</a></li>
      </li>
    </ul> --}}

    {{-- <li class="slide">
             <a class="side-menu__item " href="{{route('patient_search.index')}}"><i class="side-menu__icon fa fa-users"></i></i><span class="side-menu__label">Patients</span></a>
    </li> --}}

    {{-- <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">
          <i class="side-menu__icon ti-id-badge"></i>
          <span class="side-menu__label"> {{ __('Staffs') }}</span><i class="angle fa fa-angle-right"></i>
    </a>
    <ul class="slide-menu">
      <li><a class="slide-item" href="#">{{__('Staff Attendence View')}}</a></li>
      <li><a class="slide-item" href="#">{{__('Staff Leaves')}}</a></li>
      <li><a class="slide-item" href="#">{{__('Staff Leave Marking')}}</a></li>
      <li><a class="slide-item" href="#">{{__('Advance Salary')}}</a></li>
      <li><a class="slide-item" href="#">{{__('Salary Processing')}}</a></li>
      <li><a class="slide-item" href="#">{{__('Staff Cash Deposit')}}</a></li>
      <li><a class="slide-item" href="#">{{__('Employee Branch Transfer')}}</a></li>
      </li>
    </ul> --}}

    {{-- <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">
          <i class="side-menu__icon ti-package"></i>
          <span class="side-menu__label"> {{ __('Suppliers') }}</span><i class="angle fa fa-angle-right"></i>
    </a>
    <ul class="slide-menu">
      <li><a class="slide-item" href="{{route('supplier.index')}}">{{__('Manage Suppliers')}}</a></li>
      <li><a class="slide-item" href="#">{{__('Stock Transfer to Other Branches')}}</a></li>
      </li>
    </ul> --}}

    {{-- <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">
          <i class="side-menu__icon ti-credit-card"></i>
          <span class="side-menu__label"> {{ __('Purchase') }}</span><i class="angle fa fa-angle-right"></i>
    </a>
    <ul class="slide-menu">
      <li><a class="slide-item" href="#">{{__('Medicine Purchase')}}</a></li>
      <li><a class="slide-item" href="#">{{__('Purchase Return')}}</a></li>
      <li><a class="slide-item" href="#">{{__('Medicine Stock Correction')}}</a></li>
      </li>
    </ul> --}}


    {{-- <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">
          <i class="side-menu__icon ti-bar-chart"></i>
          <span class="side-menu__label"> {{ __('Sales') }}</span><i class="angle fa fa-angle-right"></i>
    </a>
    <ul class="slide-menu">
      <li><a class="slide-item" href="#">{{__('Medicine Sales')}}</a></li>
      <li><a class="slide-item" href="#">{{__('Sales Return')}}</a></li>
      <!--<li><a class="slide-item" href="#">{{__('Medicine Stock Transfer To Therapy')}}</a></li>-->
      <li><a class="slide-item" href="#">{{__('Prescription Printing')}}</a></li>
      </li>
    </ul> --}}

    <!-- Purchase Details  -->
    <li class="slide">
      <a class="side-menu__item" data-toggle="slide" href="#">
        <i class="side-menu__icon ti-bar-chart"></i>
        <span class="side-menu__label"> {{ __('Inventory') }}</span><i class="angle fa fa-angle-right"></i>
      </a>
      <ul class="slide-menu">
       
      <li><a class="slide-item" href="{{ url('/medicine-purchase-invoice/index')}}">{{ __('Medicine Purchase Invoice') }}</a></li>
        <li><a class="slide-item" href="{{ url('/medicine-purchase-return/index')}}">{{ __('Medicine Purchase Return') }}</a></li>
        <li><a class="slide-item" href="{{ url('/medicine-stock-updation/index')}}">{{ __('Medicine Stock Correction') }}</a></li>
        <li><a class="slide-item" href="{{ url('/medicine-stock-updations') }}">{{ __('Initial Stock Update') }}</a></li> 
        <!--<li><a class="slide-item" href="{{ route('therapy-stock-transfers.index')}}">{{ __('Stock Transfer To Therapy') }}</a></li>-->
        <li><a class="slide-item" href="{{ url('/medicine-sales-invoices') }}">{{__('Medicine Sales Invoice')}}</a></li>
        <li><a class="slide-item" href="{{ url('/medicine-sales-return') }}">{{__('Medicine Sales Return')}}</a></li>
         <li><a class="slide-item" href="{{ url('/branch/stock-transfer') }}">{{ __('Stock Transfer To Pharmacy') }}</a></li> 
        </ul>
    </li>
    <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <i class="side-menu__icon ti-layout"></i>
                    <span class="side-menu__label"> {{ __('Bookings') }}</span><i
                        class="angle fa fa-angle-right"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ url('/booking/consultation-booking') }}">{{ __('Consultation Booking') }}</a>
                    </li>
                    <li><a class="slide-item" href="{{ url('/booking/wellness-booking') }}">{{ __('Wellness Booking') }}</a>
                    </li>
                    <li><a class="slide-item" href="{{ url('/booking/therapy-booking') }}">{{ __('Therapy Booking') }}</a>
                    </li>
                    <li><a class="slide-item" href="{{ url('/booking/therapy-refund') }}">{{ __('Therapy Refund') }}</a>
                    </li>
                </ul>
            </li>
    
    
     <li class="slide">
      <a class="side-menu__item {{ Request::is('prescriptions.index') ? 'active' : '' }}" href="{{ route('prescriptions.index') }}">
        <i class="fa-solid ti ti-file"></i>
        <span class="side-menu__label">Prescriptions</span>
      </a>
    </li>
    
     <li class="slide">
              <a class="side-menu__item" data-toggle="slide" href="#">
                  <i class="side-menu__icon ti-world"></i>
                  <span class="side-menu__label"> {{ __('General') }}</span><i
                      class="angle fa fa-angle-right"></i>
              </a>
              <ul class="slide-menu">
                  <li><a class="slide-item"
                          href="{{ url('/patient/feedback/index') }}">{{ __('Feedbacks') }}</a>
                  </li>
              </ul>
          </li>
          <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <i class="side-menu__icon ti-bar-chart"></i>
                    <span class="side-menu__label"> {{ __('Reports') }}</span><i class="angle fa fa-angle-right"></i>
                </a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{url('/sales-report')}}">{{ __('Sales Report') }}</a>
                    </li>
                    <li><a class="slide-item" href="{{ url('/sales-return-report') }}">{{ __('Sales Return Report') }}</a>
                    </li>
                    <li><a class="slide-item" href="{{ url('/purchase-report') }}">{{ __('Purchase Report') }}</a>
                    </li>
                    <li><a class="slide-item" href="{{ url('/purchase-return-report') }}">{{ __('Purchase Return Report') }}</a>
                    </li>
                     <li><a class="slide-item" href="{{ url('/stock-transfer-report') }}">{{ __('Stock Transfer Report') }}</a>
                    </li>
                    <li><a class="slide-item" href="{{ url('/current-stocks-report') }}">{{ __('Current Stocks Report') }}</a>
                    </li>
                    <li><a class="slide-item" href="{{ url('/payment-made-report') }}">{{ __('Payment Made Report') }}</a>
                    </li>
                     <li><a class="slide-item" href="{{ url('/payable-report') }}">{{ __('Payable Report') }}</a>
                    </li>
                    <li><a class="slide-item" href="{{ url('/ledger-report') }}">{{ __('Ledger Report') }}</a>
                    </li>
                     <li><a class="slide-item" href="{{ url('/trail-balance-report') }}">{{ __('Trail Balance Report') }}</a>
                    </li>
                     <li><a class="slide-item" href="{{ url('/profit-loss-report') }}">{{ __('Profit Loss Report') }}</a>
                    </li>
                    </li>
                     <li><a class="slide-item" href="{{ url('/payment-received-report') }}">{{ __('Payment Received Report') }}</a>
                    </li>
                </ul>
            </li>
          <li class="slide">
            <a class="side-menu__item" data-toggle="slide" href="#">
                <i class="side-menu__icon ti-settings"></i>
                <span class="side-menu__label"> {{ __('Settings') }}</span><i class="angle fa fa-angle-right"></i>
            </a>
            <ul class="slide-menu">
                <li><a class="slide-item" href="{{ url('/application-settings') }}">{{ __('Application Settings') }}</a>
                    </li>
                <li><a class="slide-item" href="{{url('/profile')}}">{{ __('Profile') }}</a>
                </li>
                <li><a class="slide-item" href="{{url('/change-password')}}">{{ __('Change Password') }}</a>
                </li>
            </ul>
        </li>
    
    @endif

    {{-- <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">
          <i class="side-menu__icon ti-settings"></i>
          <span class="side-menu__label"> {{ __('Settings') }}</span><i class="angle fa fa-angle-right"></i>
    </a>
    <ul class="slide-menu">
      <li><a class="slide-item" href="#">{{__('Manage Admin Settings')}}</a></li>
      </li>
    </ul> --}}


    {{-- <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">
          <i class="side-menu__icon ti ti-file"></i>
          <span class="side-menu__label"> {{ __('Reports') }}</span><i class="angle fa fa-angle-right"></i>
    </a>
    <ul class="slide-menu">
      <li><a class="slide-item" href="#">{{__('Sales Report')}}</a></li>
      <li><a class="slide-item" href="#">{{__('Purchase Report')}}</a></li>
      <li><a class="slide-item" href="#">{{__('Stock Transfer Report')}}</a></li>
      <li><a class="slide-item" href="#">{{__('Current Stock Report')}}</a></li>
      <li><a class="slide-item" href="#">{{__('Payment Received Report')}}</a></li>
      <li><a class="slide-item" href="#">{{__('Payment Made Report')}}</a></li>
      <li><a class="slide-item" href="#">{{__('Receivable Report')}}</a></li>
      <li><a class="slide-item" href="#">{{__('Payable Report')}}</a></li>
      <li><a class="slide-item" href="#">{{__('Ledger Report')}}</a></li>
      <li><a class="slide-item" href="#">{{__('Profit and Loss Report')}}</a></li>
      <li><a class="slide-item" href="#">{{__('Trail Balance Report')}}</a></li>
      <li><a class="slide-item" href="#">{{__('Balance Sheet Report')}}</a></li>

      </li>
    </ul> --}}

    {{-- <li class="slide">
        <a class="side-menu__item"  data-toggle="slide" href="#"><i class="side-menu__icon fa fa-user"></i><span class="side-menu__label">{{ __('Profile') }}</span><i class="angle fa fa-angle-right"></i></a>
    <ul class="slide-menu">
      <li><a class="slide-item" href="#">{{ __('Update Profile') }}</a></li>
      <li><a class="slide-item" href="#">{{ __('Change Password') }}</a></li>
      <li><a class="slide-item" href="{{route('logout')}}">{{ __('Logout') }}</a></li>

      </li>
    </ul> --}}
  </div>
</aside>