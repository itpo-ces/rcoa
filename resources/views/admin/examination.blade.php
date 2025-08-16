@extends('layouts.master')

@section('links')
<!-- Toastr -->
<link rel="stylesheet" href="{{asset('adminlte/plugins/toastr/toastr.min.css')}}">
<!-- DataTables -->
<link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
<!-- SweetAlert2 -->
<link rel="stylesheet" href="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.css')}}">
@endsection

@section('css')
<style>
    .loading-spinner {
        display: none;
        position: absolute;
        align-items: center;
    }
    #loader {
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .dropdown-menu {
        z-index: 9999 !important;
        left: -100px;
    }

    .btn-group-sm .btn {
        margin-right: 4px;
        border-radius: 4px !important;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .btn-group-sm .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        transition: all 0.2s ease;
    }

    .custom-hover:hover {
        background-color: #007bff;
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        cursor: pointer;
    }

    .required::after {
        content: '*';
        color: #dc3545;
        margin-left: 4px;
    }

    #examinationTable td {
        vertical-align: middle;
    }
</style>
@endsection

@section('breadcrumb')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Examination</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
              <li class="breadcrumb-item active">Examination</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content-header -->
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Filter Options</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6 mx-auto text-center">
                            <button type="button" class="btn btn-primary btn-block" id="resetFilters"><i class="fas fa-sync"></i> Reset</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="examFilter">Exam</label>
                                <select class="form-control select2bs4" name="examFilter" id="examFilter" style="width: 100%;">
                                    <option value="all">All</option>
                                    @foreach($exams as $exam)
                                        <option value="{{ $exam->id }}">{{ $exam->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-10">    
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Examnination List</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                    <div class="float-right">
                        <div class="dropdown ml-2">
                            <button class="btn btn-sm btn-dark dropdown-toggle" type="button" id="actionsDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-cog mr-2"></i> Options
                            </button>
                            <div class="dropdown-menu" aria-labelledby="actionsDropdown">
                                <!-- Import Examination -->
                                <a class="dropdown-item text-info" href="#" data-toggle="modal" data-target="#import_examination_modal">
                                    <i class="fas fa-file-import mr-2"></i> Import Examination
                                </a>
                                <!-- Add Item -->
                                <a class="dropdown-item text-success" href="#" data-toggle="modal" data-target="#add_item_modal">
                                    <i class="fas fa-plus mr-2"></i> Add Item
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table id="examinationTable" class="table table-sm table-hover text-center">
                        <thead>
                            <tr>
                                <th>Exam ID</th>
                                <th>#</th>
                                <th>Title</th>
                                <th>Date</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Duration</th>
                                <th>No of Questions</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be populated by DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<!-- Toastr -->
<script src="{{ asset('adminlte/plugins/toastr/toastr.min.js')}}"></script>
<!-- SweetAlert2 -->
<script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js')}}"></script>
<!-- XLSX -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
<!-- DataTables & Plugins -->
<script src="{{ asset('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
<script>
    @if(Session::has('error'))
        toastr.error('{{ Session::get('error')}}', 'Administrator', {timeOut: 3000, progressBar: true});
    @elseif(Session::has('success'))
        toastr.success('{{ Session::get('success')}}', 'Administrator', {timeOut: 3000, progressBar: true});
    @endif

    // Handle Validation Error Messages
    @if($errors->any())
      @foreach ($errors->all() as $error)
        toastr.error('{{ $error }}', 'Administrator', {timeOut: 3000, progressBar: true});
      @endforeach
    @endif
</script>
<script>
    $(document).on('shown.bs.modal', function (e) {
        const $modal = $(e.target);

        $modal.find('.select2bs4').each(function() {
            $(this).select2('destroy'); // Always destroy first
            $(this).select2({
                theme: 'bootstrap4',
                dropdownParent: $modal
            });
        });
    });
</script>
<script>
    // Use Ajax to fetch data from the server based on filter options
    $(document).ready(function() {
       var table = $('#examinationTable').DataTable({
        dom: '<"d-flex justify-content-between"lBf>t<"d-flex justify-content-between"ip>',
        buttons: [
            { 
                extend: 'excel', 
                title: 'Examination List',
                className: 'btn-default'
            },
            { 
                extend: 'print', 
                title: 'Examination List',
                className: 'btn-default'
            }
        ],
        serverSide: true,
        processing: true,
        searching: false,
        destroy: true,
        autoWidth: false,
        responsive: false,
        scrollX: true,
        lengthMenu: [[20, 50, 100, 500, 1000], [20, 50, 100, 500, 1000]],

            ajax: {
                url: "{{ route('examination.data') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (d) {
                    d._token = "{{ csrf_token() }}";
                    d.examFilter = $('#examFilter').val();
                    // Convert -1 to a very large number for server-side processing
                        if (d.length === -1) {
                            d.length = 1000000; // Or whatever maximum you want to support
                        }
                    return d;
                },
                error: function (xhr, error, thrown) {
                    // console.log('AJAX Error:', xhr.responseText);
                    
                    // Add retry logic instead of just reloading
                    var retryCount = parseInt(localStorage.getItem('dtRetryCount') || '0');
                    
                    if (retryCount < 3) {
                        // Increment retry counter
                        localStorage.setItem('dtRetryCount', retryCount + 1);
                        
                        // Show message
                        toastr.info('Retrying data request...', 'Please wait', {
                            timeOut: 1500,
                            progressBar: true
                        });
                        
                        // Retry after a short delay
                        setTimeout(function() {
                            table.ajax.reload();
                        }, 1500);
                    } else {
                        // Reset counter and show reload message
                        localStorage.setItem('dtRetryCount', '0');
                        toastr.info('Having trouble loading data. Refreshing the page...', 'Please wait', {
                            timeOut: 2000,
                            progressBar: true
                        });
                        
                        // Reload after giving user time to see message
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    }
                }
            },
            columns: [
                { data: 'id', name: 'id', class: 'hidden', orderable: false, searchable: false  },
                { 
                    data: 'number', 
                    name: 'number', 
                    orderable: false, 
                    searchable: false,
                    title: '#',
                    className: 'text-center'
                },
                { data: 'title', name: 'title', orderable: false, searchable: false  },
                { data: 'exam_date', name: 'exam_date', orderable: false, searchable: false  },
                { data: 'start_time', name: 'start_time', orderable: false, searchable: false  },
                { data: 'end_time', name: 'end_time', orderable: false, searchable: false  },
                { data: 'duration', name: 'duration', orderable: false, searchable: false  },
                { data: 'no_of_questions', name: 'no_of_questions', orderable: false, searchable: false  },
                { data: 'status', name: 'status', orderable: false, searchable: false  },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            order: [[0, 'desc']],
            createdRow: function(row, data, dataIndex) {
                var status = data.status;
                var $statusCell = $(row).find('td:eq(8)'); // status is the 9th column (0-based index 8)

                if (status.includes('Inactive')) {
                  $statusCell.css('color', '#dc3545');
                  $statusCell.css('font-weight', 'bold');
                } else {
                  $statusCell.css('color', '#28a745');
                  $statusCell.css('font-weight', 'bold');
                }
            }
       });

        // Filter event handlers
        $('#examFilter').on('change keyup', function() {
            table.ajax.reload();
        });

        // Reset filters button
        $('#resetFilters').click(function () {
            // Get current filter values
            const currentFilters = {
                exam: $('#examFilter').val('all')
            };

            // Check if any filter is not at its default value
            const needsReset = currentFilters.exam !== '';

            // Only reset if needed
            if (needsReset) {
                // Clear input fields
                $('#examFilter').val('all').trigger('change');
                // Reload DataTable
                $('#examinationTable').DataTable().ajax.reload();
            }
        });
    });
</script>
@endsection