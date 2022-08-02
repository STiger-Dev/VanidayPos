@extends('layouts.guest')
@section('content')
@include('layouts.partials.javascripts')
<style type="text/css">
    .content {
        padding-top: 50px;
    }
    button#take_attendance {
        width: 100%;
    }
    .bg-overlay {
        position: absolute;
        background-color: rgba(0,0,0,0.5);
        z-index: 10;
        display: none;
    }
</style>

<div class="bg-overlay">
</div>
<section class="content">
    <div class="container-fluid content">
        <h1>{{$location_info['name']}}</h1>
        @if (!empty($employee_info))
            <h3>{{$employee_info['surname']}} {{$employee_info['first_name']}} {{$employee_info['last_name']}}</h3>
        @endif
        {!! Form::hidden('employee_id', Cookie::get('employee_id'), ['id' => 'employee_id_val']); !!}
        @if(Cookie::get('employee_id') == '')
            <div class="form-group">
                {!! Form::label('name',__('business.employee_id') . ':*') !!}
                {!! Form::text('name', '', ['class' => 'form-control employee_id', 'required',
                'placeholder' => __('business.employee_id')]); !!}
            </div>
        @endif
        <div class="row">
            @if (empty($employee_info))
                <div class="col-md-12 text-center">
                    <button 
                        type="button" 
                        id="register_employee_id"
                        class="btn btn-app bg-blue"
                        >
                        <i class="fas fa-user-plus"></i> @lang('essentials::lang.register_employee_id')
                    </button>                
                </div>                
            @else
                <div class="col-md-12 text-center">
                    <button 
                        type="button" 
                        id="clock_in_btn"
                        class="btn btn-app bg-blue 
                            @if(!empty($clock_in))
                                hide
                            @endif
                        "
                        >
                        <i class="fas fa-arrow-circle-down"></i> @lang('essentials::lang.clock_in')
                    </button>
                    <button 
                        type="button" 
                        id="clock_out_btn"
                        class="btn btn-app bg-yellow
                            @if(empty($clock_in))
                                hide
                            @endif
                        "  
                        >
                        <i class="fas fa-hourglass-half fa-spin"></i> @lang('essentials::lang.clock_out')
                    </button>
                    @if(!empty($clock_in))
                        <br>
                        <small class="text-muted">@lang('essentials::lang.clocked_in_at'): {{@format_datetime($clock_in->clock_in_time)}}</small>
                    @endif
                </div>
            @endif
        </div>
    </div>
</section>
@stop
@section('javascript')
<script type="text/javascript">
    var latitude, longitude;

    $('.employee_id').on('input', function() {
        $('#employee_id_val').val($('.employee_id').val());
    });

    $('#clock_in_btn, #clock_out_btn').click( function() {
        showLoading ();
        getLocation();
    });

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(submitEmployee, showError);
        } else {
            swal({
                text: "Failed to get geolocation.",
                icon: 'error'
            });
        }
    }

    function showError(error) {
        switch(error.code) {
            case error.PERMISSION_DENIED:
                swal({
                    text: "User denied the request for Geolocation.",
                    icon: 'error'
                });
            break;
            case error.POSITION_UNAVAILABLE: 
                swal({
                    text: "Location information is unavailable.",
                    icon: 'error'
                });
            break;
            case error.TIMEOUT:
                swal({
                    text: "The request to get user location timed out.",
                    icon: 'error'
                });
            break;
            case error.UNKNOWN_ERROR:
                swal({
                    text: "An unknown error occurred.",
                    icon: 'error'
                });
            break;
        }
    }

    function submitEmployee(position) {
        var employee_id = $('#employee_id_val').val();
        var is_clock_in = {{empty($clock_in) ? 1 : 0}};
        var location_id = {{$location_id}};
        var attendance_id = {{isset($clock_in['id']) ? $clock_in['id']: 0}};

        if (employee_id.length == 0) {
            swal({
                text: "Please input employee Id.",
                icon: 'error'
            });

            return;
        }

        var data = {
            latitude: position.coords.latitude,
            longitude: position.coords.longitude,
            employee_id: employee_id,
            is_clock_in: is_clock_in,
            location_id: location_id,
            attendance_id: attendance_id
        };

        $.ajax({
            method: 'post',
            url: "{{ route('business.submitAttendance') }}",
            data: data,
            dataType: 'json',
            success: function(result) {
                if (result.success) {
                    swal({
                        text: result.msg,
                        icon: 'success'
                    }).then(() => {
                        disableLoading();
                        location.reload();
                    });
                } else {
                    swal({
                        text: result.msg,
                        icon: 'failed'
                    }).then(() => {
                        disableLoading();
                        location.reload();
                    });
                }
            },
            fail: function(xhr, textStatus, errorThrown){
                swal({
                    text: xhr,
                    icon: 'failed'
                }).then(() => {
                    disableLoading();
                    location.reload();
                });
            }
        });
    }

    function showLoading () {
        $(".bg-overlay").height($( document ).height());
        $(".bg-overlay").width($( document ).width());
        $(".bg-overlay").show();
        var loadingTop = $(window).scrollTop() + $(window).innerHeight()/2;
        var loadingLeft = $(window).innerWidth()/2;
        $(".loading-bar").css({'top' : loadingTop + 'px', position: 'absolute', left: loadingLeft});
    }

    function disableLoading() {
        $(".bg-overlay").height(0);
        $(".bg-overlay").width(0);
        $(".bg-overlay").hide();
    }

    $("#register_employee_id").click(function () {
        var employee_id = $('#employee_id_val').val();
        var data = {
            employee_id: employee_id
        };

        $.ajax({
            method: 'post',
            url: "{{ route('business.registerEmployeeId') }}",
            data: data,
            dataType: 'json',
            success: function(result) {
                if (result.success) {
                    swal({
                        text: result.msg,
                        icon: 'success'
                    }).then(() => {
                        disableLoading();
                        location.reload();
                    });
                } else {
                    swal({
                        text: result.msg,
                        icon: 'failed'
                    }).then(() => {
                        disableLoading();
                        location.reload();
                    });
                }
            },
            fail: function(xhr, textStatus, errorThrown){
                swal({
                    text: xhr,
                    icon: 'failed'
                }).then(() => {
                    disableLoading();
                    location.reload();
                });
            }
        });
    })
</script>
@endsection