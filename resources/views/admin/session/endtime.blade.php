@extends('layouts.backend')

@section('content')
    <div class="container">
        <div class="row">
            @include('admin.sidebar')

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">Set End Date & Time</div>
                    <div class="card-body">
                        <a href="{{ url('/admin/session') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                        <br />
                        <br />
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
                            
                        </div>
                        
                        <br />
                        <br />
                        {!! Form::model($session, [
                            'method' => 'PATCH',
                            'url' => ['/admin/session', $session->id],
                            'class' => 'form-horizontal'
                        ]) !!}
                        {!! Form::hidden('end_date_time_manual', 'END_MANUAL', ['class' => 'form-control', 'required' => 'required' , 'id' => 'end_date_time_manual']) !!}
                        
                        <div class="form-group{{ $errors->has('end_date_time') ? ' has-error' : ''}}">
                            <h2>{!! Form::label('end_date_time', 'End Date & Time: ', ['class' => 'control-label']) !!}</h2>
                            {!! Form::text('end_date_time', null, ['class' => 'form-control', 'required' => 'required' , 'id' => 'end_date_time']) !!}
                            {!! $errors->first('end_date_time', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div class="form-group">
                            {!! Form::submit('Set End Date and Time', ['class' => 'btn btn-primary']) !!}
                        </div>
                        {!! Form::close() !!}
                        <br />
                        <br />
                        @if ($errors->any())
                            <ul class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
