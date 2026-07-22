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
                  <h4 class="card-title">{{ $warehouse->exists ? 'Edit Warehouse' : 'Add Warehouse' }}</h4>
                  <a href="{{ route('admin.warehouses') }}" class="btn btn-secondary btn-sm">Back to Warehouses</a>
              </div>
              <div class="card-body">
                  <form action="{{ route('admin.save.warehouse', $warehouse->id ?? '') }}" method="POST">
                      @csrf
                      <div class="row">
                          <div class="col-md-6 mb-3">
                              <label class="form-label">Warehouse Name</label>
                              <input type="text" name="name" class="form-control" value="{{ old('name', $warehouse->name) }}" placeholder="Enter warehouse name">
                          </div>

                          <div class="col-md-6 mb-3">
                              <label class="form-label">City</label>
                              <input type="text" name="city" class="form-control" value="{{ old('city', $warehouse->city) }}" placeholder="Enter city">
                          </div>
                      </div>

                      <div class="row">
                          <div class="col-md-12 mb-3">
                              <label class="form-label">Address</label>
                              <textarea name="address" class="form-control" rows="3" placeholder="Enter warehouse address">{{ old('address', $warehouse->address) }}</textarea>
                          </div>
                      </div>

                      <div class="row">
                          <div class="col-md-4 mb-3">
                              <label class="form-label">Price per Day per KG</label>
                              <input type="number" step="0.01" name="price_per_day_per_kg" class="form-control" value="{{ old('price_per_day_per_kg', $warehouse->price_per_day_per_kg) }}" placeholder="Enter rate">
                          </div>

                          <div class="col-md-4 mb-3">
                              <label class="form-label">Minimum Charge</label>
                              <input type="number" step="0.01" name="min_charge" class="form-control" value="{{ old('min_charge', $warehouse->min_charge) }}" placeholder="Enter minimum charge">
                          </div>
                      </div>

                      <div class="row">
                          <div class="col-md-6 mb-3">
                              <div class="form-check mt-3">
                                  <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $warehouse->is_active) ? 'checked' : '' }}>
                                  <label class="form-check-label" for="is_active">Active</label>
                              </div>
                          </div>
                      </div>

                      <div class="text-end d-flex justify-content-end gap-2">
                          <button type="reset" class="btn btn-secondary">Reset</button>
                          <button type="submit" class="btn btn-primary">{{ $warehouse->exists ? 'Update Warehouse' : 'Add Warehouse' }}</button>
                      </div>
                  </form>
              </div>
          </div>
      </div>
  </div>
@endsection
