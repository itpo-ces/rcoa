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

    #questionTable td {
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
            <h1>Question Analysis</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
              <li class="breadcrumb-item active">Question Analysis</li>
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
                                        <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="typeFilter">Type</label>
                                <select class="form-control select2bs4" name="typeFilter" id="typeFilter" style="width: 100%;">
                                    <option value="all">All</option>
                                    @foreach($types as $type)
                                        <option value="{{ $type['id'] }}">{{ $type['description'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="difficultyFilter">Difficulty</label>
                                <select class="form-control select2bs4" name="difficultyFilter" id="difficultyFilter" style="width: 100%;">
                                    <option value="all">All</option>
                                    @foreach($difficulties as $difficulty)
                                        <option value="{{ $difficulty['id'] }}">{{ $difficulty['description'] }}</option>
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
                    <h3 class="card-title">Question List</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table id="questionTable" class="table table-sm table-hover text-center">
                        <thead>
                            <tr>
                                <th>Question ID</th>
                                <th>#</th>
                                <th>Exam Title</th>
                                <th>Question</th>
                                <th>Type</th>
                                <th>Difficulty</th>
                                <th>Correct Answer</th>
                                <th>Success Rate</th>
                                <th>Correct Percentage</th>
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

<!-- Question Details Modal -->
<div class="modal fade" id="questionDetailsModal" tabindex="-1" role="dialog" aria-labelledby="questionDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="questionDetailsModalLabel">Question Analysis Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="questionDetailsContent">
                    <!-- Content will be loaded via AJAX -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
    
        // Handle view details button click
        $(document).on('click', '.view-details', function() {
            var questionId = $(this).data('id');
            
            $.ajax({
                url: "{{ route('analysis.question.details') }}",
                type: "GET",
                data: { question_id: questionId },
                success: function(response) {
                    $('#questionDetailsContent').html(response);
                },
                error: function(xhr) {
                    toastr.error('Failed to load question details', 'Error');
                }
            });
        });

        // Initialize DataTable
        var table = $('#questionTable').DataTable({
        dom: '<"d-flex justify-content-between"lBf>t<"d-flex justify-content-between"ip>',
        buttons: [
            { 
                extend: 'excel', 
                title: 'Question Analysis List',
                className: 'btn-default'
            },
            { 
                extend: 'print', 
                title: 'Question Analysis List',
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
                url: "{{ route('analysis.question.data') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (d) {
                    d._token = "{{ csrf_token() }}";
                    d.examFilter = $('#examFilter').val();
                    d.typeFilter = $('#typeFilter').val();
                    d.difficultyFilter = $('#difficultyFilter').val();
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
                { data: 'id', name: 'id', visible: false },
                { data: 'number', name: 'number', title: '#', className: 'text-center', orderable: false, searchable: false },
                { data: 'exam', name: 'exam', title: 'Exam Title', orderable: false, searchable: false },
                { data: 'question', name: 'question', title: 'Question Text', orderable: false, searchable: false },
                { data: 'type', name: 'type', title: 'Type', orderable: false, searchable: false },
                { data: 'difficulty', name: 'difficulty', title: 'Difficulty', orderable: false, searchable: false },
                { data: 'correct_answer', name: 'correct_answer', title: 'Correct Answer', orderable: false, searchable: false },
                { data: 'success_rate', name: 'success_rate', title: 'Success Rate', orderable: false, searchable: false },
                { data: 'correct_percentage', name: 'correct_percentage', title: 'Correct Percentage', orderable: false, searchable: false },
                { data: 'action', name: 'action', title: 'Actions', orderable: false, searchable: false }
            ],
            order: [[0, 'asc']]
       });

        // Filter event handlers
        $('#examFilter, #typeFilter, #difficultyFilter').on('change keyup', function() {
            table.ajax.reload();
        });

        // Reset filters button
        $('#resetFilters').click(function () {
            // Get current filter values
            const currentFilters = {
                exam: $('#examFilter').val('all'),
                type: $('#typeFilter').val('all'),
                difficulty: $('#difficultyFilter').val('all')
            };

            // Check if any filter is not at its default value
            const needsReset = currentFilters.exam !== '' || currentFilters.type !== '' || currentFilters.difficulty !== '';

            // Only reset if needed
            if (needsReset) {
                // Clear input fields
                $('#examFilter').val('all').trigger('change');
                $('#typeFilter').val('all').trigger('change');
                $('#difficultyFilter').val('all').trigger('change');
                // Reload DataTable
                $('#questionTable').DataTable().ajax.reload();
            }
        });
    });
</script>
@endsection