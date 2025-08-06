@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Data Privacy Statement</div>

                <div class="card-body">
                    <div class="privacy-statement mb-4" style="height: 200px; border: 1px solid #ddd; padding: 15px;">
                        <!-- Your data privacy statement content here -->
                        <h4>Data Privacy Consent</h4>
                        <p class="text-justify">We are committed to protecting your privacy and ensuring the confidentiality of your personal information. By participating in this exam, you acknowledge that your personal information may be collected, processed, and stored in accordance with the Data Privacy Act. Your information will only be used for the purpose of administering and evaluating this exam, and will not be shared with third parties without your consent. If you have any concerns about the handling of your personal information, please contact us.</p>
                        <!-- Add more privacy content as needed -->
                    </div>

                    <form method="POST" action="{{ route('exam.accept-privacy') }}">
                        @csrf

                        <div class="form-check mb-3">
                            <input class="form-check-input @error('accept') is-invalid @enderror" type="checkbox" name="accept" id="accept" required>
                            <label class="form-check-label" for="accept">
                                I have read and understood the Data Privacy Statement and consent to the processing of my personal data.
                            </label>

                            @error('accept')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-primary">
                                    Accept and Continue
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