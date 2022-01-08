@extends('layouts.backend')

@section('content')
    <div class="container">
        <div class="row">
            @include('admin.sidebar')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">Session</div>
                    <div class="card-body">

                        <a href="{{ url('/admin/session') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                        <!--<a href="{{ url('/admin/session/' . $session->id . '/edit') }}" title="Edit Session"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>-->
                        @php
                            if(empty($session->end_date_time)){
                        @endphp
                        {!! Form::open([
                            'method' => 'PATCH',
                            'url' => ['/admin/session', $session->id],
                            'style' => 'display:inline'
                        ]) !!}
                            {!! Form::hidden('end_date_time', 'END', ['class' => 'form-control', 'required' => 'required' , 'id' => 'end_date_time']) !!}
                            {!! Form::button('<i class="fa fa-close" aria-hidden="true"></i> Close Session', array(
                                    'type' => 'submit',
                                    'class' => 'btn btn-danger btn-sm',
                                    'title' => 'Close Session',
                                    'onclick'=>'return confirm("Confirm Close?")'
                            ))!!}
                        {!! Form::close() !!}
                        @php
                            }
                            else{
                                $end_date_time_readable = date('d-m-Y h:i:s A' , strtotime($session->end_date_time));
                        @endphp
                        <button class="btn btn-danger btn-lg">This session is already closed @ {{ $end_date_time_readable }}</button>
                        @php
                            }
                        @endphp
                        <br/>
                        <br/>

                        <div class="table-responsive">
                            <div>
                                <h4><span style="padding-right: 50px;">Session Name:</span>
                                {{$session->session_name}}</h4>
                            </div>
                            <div>
                                <h4><span style="padding-right: 50px;">Unique Code:</span>
                                    <span style="color: #DD3333;">{{$session->unique_code}}</span></h4>
                            </div>
                            <div>
                            @php
                                $start_date_time_readable = date('d-m-Y h:i A' , strtotime($session->start_date_time));
                            @endphp
                                <h4><span style="padding-right: 50px;">Start time:</span>
                                <span style="color: #007bff;">{{$start_date_time_readable}}</span></h4>
                            </div>
                            <div>
                                <h4><span style="padding-right: 50px;">Kol Name:</span>
                                {{$kol_user->name}}</h4>
                            </div>
                            <div>
                                <h4><span style="padding-right: 50px;">Kol email:</span>
                                {{$kol_user->email}}</h4>
                            </div>
                            <br>
                            <div class="col-md-6 float-left">
                                <h5>Attendee QR:</h5>
                                <img  class="col-md-9" src="{{url( !empty($qrimage->qr_code_image) ? '/qrimages/'.$qrimage->qr_code_image: '')}}" alt="{{ $session->unique_code }}" />
                            </div>
                            <div class="col-md-6 float-right">
                                <h5>Url:</h5>
                                <div class="btn-secondary">{{url($qrimage->joining_url)}}</div>
                            </div>
                            
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
