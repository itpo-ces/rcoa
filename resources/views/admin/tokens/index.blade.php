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

    #tokenTable td {
        vertical-align: middle;
    }

    #generate_token_modal .modal-body {
        padding: 20px;
    }

    #generate_token_modal .form-group {
        margin-bottom: 1.5rem;
    }

    #generate_token_modal .invalid-feedback {
        display: block;
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: #dc3545;
    }

    #generate_token_modal .text-muted {
        font-size: 0.8rem;
        color: #6c757d !important;
    }
</style>
@endsection

@section('breadcrumb')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Token Management</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
              <li class="breadcrumb-item active">Token Management</li>
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
                                <label for="tokenFilter">Token</label>
                                <select class="form-control select2bs4" name="tokenFilter" id="tokenFilter" style="width: 100%;">
                                    <option value="all">All</option>
                                    @foreach($tokens as $token)
                                        <option value="{{ $token->id }}">{{ $token->token }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="examineeFilter">Examinee</label>
                                <select class="form-control select2bs4" name="examineeFilter" id="examineeFilter" style="width: 100%;">
                                    <option value="all">All</option>
                                    @foreach($examinees as $examinee)
                                        <option value="{{ $examinee->id }}">{{ $examinee->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="statusFilter">Status</label>
                                <select class="form-control select2bs4" name="statusFilter" id="statusFilter" style="width: 100%;">
                                    <option value="all">All</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status['id'] }}">{{ $status['description'] }}</option>
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
                    <h3 class="card-title">Token List</h3>
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
                                <!-- Generate Token -->
                                <a class="dropdown-item text-success" href="#" data-toggle="modal" data-target="#generate_token_modal">
                                    <i class="fas fa-plus mr-2"></i> Generate Token
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table id="tokenTable" class="table table-sm table-hover text-center">
                        <thead>
                            <tr>
                                <th>Token ID</th>
                                <th>#</th>
                                <th>Token</th>
                                <th>Status</th>
                                <th>Examinee</th>
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

<!-- Generate Token Modal -->
<div class="modal fade" id="generate_token_modal" tabindex="-1" role="dialog" aria-labelledby="generate_token_modal_label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="generate_token_modal_label">Generate Tokens</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="generateTokenForm" method="POST" action="{{ route('tokens.generate') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="token_count" class="required">Number of Tokens to Generate</label>
                        <input type="number" class="form-control" id="token_count" name="token_count" 
                               placeholder="Enter number of tokens" min="1" max="1000" required>
                        <small class="form-text text-muted">Maximum 1000 tokens at a time</small>
                    </div>
                    <div class="form-group">
                        <label for="token_length">Token Length</label>
                        <input type="number" class="form-control" id="token_length" name="token_length" 
                               placeholder="Enter token length" min="8" max="32" value="16">
                        <small class="form-text text-muted">Default is 16 characters (8-32 allowed)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="generateTokensBtn">Generate Tokens</button>
                </div>
            </form>
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
       var table = $('#tokenTable').DataTable({
        dom: '<"d-flex justify-content-between"lBf>t<"d-flex justify-content-between"ip>',
        buttons: [
            { 
                extend: 'excel', 
                title: 'Token List',
                className: 'btn-default'
            },
            { 
                extend: 'print', 
                title: 'Token List',
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
                url: "{{ route('tokens.data') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (d) {
                    d._token = "{{ csrf_token() }}";
                    d.tokenFilter = $('#tokenFilter').val();
                    d.examineeFilter = $('#examineeFilter').val();
                    d.statusFilter = $('#statusFilter').val();
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
                { data: 'number', name: 'number', orderable: false, searchable: false, title: '#', className: 'text-center'},
                { data: 'token', name: 'token', orderable: false, searchable: false  },
                { data: 'status', name: 'status', orderable: false, searchable: false  },
                { data: 'examinee', name: 'examinee', orderable: false, searchable: false  },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            order: [[0, 'asc']],
            createdRow: function(row, data, dataIndex) {
                var status = data.status ? String(data.status) : '';
                var $statusCell = $(row).find('td:eq(3)'); // status is the 4th column (0-based index 3)

                if (status.includes('In Use')) {
                  $statusCell.css('color', '#dc3545');
                  $statusCell.css('font-weight', 'bold');
                } else if (status.includes('Available')) {
                  $statusCell.css('color', '#28a745');
                  $statusCell.css('font-weight', 'bold');
                } else if (status.includes('Used')) {
                  $statusCell.css('color', '#6c757d');
                  $statusCell.css('font-weight', 'bold');
                }
            }
       });

       // Auto-refresh functionality
        var autoRefreshInterval = setInterval(function() {
            $('#tokenTable').DataTable().ajax.reload(null, false);
        }, 3000);

        // Clean up on page unload
        $(window).on('beforeunload', function() {
            clearInterval(autoRefreshInterval);
        });

        // Pause auto-refresh when modals are open
        $(document).on('shown.bs.modal', function() {
            clearInterval(autoRefreshInterval);
        }).on('hidden.bs.modal', function() {
            autoRefreshInterval = setInterval(function() {
                $('#tokenTable').DataTable().ajax.reload(null, false);
            }, 3000);
        });

        // Filter event handlers
        $('#tokenFilter, #examineeFilter, #statusFilter').on('change keyup', function() {
            table.ajax.reload();
        });

        // Reset filters button
        $('#resetFilters').click(function () {
            // Get current filter values
            const currentFilters = {
                token: $('#tokenFilter').val('all'),
                examinee: $('#examineeFilter').val('all'),
                status: $('#statusFilter').val('all')
            };

            // Check if any filter is not at its default value
            const needsReset = currentFilters.token !== '' || currentFilters.examinee !== '' || currentFilters.status !== '';

            // Only reset if needed
            if (needsReset) {
                // Clear input fields
                $('#tokenFilter').val('all').trigger('change');
                $('#examineeFilter').val('all').trigger('change');
                $('#statusFilter').val('all').trigger('change');
                // Reload DataTable
                $('#tokenTable').DataTable().ajax.reload();
            }
        });
    });
</script>
<script>
    // QR Code Modal
    $(document).on('click', '.qr-btn', function(e) {
        e.preventDefault();
        const tokenId = $(this).data('token-id');
        const token = $(this).data('token');
        const qrCodeUrl = $(this).attr('href');
        
        Swal.fire({
            title: 'Token QR Code',
            html: `<div class="text-center">
                    <img src="${qrCodeUrl}" class="img-fluid mb-3" alt="QR Code" 
                        onerror="this.onerror=null;this.src='data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';">
                    <p class="text-monospace">${token}</p>
                </div>`,
            showCloseButton: true,
            showConfirmButton: false,
            width: 600,
            height: 600,
            didOpen: () => {
                // Force refresh the image if it fails to load
                const img = Swal.getHtmlContainer().querySelector('img');
                img.onerror = function() {
                    this.src = qrCodeUrl + '&t=' + new Date().getTime();
                };
            }
        });
    });

    // Delete Token
    $(document).on('click', '.delete-btn', function() {
        const tokenId = $(this).data('token-id');
        
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
                    url: "{{ route('tokens.delete') }}",
                    method: 'POST',
                    data: {
                        ids: [tokenId],
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#tokenTable').DataTable().ajax.reload();
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        toastr.error('An error occurred while deleting the token');
                    }
                });
            }
        });
    });
</script>
<script>
    // Generate Tokens Form Handling
    $('#generateTokenForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = $('#generateTokensBtn');
        const originalBtnText = submitBtn.html();
        
        // Clear previous errors
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').remove();
        
        // Disable button and show loading state
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Generating...');
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 3000,
                        progressBar: true
                    });
                    // Close modal
                    $('#generate_token_modal').modal('hide');
                    // Reset form
                    form[0].reset();
                    // Reload table
                    $('#tokenTable').DataTable().ajax.reload();
                } else {
                    toastr.error(response.message, 'Error', {
                        timeOut: 3000,
                        progressBar: true
                    });
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    // Validation errors
                    const errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        const input = form.find('[name="' + key + '"]');
                        input.addClass('is-invalid');
                        input.after('<div class="invalid-feedback">' + value + '</div>');
                    });
                } else {
                    toastr.error('An error occurred while generating tokens', 'Error', {
                        timeOut: 3000,
                        progressBar: true
                    });
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalBtnText);
            }
        });
    });

    // Reset form when modal is closed
    $('#generate_token_modal').on('hidden.bs.modal', function() {
        $('#generateTokenForm')[0].reset();
        $('#generateTokenForm').find('.is-invalid').removeClass('is-invalid');
        $('#generateTokenForm').find('.invalid-feedback').remove();
        $('#generateTokensBtn').prop('disabled', false).html('Generate Tokens');
    });
</script>
@endsection