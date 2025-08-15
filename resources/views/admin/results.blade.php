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
</style>
@endsection

@section('breadcrumb')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Results</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
              <li class="breadcrumb-item active">Results</li>
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
                                <label for="nameFilter">Name</label>
                                <select class="form-control select2bs4" name="nameFilter" id="nameFilter" style="width: 100%;">
                                    <option value="all">All</option>
                                    @foreach($examinees as $examinee)
                                        <option value="{{ $examinee->id }}">{{ $examinee->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="designationFilter">Designation</label>
                            <select class="form-control select2bs4" name="designationFilter" id="designationFilter" style="width: 100%;">
                                <option value="all">All</option>
                                @foreach($designations as $designation)
                                    <option value="{{ $designation }}">{{ $designation }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                  </div>
                  <div class="row">
                      <div class="col-md-12">
                          <div class="form-group">
                              <label for="unitFilter">Unit</label>
                              <select class="form-control select2bs4" name="unitFilter" id="unitFilter" style="width: 100%;">
                                  <option value="all">All</option>
                                  @foreach($units as $unitId => $unitName)
                                      <option value="{{ $unitId }}">{{ $unitName }}</option>
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
                    <h3 class="card-title">Results List</h3>
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
                                <!-- Import Questionnaire -->
                                <a class="dropdown-item text-info" href="#" data-toggle="modal" data-target="#import_questionnaire_modal">
                                    <i class="fas fa-file-import mr-2"></i> Import Questionnaire
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
                    <table id="resultsTable" class="table table-sm table-hover text-center">
                        <thead>
                            <tr>
                                <th>Examinee ID</th>
                                <th>#</th>
                                <th>Examination</th>
                                <th>Name</th>
                                <th>Designation</th>
                                <th>Unit</th>
                                <th>Total Questions</th>
                                <th>Score</th>
                                <th>Rating</th>
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
       var table = $('#resultsTable').DataTable({
        dom: '<"d-flex justify-content-between"lBf>t<"d-flex justify-content-between"ip>',
        buttons: [
            { 
                extend: 'excel', 
                title: 'Results List',
                className: 'btn-default'
            },
            { 
                extend: 'print', 
                title: 'Results List',
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
                url: "{{ route('results.data') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (d) {
                    d._token = "{{ csrf_token() }}";
                    d.examFilter = $('#examFilter').val();
                    d.nameFilter = $('#nameFilter').val();
                    d.designationFilter = $('#designationFilter').val();
                    d.unitFilter = $('#unitFilter').val();
                    // Convert -1 to a very large number for server-side processing
                        if (d.length === -1) {
                            d.length = 1000000; // Or whatever maximum you want to support
                        }
                    return d;
                },
                // error: function (xhr, error, thrown) {
                //     // console.log('AJAX Error:', xhr.responseText);
                    
                //     // Add retry logic instead of just reloading
                //     var retryCount = parseInt(localStorage.getItem('dtRetryCount') || '0');
                    
                //     if (retryCount < 3) {
                //         // Increment retry counter
                //         localStorage.setItem('dtRetryCount', retryCount + 1);
                        
                //         // Show message
                //         toastr.info('Retrying data request...', 'Please wait', {
                //             timeOut: 1500,
                //             progressBar: true
                //         });
                        
                //         // Retry after a short delay
                //         setTimeout(function() {
                //             table.ajax.reload();
                //         }, 1500);
                //     } else {
                //         // Reset counter and show reload message
                //         localStorage.setItem('dtRetryCount', '0');
                //         toastr.info('Having trouble loading data. Refreshing the page...', 'Please wait', {
                //             timeOut: 2000,
                //             progressBar: true
                //         });
                        
                //         // Reload after giving user time to see message
                //         setTimeout(function() {
                //             location.reload();
                //         }, 2000);
                //     }
                // }
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
                { data: 'exam', name: 'exam', orderable: false, searchable: false  },
                { data: 'name', name: 'name', orderable: false, searchable: false  },
                { data: 'designation', name: 'designation', orderable: false, searchable: false  },
                { data: 'unit', name: 'unit', orderable: false, searchable: false  },
                { data: 'total_question', name: 'total_question', orderable: false, searchable: false  },
                { data: 'score', name: 'score', orderable: false, searchable: false  },
                { data: 'rating', name: 'rating', orderable: false, searchable: false  },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            order: [[0, 'desc']],
            createdRow: function(row, data, dataIndex) {
                var rating = data.rating;
                var $ratingCell = $(row).find('td:eq(7)'); // Rating is the 8th column (0-based index 7)
                
                if (rating.includes('Failed')) {
                    $ratingCell.css('color', '#dc3545');
                } else {
                    $ratingCell.css('color', '#28a745');
                }
            }
       });

        // Filter event handlers
        $('#examFilter, #nameFilter, #designationFilter, #unitFilter').on('change keyup', function() {
            table.ajax.reload();
        });

        // Reset filters button
        $('#resetFilters').click(function () {
            // Get current filter values
            const currentFilters = {
                exam: $('#examFilter').val('all'),
                name: $('#nameFilter').val('all'),
                designation: $('#designationFilter').val('all'),
                unit: $('#unitFilter').val('all'),
            };

            // Check if any filter is not at its default value
            const needsReset = currentFilters.exam !== '' || currentFilters.name !== '' || currentFilters.designation !== '' || currentFilters.unit !== '';

            // Only reset if needed
            if (needsReset) {
                // Clear input fields
                $('#examFilter').val('all').trigger('change');
                $('#nameFilter').val('all').trigger('change');
                $('#designationFilter').val('all').trigger('change');
                $('#unitFilter').val('all').trigger('change');
                // Reload DataTable
                $('#resultsTable').DataTable().ajax.reload();
            }
        });
    });
</script>
@endsection