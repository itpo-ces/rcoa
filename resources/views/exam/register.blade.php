@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Registration Form</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('exam.store-registration') }}">
                        @csrf

                        <!-- Rank -->
                        <div class="form-group row">
                            <label for="rank" class="col-md-4 col-form-label text-md-right">Rank</label>
                            <div class="col-md-6">
                                <select id="rank" class="form-control select2 @error('rank') is-invalid @enderror" style="width: 100%;" name="rank" required>
                                    <option value="" selected disabled>Select Rank</option>
                                    <option value="PGEN" @if(old('rank') == 'PGEN') selected @endif>PGEN</option>
                                    <option value="PLTGEN" @if(old('rank') == 'PLTGEN') selected @endif>PLTGEN</option>
                                    <option value="PMGEN" @if(old('rank') == 'PMGEN') selected @endif>PMGEN</option>
                                    <option value="PBGEN" @if(old('rank') == 'PBGEN') selected @endif>PBGEN</option>
                                    <option value="PCOL" @if(old('rank') == 'PCOL') selected @endif>PCOL</option>
                                    <option value="PTCOL" @if(old('rank') == 'PTCOL') selected @endif>PTCOL</option>
                                    <option value="PMAJ" @if(old('rank') == 'PMAJ') selected @endif>PMAJ</option>
                                    <option value="PCPT" @if(old('rank') == 'PCPT') selected @endif>PCPT</option>
                                    <option value="PLT" @if(old('rank') == 'PLT') selected @endif>PLT</option>
                                    <option value="PEMS" @if(old('rank') == 'PEMS') selected @endif>PEMS</option>
                                    <option value="PCMS" @if(old('rank') == 'PCMS') selected @endif>PCMS</option>
                                    <option value="PSMS" @if(old('rank') == 'PSMS') selected @endif>PSMS</option>
                                    <option value="PMSg" @if(old('rank') == 'PMSg') selected @endif>PMSg</option>
                                    <option value="PSSg" @if(old('rank') == 'PSSg') selected @endif>PSSg</option>
                                    <option value="PCpl" @if(old('rank') == 'PCpl') selected @endif>PCpl</option>
                                    <option value="Pat" @if(old('rank') == 'Pat') selected @endif>Pat</option>
                                    <option value="NUP" @if(old('rank') == 'NUP') selected @endif>NUP</option>
                                </select>
                                @error('rank')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- First Name -->
                        <div class="form-group row">
                            <label for="first_name" class="col-md-4 col-form-label text-md-right">First Name</label>
                            <div class="col-md-6">
                                <input id="first_name" type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" value="{{ old('first_name') }}" required>
                                @error('first_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Middle Name -->
                        <div class="form-group row">
                            <label for="middle_name" class="col-md-4 col-form-label text-md-right">Middle Name</label>
                            <div class="col-md-6">
                                <input id="middle_name" type="text" class="form-control @error('middle_name') is-invalid @enderror" name="middle_name" value="{{ old('middle_name') }}">
                                @error('middle_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Last Name -->
                        <div class="form-group row">
                            <label for="last_name" class="col-md-4 col-form-label text-md-right">Last Name</label>
                            <div class="col-md-6">
                                <input id="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name') }}" required>
                                @error('last_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Qualifier -->
                        <div class="form-group row">
                            <label for="qualifier" class="col-md-4 col-form-label text-md-right">Qualifier</label>
                            <div class="col-md-6">
                                <input id="qualifier" type="text" class="form-control @error('qualifier') is-invalid @enderror" name="qualifier" value="{{ old('qualifier') }}">
                                @error('qualifier')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Designation -->
                        <div class="form-group row">
                            <label for="designation" class="col-md-4 col-form-label text-md-right">Designation</label>
                            <div class="col-md-6">
                                <input id="designation" type="text" class="form-control @error('designation') is-invalid @enderror" name="designation" value="{{ old('designation') }}" required>
                                @error('designation')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Unit -->
                        <div class="form-group row">
                            <label for="unit" class="col-md-4 col-form-label text-md-right">Unit</label>
                            <div class="col-md-6">
                                <select id="unit" class="form-control select2 @error('unit') is-invalid @enderror" style="width: 100%;" name="unit" required>
                                    <option value="" selected disabled>Select Unit</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->UnitId }}" @if(old('unit') == $unit->UnitId) selected @endif>{{ $unit->OrderNumberPrefix }}</option>
                                    @endforeach
                                </select>
                                @error('unit')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Subunit -->
                        <div class="form-group row">
                            <label for="subunit" class="col-md-4 col-form-label text-md-right">Subunit</label>
                            <div class="col-md-6">
                                <select id="subunit" class="form-control select2 @error('subunit') is-invalid @enderror" style="width: 100%;" name="subunit" required>
                                    <option value="" selected>Select Subunit</option>
                                </select>
                                @error('subunit')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Station -->
                        <div class="form-group row">
                            <label for="station" class="col-md-4 col-form-label text-md-right">Station</label>
                            <div class="col-md-6">
                                <select id="station" class="form-control select2 @error('station') is-invalid @enderror" style="width: 100%;" name="station" required>
                                    <option value="" selected>Select Station</option>
                                </select>
                                @error('station')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Submit Registration
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        $(document).ready(function () {
            var unitSelect = $('#unit');
            var subunitSelect = $('#subunit');
            var stationSelect = $('#station');
            
            // When the Unit changes, fetch Subunits
            unitSelect.on('change', function () {
                var selectedUnitId = $(this).val();

                console.log(selectedUnitId);

                // Clear and reset subunit and station dropdowns
                subunitSelect.empty().append($('<option>', { value: 'NONE', text: 'Select Subunit', selected: true }));
                stationSelect.empty().append($('<option>', { value: 'NONE', text: 'Select Station', selected: true }));

                if (selectedUnitId !== 'NONE') {
                    $.get('{{ route("getPersonnelSubunits") }}', { unitId: selectedUnitId }, function (data) {
                        data.forEach(function (subunit) {
                            subunitSelect.append($('<option>', {
                                value: subunit.SubUnitId,
                                text: subunit.Description
                            }));
                        });
                    });
                }
            });

            // When the Subunit changes, fetch Stations
            subunitSelect.on('change', function () {
                var selectedSubunitId = $(this).val();

                console.log(selectedSubunitId);

                // Clear and reset station dropdown
                stationSelect.empty().append($('<option>', { value: 'NONE', text: 'Select Station', selected: true }));

                if (selectedSubunitId !== 'NONE') {
                    $.get('{{ route("getPersonnelStations") }}', { subunitId: selectedSubunitId }, function (data) {
                        data.forEach(function (station) {
                            stationSelect.append($('<option>', {
                                value: station.StationId,
                                text: station.Name
                            }));
                        });
                    });
                }
            });
        });
    });
</script>
@endsection