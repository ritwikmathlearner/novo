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
<!--                        {!! Form::open([
                            'method' => 'DELETE',
                            'url' => ['/admin/session', $session->id],
                            'style' => 'display:inline'
                        ]) !!}
                            {!! Form::button('<i class="fa fa-trash-o" aria-hidden="true"></i> Delete', array(
                                    'type' => 'submit',
                                    'class' => 'btn btn-danger btn-sm',
                                    'title' => 'Delete Session',
                                    'onclick'=>'return confirm("Confirm delete?")'
                            ))!!}
                        {!! Form::close() !!}-->
                        <br/>
                        <br/>

                        <div class="table-responsive">
                            <div>
                                <h4><span style="padding-right: 50px;">Session Name:</span>
                                {{$session->session_name}}</h4>
                            </div>
                            <div>
                                <h4><span style="padding-right: 50px;">Unique Code:</span>
                                {{$session->unique_code}}</h4>
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
