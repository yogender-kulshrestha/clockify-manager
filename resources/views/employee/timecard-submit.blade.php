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
                                <a href="{{route('employee.timecard', ['week' => $week])}}" class="btn bg-gradient-primary btn-sm mb-0"> Back to Edit </a>
                                <table class="border text-sm mt-3 w-100" style="min-width: 150px;">
                                    <tbody class="">
                                    <tr class="border-bottom">
                                        <td>Total Hours</td>
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
                    <div class="px-3 mt-n6">
                        <h5>Name :- {{auth()->user()->name ?? ''}}</h5>
                        <h5>Date &nbsp; :- {{\Carbon\Carbon::parse($startDate)->format('d-M-Y')}} - {{\Carbon\Carbon::parse($endDate)->format('d-M-Y')}}</h5>
                    </div>
                    <div class="table-responsive p-3">
                        <table class="table table-flush" id="datatable">
                            <thead class="thead-light text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                            <tr>
                                <td>Date</td>
                                <td>Flags</td>
                                <td>OT Hours</td>
                                <td>Net Hours</td>
                                <td>Employee Remarks</td>
                                <td>Approver Remarks</td>
                                {{--<td>Action</td>--}}
                            </tr>
                            </thead>
                            <tbody class="text-xs">
                            @foreach($rows as $row)
                                @php
                                    $dt = \Carbon\Carbon::now();
                                    $ot_hours = $dt->diffInHours($dt->copy()->addSeconds($row->ot_hours));
                                    $ot_minutes = $dt->diffInMinutes($dt->copy()->addSeconds($row->ot_hours)->subHours($ot_hours));
                                    $net_hours2 = $dt->diffInHours($dt->copy()->addSeconds($row->net_hours));
                                    $net_minutes = $dt->diffInMinutes($dt->copy()->addSeconds($row->net_hours)->subHours($net_hours2));
                                @endphp
                                <tr @if($row->exception == 1 ) style="background-color: rgba(255,0,0,0.3);" @endif>
                                    <td>{{$row->date}}</td>
                                    <td>{!! $row->flags !!}</td>
                                    <td>{{$ot_hours}}:{{$ot_minutes}}</td>
                                    <td>{{$net_hours2}}:{{$net_minutes}}</td>
                                    <td>{!! $row->employee_remarks !!}</td>
                                    <td>{{$row->approver_remarks}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div class="row">
                            <div class="col-md-4">
                                <button type="button" class="btn bg-gradient-primary btn-sm mb-0 rowadd" data-bs-toggle="modal" data-bs-target="#modal-create">+&nbsp; Request Leave </button>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <form id="add_form" method="POST" action="{{route('employee.timecard.submit', ['week' => $week])}}">
                                                    @csrf
                                                    <input type="hidden" name="start_time" value="{{$startDate}}"/>
                                                    <input type="hidden" name="end_time" value="{{$endDate}}"/>
                                                    <input type="hidden" name="week" value="{{$week}}"/>
                                                    <input type="hidden" name="user_id" value="{{auth()->user()->clockify_id}}"/>
                                                    <input type="hidden" name="status" id="status" value="Submitted"/>
                                                    <input type="submit" value="Submit Timecard" id="submit_button" class="btn btn-success btn-sm"/>
                                                </form>
                                            </div>
                                            <div class="col-md-6">
                                                <form id="add_form3" method="POST" action="{{route('employee.timecard.submit', ['week' => $week])}}">
                                                    @csrf
                                                    <input type="hidden" name="start_time" value="{{$startDate}}"/>
                                                    <input type="hidden" name="end_time" value="{{$endDate}}"/>
                                                    <input type="hidden" name="week" value="{{$week}}"/>
                                                    <input type="hidden" name="user_id" value="{{auth()->user()->clockify_id}}"/>
                                                    <input type="hidden" name="status" id="status" value="Edit Later"/>
                                                    <input type="submit" value="Save & Edit Later" id="submit_button2" class="btn btn-info btn-sm"/>
                                                </form>
                                            </div>
                                        </div>
                                </div></div>
                            <div class="col-md-4"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="import" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog mt-lg-10">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">Import CSV</h5>
                    <i class="fas fa-upload ms-3"></i>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>You can browse your computer for a file.</p>
                    <input type="text" placeholder="Browse file..." class="form-control mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="importCheck" checked="">
                        <label class="custom-control-label" for="importCheck">I accept the terms and conditions</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn bg-gradient-primary btn-sm">Upload</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-create" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="add_form2" autocomplete="off" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="form_title">Create Leave Request</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id"/>
                        {{--<input type="hidden" name="status" id="status" value="Submitted"/>--}}
                        <input type="hidden" name="user_id" id="user_id" value="{{auth()->user()->clockify_id}}"/>
                        <div class="form-group">
                            <label for="leave_type_id">Leave Type <span class="text-danger">*</span></label>
                            <select required class="form-control" name="leave_type_id" id="leave_type_id" placeholder="Select Leave Type">
                                <option value="" disabled selected>-- Select --</option>
                                @foreach($leave_categories as $category)
                                    <option value="{{$category->id}}">{{$category->name}}</option>
                                @endforeach
                            </select>
                            <span id="leave_type_id_error" class="text-danger"></span>
                        </div>
                        <div class="form-group">
                            <label for="date_from">Date From <span class="text-danger">*</span></label>
                            <input required type="date" class="form-control" name="date_from" id="date_from" placeholder="Select Date From">
                            <span id="date_from_error" class="text-danger"></span>
                        </div>
                        <div class="form-group">
                            <label for="date_to">Date To <span class="text-danger">*</span></label>
                            <input required type="date" class="form-control" name="date_to" id="date_to" placeholder="Select Date To">
                            <span id="date_to_error" class="text-danger"></span>
                        </div>
                        <div class="form-group">
                            <label for="title">Explanation / Handover of Responsibilities</label>
                            <textarea class="form-control" name="title" id="title" placeholder="Enter Explanation / Handover of Responsibilities"></textarea>
                            <span id="title_error" class="text-danger"></span>
                        </div>
                        <div class="form-group">
                            <label class="" for="attachment">Attachment</label>
                            <input type="file" class="form-control" name="attachment" id="attachment"/>
                            <span id="attachment_error" class="text-danger"></span>
                        </div>
                    </div>
                    <div class="modal-footer text-right">
                        <button type="button" class="btn bg-gradient-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn bg-gradient-primary btn-sm" id="add_button2">Submit</button>
                    </div>
                </form>
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

            $('#add_form').submit(function (e) {
                e.preventDefault();
                //var form_data = new FormData(this);
                //var net_hours = '{{$net_hours ?? 0}}';
                var titleText = 'Are you sure?';
                var msgText = "Once you submit you cannot make changes.";
                var buttonText = 'Yes, submit it!';
                /*if(net_hours < 45){
                    titleText = 'Oops you are not able to submit.';
                    msgText = 'Your claimed time is less from 45 hours';
                    buttonText = 'Save, (edit later)';
                } else {
                    titleText = 'Are you sure?';
                    buttonText = "Once you submit you cannot make changes.";
                    msgText = 'Yes, submit it!';
                }*/
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

            $('#add_form3').submit(function (e) {
                e.preventDefault();
                //var form_data = new FormData(this);
                var titleText = 'Are you sure?';
                var msgText = 'You went save & edit later.';
                var buttonText = "Yes, save it!";
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
    <script>
        $(document).ready(function (){
            $('#date_from').attr('min', new Date('{{Carbon\Carbon::parse($startDate)->addDay()}}').toISOString().split('T')[0]);
            $('#date_from').attr('max', new Date('{{Carbon\Carbon::parse($endDate)}}').toISOString().split('T')[0]);
            $('#date_to').attr('min', new Date('{{Carbon\Carbon::parse($startDate)->addDay()}}').toISOString().split('T')[0]);
            $('#date_to').attr('max', new Date('{{Carbon\Carbon::parse($endDate)}}').toISOString().split('T')[0]);

            $("#date_from").on('change', function () {
                var minDate = new Date($(this).val()).toISOString().split('T')[0];
                $('#date_to').attr('min', minDate);
            });

            $("#date_to").on('change', function () {
                var maxDate = new Date($(this).val()).toISOString().split('T')[0];
                $('#date_from').attr('max', maxDate);
            });

            const addForm = '{{ route('employee.request-leave') }}';
            $('#add_form2').submit(function (e) {
                e.preventDefault();
                var form_data = new FormData(this);
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Once you submit you cannot make changes.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, submit it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            method: "POST",
                            url: addForm,
                            data: form_data,
                            contentType: false,
                            processData: false,
                            dataType: "json",
                            headers: {"X-CSRF-Token": $('meta[name="csrf-token"]').attr('content')},
                            beforeSend: function () {
                                $('#add_button').attr('disabled', 'disabled');
                                $('#title_error').text('');
                                $('#leave_type_id_error').text('');
                                $('#date_from_error').text('');
                                $('#date_to_error').text('');
                                $('#remarks_error').text('');
                            },
                            success: function (data) {
                                $("#add_form2")[0].reset();
                                $('#modal-create').modal('hide');
                                if (data.success === true) {
                                    toastr.success(data.message);
                                    window.location.reload();
                                } else {
                                    toastr.error(data.message);
                                }
                                $('#add_button').attr('disabled', false);
                            },
                            error: function (data) {
                                $('#add_button').attr('disabled', false);
                                let responseData = data.responseJSON;
                                $('#title_error').text(responseData.errors['title']);
                                $('#leave_type_id_error').text(responseData.errors['leave_type_id']);
                                $('#date_from_error').text(responseData.errors['date_from']);
                                $('#date_to_error').text(responseData.errors['date_to']);
                                $('#remarks_error').text(responseData.errors['remarks']);
                            }
                        });
                    }
                })
            });
        });
    </script>
@endsection
