<div class="form-group{{ $errors->has('users') ? ' has-error' : ''}}">
    {!! Form::label('user', 'Kol User: ', ['class' => 'control-label']) !!}
    {!! Form::select('user_id', $users, $users, ['class' => 'form-control', 'multiple' => false]) !!}
</div>
<div class="form-group{{ $errors->has('name') ? ' has-error' : ''}}">
    {!! Form::label('session_name', 'Session Name: ', ['class' => 'control-label']) !!}
    {!! Form::text('session_name', null, ['class' => 'form-control', 'required' => 'required']) !!}
    {!! $errors->first('session_name', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group{{ $errors->has('start_date_time') ? ' has-error' : ''}}">
    {!! Form::label('start_date_time', 'Start Date & Time: ', ['class' => 'control-label']) !!}
    {!! Form::text('start_date_time', null, ['class' => 'form-control', 'required' => 'required' , 'id' => 'start_date_time']) !!}
    {!! $errors->first('start_date_time', '<p class="help-block">:message</p>') !!}
</div>

<!--<div class="form-group{{ $errors->has('email') ? ' has-error' : ''}}">
    {!! Form::label('email', 'Email: ', ['class' => 'control-label']) !!}
    {!! Form::email('email', null, ['class' => 'form-control', 'required' => 'required']) !!}
    {!! $errors->first('email', '<p class="help-block">:message</p>') !!}
</div>-->
<!--<div class="form-group{{ $errors->has('password') ? ' has-error' : ''}}">
    {!! Form::label('password', 'Password: ', ['class' => 'control-label']) !!}
    @php
        $passwordOptions = ['class' => 'form-control'];
        if ($formMode === 'create') {
            $passwordOptions = array_merge($passwordOptions, ['required' => 'required']);
        }
    @endphp
    {!! Form::password('password', $passwordOptions) !!}
    {!! $errors->first('password', '<p class="help-block">:message</p>') !!}
</div>-->
<div class="form-group">
    {!! Form::submit($formMode === 'edit' ? 'Update' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>
