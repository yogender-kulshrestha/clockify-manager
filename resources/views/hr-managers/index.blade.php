@extends('layouts.master')

@section('title', 'All HR Managers')

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
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">HR Managers</li>
        </ol>
        <h6 class="font-weight-bolder mb-0">All HR Managers</h6>
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
                            <h5 class="mb-0">All HR Managers</h5>
                            <p class="text-sm mb-0">
                            </p>
                        </div>
                        <div class="ms-auto my-auto mt-lg-0 mt-4">
                            <div class="ms-auto my-auto">
                                <button type="button" class="btn bg-gradient-primary btn-sm mb-0 rowadd" data-bs-toggle="modal" data-bs-target="#modal-create">+&nbsp; New </button>
                                {{--<button type="button" class="btn btn-outline-primary btn-sm mb-0" data-bs-toggle="modal" data-bs-target="#import">
                                    Import
                                </button>--}}
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
                                <td>Name</td>
                                <td>Email</td>
                                <td>Status</td>
                                <td>Registration Date</td>
                                <td>Action</td>
                            </tr>
                            </thead>
                            <tbody class="text-sm">
                            </tbody>
                        </table>
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
                            <label for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" id="name" placeholder="Enter Name">
                            <span id="name_error" class="text-danger text-sm"></span>
                        </div>
                        <div class="form-group">
                            <label for="email">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" id="email" placeholder="Enter Email">
                            <span id="email_error" class="text-danger text-sm"></span>
                        </div>
                        <div class="form-group">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select class="form-control" name="status" id="status">
                                <option value="">-- Select --</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            <span id="status_error" class="text-danger text-sm"></span>
                        </div>
                        <div class="form-group">
                            <label for="password">Password <span class="text-danger hidden">*</span></label>
                            <input type="password" class="form-control" name="password" id="password" placeholder="Enter Password">
                            <span id="password_error" class="text-danger text-sm"></span>
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Confirm Password <span class="text-danger hidden">*</span></label>
                            <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="Enter Confirm Password">
                            <span id="password_confirmation_error" class="text-danger text-sm"></span>
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
                dom: 'B<"row"<"col-sm-6"l><"float-right col-sm-6"f>>rt<"row"<"col-sm-6"i><"col-sm-6"p>>',
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
                    url: '{{ route('hr-managers.index') }}',
                },
                "order": [[ 1, "desc" ]],
                "columns": [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'id',
                        name: 'id',
                        defaultContent: '' ,
                        visible: false
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
                        data: 'status',
                        name: 'status',
                        defaultContent: ''
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
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
                $("#name").val('');
                $("#email").val('');
                $("#status").val('');
                $('#name_error').text('');
                $('#email_error').text('');
                $('#status_error').text('');
                $('#password_error').text('');
                $('#password_confirmation_error').text('');
                $('.text-danger.hidden').text('*');
                $("#add_button").text('Add');
            });
            $(document).on("click", ".rowedit", function () {
                $("#form_title").text('Edit');
                $("#id").val($(this).data('id'));
                $("#name").val($(this).data('name'));
                $("#email").val($(this).data('email'));
                $("#status").val($(this).data('status'));
                $('#name_error').text('');
                $('#email_error').text('');
                $('#status_error').text('');
                $('#password_error').text('');
                $('#password_confirmation_error').text('');
                $('.text-danger.hidden').text('');
                $("#add_button").text('Update');
            });
            const addForm = '{{ route('hr-managers.store') }}';
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
                        $('#name_error').text('');
                        $('#email_error').text('');
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
                        $('#name_error').text(responseData.errors['name']);
                        $('#email_error').text(responseData.errors['email']);
                        $('#status_error').text(responseData.errors['status']);
                        $('#password_error').text(responseData.errors['password']);
                        $('#password_confirmation_error').text(responseData.errors['password_confirmation']);
                    }
                });
            });
            $(document).on('click', '.rowdelete', function() {
                var id = $(this).data('id');
                var url = '{{ route('hr-managers.destroy', ':id') }}';
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
