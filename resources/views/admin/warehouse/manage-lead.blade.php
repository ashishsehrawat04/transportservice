@extends('admin.Layout')

@section('content')
  @if(session('success'))
      <div class="alert alert-success">
          {{ session('success') }}
      </div>
  @endif

  @if(session('error'))
      <div class="alert alert-danger">
          {{ session('error') }}
      </div>
  @endif

  @if ($errors->any())
      <div class="alert alert-danger">
          <ul class="mb-0">
              @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
          </ul>
      </div>
  @endif

  @if($warehouseLead->exists && $quote)
      @php
          $summaryUser = $warehouseLead->user;
          $summaryUserAddress = collect([
              $summaryUser->address_line_1 ?? null,
              $summaryUser->address_line_2 ?? null,
              $summaryUser->city ?? null,
              $summaryUser->state ?? null,
              $summaryUser->country ?? null,
              $summaryUser->pincode ?? null,
          ])->filter()->join(', ');
          $summaryPickupAddress = $warehouseAddress->pickup_address ?? $summaryUserAddress;

          $summaryItems = $quote->quote_data['warehouse_items'] ?? null;
          if (empty($summaryItems)) {
              $summaryItems = [[
                  'item_name' => $warehouseLead->item_name,
                  'item_type' => $warehouseLead->item_type,
                  'quantity' => $warehouseLead->quantity,
                  'length_cm' => $warehouseLead->length_cm,
                  'width_cm' => $warehouseLead->width_cm,
                  'height_cm' => $warehouseLead->height_cm,
                  'weight_kg' => $warehouseLead->weight_kg,
                  'charge_basis' => $warehouseLead->calculation_type,
                  'volumetric_weight_kg' => null,
                  'estimated_total' => $warehouseLead->subtotal,
              ]];
          }
      @endphp

      <div class="row">
          <div class="col-md-12">
              <div class="card mb-3">
                  <div class="card-header">
                      <h5 class="card-title mb-0">Lead Summary <small class="text-muted">(read-only)</small></h5>
                  </div>
                  <div class="card-body">
                      <div class="row g-3 mb-4">
                          <div class="col-md-3 col-sm-6">
                              <div class="text-muted small text-uppercase">Customer</div>
                              <div class="fw-semibold">{{ $summaryUser->name ?? '-' }}</div>
                          </div>
                          <div class="col-md-3 col-sm-6">
                              <div class="text-muted small text-uppercase">Phone Number</div>
                              <div class="fw-semibold">{{ $quote->customer_mobile ?: ($summaryUser->mobile ?? '-') }}</div>
                          </div>
                          <div class="col-md-3 col-sm-6">
                              <div class="text-muted small text-uppercase">Pickup Date</div>
                              <div class="fw-semibold">{{ optional($warehouseLead->requested_pickup_date)->format('d M Y') ?: '-' }}</div>
                          </div>
                          <div class="col-md-3 col-sm-6">
                              <div class="text-muted small text-uppercase">Total Amount</div>
                              <div class="fw-semibold">{{ number_format((float) $warehouseLead->total_payment, 2) }}</div>
                          </div>
                          <div class="col-md-6">
                              <div class="text-muted small text-uppercase">Pickup Address</div>
                              <div class="fw-semibold">{{ $summaryPickupAddress ?: '-' }}</div>
                          </div>
                          <div class="col-md-3 col-sm-6">
                              <div class="text-muted small text-uppercase">Warehouse</div>
                              <div class="fw-semibold">{{ $quote->warehouse_name ?: '-' }}{{ $quote->warehouse_city ? ' — ' . $quote->warehouse_city : '' }}</div>
                          </div>
                          <div class="col-md-2 col-sm-4">
                              <div class="text-muted small text-uppercase">Admin Status</div>
                              <span class="badge bg-info">{{ $warehouseLead->admin_status ?: '-' }}</span>
                          </div>
                          <div class="col-md-2 col-sm-4">
                              <div class="text-muted small text-uppercase">User Status</div>
                              <span class="badge bg-primary">{{ $warehouseLead->user_status ?: '-' }}</span>
                          </div>
                          <div class="col-md-2 col-sm-4">
                              <div class="text-muted small text-uppercase">Payment Status</div>
                              <span class="badge bg-success">{{ $warehouseLead->payment_status ?: '-' }}</span>
                          </div>
                      </div>

                      <h6 class="mb-3">Items ({{ count($summaryItems) }})</h6>
                      <div class="row g-3">
                          @foreach ($summaryItems as $item)
                              <div class="col-md-6 col-lg-4">
                                  <div class="border rounded p-3 h-100">
                                      <div class="d-flex justify-content-between align-items-start mb-2">
                                          <strong>{{ $item['item_name'] ?: '-' }}</strong>
                                          <span class="badge bg-secondary">Qty: {{ $item['quantity'] ?: 1 }}</span>
                                      </div>
                                      <div class="small text-muted mb-1">{{ $item['item_type'] ?: '-' }}</div>
                                      <div class="small mb-1">
                                          {{ number_format((float) ($item['length_cm'] ?? 0), 2) }} x
                                          {{ number_format((float) ($item['width_cm'] ?? 0), 2) }} x
                                          {{ number_format((float) ($item['height_cm'] ?? 0), 2) }} CM
                                          &middot; {{ number_format((float) ($item['weight_kg'] ?? 0), 2) }} KG
                                      </div>
                                      <div class="small mb-2">Charge Basis: {{ ucfirst($item['charge_basis'] ?? '-') }}</div>
                                      <div class="fw-bold text-end">Price: {{ number_format((float) ($item['estimated_total'] ?? 0), 2) }}</div>
                                  </div>
                              </div>
                          @endforeach
                      </div>
                  </div>
              </div>
          </div>
      </div>
  @endif

  <div class="row">
      <div class="col-md-12">
          <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                  <h4 class="card-title">{{ $warehouseLead->exists ? 'Edit Warehouse Lead' : 'Add Warehouse Lead' }}</h4>
                  <a href="{{ route('admin.warehouse_leads') }}" class="btn btn-secondary btn-sm">Back to Warehouse Leads</a>
              </div>
              <div class="card-body">
                  <form action="{{ route('admin.save.warehouse_lead', $warehouseLead->id ?? '') }}" method="POST">
                      @csrf

                      <h5 class="mb-3">User & Item</h5>
                      <div class="row">
                          <div class="col-md-4 mb-3">
                              <label class="form-label">User</label>
                              <select name="user_id" class="form-control">
                                  <option value="">Select user</option>
                                  @foreach($users as $user)
                                      <option value="{{ $user->id }}" {{ old('user_id', $warehouseLead->user_id) == $user->id ? 'selected' : '' }}>{{ $user->name }} - {{ $user->email }}</option>
                                  @endforeach
                              </select>
                          </div>

                          <div class="col-md-4 mb-3">
                              <label class="form-label">Item Name</label>
                              <input type="text" name="item_name" class="form-control" value="{{ old('item_name', $warehouseLead->item_name) }}" placeholder="Enter item name">
                          </div>

                          <div class="col-md-4 mb-3">
                              <label class="form-label">Item Type</label>
                              <input type="text" name="item_type" class="form-control" value="{{ old('item_type', $warehouseLead->item_type) }}" placeholder="Enter item type">
                          </div>
                      </div>

                      <div class="row">
                          <div class="col-md-3 mb-3">
                              <label class="form-label">Quantity</label>
                              <input type="number" name="quantity" class="form-control" value="{{ old('quantity', $warehouseLead->quantity ?? 1) }}" min="1">
                          </div>

                          <div class="col-md-3 mb-3">
                              <label class="form-label">Weight (KG)</label>
                              <input type="number" step="0.01" name="weight_kg" class="form-control" value="{{ old('weight_kg', $warehouseLead->weight_kg) }}" placeholder="Enter weight">
                          </div>

                          <div class="col-md-2 mb-3">
                              <label class="form-label">Length (CM)</label>
                              <input type="number" step="0.01" name="length_cm" class="form-control" value="{{ old('length_cm', $warehouseLead->length_cm) }}">
                          </div>

                          <div class="col-md-2 mb-3">
                              <label class="form-label">Width (CM)</label>
                              <input type="number" step="0.01" name="width_cm" class="form-control" value="{{ old('width_cm', $warehouseLead->width_cm) }}">
                          </div>

                          <div class="col-md-2 mb-3">
                              <label class="form-label">Height (CM)</label>
                              <input type="number" step="0.01" name="height_cm" class="form-control" value="{{ old('height_cm', $warehouseLead->height_cm) }}">
                          </div>
                      </div>

                      <h5 class="mb-3 mt-2">Warehouse & Storage</h5>
                      <div class="row">
                          <div class="col-md-6 mb-3">
                              <label class="form-label">Warehouse</label>
                              <select name="warehouse_id" class="form-control">
                                  <option value="">Select warehouse</option>
                                  @foreach($warehouses as $warehouse)
                                      <option value="{{ $warehouse->id }}" {{ old('warehouse_id', $warehouseLead->warehouse_id) == $warehouse->id ? 'selected' : '' }}>
                                          {{ $warehouse->name }} — {{ $warehouse->city }}
                                      </option>
                                  @endforeach
                              </select>
                          </div>

                          <div class="col-md-3 mb-3">
                              <label class="form-label">Storage Days</label>
                              <input type="number" name="storage_days" class="form-control" value="{{ old('storage_days', $warehouseLead->storage_days ?? 1) }}" min="1">
                          </div>

                          <div class="col-md-3 mb-3">
                              <label class="form-label">Requested Pickup Date</label>
                              <input type="date" name="requested_pickup_date" class="form-control" value="{{ old('requested_pickup_date', optional($warehouseLead->requested_pickup_date)->format('Y-m-d')) }}">
                          </div>
                      </div>

                      <h5 class="mb-3 mt-2">Status</h5>
                      <div class="row">
                          <div class="col-md-3 mb-3">
                              <label class="form-label">Admin Status</label>
                              <select name="admin_status" class="form-control">
                                  @foreach(['pending', 'reviewed', 'approved', 'dispatched', 'delivered', 'cancelled', 'rejected'] as $status)
                                      <option value="{{ $status }}" {{ old('admin_status', $warehouseLead->admin_status ?? 'pending') == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                                  @endforeach
                              </select>
                          </div>

                          <div class="col-md-3 mb-3">
                              <label class="form-label">User Status</label>
                              <select name="user_status" class="form-control">
                                  @foreach(['pending', 'confirmed', 'in_transit', 'delivered', 'cancelled'] as $status)
                                      <option value="{{ $status }}" {{ old('user_status', $warehouseLead->user_status ?? 'pending') == $status ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                                  @endforeach
                              </select>
                          </div>

                          <div class="col-md-3 mb-3">
                              <label class="form-label">Assigned To</label>
                              <select name="assigned_to" class="form-control">
                                  <option value="">Select staff</option>
                                  @foreach($users as $user)
                                      <option value="{{ $user->id }}" {{ old('assigned_to', $warehouseLead->assigned_to) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                  @endforeach
                              </select>
                          </div>

                          <div class="col-md-3 mb-3">
                              <label class="form-label">Payment Status</label>
                              <select name="payment_status" class="form-control">
                                  @foreach(['unpaid', 'partial', 'paid', 'refunded'] as $status)
                                      <option value="{{ $status }}" {{ old('payment_status', $warehouseLead->payment_status ?? 'unpaid') == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                                  @endforeach
                              </select>
                          </div>
                      </div>

                      <h5 class="mb-3 mt-2">Payment & Notes</h5>
                      <div class="row">
                          <div class="col-md-3 mb-3">
                              <label class="form-label">Tax Amount</label>
                              <input type="number" step="0.01" name="tax_amount" class="form-control" value="{{ old('tax_amount', $warehouseLead->tax_amount ?? 0) }}">
                          </div>

                          <div class="col-md-3 mb-3">
                              <label class="form-label">Discount Amount</label>
                              <input type="number" step="0.01" name="discount_amount" class="form-control" value="{{ old('discount_amount', $warehouseLead->discount_amount ?? 0) }}">
                          </div>

                          <div class="col-md-3 mb-3">
                              <label class="form-label">Payment Method</label>
                              <select name="payment_method" class="form-control">
                                  <option value="">Select method</option>
                                  @foreach(['cash', 'online', 'upi', 'bank_transfer'] as $method)
                                      <option value="{{ $method }}" {{ old('payment_method', $warehouseLead->payment_method) == $method ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $method)) }}</option>
                                  @endforeach
                              </select>
                          </div>

                          <div class="col-md-3 mb-3">
                              <label class="form-label">Transaction ID</label>
                              <input type="text" name="transaction_id" class="form-control" value="{{ old('transaction_id', $warehouseLead->transaction_id) }}">
                          </div>
                      </div>

                      @if($warehouseLead->exists)
                          <div class="row">
                              <div class="col-md-12 mb-3">
                                  <div class="alert alert-info mb-0">
                                      @php
                                          $calculationType = in_array($warehouseLead->calculation_type, ['weight', 'volume', 'mixed'], true)
                                              ? $warehouseLead->calculation_type
                                              : (((float) $warehouseLead->volume_charge > (float) $warehouseLead->weight_charge) ? 'volume' : 'weight');
                                      @endphp
                                      Calculation By: {{ ucfirst($calculationType) }} |
                                      Minimum Charge: {{ number_format($warehouseLead->base_price, 2) }} |
                                      @if(in_array($calculationType, ['weight', 'mixed'], true))
                                          Weight Charge: {{ number_format($warehouseLead->weight_charge, 2) }} |
                                      @endif
                                      @if(in_array($calculationType, ['volume', 'mixed'], true))
                                          Volume Charge: {{ number_format($warehouseLead->volume_charge, 2) }} |
                                      @endif
                                      Subtotal: {{ number_format($warehouseLead->subtotal, 2) }} |
                                      Total: {{ number_format($warehouseLead->total_payment, 2) }}
                                  </div>
                              </div>
                          </div>
                      @endif

                      <div class="row">
                          <div class="col-md-6 mb-3">
                              <label class="form-label">Admin Description</label>
                              <textarea name="admin_description" class="form-control" rows="3">{{ old('admin_description', $warehouseLead->admin_description) }}</textarea>
                          </div>

                          <div class="col-md-6 mb-3">
                              <label class="form-label">Special Instructions</label>
                              <textarea name="special_instructions" class="form-control" rows="3">{{ old('special_instructions', $warehouseLead->special_instructions) }}</textarea>
                          </div>
                      </div>

                      <div class="text-end d-flex justify-content-end gap-2">
                          <button type="reset" class="btn btn-secondary">Reset</button>
                          <button type="submit" class="btn btn-primary">{{ $warehouseLead->exists ? 'Update Warehouse Lead' : 'Add Warehouse Lead' }}</button>
                      </div>
                  </form>
              </div>
          </div>
      </div>
  </div>
@endsection
