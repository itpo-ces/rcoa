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
<!-- FancyBox CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/fancybox/dist/jquery.fancybox.min.css" />
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

    #questionnaireTable td {
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
            <h1>Questionnaire</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
              <li class="breadcrumb-item active">Questionnaire</li>
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
                    <h3 class="card-title">Questionnaire List</h3>
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
                    <table id="questionnaireTable" class="table table-sm table-hover text-center">
                        <thead>
                            <tr>
                                <th class="hidden">Exam ID</th>
                                <th>Exam</th>
                                <th>Question</th>
                                <th>Type</th>
                                <th>Difficulty</th>
                                <th>Correct Answer</th>
                                <th>Options</th>
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

<!-- Add Item Modal -->
<div class="modal fade" id="add_item_modal" tabindex="-1" role="dialog" aria-labelledby="addItemModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <h5 class="modal-title" id="addItemModalLabel">Add New Item to Questionnaire</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addItemForm" method="POST" action="{{ route('questionnaire.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <div class="alert custom-alert" style="background-color: #e7f4ff; color: #004085; border-left: 4px solid #007bff; padding: 12px 15px;">
                                <small>
                                    <strong class="alert-heading" style="font-weight: 600; color: #004085;"><i class="fas fa-info-circle mr-2"></i>Before Saving:</strong>
                                    <span style="color: #004085;">Please carefully review all entries before submitting. The following information will be used in exams and assessments:</span>
                                    <ul class="mb-0 pl-3" style="color: #004085;">
                                        <li>Select the correct <strong>Exam</strong> this question belongs to</li>
                                        <li>Choose the appropriate <strong>Question Type</strong> (Multiple Choice, Fill in the Blanks, etc.)</li>
                                        <li>Set the proper <strong>Difficulty Level</strong> for the question</li>
                                        <li>Ensure the <strong>Question Text</strong> is clear and free of errors</li>
                                        <li>Provide a complete and accurate <strong>Correct Answer</strong></li>
                                        <li>For multiple choice questions, include all possible <strong>Options</strong> in JSON format</li>
                                        <li>Incomplete or inaccurate questions may affect exam quality and scoring</li>
                                    </ul>
                                </small>
                            </div>
                            <hr style="margin-bottom: 0rem;">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="exam_id">Exam<span class="text-danger">*</span></label>
                                <select class="form-control select2bs4" name="exam_id" id="exam_id" style="width: 100%;">
                                    <option value="">Select Exam</option>
                                    @foreach($exams as $exam)
                                        <option value="{{ $exam->id }}">{{ $exam->title }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="type">Type<span class="text-danger">*</span></label>
                                <select class="form-control select2bs4" name="type" id="type" style="width: 100%;">
                                    <option value="">Select Type</option>
                                    <option value="multiple_choice">Multiple Choice</option>
                                    <option value="fill_in_the_blanks">Fill in the Blanks</option>
                                    <option value="true_or_false">True or False</option>
                                    <option value="yes_or_no">Yes or No</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="difficulty">Difficulty<span class="text-danger">*</span></label>
                                <select class="form-control select2bs4" name="difficulty" id="difficulty" style="width: 100%;">
                                    <option value="">Select Difficulty</option>
                                    <option value="easy">Easy</option>
                                    <option value="moderate">Moderate</option>
                                    <option value="difficult">Difficult</option>
                                    <option value="extra_difficult">Extra Difficult</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="question_text">Question<span class="text-danger">*</span></label>
                                <textarea class="form-control" id="question_text" name="question_text" rows="3" placeholder="Enter the question here..."></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="correct_answer">Correct Answer<span class="text-danger">*</span></label>
                                <textarea class="form-control" id="correct_answer" name="correct_answer" rows="3" placeholder="Enter the correct answer here..."></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="options">Options<span class="text-danger">*</span> <span class="text-muted"><em>(JSON Format)</em></span></label>
                                <textarea class="form-control" id="options" name="options" rows="3" placeholder='["Option 1", "Option 2", "Option 3", "Option 4"]'></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveItemBtn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Import Questionnaire Modal -->
<div class="modal fade" id="import_questionnaire_modal" tabindex="-1" role="dialog" aria-labelledby="importQuestionnaireLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <h5 class="modal-title" id="importInventoryLabel">Import Questionnaire</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert custom-alert" style="background-color: #e7f4ff; color: #004085; border-left: 4px solid #007bff; padding: 12px 15px;">
                    <small>
                        <strong class="alert-heading" style="font-weight: 600; color: #004085;">
                            <i class="fas fa-info-circle mr-2"></i>Before Importing:
                        </strong>
                        <span style="color: #004085;">Please verify the Excel file format and content before proceeding with the import:</span>
                        <ul class="mb-0 pl-3" style="color: #004085;">
                            <li>Only <strong>.xls</strong> or <strong>.xlsx</strong> file formats are accepted</li>
                            <li>Ensure all required columns are present: <em>Exam ID, Question, Type, Difficulty, Correct Answer, Options</em></li>
                            <li>For multiple choice questions, provide options as a JSON array in the Options column</li>
                            <li>Question types must be one of: multiple_choice, fill_in_the_blanks, true_or_false, yes_or_no</li>
                            <li>Difficulty levels must be one of: easy, moderate, difficult, extra_difficult</li>
                        </ul>
                    </small>
                </div>
                <!-- File Upload Form -->
                <form id="importInventoryForm">
                    @csrf
                    <div class="form-group">
                        <label for="file">Upload Excel File <em>(.xls or .xlsx only)</em><span style="color:red">*</span>:</label>
                        <input type="file" name="file" id="file" accept=".xls,.xlsx" class="form-control custom-hover" required>
                    </div>
                </form>
                <div id="loader" style="text-align: center; display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden"><i class="fas fa-fan"></i></span>
                    </div>
                </div>
                <!-- Table to Display Excel Data -->
                <div class="table-responsive mt-3">
                    <table id="importQuestionnaireTable" class="table table-bordered table-striped" style="display: none;">
                        <thead>
                            <tr>
                                <th>Exam</th>
                                <th>Question</th>
                                <th>Type</th>
                                <th>Difficulty</th>
                                <th>Correct Answer</th>
                                <th>Options</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Rows will be populated dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="confirmImport">Import</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Item Modal -->
<div class="modal fade" id="edit_item_modal" tabindex="-1" role="dialog" aria-labelledby="addItemModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header btn-primary">
                <h5 class="modal-title" id="addItemModalLabel">Edit Questionnaire Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editItemForm" method="POST" action="{{ route('questionnaire.update') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" id="edit_item_id">
                <div class="modal-body">
                <div class="row mb-2">
                    <div class="col-md-12">
                        <div class="alert custom-alert" style="background-color: #e7f4ff; color: #004085; border-left: 4px solid #007bff; padding: 12px 15px;">
                            <small>
                                <strong class="alert-heading" style="font-weight: 600; color: #004085;"><i class="fas fa-info-circle mr-2"></i>Before Saving:</strong>
                                <span style="color: #004085;">Please review all entries before submitting. The following information will directly affect inventory records, issuance logs, and future audits:</span>
                                <ul class="mb-0 pl-3" style="color: #004085;">
                                    <li>Ensure the <strong>Serial Number</strong> is correct and unique</li>
                                    <li>If applicable, specify the correct quantity of <strong>Magazines</strong> and <strong>Rounds</strong></li>
                                    <li>Select the appropriate <strong>Repository</strong> and <strong>Status</strong> of the item</li>
                                    <li>Indicate the correct <strong>Primary User</strong> and their corresponding <strong>Unit</strong></li>
                                    <li>Choose the proper <strong>Category</strong>, <strong>Type</strong>, <strong>Model</strong>, and <strong>Caliber</strong></li>
                                    <li>Incomplete or inaccurate information may affect system tracking and reports</li>
                                </ul>
                            </small>
                        </div>
                        <hr style="margin-bottom: 0rem;">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="edit_exam_id">Exam<span class="text-danger">*</span></label>
                            <select class="form-control select2bs4" name="exam_id" id="edit_exam_id" style="width: 100%;">
                                <option value="">Select Exam</option>
                                @foreach($exams as $exam)
                                    <option value="{{ $exam->id }}">{{ $exam->title }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="edit_type">Type<span class="text-danger">*</span></label>
                            <select class="form-control select2bs4" name="type" id="edit_type" style="width: 100%;">
                                <option value="">Select Type</option>
                                <option value="multiple_choice">Multiple Choice</option>
                                <option value="fill_in_the_blanks">Fill in the Blanks</option>
                                <option value="true_or_false">True or False</option>
                                <option value="yes_or_no">Yes or No</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="edit_difficulty">Difficulty<span class="text-danger">*</span></label>
                            <select class="form-control select2bs4" name="difficulty" id="edit_difficulty" style="width: 100%;">
                                <option value="">Select Difficulty</option>
                                <option value="easy">Easy</option>
                                <option value="moderate">Moderate</option>
                                <option value="difficult">Difficult</option>
                                <option value="extra_difficult">Extra Difficult</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="edit_question_text">Question<span class="text-danger">*</span></label>
                            <textarea class="form-control" id="edit_question_text" name="question_text" rows="3" placeholder="Enter the question here..."></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="edit_correct_answer">Correct Answer<span class="text-danger">*</span></label>
                            <textarea class="form-control" id="edit_correct_answer" name="correct_answer" rows="3" placeholder="Enter the correct answer here..."></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="edit_options">Options<span class="text-danger">*</span> <span class="text-muted"><em>(JSON Format)</em></span></label>
                            <textarea class="form-control" id="edit_options" name="options" rows="6" placeholder="['Option 1', 'Option 2', 'Option 3', 'Option 4']"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
            </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="updateItemBtn">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Item Modal -->
<div class="modal fade" id="delete_item_modal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="deleteUserForm" method="POST" action="{{ route('questionnaire.delete') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="delete_id" value="">
                    <p class="text-center">Are you sure you want to delete this question?<br>
                    <strong class="text-muted" id="delete_question"></strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger" id="confirmDelete">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Restore Item Modal -->
<div class="modal fade" id="restore_item_modal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="restoreUserForm" method="POST" action="{{ route('questionnaire.restore') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="restoreModalLabel">Restore Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="restore_id" value="">
                    <p class="text-center">Are you sure you want to restore this question?<br> <strong id="restore_question"></strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning" id="confirmRestore">Restore</button>
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
<!-- FAncybox JS -->
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/fancybox/dist/jquery.fancybox.min.js"></script>
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
    $(document).ready(function() {
        $('[data-fancybox="qrcodes"]').fancybox({
            buttons: [
                "zoom",
                "share",
                "slideShow",
                "fullScreen",
                "download",
                "thumbs",
                "close"
            ],
            arrows: false
        });
    });
</script>
<script>
    // Use Ajax to fetch data from the server based on filter options
    $(document).ready(function() {
       var table = $('#questionnaireTable').DataTable({
        dom: '<"d-flex justify-content-between"lBf>t<"d-flex justify-content-between"ip>',
        buttons: [
            { 
                extend: 'excel', 
                title: 'Questionnaire List',
                className: 'btn-default'
            },
            { 
                extend: 'print', 
                title: 'Questionnaire List',
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
                url: "{{ route('questionnaire.data') }}",
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
                { data: 'id', name: 'id', class: 'hidden', orderable: false, searchable: false  },
                { data: 'exam', name: 'exam', orderable: false, searchable: false  },
                { data: 'question', name: 'question', orderable: false, searchable: false  },
                { data: 'type', name: 'type', orderable: false, searchable: false  },
                { data: 'difficulty', name: 'difficulty', orderable: false, searchable: false  },
                { data: 'answer', name: 'answer', orderable: false, searchable: false  },
                { data: 'options', name: 'options', orderable: false, searchable: false  },
                { data: 'status', name: 'status', orderable: false, searchable: false  },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            order: [[0, 'desc']],
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
                difficulty: $('#difficultyFilter').val('all'),
                type: $('#typeFilter').val('all'),
            };

            // Check if any filter is not at its default value
            const needsReset = currentFilters.exam !== '' || currentFilters.difficulty !== '' || currentFilters.type !== '';

            // Only reset if needed
            if (needsReset) {
                // Clear input fields
                $('#examFilter').val('').trigger('change');
                $('#difficultyFilter').val('').trigger('change');
                $('#typeFilter').val('').trigger('change');
                // Reload DataTable
                $('#questionnaireTable').DataTable().ajax.reload();
            }
        });
    });
</script>
<script>
    // Inside the JavaScript file or script section where you handle modal opening
    $('#edit_item_modal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
            var item = button.data('item');

            console.log(item);

            $('#edit_item_id').val(item.id);
            $('#edit_exam_id').val(item.exam_id).trigger('change');
            $('#edit_type').val(item.type).trigger('change');
            $('#edit_difficulty').val(item.difficulty).trigger('change');
            $('#edit_question_text').val(item.question_text);
            $('#edit_correct_answer').val(item.correct_answer);
            $('#edit_options').val(item.options);
    });

    $('#delete_item_modal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var item = button.data('item');
        var itemID = item.id;

        console.log(item);

        $('#delete_id').val(item.id);
        $('#delete_question').text(item.question_text);

        // Detach previous click handlers to avoid multiple triggers
        $('#confirmDelete').off('click').on('click', function(e) {
            e.preventDefault();
            $.ajax({
                url: $('#deleteUserForm').attr('action'),
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    id: itemID
                },
                success: function(response) {
                    $('#delete_item_modal').modal('hide');
                    try {
                        $('#questionnaireTable').DataTable().ajax.reload(null, false);
                    } catch (e) {
                        console.error('DataTable reload error:', e);
                    }
                    toastr.success(response.message || 'Item deleted successfully!', 'Success', {
                        timeOut: 3000,
                        progressBar: true,
                        closeButton: true,
                        newestOnTop: true
                    });
                },
                error: function(xhr) {
                    toastr.error(
                        xhr.responseJSON?.message || 'An error occurred while deleting the item. Please try again.',
                        'Error',
                        { timeOut: 3000, progressBar: true }
                    );
                }
            });
        });
    });

    $('#restore_item_modal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var item = button.data('item');
        var itemID = item.id;

        console.log(item);

        $('#restore_id').val(item.id);
        $('#restore_question').text(item.question_text);

        // Detach previous click handlers to avoid multiple triggers
        $('#confirmRestore').off('click').on('click', function(e) {
            e.preventDefault();
            $.ajax({
                url: $('#restoreUserForm').attr('action'),
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    id: itemID
                },
                success: function(response) {
                    $('#restore_item_modal').modal('hide');
                    try {
                        $('#questionnaireTable').DataTable().ajax.reload(null, false);
                    } catch (e) {
                        console.error('DataTable reload error:', e);
                    }
                    toastr.success(response.message || 'Item restored successfully!', 'Success', {
                        timeOut: 3000,
                        progressBar: true,
                        closeButton: true,
                        newestOnTop: true
                    });
                },
                error: function(xhr) {
                    toastr.error(
                        xhr.responseJSON?.message || 'An error occurred while restoring the item. Please try again.',
                        'Error',
                        { timeOut: 3000, progressBar: true }
                    );
                }
            });
        });
    });
</script>
<script>
    // Handle add item form submission
    $('#addItemForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = $('#saveItemBtn');
        const originalBtnText = submitBtn.html();

        clearErrors();
        disableSubmitButton();
        
        // Try to parse options as JSON if it's a string
        const optionsInput = $('#options');
        if (optionsInput.val().trim() !== '') {
            try {
                JSON.parse(optionsInput.val());
            } catch (e) {
                showFieldError(optionsInput, 'Invalid JSON format for options');
                enableSubmitButton();
                return;
            }
        }
        
        // Prepare form data
        const formData = new FormData(form[0]);
            
        // AJAX request
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: handleSuccess,
            error: handleError,
            complete: enableSubmitButton
        });

        // Helper function to show field-specific errors
        function showFieldError(field, message) {
            field.addClass('is-invalid');
            const existingFeedback = field.next('.invalid-feedback');
            if (existingFeedback.length) {
                existingFeedback.text(message);
            } else {
                field.closest('.form-group').append('<div class="invalid-feedback">' + message + '</div>');
            }
        }

        // Handle clearing of previous errors
        function clearErrors() {
            form.find('.is-invalid').removeClass('is-invalid');
            form.find('.invalid-feedback').remove();
        }

        // Handle submit button state
        function enableSubmitButton() {
            submitBtn.prop('disabled', false).html('Save');
        }
        
        function disableSubmitButton() {
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
        }

        function handleSuccess(response) {
            toastr.success(response.message || 'Question added successfully!', 'Success', {
                timeOut: 3000,
                progressBar: true,
                closeButton: true,
                newestOnTop: true
            });
            enableSubmitButton();
            // Reset form
            form[0].reset();
            clearErrors();
            // Hide modal
            $('#add_item_modal').modal('hide');
            // Reload DataTable
            $('#questionnaireTable').DataTable().ajax.reload();
        }

        function handleError(xhr) {
            enableSubmitButton();
            
            if (xhr.status === 422) {
                // Validation errors
                const errors = xhr.responseJSON.errors;
                $.each(errors, function(key, value) {
                    const input = form.find('[name="' + key + '"]');
                    showFieldError(input, Array.isArray(value) ? value[0] : value);
                });
            } else {
                const errorMessage = xhr.responseJSON && xhr.responseJSON.message 
                    ? xhr.responseJSON.message 
                    : 'An error occurred. Please try again.';
                    
                toastr.error(errorMessage, 'Error', {
                    timeOut: 3000,
                    progressBar: true,
                    closeButton: true,
                    newestOnTop: true
                });
            }
        }
    });

    // Event listener for modal close
    $('#add_item_modal').on('hidden.bs.modal', function() {
        $('#addItemForm')[0].reset();
        $('#addItemForm').find('.is-invalid').removeClass('is-invalid');
        $('#addItemForm').find('.invalid-feedback').remove();
        $('#saveItemBtn').prop('disabled', false).html('Save');
    });

    // Optional: Add real-time JSON validation for options field
    $('#options').on('blur', function() {
        const field = $(this);
        const value = field.val().trim();
        
        // Clear previous error
        field.removeClass('is-invalid');
        field.next('.invalid-feedback').remove();
        
        if (value) {
            try {
                const parsed = JSON.parse(value);
                if (!Array.isArray(parsed)) {
                    field.addClass('is-invalid');
                    field.closest('.form-group').append('<div class="invalid-feedback">Options must be a JSON array.</div>');
                }
            } catch (e) {
                field.addClass('is-invalid');
                field.closest('.form-group').append('<div class="invalid-feedback">Invalid JSON format.</div>');
            }
        }
    });
</script>
<script>
    // Handle add item form submission
    $('#editItemForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = $('#updateItemBtn');
        const originalBtnText = submitBtn.html();

        clearErrors();
        disableSubmitButton();

        // Try to parse options as JSON if it's a string
        const optionsInput = $('#options');
        if (optionsInput.val().trim() !== '') {
            try {
                JSON.parse(optionsInput.val());
            } catch (e) {
                showFieldError(optionsInput, 'Invalid JSON format for options');
                enableSubmitButton();
                return;
            }
        }

        // Prepare form data
        const formData = new FormData(form[0]);
               
        // AJAX request
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: handleSuccess,
            error: handleError,
            complete: enableSubmitButton
        });

        // Helper function to show field-specific errors
        function showFieldError(field, message) {
            field.addClass('is-invalid');
            const existingFeedback = field.next('.invalid-feedback');
            if (existingFeedback.length) {
                existingFeedback.text(message);
            } else {
                field.closest('.form-group').append('<div class="invalid-feedback">' + message + '</div>');
            }
        }

        // Handle clearing of previous errors
        function clearErrors() {
            form.find('.is-invalid').removeClass('is-invalid');
            form.find('.invalid-feedback').remove();
        }

        // Handle submit button state
        function enableSubmitButton() {
            submitBtn.prop('disabled', false).html('Save');
        }
        
        function disableSubmitButton() {
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
        }

        function handleSuccess(response) {
            toastr.success(response.message || 'Item added successfully!', 'Success', {
                timeOut: 3000,
                progressBar: true,
                closeButton: true,
                newestOnTop: true
            });
            enableSubmitButton();
            // Reset form
            form[0].reset();
            clearErrors();
            // Hide modal
            $('#edit_item_modal').modal('hide');
            // Reload DataTable
            $('#questionnaireTable').DataTable().ajax.reload();
        }

        function handleError(xhr) {
            enableSubmitButton();
            
            if (xhr.status === 422) {
                // Validation errors
                const errors = xhr.responseJSON.errors;
                $.each(errors, function(key, value) {
                    const input = form.find('[name="' + key + '"]');
                    showFieldError(input, Array.isArray(value) ? value[0] : value);
                });
            } else {
                const errorMessage = xhr.responseJSON && xhr.responseJSON.message 
                    ? xhr.responseJSON.message 
                    : 'An error occurred. Please try again.';
                    
                toastr.error(errorMessage, 'Error', {
                    timeOut: 3000,
                    progressBar: true,
                    closeButton: true,
                    newestOnTop: true
                });
            }
        }
    });

    // Event listener for modal close
    $('#edit_item_modal').on('hidden.bs.modal', function() {
        $('#editItemForm')[0].reset();
        $('#editItemForm').find('.is-invalid').removeClass('is-invalid');
        $('#editItemForm').find('.invalid-feedback').remove();
        $('#updateItemBtn').prop('disabled', false).html('Save');
    });

    // Optional: Add real-time JSON validation for options field
    $('#options').on('blur', function() {
        const field = $(this);
        const value = field.val().trim();
        
        // Clear previous error
        field.removeClass('is-invalid');
        field.next('.invalid-feedback').remove();
        
        if (value) {
            try {
                const parsed = JSON.parse(value);
                if (!Array.isArray(parsed)) {
                    field.addClass('is-invalid');
                    field.closest('.form-group').append('<div class="invalid-feedback">Options must be a JSON array.</div>');
                }
            } catch (e) {
                field.addClass('is-invalid');
                field.closest('.form-group').append('<div class="invalid-feedback">Invalid JSON format.</div>');
            }
        }
    });
</script>
{{-- <script>
$(document).ready(function () {
    const requiredHeaders = [
        "exam_id", "question_text", "type", "difficulty",
        "correct_answer", "options"
    ];

    let inventoryData = [];

    // Read Excel file and display in table
    $("#file").on("change", function (event) {
        $("#loader").show();
        const file = event.target.files[0];

        if (!file) {
            toastr.error("Please select a file!", "Error");
            $("#loader").hide();
            return;
        }

        const fileExtension = file.name.split('.').pop().toLowerCase();
        if (!["xls", "xlsx"].includes(fileExtension)) {
            toastr.error("Invalid file type.", "Error");
            $("#loader").hide();
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            const data = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, { type: "array" });
            const sheetName = workbook.SheetNames[0];
            const sheet = workbook.Sheets[sheetName];
            const jsonData = XLSX.utils.sheet_to_json(sheet, { header: 1 });

            if (jsonData.length > 0) {
                const headers = jsonData[0].map(header => header.toString().trim().toLowerCase());
                const isValid = requiredHeaders.every(header => headers.includes(header));

                if (!isValid) {
                    toastr.error("Invalid file format! Required headers: " + requiredHeaders.join(", "), "Error");
                    $("#loader").hide();
                    return;
                }

                inventoryData = [];
                let tableRows = "";

                jsonData.slice(1).forEach((row) => {
                    let rowData = {};
                    let isValidRow = true;
                    let tableRow = "<tr>";

                    requiredHeaders.forEach((header, index) => {
                        let cellValue = row[index] || "";
                        if (header === "serial" && !cellValue) isValidRow = false;
                        rowData[header] = cellValue;
                        tableRow += `<td>${cellValue}</td>`;
                    });

                    tableRow += "</tr>";
                    if (isValidRow) {
                        inventoryData.push(rowData);
                        tableRows += tableRow;
                    }
                });

                // Populate modal table
                $("#importQuestionnaireTable tbody").html(tableRows);
                $("#importQuestionnaireTable").show();

                // Destroy existing DataTable instance if re-importing
                if ($.fn.DataTable.isDataTable("#importQuestionnaireTable")) {
                    $("#importQuestionnaireTable").DataTable().destroy();
                }

                // Initialize DataTables
                $("#importQuestionnaireTable").DataTable({
                    pageLength: 10
                });
                toastr.success(`Loaded ${inventoryData.length} records.`, "Success");
            } else {
                toastr.error("The uploaded file is empty!", "Error");
            }
            $("#loader").hide();
        };
        reader.readAsArrayBuffer(file);
    });

    // Upload data in chunks
    function uploadChunkedData() {
        let chunkSize = 100;
        let chunks = [];

        for (let i = 0; i < inventoryData.length; i += chunkSize) {
            chunks.push(inventoryData.slice(i, i + chunkSize));
        }

        let uploadedChunks = 0;
        let totalSuccessCount = 0;
        let totalSkippedCount = 0;
        let totalFailedRecords = [];
        let totalSkippedInventory = [];

        function uploadNextChunk() {
            if (uploadedChunks >= chunks.length) {
                // All chunks have been processed
                let message = "";
                if (totalSuccessCount > 0) {
                    message += `Successfully imported ${totalSuccessCount} records. `;
                }
                if (totalSkippedCount > 0) {
                    message += `${totalSkippedCount} records were skipped. `;
                }
                if (totalFailedRecords.length > 0) {
                    message += `${totalFailedRecords.length} records failed to import.`;
                }

                if (message === "") {
                    message = "No new records were imported.";
                }

                let htmlContent = `${message}`;
                if (totalSkippedInventory.length > 0) {
                    htmlContent += `<br><br><button id="viewSkipped" class="btn btn-primary">View Skipped Records</button>`;
                }

                Swal.fire({
                    icon: totalSuccessCount > 0 ? "success" : "info",
                    title: "Import Completed!",
                    html: htmlContent,
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    timer: (totalSkippedInventory.length === 0) ? 2000 : undefined
                }).then(() => {
                    if (totalSkippedInventory.length === 0) {
                        $("#import_questionnaire_modal").modal("hide");
                        location.reload();
                    }
                });

                // Add event listener for the "View Skipped Records" button
                if (totalSkippedInventory.length > 0) {
                    $(document).on("click", "#viewSkipped", function () {
                        // Generate HTML for skipped inventory table
                        let skippedTable = `
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Exam ID</th>
                                            <th>Exam Title</th>
                                            <th>Question</th>
                                            <th>Type</th>
                                            <th>Difficulty</th>
                                            <th>Correct Answer</th>
                                            <th>Options</th>
                                            <th>Reason</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${totalSkippedInventory.map(item => {
                                            return `
                                                <tr>
                                                    <td>${item.exam_id}</td>
                                                    <td>${item.exam_title || 'N/A'}</td>
                                                    <td>${item.question_text || 'N/A'}</td>
                                                    <td>${item.type || 'N/A'}</td>
                                                    <td>${item.difficulty || 'N/A'}</td>
                                                    <td>${item.correct_answer || 'N/A'}</td>
                                                    <td>${item.options || 'N/A'}</td>
                                                    <td class="${item.reason.includes('restriction') ? 'text-danger' : 'text-warning'}">${item.reason}</td>
                                                </tr>
                                            `;
                                        }).join("")}
                                    </tbody>
                                </table>
                            </div>
                            <br>
                            <button id="exportSkipped" class="btn btn-success">Export to Excel</button>
                        `;

                        // Show skipped inventory in a new SweetAlert dialog
                        Swal.fire({
                            title: "Skipped Records",
                            html: skippedTable,
                            width: "90%",
                            showConfirmButton: false,
                            showCancelButton: true,
                            cancelButtonText: "Close",
                            allowOutsideClick: false,
                        }).then((result) => {
                            if (result.dismiss === Swal.DismissReason.cancel) {
                                $("#import_questionnaire_modal").modal("hide");
                                location.reload();
                            }
                        });

                        // Export functionality
                        $(document).on("click", "#exportSkipped", function () {
                            const exportData = totalSkippedInventory.map(item => ({
                                "Exam ID": item.exam_id,
                                "Exam Title": item.exam_title || 'N/A',
                                "Question": item.question_text || 'N/A',
                                "Type": item.type || 'N/A',
                                "Difficulty": item.difficulty || 'N/A',
                                "Correct Answer": item.correct_answer || 'N/A',
                                "Options": item.options || 'N/A',
                                "Reason": item.reason
                            }));

                            const worksheet = XLSX.utils.json_to_sheet(exportData);
                            const workbook = XLSX.utils.book_new();
                            XLSX.utils.book_append_sheet(workbook, worksheet, "Skipped Records");
                            XLSX.writeFile(workbook, "skipped_records.xlsx");
                        });
                    });
                }

                return;
            }

            $.ajax({
                url: "{{ route('questionnaire.import') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    data: chunks[uploadedChunks]
                },
                success: function (response) {
                    if (response.success) {
                        totalSuccessCount += response.successCount || 0;
                        totalSkippedCount += response.skippedCount || 0;
                        
                        if (response.failedRecords && response.failedRecords.length > 0) {
                            totalFailedRecords = totalFailedRecords.concat(response.failedRecords);
                        }
                        
                        if (response.skippedInventory && response.skippedInventory.length > 0) {
                            totalSkippedInventory = totalSkippedInventory.concat(response.skippedInventory);
                        }
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Import Failed",
                            text: response.message || "An error occurred while importing data.",
                            showConfirmButton: true
                        });
                        return;
                    }

                    uploadedChunks++;
                    uploadNextChunk();
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: "error",
                        title: "Import Failed",
                        text: xhr.responseJSON?.message || "An error occurred while uploading data.",
                        showConfirmButton: true
                    });
                }
            });
        }

        uploadNextChunk();
    }

    $("#confirmImport").on("click", function () {
        if (inventoryData.length === 0) {
            toastr.error("No data to import.", "Error");
            return;
        }

        Swal.fire({
            icon: "info",
            title: "Importing Data...",
            text: "Please wait while inventory data is being imported.",
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        uploadChunkedData();
    });

    // Reset function
    function resetImportModal() {
        const fileInput = $('#file');
        fileInput.wrap('<form>').closest('form').get(0).reset();
        fileInput.unwrap();
        
        $('#importQuestionnaireTable').hide().find('tbody').empty();
        inventoryData = [];
        $('#loader').hide();
    }

    // Set up modal handlers
    $('#import_questionnaire_modal')
        .on('hidden.bs.modal', resetImportModal)
        .on('show.bs.modal', function() {
            $('.is-invalid').removeClass('is-invalid');
        });
});
</script> --}}
<script>
    $(document).ready(function () {
        const requiredHeaders = [
                "exam_id", "question_text", "type", "difficulty",
                "correct_answer", "options"
            ];

        let inventoryData = [];
        let loadingSwal = null;
        let totalSkippedQuestions = [];

        // Make function globally accessible
        window.showSkippedRecords = function(skippedData) {
            // Parse the data if it's a string
            if (typeof skippedData === 'string') {
                try {
                    skippedData = JSON.parse(skippedData);
                } catch (e) {
                    console.error('Error parsing skipped data:', e);
                    return;
                }
            }

            // Show loading indicator
            showLoadingDataIndicator("Preparing Skipped Records", "Please wait while we prepare the skipped records...");
            
            let skippedTable = `
                <div class="table-responsive" style="max-height: 60vh; overflow-y: auto;">
                    <table class="table table-sm table-bordered">
                        <thead class="thead-light sticky-top">
                            <tr>
                                <th>Exam ID</th>
                                <th>Exam Title</th>
                                <th>Question</th>
                                <th>Type</th>
                                <th>Difficulty</th>
                                <th>Correct Answer</th>
                                <th>Options</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${skippedData.map(item => `
                                <tr>
                                    <td>${item.exam_id}</td>
                                    <td>${item.exam_title || 'None'}</td>
                                    <td>${item.question_text || 'None'}</td>
                                    <td>${item.type || 'None'}</td>
                                    <td>${item.difficulty || 'None'}</td>
                                    <td>${item.correct_answer || 'None'}</td>
                                    <td>${item.options || 'None'}</td>
                                    <td class="${item.reason.includes('restriction') ? 'text-danger' : 'text-warning'}">${item.reason}</td>
                                </tr>
                            `).join("")}
                        </tbody>
                    </table>
                </div>
                <br>
                <button id="exportSkipped" class="btn btn-success">Export to Excel</button>
            `;

            closeLoadingDataIndicator();
            
            Swal.fire({
                title: "Skipped Records",
                html: skippedTable,
                width: "90%",
                showConfirmButton: false,
                showCancelButton: true,
                cancelButtonText: "Close",
                allowOutsideClick: false,
                didOpen: () => {
                    $(document).on("click", "#exportSkipped", function () {
                        showLoadingDataIndicator("Exporting Data", "Preparing Excel file...");
                        
                        const exportData = skippedData.map(item => ({
                            "Exam ID": item.exam_id,
                            "Exam Title": item.exam_title || 'None',
                            "Question": item.question_text || 'None',
                            "Type": item.type || 'None',
                            "Difficulty": item.difficulty || 'None',
                            "Correct Answer": item.correct_answer || 'None',
                            "Options": item.options || 'None',
                            "Reason": item.reason || 'None'
                        }));

                        const worksheet = XLSX.utils.json_to_sheet(exportData);
                        const workbook = XLSX.utils.book_new();
                        XLSX.utils.book_append_sheet(workbook, worksheet, "Skipped Records");
                        XLSX.writeFile(workbook, "skipped_records.xlsx");
                        
                        closeLoadingDataIndicator();
                        $("#import_questionnaire_modal").modal("hide");
                        location.reload();
                    });
                }
            }).then((result) => {
                if (result.dismiss === Swal.DismissReason.cancel) {
                    $("#import_questionnaire_modal").modal("hide");
                    location.reload();
                }
            });
        };

        function showLoadingDataIndicator(title = "Loading Data...", text = "Please wait while data is being loaded.") {
            loadingSwal = Swal.fire({
                icon: "info",
                title: title,
                text: text,
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        function closeLoadingDataIndicator() {
            if (loadingSwal) {
                loadingSwal.close();
                loadingSwal = null;
            }
        }

        // Read Excel file and display in table
        $("#file").on("change", function (event) {
            showLoadingDataIndicator();
            const file = event.target.files[0];

            if (!file) {
                toastr.error("Please select a file!", "Error");
                closeLoadingDataIndicator();
                return;
            }

            const fileExtension = file.name.split('.').pop().toLowerCase();
            if (!["xls", "xlsx"].includes(fileExtension)) {
                toastr.error("Invalid file type.", "Error");
                closeLoadingDataIndicator();
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, { type: "array" });
                const sheetName = workbook.SheetNames[0];
                const sheet = workbook.Sheets[sheetName];
                const jsonData = XLSX.utils.sheet_to_json(sheet, { header: 1 });

                if (jsonData.length > 0) {
                    const headers = jsonData[0].map(header => header.toString().trim().toLowerCase());
                    const isValid = requiredHeaders.every(header => headers.includes(header));

                    if (!isValid) {
                        toastr.error("Invalid file format! Required headers: " + requiredHeaders.join(", "), "Error");
                        closeLoadingDataIndicator();
                        return;
                    }

                    inventoryData = [];
                    let tableRows = "";

                    jsonData.slice(1).forEach((row) => {
                        let rowData = {};
                        let isValidRow = true;
                        let tableRow = "<tr>";

                        requiredHeaders.forEach((header, index) => {
                            let cellValue = row[index] || "";

                            if (header === "date_issued") {
                                if (typeof cellValue === "number") {
                                    const excelEpoch = new Date(1899, 11, 30);
                                    const jsDate = new Date(excelEpoch.getTime() + cellValue * 86400000);
                                    const yyyy = jsDate.getFullYear();
                                    const mm = String(jsDate.getMonth() + 1).padStart(2, '0');
                                    const dd = String(jsDate.getDate()).padStart(2, '0');
                                    cellValue = `${yyyy}-${mm}-${dd}`;
                                }
                            }
                            if (header === "serial_number" && !cellValue) isValidRow = false;
                            rowData[header] = cellValue;
                            tableRow += `<td>${cellValue}</td>`;
                        });

                        tableRow += "</tr>";
                        if (isValidRow) {
                            inventoryData.push(rowData);
                            tableRows += tableRow;
                        }
                    });

                    $("#importQuestionnaireTable tbody").html(tableRows);
                    $("#importQuestionnaireTable").show();

                    if ($.fn.DataTable.isDataTable("#importQuestionnaireTable")) {
                        $("#importQuestionnaireTable").DataTable().destroy();
                    }

                    $("#importQuestionnaireTable").DataTable({
                        pageLength: 10
                    });
                    toastr.success(`Loaded ${inventoryData.length} records.`, "Success");
                } else {
                    toastr.error("The uploaded file is empty!", "Error");
                }
                closeLoadingDataIndicator();
            };
            reader.readAsArrayBuffer(file);
        });

        // Upload data in chunks
        function uploadChunkedData() {
            let chunkSize = 100;
            let chunks = [];

            for (let i = 0; i < inventoryData.length; i += chunkSize) {
                chunks.push(inventoryData.slice(i, i + chunkSize));
            }

            let uploadedChunks = 0;
            let totalSuccessCount = 0;
            let totalSkippedCount = 0;
            let totalFailedRecords = [];
            totalSkippedInventory = [];

            function uploadNextChunk() {
                if (uploadedChunks >= chunks.length) {
                    let message = "";
                    if (totalSuccessCount > 0) {
                        message += `Successfully imported ${totalSuccessCount} records. `;
                    }
                    if (totalSkippedCount > 0) {
                        message += `${totalSkippedCount} records were skipped. `;
                    }
                    if (totalFailedRecords.length > 0) {
                        message += `${totalFailedRecords.length} records failed to import.`;
                    }

                    if (message === "") {
                        message = "No new records were imported.";
                    }

                    // Log skipped data for debugging
                    console.log('Skipped data:', totalSkippedQuestions, 'Stringified:', JSON.stringify(totalSkippedQuestions));

                    let htmlContent = `${message}`;
                    if (totalSkippedQuestions.length > 0) {
                        const skippedDataJson = encodeURIComponent(JSON.stringify(totalSkippedQuestions));
                        htmlContent += `
                            <br><br>
                            <button class="btn btn-primary" 
                                    onclick="window.showSkippedRecords(decodeURIComponent('${skippedDataJson}'))">
                                View Skipped Records
                            </button>
                        `;
                    }

                    closeLoadingDataIndicator();
                    
                    Swal.fire({
                        icon: totalSuccessCount > 0 ? "success" : "info",
                        title: "Import Completed!",
                        html: htmlContent,
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        timer: (totalSkippedInventory.length === 0) ? 2000 : undefined
                    }).then((result) => {
                        if (result.dismiss === Swal.DismissReason.timer && totalSkippedInventory.length === 0) {
                            $("#import_questionnaire_modal").modal("hide");
                            location.reload();
                        }
                    });

                    return;
                }

                $.ajax({
                    url: "{{ route('questionnaire.import') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        data: chunks[uploadedChunks]
                    },
                    success: function (response) {
                        if (response.success) {
                            totalSuccessCount += response.successCount || 0;
                            totalSkippedCount += response.skippedCount || 0;
                            
                            if (response.failedRecords && response.failedRecords.length > 0) {
                                totalFailedRecords = totalFailedRecords.concat(response.failedRecords);
                            }
                            
                            if (response.skippedQuestions && response.skippedQuestions.length > 0) {
                                totalSkippedQuestions = totalSkippedQuestions.concat(response.skippedQuestions);
                            }
                        } else {
                            closeLoadingDataIndicator();
                            Swal.fire({
                                icon: "error",
                                title: "Import Failed",
                                text: response.message || "An error occurred while importing data.",
                                showConfirmButton: true
                            });
                            return;
                        }

                        uploadedChunks++;
                        uploadNextChunk();
                    },
                    error: function (xhr) {
                        closeLoadingDataIndicator();
                        Swal.fire({
                            icon: "error",
                            title: "Import Failed",
                            text: xhr.responseJSON?.message || "An error occurred while uploading data.",
                            showConfirmButton: true
                        });
                    }
                });
            }

            uploadNextChunk();
        }

        $("#confirmImport").on("click", function () {
            if (inventoryData.length === 0) {
                toastr.error("No data to import.", "Error");
                return;
            }

            showLoadingDataIndicator("Importing Data", "Please wait while inventory data is being imported.");
            uploadChunkedData();
        });

        // Reset function
        function resetImportModal() {
            const fileInput = $('#file');
            fileInput.wrap('<form>').closest('form').get(0).reset();
            fileInput.unwrap();
            
            $('#importQuestionnaireTable').hide().find('tbody').empty();
            inventoryData = [];
            closeLoadingDataIndicator();
        }

        // Set up modal handlers
        $('#import_questionnaire_modal')
            .on('hidden.bs.modal', resetImportModal)
            .on('show.bs.modal', function() {
                $('.is-invalid').removeClass('is-invalid');
            });
    });
</script>
@endsection