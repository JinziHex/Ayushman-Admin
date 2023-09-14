@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><strong>Add New Slots</strong></h3>
            </div>
            <form action="{{ route('mastervalues.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="slot_name"><b>Slot Name*</b></label>
                            <input type="text" id="slot_name" required name="master_value" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div>
                           
                                   <button type="submit" class="btn btn-raised btn-primary">
                                   <i class="fa fa-check-square-o"></i>Submit
                                   </button>
        
                            <a class="btn btn-secondary ml-2" href="{{ route('timeslot.index') }}">
                                <i class="fa fa-times" aria-hidden="true"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif
        @if ($message = Session::get('error'))
            <div class="alert alert-danger">
                <p></p>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">List Timeslots</h3>
            </div>
            <div class="card-body">
                

                <div class="table-responsive">
                    <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                        <thead>
                            <tr>
                                <th class="wd-20p">SL.NO</th>
                                <th class="wd-15p">Slot</th>
                                <th class="wd-15p">Status</th>
                                <th class="wd-15p">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $i = 0;
                            @endphp
                            @foreach($timeslots as $timeslot)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $timeslot->master_value }}</td>
                                <td>
                                    <form action="{{ route('timeslot.changeStatus', $timeslot->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            onclick="return confirm('Do you want to Change status?');"
                                            class="btn btn-sm @if($timeslot->is_active == 0) btn-danger @else btn-success @endif">
                                            @if($timeslot->is_active == 0)
                                            InActive
                                            @else
                                            Active
                                            @endif
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <a class="btn btn-primary"
                                        href="{{ route('timeslot.edit', $timeslot->id) }}"><i
                                            class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit </a>
            
                                    <form style="display: inline-block"
                                        action="{{ route('timeslot.destroy', $timeslot->id) }}" method="post">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" onclick="return confirm('Do you want to delete it?');"
                                            class="btn btn-danger"><i class="fa fa-trash" aria-hidden="true"></i>Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
