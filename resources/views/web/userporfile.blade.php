@include('web.header')

@php
    $isEdit = $isEdit ?? false;
    $initial = strtoupper(substr($user->name ?: 'U', 0, 1));

    $statusBadgeMap = [
        'delivered' => 'done',
        'cancelled' => 'stopped',
        'rejected' => 'stopped',
    ];
@endphp

<style>
body{background:var(--ot-bg);}
.profile-cover{height:160px;background:linear-gradient(135deg,#0a6c5c,#2f8fe0 58%,#ff7a45);}
.profile-avatar{width:120px;height:120px;border-radius:50%;background:#fff;border:5px solid #fff;box-shadow:0 8px 25px rgba(0,0,0,.15);color:#0a6c5c;font-size:42px;font-weight:700;font-family:var(--ot-display);display:flex;align-items:center;justify-content:center;margin:auto;margin-top:-60px;}
.profile-card{overflow:hidden;border:none;border-radius:16px;}
.info-card,.stat-card,.shipment-card{border:1px solid var(--ot-line);border-radius:16px;}
.stat-card{transition:.3s;}
.stat-card:hover{transform:translateY(-5px);box-shadow:var(--ot-shadow);}
.stat-card h3,.stat-card h4{font-family:var(--ot-mono);}
.table tbody tr{transition:.3s;}
.table tbody tr:hover{background:var(--ot-panel-tint);}
.table thead th{font-size:11px;letter-spacing:.05em;color:var(--ot-muted);border-bottom:1px solid var(--ot-line);}
.table td strong{font-family:var(--ot-mono);}
.info-label{font-size:12.5px;color:var(--ot-muted);margin-bottom:4px;text-transform:uppercase;letter-spacing:.03em;}
.info-value{font-weight:600;color:var(--ot-ink);word-break:break-word;}
.edit-btn{border-radius:9px;padding:8px 20px;font-family:var(--ot-display);}
.card-header{background:var(--ot-panel);border-bottom:1px solid var(--ot-line);}
.badge{font-size:12px;}
.shadow-custom{box-shadow:var(--ot-shadow-sm);}
.form-control{border-radius:8px;padding:11px 14px;}
.text-primary{color:#0a6c5c !important;}
.btn-primary{background:#0e8f7a !important;border-color:#0e8f7a !important;}
.btn-primary:hover{background:#0a6c5c !important;border-color:#0a6c5c !important;}
.btn-outline-primary{color:#0a6c5c !important;border-color:#0e8f7a !important;}
.btn-outline-primary:hover{background:#0e8f7a !important;color:#fff !important;}
.status-badge{border-radius:999px;display:inline-block;font-size:10.5px;font-weight:800;letter-spacing:.03em;padding:5px 12px;text-transform:uppercase;white-space:nowrap;}
.status-badge.ongoing{background:#fff6e8;color:#b5750c;border:1px solid #ffe1ab;}
.status-badge.done{background:#ecfdf3;color:#067647;border:1px solid #abefc6;}
.status-badge.stopped{background:#fef3f2;color:#b42318;border:1px solid #fecdca;}
</style>

<div class="container py-5">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card profile-card shadow-custom">
                <div class="profile-cover"></div>
                <div class="card-body text-center pb-4">
                    <div class="profile-avatar">{{ $initial }}</div>
                    <h4 class="fw-bold mt-3 mb-1">{{ $user->name }}</h4>
                    <p class="text-muted mb-1">{{ $user->email ?: 'Email not added' }}</p>
                    <p class="text-muted">{{ $user->mobile ?: 'Mobile not added' }}</p>

                    @if ($isEdit)
                        <a href="{{ route('user.profile') }}" class="btn btn-outline-secondary edit-btn">
                            <i class="bi bi-arrow-left me-1"></i> Back
                        </a>
                    @else
                        <a href="{{ route('user.profile.edit') }}" class="btn btn-primary edit-btn">
                            <i class="bi bi-pencil-square me-1"></i> Edit Profile
                        </a>
                    @endif
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12 mb-3">
                    <div class="card stat-card shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-truck fs-2 text-primary"></i>
                            <h3 class="fw-bold mt-2 mb-0">{{ $shipmentStats['total'] ?? 0 }}</h3>
                            <small class="text-muted">Total Shipments</small>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card stat-card shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-check-circle-fill fs-2 text-success"></i>
                            <h4 class="fw-bold mt-2">{{ $shipmentStats['delivered'] ?? 0 }}</h4>
                            <small>Delivered</small>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card stat-card shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-clock-history fs-2 text-warning"></i>
                            <h4 class="fw-bold mt-2">{{ $shipmentStats['pending'] ?? 0 }}</h4>
                            <small>Pending</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card info-card shadow-custom mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-geo-alt-fill text-primary me-2"></i>
                        {{ $isEdit ? 'Edit Profile' : 'Address Information' }}
                    </h5>

                    @unless ($isEdit)
                        <a href="{{ route('user.profile.edit') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                    @endunless
                </div>

                <div class="card-body">
                    @if ($isEdit)
                        <form action="{{ route('user.profile.update') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}">
                                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Mobile Number</label>
                                    <input type="text" name="mobile" class="form-control @error('mobile') is-invalid @enderror" value="{{ old('mobile', $user->mobile) }}">
                                    @error('mobile') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Pincode</label>
                                    <input type="text" name="pincode" class="form-control @error('pincode') is-invalid @enderror" value="{{ old('pincode', $user->pincode) }}">
                                    @error('pincode') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">City</label>
                                    <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city', $user->city) }}">
                                    @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">State</label>
                                    <input type="text" name="state" class="form-control @error('state') is-invalid @enderror" value="{{ old('state', $user->state) }}">
                                    @error('state') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Country</label>
                                    <input type="text" name="country" class="form-control @error('country') is-invalid @enderror" value="{{ old('country', $user->country ?? 'India') }}">
                                    @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">Address Line 1</label>
                                    <textarea name="address_line_1" class="form-control @error('address_line_1') is-invalid @enderror" rows="2">{{ old('address_line_1', $user->address_line_1) }}</textarea>
                                    @error('address_line_1') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12 mb-4">
                                    <label class="form-label">Address Line 2</label>
                                    <textarea name="address_line_2" class="form-control @error('address_line_2') is-invalid @enderror" rows="2">{{ old('address_line_2', $user->address_line_2) }}</textarea>
                                    @error('address_line_2') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary edit-btn">
                                <i class="bi bi-check2-circle me-1"></i> Update Profile
                            </button>
                            <a href="{{ route('user.profile') }}" class="btn btn-light edit-btn ms-2">Cancel</a>
                        </form>
                    @else
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="info-label">Full Name</div>
                                <div class="info-value">{{ $user->name ?: '-' }}</div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="info-label">Mobile Number</div>
                                <div class="info-value">{{ $user->mobile ?: '-' }}</div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="info-label">Email</div>
                                <div class="info-value">{{ $user->email ?: '-' }}</div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="info-label">City</div>
                                <div class="info-value">{{ $user->city ?? '-' }}</div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="info-label">State</div>
                                <div class="info-value">{{ $user->state ?? '-' }}</div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="info-label">Pincode</div>
                                <div class="info-value">{{ $user->pincode ?? '-' }}</div>
                            </div>

                            <div class="col-12">
                                <div class="info-label">Full Address</div>
                                <div class="info-value">
                                    @if ($user->address_line_1 || $user->address_line_2 || $user->city || $user->state || $user->country)
                                        {{ collect([$user->address_line_1, $user->address_line_2, $user->city, $user->state, $user->country])->filter()->join(', ') }}
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card shipment-card shadow-custom">
                <div class="card-header py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-truck text-success me-2"></i>
                        Recent Shipments
                    </h5>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Tracking ID</th>
                                    <th>Destination</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentShipments as $shipment)
                                    @php
                                        $status = $shipment->admin_status ?: $shipment->user_status;
                                        $badgeClass = $statusBadgeMap[$status] ?? 'ongoing';
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $shipment->tracking_number ?: '#' . $shipment->id }}</strong>
                                        </td>
                                        <td>{{ optional($shipment->cityRoute)->to_city ?? '-' }}</td>
                                        <td>{{ optional($shipment->requested_pickup_date)->format('d M Y') ?? $shipment->created_at->format('d M Y') }}</td>
                                        <td>
                                            <span class="status-badge {{ $badgeClass }}">
                                                {{ \Illuminate\Support\Str::headline($status ?: 'pending') }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            No shipments found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
