@extends('admin.Layout')

@section('content')
  @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="row">
      <div class="col-md-8">
          <div class="card">
              <div class="card-header">
                  <h4 class="card-title">Pricing Settings</h4>
              </div>
              <div class="card-body">
                  <form action="{{ route('admin.save.pricing_settings') }}" method="POST">
                      @csrf

                      <div class="form-group mb-3">
                          <label for="gst_percent">GST (%)</label>
                          <input type="number" step="0.01" min="0" max="100" class="form-control @error('gst_percent') is-invalid @enderror" name="gst_percent" id="gst_percent" value="{{ old('gst_percent', $settings->gst_percent) }}">
                          @error('gst_percent')
                              <span class="invalid-feedback">{{ $message }}</span>
                          @enderror
                          <small class="form-text text-muted">Applied as a percentage on the final billable amount of every transport and warehouse lead, and added to the total payable.</small>
                      </div>

                      <button type="submit" class="btn btn-primary">Save Settings</button>
                  </form>
              </div>
          </div>
      </div>
  </div>
@endsection
