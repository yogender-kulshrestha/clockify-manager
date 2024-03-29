@php
    $layout = (auth()->user()->role == 'admin') ? 'layouts.master' : 'employee.master';
@endphp
@extends($layout)

@section('title', 'All Employees')

@section('style')
@endsection

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm">
                <a class="opacity-3 text-dark" href="javascript:;">
                    <svg width="12px" height="12px" class="mb-1" viewBox="0 0 45 40" version="1.1"
                         xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                        <title>shop </title>
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <g transform="translate(-1716.000000, -439.000000)" fill="#252f40" fill-rule="nonzero">
                                <g transform="translate(1716.000000, 291.000000)">
                                    <g transform="translate(0.000000, 148.000000)">
                                        <path
                                            d="M46.7199583,10.7414583 L40.8449583,0.949791667 C40.4909749,0.360605034 39.8540131,0 39.1666667,0 L7.83333333,0 C7.1459869,0 6.50902508,0.360605034 6.15504167,0.949791667 L0.280041667,10.7414583 C0.0969176761,11.0460037 -1.23209662e-05,11.3946378 -1.23209662e-05,11.75 C-0.00758042603,16.0663731 3.48367543,19.5725301 7.80004167,19.5833333 L7.81570833,19.5833333 C9.75003686,19.5882688 11.6168794,18.8726691 13.0522917,17.5760417 C16.0171492,20.2556967 20.5292675,20.2556967 23.494125,17.5760417 C26.4604562,20.2616016 30.9794188,20.2616016 33.94575,17.5760417 C36.2421905,19.6477597 39.5441143,20.1708521 42.3684437,18.9103691 C45.1927731,17.649886 47.0084685,14.8428276 47.0000295,11.75 C47.0000295,11.3946378 46.9030823,11.0460037 46.7199583,10.7414583 Z"></path>
                                        <path
                                            d="M39.198,22.4912623 C37.3776246,22.4928106 35.5817531,22.0149171 33.951625,21.0951667 L33.92225,21.1107282 C31.1430221,22.6838032 27.9255001,22.9318916 24.9844167,21.7998837 C24.4750389,21.605469 23.9777983,21.3722567 23.4960833,21.1018359 L23.4745417,21.1129513 C20.6961809,22.6871153 17.4786145,22.9344611 14.5386667,21.7998837 C14.029926,21.6054643 13.533337,21.3722507 13.0522917,21.1018359 C11.4250962,22.0190609 9.63246555,22.4947009 7.81570833,22.4912623 C7.16510551,22.4842162 6.51607673,22.4173045 5.875,22.2911849 L5.875,44.7220845 C5.875,45.9498589 6.7517757,46.9451667 7.83333333,46.9451667 L19.5833333,46.9451667 L19.5833333,33.6066734 L27.4166667,33.6066734 L27.4166667,46.9451667 L39.1666667,46.9451667 C40.2482243,46.9451667 41.125,45.9498589 41.125,44.7220845 L41.125,22.2822926 C40.4887822,22.4116582 39.8442868,22.4815492 39.198,22.4912623 Z"></path>
                                    </g>
                                </g>
                            </g>
                        </g>
                    </svg>
                </a>
            </li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Employees</li>
        </ol>
        <h6 class="font-weight-bolder mb-0">All Employees</h6>
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
                            <h5 class="mb-0">All Employees</h5>
                            <p class="text-sm mb-0">

                            </p>
                        </div>
                        <div class="ms-auto my-auto mt-lg-0 mt-4">
                            <div class="ms-auto my-auto">
                                <a href="{{route('employee.home')}}" class="btn bg-gradient-primary btn-sm mb-0"> Return
                                    to Dashboard </a>
                            </div>
                        </div>
                        <div class="ms-auto my-auto mt-lg-0 mt-4">
                            <div class="ms-auto my-auto">
                                @if(auth()->user()->role == 'admin')
                                    <button type="button" class="btn bg-gradient-primary btn-sm mb-0 rowadd"
                                            data-bs-toggle="modal" data-bs-target="#modal-create">+&nbsp; New
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-0">
                    <div class="table-responsive p-3">
                        <table class="table table-flush" id="datatable">
                            <thead
                                class="thead-light text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                            <tr>
                                <td>#</td>
                                <th>Employee ID</th>
                                <th>Image</th>
                                <td>Name</td>
                                <td>Email</td>
                                <td>Employee Type</td>
                                <td>Registration Date</td>
                                <td>Status</td>
                                <td>Action</td>
                            </tr>
                            </thead>
                            <tbody class="text-sm">
                            </tbody>
                        </table>
                    </div>

                    <div class="m-3">
                        <button type="button" class="btn bg-gradient-danger btn-sm mb-0 flashRecords"><i
                                class='fas fa-sync fa-3x text-white'></i>&nbsp; Flash Records
                        </button>
                        <button type="button" class="btn bg-gradient-danger btn-sm mb-0 flashUsers"><i
                                class='fas fa-trash fa-3x text-white'></i>&nbsp; Flash Users
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-create" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="add_form" autocomplete="off" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="form_title">Create</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" name="id" id="id"/>
                            <div class="form-group col-md-6">
                                <label for="employee_id">Employee ID <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="employee_id" id="employee_id"
                                       placeholder="Enter Employee ID">
                                <span id="employee_id_error" class="text-danger text-sm"></span>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="name" placeholder="Enter Name">
                                <span id="name_error" class="text-danger text-sm"></span>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="email">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" id="email"
                                       placeholder="Enter Email">
                                <span id="email_error" class="text-danger text-sm"></span>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="status">Employee Type</label>
                                <select class="form-control" name="type" id="type">
                                    <option value="">-- Select --</option>
                                    <option value="full time - permanent">Full time - permanent</option>
                                    <option value="full time – fixed contract">Full time – fixed contract</option>
                                    <option value="fellows">Fellows</option>
                                    <option value="contractors">Contractors</option>
                                </select>
                                <span id="type_error" class="text-danger text-sm"></span>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="status">Status <span class="text-danger">*</span></label>
                                <select class="form-control" name="status" id="status">
                                    <option value="">-- Select --</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <span id="status_error" class="text-danger text-sm"></span>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="image">Image</label>
                                <input type="file" class="form-control" name="image" id="image"
                                       placeholder="Select Image">
                                <span id="image_error" class="text-danger text-sm"></span>
                            </div>
                            @if(auth()->user()->role == 'admin')
                                <div class="form-group col-md-6">
                                    <label for="password">Password <span class="text-danger hidden">*</span></label>
                                    <input type="password" class="form-control" name="password" id="password"
                                           placeholder="Enter Password">
                                    <span id="password_error" class="text-danger text-sm"></span>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="password_confirmation">Confirm Password <span
                                            class="text-danger hidden">*</span></label>
                                    <input type="password" class="form-control" name="password_confirmation"
                                           id="password_confirmation" placeholder="Enter Confirm Password">
                                    <span id="password_confirmation_error" class="text-danger text-sm"></span>
                                </div>
                            @endif
                            <div class="col-md-12" id="monthly_holiday">
                                <hr/>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="paid_holidays">Allow Monthly Paid Leave <span
                                                class="text-danger">*</span></label>
                                        <input type="number" min="0" class="form-control" value="0" name="paid_holidays"
                                               id="paid_holidays" placeholder="Enter Monthly Paid Holidays">
                                        <span id="paid_holidays_error" class="text-danger text-sm"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div id="leave_balances" class="row"></div>
                            </div>
                        </div>
                        <div class="modal-footer text-right">
                            <button type="button" class="btn bg-gradient-secondary btn-sm" data-bs-dismiss="modal">
                                Close
                            </button>
                            <button type="submit" class="btn bg-gradient-primary btn-sm" id="add_button">Add</button>
                        </div>
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
        $(document).ready(function () {
            var datatable = $('#datatable').DataTable({
                dom: '<"row"<"col-sm-6"l><"float-right col-sm-6"f>>rt<"row"<"col-sm-6"i><"col-sm-6"p>>',
                //dom: 'Blfrtip',
                language: {
                    paginate: {
                        next: '›',
                        previous: '‹'
                    }
                },
                "select": true,
                "paging": true,
                "pageLength": "10",
                "lengthMenu": [
                    [5, 10, 25, 50, 100, 1000, -1],
                    [5, 10, 25, 50, 100, 1000, 'ALL']
                ],
                "processing": true,
                "serverSide": true,
                "searching": true,
                "responsive": true,
                //"lengthChange": false,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                "ajax": {
                    url: '{{ route('employees.index') }}',
                },
                "order": [[1, "desc"]],
                "columns": [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'employee_id',
                        name: 'employee_id',
                        defaultContent: '',
                    },
                    {
                        data: 'image',
                        name: 'image',
                        defaultContent: ''
                    },
                    {
                        data: 'name',
                        name: 'name',
                        defaultContent: ''
                    },
                    {
                        data: 'email',
                        name: 'email',
                        defaultContent: ''
                    },
                    {
                        data: 'type',
                        name: 'type',
                        defaultContent: ''
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        defaultContent: ''
                    },
                    {
                        data: 'status',
                        name: 'status',
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
            $(document).on("click", ".rowadd", function () {
                $("#form_title").text('Create');
                $("#id").val('');
                $("#employee_id").val('');
                $("#name").val('');
                $("#email").val('');
                $('#type').val('');
                $("#status").val('');
                $('#paid_holidays').val('0');
                $('#leave_balances').html('');
                $('#image_error').text('');
                $('#employee_id_error').text('');
                $('#name_error').text('');
                $('#email_error').text('');
                $('#type_error').text('');
                $('#status_error').text('');
                $('#password_error').text('');
                $('#password_confirmation_error').text('');
                $('#monthly_holiday').hide();
                $('.text-danger.hidden').text('*');
                $("#add_button").text('Add');
            });
            $(document).on("click", ".rowedit", function () {
                $('#leave_balances').html('<hr/><span>LEAVE ALLOCATIONS FOR THIS YEAR</span>');
                let leave_balances = $(this).data('leave_balances');
                $.each(leave_balances, function (key, val) {
                    $('#leave_balances').append('<div class="form-group col-md-6"> ' +
                        '<label for="leave_balances' + key + '">' + val.leave_type.name + ' <span class="text-danger">*</span></label> ' +
                        '<input type="hidden" name="leave_balances[' + key + '][leave_type_id]" value="' + val.leave_type.id + '"/>' +
                        '<input required type="number" min="0" class="form-control" value="' + val.balance + '" name="leave_balances[' + key + '][balance]" id="leave_balances' + key + '" placeholder="Enter ' + val.leave_type.name + ' Balance"> ' +
                        '</div>');
                });
                $("#form_title").text('Edit');
                $("#id").val($(this).data('id'));
                $("#employee_id").val($(this).data('employee_id'));
                $("#name").val($(this).data('name'));
                $("#email").val($(this).data('email'));
                //$("#email").attr('disabled', 'disabled');
                $("#type").val($(this).data('type'));
                $("#status").val($(this).data('status'));
                $('#paid_holidays').val($(this).data('paid_holidays'));
                $('#image_error').text('');
                $('#employee_id_error').text('');
                $('#name_error').text('');
                $('#email_error').text('');
                $('#type_error').text('');
                $('#status_error').text('');
                $('#password_error').text('');
                $('#password_confirmation_error').text('');
                $('#monthly_holiday').show();
                $('.text-danger.hidden').text('');
                $("#add_button").text('Update');
            });

            const addForm = '{{ route('employees.store') }}';
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
                        $('#image_error').text('');
                        $('#employee_id_error').text('');
                        $('#name_error').text('');
                        $('#email_error').text('');
                        $('#type_error').text('');
                        $('#status_error').text('');
                        $('#password_error').text('');
                        $('#password_confirmation_error').text('');
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
                        $('#image_error').text(responseData.errors['image']);
                        $('#employee_id_error').text(responseData.errors['employee_id']);
                        $('#name_error').text(responseData.errors['name']);
                        $('#email_error').text(responseData.errors['email']);
                        $('#type_error').text(responseData.errors['type']);
                        $('#status_error').text(responseData.errors['status']);
                        $('#password_error').text(responseData.errors['password']);
                        $('#password_confirmation_error').text(responseData.errors['password_confirmation']);
                    }
                });
            });

            $(document).on('click', '.flashRecords', function () {
                var url = '{{ route('delete.all-records') }}';
                Swal.fire({
                    title: 'Are you sure to delete all records?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        spinnershow();
                        $.ajax({
                            url: url,
                            type: "POST",
                            dataType: "JSON",
                            data: {
                                '_token': '{{ csrf_token() }}',
                            },
                            success: function (data) {
                                //console.log(data);
                                spinnerhide();
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

            $(document).on('click', '.flash-records', function () {
                var id = $(this).data('id');
                var url = '{{ route('delete.user-records') }}';
                Swal.fire({
                    title: 'Are you sure to delete all records?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        spinnershow();
                        $.ajax({
                            url: url,
                            type: "POST",
                            dataType: "JSON",
                            data: {
                                'user_id': id,
                                '_token': '{{ csrf_token() }}',
                            },
                            success: function (data) {
                                //console.log(data);
                                spinnerhide();
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

            $(document).on('click', '.flashUsers', function () {
                var url = '{{ route('delete.all-users') }}';
                Swal.fire({
                    title: 'Are you sure to delete all users?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        spinnershow();
                        $.ajax({
                            url: url,
                            type: "POST",
                            dataType: "JSON",
                            data: {
                                '_token': '{{ csrf_token() }}',
                            },
                            success: function (data) {
                                datatable.draw();
                                spinnerhide();
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

            $(document).on('click', '.rowdelete', function () {
                var id = $(this).data('id');
                var url = '{{ route('delete.user') }}';
                Swal.fire({
                    title: 'Are you sure to delete this user?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        datatable.draw();
                        spinnershow();
                        $.ajax({
                            url: url,
                            type: "POST",
                            dataType: "JSON",
                            data: {
                                'user_id': id,
                                '_token': '{{ csrf_token() }}',
                            },
                            success: function (data) {
                                //console.log(data);
                                spinnerhide();
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
