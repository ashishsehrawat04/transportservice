@extends('Admin.Layout')

@section('content')
  @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="row">
      <div class="col-md-8">
          <div class="card">
              <div class="card-header">
                  <h4 class="card-title">Auth Settings</h4>
              </div>
              <div class="card-body">
                  <form action="{{ route('admin.save.auth_settings') }}" method="POST">
                      @csrf

                      <div class="form-check mb-3">
                          <input class="form-check-input" type="checkbox" name="email_login_enabled" id="email_login_enabled" value="1" {{ $settings->email_login_enabled ? 'checked' : '' }}>
                          <label class="form-check-label" for="email_login_enabled">Enable Email Login/Register</label>
                      </div>

                      <div class="form-check mb-3">
                          <input class="form-check-input" type="checkbox" name="mobile_login_enabled" id="mobile_login_enabled" value="1" {{ $settings->mobile_login_enabled ? 'checked' : '' }}>
                          <label class="form-check-label" for="mobile_login_enabled">Enable Mobile OTP Login</label>
                      </div>

                      <div class="form-check mb-3">
                          <input class="form-check-input" type="checkbox" name="google_login_enabled" id="google_login_enabled" value="1" {{ $settings->google_login_enabled ? 'checked' : '' }}>
                          <label class="form-check-label" for="google_login_enabled">Enable Google Login</label>
                      </div>

                      <div class="form-check mb-4">
                          <input class="form-check-input" type="checkbox" name="admin_approval_required" id="admin_approval_required" value="1" {{ $settings->admin_approval_required ? 'checked' : '' }}>
                          <label class="form-check-label" for="admin_approval_required">Require Admin Approval For Users</label>
                      </div>

                      <button type="submit" class="btn btn-primary">Save Settings</button>
                  </form>
              </div>
          </div>
      </div>
  </div>
@endsection
