
@extends('layouts.master')

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
        <h6 class="font-weight-bolder mb-0">Time Cards</h6>
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
                                <a href="{{route('employee.home')}}" class="btn bg-gradient-primary btn-sm mb-0"> Return to Home </a>
                                <button type="button" class="btn bg-gradient-primary btn-sm mb-0 rowadd" data-bs-toggle="modal" data-bs-target="#modal-create">+&nbsp; New </button>
                                {{--<button type="button" class="btn btn-outline-primary btn-sm mb-0" data-bs-toggle="modal" data-bs-target="#import">
                                Import
                            </button>
                            <button class="btn btn-outline-primary btn-sm export mb-0 mt-sm-0 mt-1" data-type="csv" type="button" name="button">Export</button>
                        --}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-0">
                    <div class="table-responsive p-3">
                        <table class="table table-flush" id="datatable">
                            <thead class="thead-light text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                            <tr>
                                <td>#</td>
                                <td>ID</td>
                                <td style="max-width: 40% !important;">Description</td>
                                <td>Start Date</td>
                                <td>Start Time</td>
                                <td>End Date</td>
                                <td>End Time</td>
                                <td>Duration</td>
                                <td>Error</td>
                                <td>Remarks</td>
                                <td>Action</td>
                            </tr>
                            </thead>
                            <tbody class="text-xs">
                            </tbody>
                        </table>
                        <div class="text-center">
                            <form id="submit_form" method="POST" action="{{route('employee.timecard.create')}}">
                                @csrf
                                <input type="hidden" name="start_time" value="{{$startDate}}"/>
                                <input type="hidden" name="end_time" value="{{$endDate}}"/>
                                <input type="hidden" name="week" value="{{$currentWeek}}"/>
                                <input type="submit" value="Create Timecard" id="submit_button" class="btn btn-success btn-sm"/>
                            </form>
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
                <form id="add_form" autocomplete="off" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="form_title">Create</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id"/>
                        <div class="form-group">
                            <label for="description">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="description" id="description" placeholder="Enter Description"></textarea>
                            <span id="description_error" class="text-danger"></span>
                        </div>
                        <div class="form-group">
                            <label for="duration">From <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" name="start_time" id="start_time"/>
                            <span id="start_time_error" class="text-danger"></span>
                        </div>
                        <div class="form-group">
                            <label for="duration">To <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" name="end_time" id="end_time"/>
                            <span id="end_time_error" class="text-danger"></span>
                        </div>
                        <div class="form-group">
                            <label for="remarks">Remarks <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="employee_remarks" id="remarks" placeholder="Enter Remarks"></textarea>
                            <span id="remarks_error" class="text-danger"></span>
                        </div>
                    </div>
                    <div class="modal-footer text-right">
                        <button type="button" class="btn bg-gradient-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn bg-gradient-primary btn-sm" id="add_button">Add</button>
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
                //dom: 'B<"row"<"col-sm-6"l><"float-right col-sm-6"f>>rt<"row"<"col-sm-6"i><"col-sm-6"p>>',
                dom: 'f',
                language: {
                    paginate: {
                        next: '›',
                        previous: '‹'
                    }
                },
                "select": true,
                "paging": true,
                "pageLength": "-1",
                "lengthMenu": [
                    [5, 10, 25, 50, 100, 1000, -1],
                    [5, 10, 25, 50, 100, 1000, 'ALL']
                ],
                "processing": true,
                "serverSide": true,
                "searching": true,
                "responsive": true,
                "lengthChange": false,
                'ordering': false,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                "ajax": {
                    url: '{{ route('employee.timecard') }}',
                    data: function (d) {
                        d.start_time = '2010-10-30',
                        /*d.start_time = '{{$startDate}}',*/
                        d.end_time = '{{$endDate}}',
                        d.seletedWeek = '{{$currentWeek}}'
                    }
                },
                "order": [[ 3, "desc" ],[ 4, "desc" ]],
                "columns": [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        defaultContent: ''
                    },
                    {
                        data: 'id',
                        name: 'id',
                        defaultContent: '' ,
                        visible: false
                    },
                    {
                        data: 'description',
                        name: 'description',
                        defaultContent: ''
                    },
                    {
                        data: 'start_date',
                        name: 'start_date',
                        defaultContent: ''
                    },
                    {
                        data: 'start_time',
                        name: 'start_time',
                        defaultContent: ''
                    },
                    {
                        data: 'end_date',
                        name: 'end_date',
                        defaultContent: ''
                    },
                    {
                        data: 'end_time',
                        name: 'end_time',
                        defaultContent: ''
                    },
                    {
                        data: 'time_duration',
                        name: 'time_duration',
                        defaultContent: ''
                    },
                    {
                        data: 'time_error',
                        name: 'time_error',
                        defaultContent: ''
                    },
                    {
                        data: 'employee_remarks',
                        name: 'employee_remarks',
                        defaultContent: ''
                    },
                    {
                        data: 'action',
                        name: 'action',
                        defaultContent: '',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            $('#start_time').attr('min', '{{\Carbon\Carbon::parse($startDate)->format('Y-m-d\TH:i')}}');
            $('#start_time').attr('max', '{{\Carbon\Carbon::parse($endDate)->format('Y-m-d\TH:i')}}');
            $('#end_time').attr('min', '{{\Carbon\Carbon::parse($startDate)->format('Y-m-d\TH:i')}}');
            $('#end_time').attr('max', '{{\Carbon\Carbon::parse($endDate)->format('Y-m-d\TH:i')}}');

            /*$("#start_time").on('change', function () {
                var minDate = new Date($(this).val()).toISOString().slice(0, -5);
                $('#end_time').attr('min', minDate);
            });

            $("#end_time").on('change', function () {
                var maxDate = new Date($(this).val()).toISOString().slice(0, -5);
                $('#start_time').attr('max', maxDate);
            });*/

            $(document).on("click", ".rowadd", function () {
                $("#form_title").text('Create');
                $("#id").val('');
                $("#description").val('');
                $("#start_time").val('');
                $("#end_time").val('');
                $("#remarks").val('');
                $('#description_error').text('');
                $('#start_time_error').text('');
                $('#end_time_error').text('');
                $('#remarks_error').text('');
                $('.text-danger.hidden').text('*');
                $("#add_button").text('Add');
            });
            $(document).on("click", ".rowedit", function () {
                $("#form_title").text('Edit');
                $("#id").val($(this).data('id'));
                $("#description").val($(this).data('description'));
                //console.log($(this).data('start_time'));
                //console.log(new Date($(this).data('start_time')).toISOString().slice(0, -5));
                $("#start_time").val(new Date($(this).data('start_time')).toISOString().slice(0, -5));
                $("#end_time").val(new Date($(this).data('end_time')).toISOString().slice(0, -5));
                $("#remarks").val($(this).data('remarks'));
                $('#description_error').text('');
                $('#start_time_error').text('');
                $('#end_time_error').text('');
                $('#remarks_error').text('');
                $('.text-danger.hidden').text('');
                $("#add_button").text('Update');
            });
            const addForm = '{{ route('employee.timecard') }}';
            $('#add_form').submit(function (e) {
                e.preventDefault();
                var form_data = new FormData(this);
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
                        $('#description_error').text('');
                        $('#duration_error').text('');
                        $('#end_time_error').text('');
                        $('#remarks_error').text('');
                        $('.text-danger.hidden').text('');
                    },
                    success: function (data) {
                        $("#add_form")[0].reset();
                        $('#modal-create').modal('hide');
                        datatable.draw();
                        if (data.success === true) {
                            toastr.success(data.message);
                        } else {
                            toastr.error(data.message);
                        }
                        $('#add_button').attr('disabled', false);
                    },
                    error: function (data) {
                        $('#add_button').attr('disabled', false);
                        let responseData = data.responseJSON;
                        $('#description_error').text(responseData.errors['description']);
                        $('#duration_error').text(responseData.errors['start_time']);
                        $('#end_time_error').text(responseData.errors['end_time']);
                        $('#remarks_error').text(responseData.errors['employee_remarks']);
                    }
                });
            });
            $(document).on('click', '.rowdelete', function() {
                var id = $(this).data('id');
                var url = '{{ route('time-cards.destroy', ':id') }}';
                url = url.replace(':id', id);
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: "DELETE",
                            dataType: "JSON",
                            data:{
                                'id': id,
                                '_token': '{{ csrf_token() }}',
                            },
                            success: function(data) {
                                //console.log(data);
                                datatable.draw();
                                if (data.success === true) {
                                    toastr.success(data.message)
                                } else {
                                    toastr.error(data.message)
                                }
                            }
                        });
                    }
                })
            });
        });
    </script>
@endsection
