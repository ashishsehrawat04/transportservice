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

  <div class="row">
      <div class="col-md-12">
          <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                  <h4 class="card-title">
                      @if($quoteMode)
                          Create Quote
                      @else
                          {{ $transportLead->exists ? 'Edit Transport Lead' : 'Add Transport Lead' }}
                      @endif
                  </h4>
                  <a href="{{ route('admin.transport_leads') }}" class="btn btn-secondary btn-sm">Back to Transport Leads</a>
              </div>
              <div class="card-body">
                  @if(!$servicePrice)
                      <div class="alert alert-warning">Please add an active transport service price before creating a lead.</div>
                  @endif

                  @if($quoteMode && $transportLead->exists)
                      <div class="alert alert-info">
                          <strong>Quote for {{ $transportLead->tracking_number }}</strong><br>
                          Subtotal: {{ number_format($transportLead->subtotal, 2) }} |
                          Tax: {{ number_format($transportLead->tax_amount, 2) }} |
                          Discount: {{ number_format($transportLead->discount_amount, 2) }} |
                          Total Payable: {{ number_format($transportLead->total_payment, 2) }}
                      </div>
                  @endif

                  <form action="{{ route('admin.save.transport_lead', $transportLead->id ?? '') }}" method="POST">
                      @csrf

                      <h5 class="mb-3">User & Item</h5>
                      <div class="row">
                          <div class="col-md-4 mb-3">
                              <label class="form-label">User</label>
                              <select name="user_id" class="form-control">
                                  <option value="">Select user</option>
                                  @foreach($users as $user)
                                      <option value="{{ $user->id }}" {{ old('user_id', $transportLead->user_id) == $user->id ? 'selected' : '' }}>{{ $user->name }} - {{ $user->email }}</option>
                                  @endforeach
                              </select>
                          </div>

                          <div class="col-md-4 mb-3">
                              <label class="form-label">Item Name</label>
                              <input type="text" name="item_name" class="form-control" value="{{ old('item_name', $transportLead->item_name) }}" placeholder="Enter item name">
                          </div>

                          <div class="col-md-4 mb-3">
                              <label class="form-label">Item Type</label>
                              <input type="text" name="item_type" class="form-control" value="{{ old('item_type', $transportLead->item_type ?: optional($servicePrice)->item_type) }}" placeholder="Default from active service price">
                          </div>
                      </div>

                      <div class="row">
                          <div class="col-md-3 mb-3">
                              <label class="form-label">Quantity</label>
                              <input type="number" name="quantity" class="form-control" value="{{ old('quantity', $transportLead->quantity ?? 1) }}" min="1">
                          </div>

                          <div class="col-md-3 mb-3">
                              <label class="form-label">Weight (KG)</label>
                              <input type="number" step="0.01" name="weight_kg" class="form-control" value="{{ old('weight_kg', $transportLead->weight_kg) }}" placeholder="Enter weight">
                          </div>

                          <div class="col-md-2 mb-3">
                              <label class="form-label">Length (CM)</label>
                              <input type="number" step="0.01" name="length_cm" class="form-control" value="{{ old('length_cm', $transportLead->length_cm) }}">
                          </div>

                          <div class="col-md-2 mb-3">
                              <label class="form-label">Width (CM)</label>
                              <input type="number" step="0.01" name="width_cm" class="form-control" value="{{ old('width_cm', $transportLead->width_cm) }}">
                          </div>

                          <div class="col-md-2 mb-3">
                              <label class="form-label">Height (CM)</label>
                              <input type="number" step="0.01" name="height_cm" class="form-control" value="{{ old('height_cm', $transportLead->height_cm) }}">
                          </div>
                      </div>

                      <h5 class="mb-3 mt-2">Route</h5>
                      <div class="row">
                          <div class="col-md-12 mb-3">
                              <label class="form-label">City Route</label>
                              <select name="city_route_id" class="form-control">
                                  <option value="">Select city route</option>
                                  @foreach($cityRoutes as $route)
                                      <option value="{{ $route->id }}" {{ old('city_route_id', $transportLead->city_route_id) == $route->id ? 'selected' : '' }}>
                                          {{ $route->from_city }} to {{ $route->to_city }}
                                      </option>
                                  @endforeach
                              </select>
                          </div>
                      </div>

                      <h5 class="mb-3 mt-2">Dates & Status</h5>
                      <div class="row">
                          <div class="col-md-3 mb-3">
                              <label class="form-label">Requested Pickup Date</label>
                              <input type="date" name="requested_pickup_date" class="form-control" value="{{ old('requested_pickup_date', optional($transportLead->requested_pickup_date)->format('Y-m-d')) }}">
                          </div>

                          <div class="col-md-3 mb-3">
                              <label class="form-label">Confirmed Pickup Date</label>
                              <input type="date" name="confirmed_pickup_date" class="form-control" value="{{ old('confirmed_pickup_date', optional($transportLead->confirmed_pickup_date)->format('Y-m-d')) }}">
                          </div>

                          <div class="col-md-3 mb-3">
                              <label class="form-label">Expected Delivery Date</label>
                              <input type="date" name="expected_delivery_date" class="form-control" value="{{ old('expected_delivery_date', optional($transportLead->expected_delivery_date)->format('Y-m-d')) }}">
                          </div>

                          <div class="col-md-3 mb-3">
                              <label class="form-label">Actual Delivery Date</label>
                              <input type="date" name="actual_delivery_date" class="form-control" value="{{ old('actual_delivery_date', optional($transportLead->actual_delivery_date)->format('Y-m-d')) }}">
                          </div>
                      </div>

                      <div class="row">
                          <div class="col-md-3 mb-3">
                              <label class="form-label">Admin Status</label>
                              <select name="admin_status" class="form-control">
                                  @foreach(['pending', 'reviewed', 'approved', 'dispatched', 'delivered', 'cancelled', 'rejected'] as $status)
                                      <option value="{{ $status }}" {{ old('admin_status', $transportLead->admin_status ?? 'pending') == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                                  @endforeach
                              </select>
                          </div>

                          <div class="col-md-3 mb-3">
                              <label class="form-label">User Status</label>
                              <select name="user_status" class="form-control">
                                  @foreach(['pending', 'confirmed', 'in_transit', 'delivered', 'cancelled'] as $status)
                                      <option value="{{ $status }}" {{ old('user_status', $transportLead->user_status ?? 'pending') == $status ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                                  @endforeach
                              </select>
                          </div>

                          <div class="col-md-3 mb-3">
                              <label class="form-label">Assigned To</label>
                              <select name="assigned_to" class="form-control">
                                  <option value="">Select staff/driver</option>
                                  @foreach($users as $user)
                                      <option value="{{ $user->id }}" {{ old('assigned_to', $transportLead->assigned_to) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                  @endforeach
                              </select>
                          </div>

                          <div class="col-md-3 mb-3">
                              <label class="form-label">Payment Status</label>
                              <select name="payment_status" class="form-control">
                                  @foreach(['unpaid', 'partial', 'paid', 'refunded'] as $status)
                                      <option value="{{ $status }}" {{ old('payment_status', $transportLead->payment_status ?? 'unpaid') == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                                  @endforeach
                              </select>
                          </div>
                      </div>

                      <h5 class="mb-3 mt-2">{{ $quoteMode ? 'Quote, Payment & Notes' : 'Payment & Notes' }}</h5>
                      <div class="row">
                          <div class="col-md-3 mb-3">
                              <label class="form-label">Tax Amount</label>
                              <input type="number" step="0.01" name="tax_amount" class="form-control" value="{{ old('tax_amount', $transportLead->tax_amount ?? 0) }}">
                          </div>

                          <div class="col-md-3 mb-3">
                              <label class="form-label">Discount Amount</label>
                              <input type="number" step="0.01" name="discount_amount" class="form-control" value="{{ old('discount_amount', $transportLead->discount_amount ?? 0) }}">
                          </div>

                          <div class="col-md-3 mb-3">
                              <label class="form-label">Payment Method</label>
                              <select name="payment_method" class="form-control">
                                  <option value="">Select method</option>
                                  @foreach(['cash', 'online', 'upi', 'bank_transfer'] as $method)
                                      <option value="{{ $method }}" {{ old('payment_method', $transportLead->payment_method) == $method ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $method)) }}</option>
                                  @endforeach
                              </select>
                          </div>

                          <div class="col-md-3 mb-3">
                              <label class="form-label">Transaction ID</label>
                              <input type="text" name="transaction_id" class="form-control" value="{{ old('transaction_id', $transportLead->transaction_id) }}">
                          </div>
                      </div>

                      @if($transportLead->exists)
                          <div class="row">
                              <div class="col-md-12 mb-3">
                                  <div class="alert alert-info mb-0">
                                      Base Price: {{ number_format($transportLead->base_price, 2) }} |
                                      Weight Charge: {{ number_format($transportLead->weight_charge, 2) }} |
                                      Volume Charge: {{ number_format($transportLead->volume_charge, 2) }} |
                                      Distance Charge: {{ number_format($transportLead->distance_charge, 2) }} |
                                      Subtotal: {{ number_format($transportLead->subtotal, 2) }} |
                                      Tax: {{ number_format($transportLead->tax_amount, 2) }} |
                                      Discount: {{ number_format($transportLead->discount_amount, 2) }} |
                                      Total: {{ number_format($transportLead->total_payment, 2) }}
                                  </div>
                              </div>
                          </div>
                      @endif

                      <div class="row">
                          <div class="col-md-6 mb-3">
                              <label class="form-label">Admin Description</label>
                              <textarea name="admin_description" class="form-control" rows="3">{{ old('admin_description', $transportLead->admin_description) }}</textarea>
                          </div>

                          <div class="col-md-6 mb-3">
                              <label class="form-label">Special Instructions</label>
                              <textarea name="special_instructions" class="form-control" rows="3">{{ old('special_instructions', $transportLead->special_instructions) }}</textarea>
                          </div>
                      </div>

                      <div class="text-end d-flex justify-content-end gap-2">
                          <button type="reset" class="btn btn-secondary">Reset</button>
                          <button type="submit" class="btn btn-primary" {{ !$servicePrice ? 'disabled' : '' }}>
                              @if($quoteMode)
                                  Save Quote
                              @else
                                  {{ $transportLead->exists ? 'Update Transport Lead' : 'Add Transport Lead' }}
                              @endif
                          </button>
                      </div>
                  </form>
              </div>
          </div>
      </div>
  </div>
@endsection
