@php
    $layout = (auth()->user()->role == 'admin') ? 'layouts.master' : 'employee.master';
@endphp
@extends($layout)

@section('title', 'Time Card')

@section('style')
@endsection

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm">
                <a class="opacity-3 text-dark" href="javascript:;">
                    <svg width="12px" height="12px" class="mb-1" viewBox="0 0 45 40" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                        <title>shop </title>
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <g transform="translate(-1716.000000, -439.000000)" fill="#252f40" fill-rule="nonzero">
                                <g transform="translate(1716.000000, 291.000000)">
                                    <g transform="translate(0.000000, 148.000000)">
                                        <path d="M46.7199583,10.7414583 L40.8449583,0.949791667 C40.4909749,0.360605034 39.8540131,0 39.1666667,0 L7.83333333,0 C7.1459869,0 6.50902508,0.360605034 6.15504167,0.949791667 L0.280041667,10.7414583 C0.0969176761,11.0460037 -1.23209662e-05,11.3946378 -1.23209662e-05,11.75 C-0.00758042603,16.0663731 3.48367543,19.5725301 7.80004167,19.5833333 L7.81570833,19.5833333 C9.75003686,19.5882688 11.6168794,18.8726691 13.0522917,17.5760417 C16.0171492,20.2556967 20.5292675,20.2556967 23.494125,17.5760417 C26.4604562,20.2616016 30.9794188,20.2616016 33.94575,17.5760417 C36.2421905,19.6477597 39.5441143,20.1708521 42.3684437,18.9103691 C45.1927731,17.649886 47.0084685,14.8428276 47.0000295,11.75 C47.0000295,11.3946378 46.9030823,11.0460037 46.7199583,10.7414583 Z"></path>
                                        <path d="M39.198,22.4912623 C37.3776246,22.4928106 35.5817531,22.0149171 33.951625,21.0951667 L33.92225,21.1107282 C31.1430221,22.6838032 27.9255001,22.9318916 24.9844167,21.7998837 C24.4750389,21.605469 23.9777983,21.3722567 23.4960833,21.1018359 L23.4745417,21.1129513 C20.6961809,22.6871153 17.4786145,22.9344611 14.5386667,21.7998837 C14.029926,21.6054643 13.533337,21.3722507 13.0522917,21.1018359 C11.4250962,22.0190609 9.63246555,22.4947009 7.81570833,22.4912623 C7.16510551,22.4842162 6.51607673,22.4173045 5.875,22.2911849 L5.875,44.7220845 C5.875,45.9498589 6.7517757,46.9451667 7.83333333,46.9451667 L19.5833333,46.9451667 L19.5833333,33.6066734 L27.4166667,33.6066734 L27.4166667,46.9451667 L39.1666667,46.9451667 C40.2482243,46.9451667 41.125,45.9498589 41.125,44.7220845 L41.125,22.2822926 C40.4887822,22.4116582 39.8442868,22.4815492 39.198,22.4912623 Z"></path>
                                    </g>
                                </g>
                            </g>
                        </g>
                    </svg>
                </a>
            </li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Time Card</li>
        </ol>
        <h6 class="font-weight-bolder mb-0">Time Card</h6>
    </nav>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <!-- Card header -->
                <div class="card-header pb-0">
                    <div class="d-lg-flex">
                        <div>
                            <h5 class="mb-0">Time Card</h5>
                            <p class="text-sm mb-0">

                            </p>
                        </div>
                        <div class="ms-auto my-auto mt-lg-0 mt-4">
                            <div class="ms-auto my-auto">
                                <a href="{{route('employee.home')}}" class="btn bg-gradient-primary btn-sm mb-0"> Return to Dashboard </a>
                            </div>
                        </div>
                        <div class="ms-auto my-auto mt-lg-0 mt-4">
                            <div class="ms-auto my-auto">
                                <span class="p-2" style="background-color: rgba(0,255,0,0.3);">Holiday</span>
                                <span class="p-2" style="background-color: rgba(255,255,0,0.5);">Leave</span>
                                <span class="p-2" style="background-color: rgba(255,0,0,0.3);">Exception</span>
                                <span class="p-2" style="background-color: rgba(1,0,0,0.2);">Exception Fixed</span>
                                <table class="border text-sm mt-3 w-100" style="min-width: 150px;">
                                    <tbody class="">
                                    <tr class="border-bottom">
                                        <td>Total Hours</td>
                                        <td>{{$leave_hours+$net_hours+$holiday_hours ?? '0'}}</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <td>Net Hours</td>
                                        <td>{{$net_hours ?? '0'}}</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <td>Leave Hours</td>
                                        <td>{{$leave_hours ?? '0'}}</td>
                                    </tr>
                                    @if($nleave_hours > 0)
                                        <tr class="border-bottom">
                                            <td>Unapproved <br/>Leave Hours</td>
                                            <td>{{$nleave_hours ?? '0'}}</td>
                                        </tr>
                                    @endif
                                    <tr class="border-bottom">
                                        <td>Holiday Hours</td>
                                        <td>{{$holiday_hours ?? '0'}}</td>
                                    </tr>
                                    <tr class="border-bottom d-none">
                                        <td>Short Hours</td>
                                        <td>{{$short_hours ?? '0'}}</td>
                                    </tr>
                                    <tr>
                                        <td>Unpaid Hours</td>
                                        <td>{{$unpaid_hours ?? '0'}}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-0">
                    <div class="px-3 mt-lg-n6">
                        <h5>Name :- {{$data->user->name ?? ''}}</h5>
                        <h5>Date &nbsp; :- {{\Carbon\Carbon::parse($startDate)->format('d-M-Y')}} - {{\Carbon\Carbon::parse($endDate)->format('d-M-Y')}}</h5>
                        @if(auth()->user()->role == 'admin' || auth()->user()->role == 'hr')
                        <a href="{{route('export.timecard',['user_id' => $data->user->clockify_id,'week' => $week])}}" class="btn bg-gradient-primary btn-sm mb-0"> Export </a>
                        @endif
                    </div>
                    <div class="table-responsive p-3">
                        <form id="add_form" method="POST" action="{{route('employee.timecard.review', ['week' => $week])}}">
                            @csrf
                            <table class="table table-flush" id="datatable">
                                <thead class="thead-light text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                <tr>
                                    <td>Date</td>
                                    <td>Flags</td>
                                    <td>OT Hours</td>
                                    <td>Net Hours</td>
                                    <td>Employee Remarks</td>
                                    <td>Approver Remarks</td>
                                </tr>
                                </thead>
                                <tbody class="text-xs">
                                @foreach($rows as $k=>$row)
                                    @php
                                        $dt = \Carbon\Carbon::now();
                                        $ot_hours = $dt->diffInHours($dt->copy()->addSeconds($row->ot_hours));
                                        $ot_minutes = $dt->diffInMinutes($dt->copy()->addSeconds($row->ot_hours)->subHours($ot_hours));
                                        $net_hours2 = $dt->diffInHours($dt->copy()->addSeconds($row->net_hours));
                                        $net_minutes = $dt->diffInMinutes($dt->copy()->addSeconds($row->net_hours)->subHours($net_hours2));
                                        $is_holiday = is_holiday($row->date);
                                        $is_leave = leave_count($row->user_id, $row->date, $row->date, null, null, null);
                                    @endphp
                                    <tr style="@if($is_holiday > 0) background-color: rgba(0,255,0,0.3); @elseif($is_leave > 0) background-color: rgba(255,255,0,0.5); @elseif($row->exception == 1 && empty($row->flags)) background-color: rgba(1,0,0,0.2); @elseif($row->exception == 1) background-color: rgba(255,0,0,0.3); @endif">
                                        <td>{{$row->date}}</td>
                                        <td>{!! $row->flags !!}</td>
                                        <td>{{$ot_hours}}:{{$ot_minutes}}</td>
                                        <td>{{$net_hours2}}:{{$net_minutes}}</td>
                                        <td>{!! $row->employee_remarks !!}</td>
                                        <td>
                                            <input type="hidden" name="remarks[{{$k}}][id]" value="{{$row->id}}"/>
                                            <input type="hidden" name="remarks[{{$k}}][date]" value="{{$row->date}}"/>
                                            <textarea name="remarks[{{$k}}][remarks]">{{$row->approver_remarks}}</textarea>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="text-center">
                                <input type="hidden" name="start_time" value="{{$startDate}}"/>
                                <input type="hidden" name="end_time" value="{{$endDate}}"/>
                                <input type="hidden" name="week" value="{{$week}}"/>
                                <input type="hidden" name="user_id" value="{{$data->user->clockify_id}}"/>
                                <input type="hidden" name="status" id="status" value="Approved"/>
                                <input type="submit" value="Revise and Resubmit" id="submit_button" class="btn btn-danger btn-sm"/>
                                <input type="submit" value="Approved" id="submit_button" class="btn btn-success btn-sm"/>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!--  Datatable JS  -->
    <script src="{{asset('assets/js/plugins/datatables.js')}}"></script>
    <script>
        $(document).ready(function (){
            var datatable = $('#datatable').DataTable({
                dom: 'f',
                'ordering': false,
            });

            $(":submit").click(function() {
                $('#status').val($(this).val());
            });

            $('#add_form').submit(function (e) {
                e.preventDefault();
                var status = $('#status').val();
                var net_hours = '{{$total_net_hours ?? 0}}';
                var weekly_hours = '{{ setting('weekly_hours') }}';
                console.log(net_hours);
                console.log(weekly_hours);
                var titleText = 'Are you sure?';
                var msgText = "Once you "+status+" you cannot make changes.";
                var buttonText = 'Yes, '+status+'!';
                if(net_hours > weekly_hours && status=='Approved') {
                    titleText = 'Oops!';
                    msgText = 'The employee working hours are greater then '+weekly_hours+' hours.';
                    buttonText = 'Yes, '+status+'!';
                } else {
                    var titleText = 'Are you sure?';
                    var msgText = "Once you "+status+" you cannot make changes.";
                    var buttonText = 'Yes, '+status+'!';
                }
                Swal.fire({
                    title: titleText,
                    text: msgText,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: buttonText
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                })
            });
        });
    </script>
@endsection
