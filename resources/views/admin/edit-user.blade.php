
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit User</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f0f2f5;
      min-height: 100vh;
      display: flex;
      align-items: flex-start;
      justify-content: center;
      padding: 40px 16px;
      font-family: 'Segoe UI', sans-serif;
    }

    .wrapper {
      width: 100%;
      max-width: 800px;
    }

    .card {
      border: none;
      border-radius: 16px;
      box-shadow: 0 4px 24px rgba(0,0,0,0.08);
      overflow: hidden;
    }

    .card-header {
      background: linear-gradient(135deg, #4f46e5, #6366f1);
      padding: 20px 28px;
      border: none;
    }

    .card-header h4 {
      color: #fff;
      margin: 0;
      font-weight: 600;
      font-size: 1.2rem;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .card-header h4::before {
      content: '✏️';
      font-size: 1.1rem;
    }

    .card-body {
      padding: 28px;
      background: #fff;
    }

    .form-label {
      font-size: 0.82rem;
      font-weight: 600;
      color: #555;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 6px;
    }

    .form-control,
    .form-select {
      border-radius: 10px;
      border: 1.5px solid #e0e0e0;
      padding: 10px 14px;
      font-size: 0.95rem;
      color: #333;
      transition: border-color 0.2s, box-shadow 0.2s;
      background-color: #fafafa;
    }

    .form-control:focus,
    .form-select:focus {
      border-color: #6366f1;
      box-shadow: 0 0 0 3px rgba(99,102,241,0.15);
      background-color: #fff;
      outline: none;
    }

    .form-control::placeholder {
      color: #bbb;
      font-size: 0.9rem;
    }

    .input-icon-wrapper {
      position: relative;
    }

    .input-icon-wrapper .icon {
      position: absolute;
      left: 12px;
      top: 50%;
      transform: translateY(-50%);
      color: #aaa;
      font-size: 16px;
      pointer-events: none;
    }

    .input-icon-wrapper .form-control,
    .input-icon-wrapper .form-select {
      padding-left: 38px;
    }

    .section-divider {
      font-size: 0.78rem;
      font-weight: 700;
      color: #aaa;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin: 8px 0 16px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .section-divider::after {
      content: '';
      flex: 1;
      height: 1px;
      background: #eee;
    }

    .btn-primary {
      background: linear-gradient(135deg, #4f46e5, #6366f1);
      border: none;
      border-radius: 10px;
      padding: 10px 26px;
      font-weight: 600;
      font-size: 0.9rem;
      letter-spacing: 0.3px;
      transition: opacity 0.2s, transform 0.1s;
    }

    .btn-primary:hover {
      opacity: 0.92;
      transform: translateY(-1px);
    }

    .btn-secondary {
      border-radius: 10px;
      padding: 10px 22px;
      font-weight: 600;
      font-size: 0.9rem;
      background: #f3f4f6;
      color: #555;
      border: 1.5px solid #e0e0e0;
      transition: background 0.2s;
    }

    .btn-secondary:hover {
      background: #e9eaf0;
      color: #333;
    }
  </style>
</head>
<body>
  <div class="wrapper">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="card shadow">
      <div class="card-header">
        <h4>Edit User</h4>
      </div>

      <div class="card-body">
        <form action="{{ route('admin.update.users', $user->slug) }}" method="POST">
          @csrf

          <div class="section-divider">Personal Info</div>

          <div class="row">
            <!-- Name -->
            <div class="col-md-6 mb-3">
              <label class="form-label">Name</label>
              <div class="input-icon-wrapper">
                <span class="icon">👤</span>
                <input type="text" class="form-control" value="{{ $user->name }}" name="name" placeholder="Enter full name">
              </div>
            </div>

            <!-- Email -->
            <div class="col-md-6 mb-3">
              <label class="form-label">Email</label>
              <div class="input-icon-wrapper">
                <span class="icon">✉️</span>
                <input type="email" class="form-control" name="email" value="{{ $user->email }}" placeholder="Enter email address">
              </div>
            </div>

            <!-- Mobile -->
            <div class="col-md-6 mb-3">
              <label class="form-label">Mobile</label>
              <div class="input-icon-wrapper">
                <span class="icon">📱</span>
                <input type="text" class="form-control" value="{{ $user->mobile }}" name="mobile" placeholder="Enter mobile number">
              </div>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Pincode</label>
              <input type="text" class="form-control" value="{{ old('pincode', $user->pincode) }}" name="pincode" placeholder="Enter pincode">
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">City</label>
              <input type="text" class="form-control" value="{{ old('city', $user->city) }}" name="city" placeholder="Enter city">
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">State</label>
              <input type="text" class="form-control" value="{{ old('state', $user->state) }}" name="state" placeholder="Enter state">
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Country</label>
              <input type="text" class="form-control" value="{{ old('country', $user->country ?? 'India') }}" name="country" placeholder="Enter country">
            </div>

            <div class="col-12 mb-3">
              <label class="form-label">Address Line 1</label>
              <textarea class="form-control" name="address_line_1" rows="3" placeholder="Enter address">{{ old('address_line_1', $user->address_line_1) }}</textarea>
            </div>

            <div class="col-12 mb-3">
              <label class="form-label">Address Line 2</label>
              <textarea class="form-control" name="address_line_2" rows="3" placeholder="Enter address">{{ old('address_line_2', $user->address_line_2) }}</textarea>
            </div>
          </div>



          <div class="section-divider">Account Settings</div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Role</label>
              <div class="input-icon-wrapper">
                <span class="icon">🛡️</span>
                <select class="form-select" name="role">
                  <option value="">Select Role</option>
                  <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                  <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
              </div>
            </div>

            <!-- Status -->
            <div class="col-md-6 mb-3">
              <label class="form-label">Status</label>
              <div class="input-icon-wrapper">
                <span class="icon">📋</span>
                <select class="form-select" name="status">
                  <option value="">Select Status</option>
                  <option value="pending" {{ $user->status == 'pending' ? 'selected' : '' }}>Pending</option>
                  <option value="approved" {{ $user->status == 'approved' ? 'selected' : '' }}>Approved</option>
                  <option value="rejected" {{ $user->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                  <option value="blocked" {{ $user->status == 'blocked' ? 'selected' : '' }}>Blocked</option>
                </select>
              </div>
            </div>
          </div>

          <hr style="margin: 8px 0 20px; border-color: #eee;">

          <div class="text-end d-flex justify-content-end gap-2">
            <button type="reset" class="btn btn-secondary">
              Reset
            </button>
            <button type="submit" class="btn btn-primary">
              Update User
            </button>
          </div>

        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
