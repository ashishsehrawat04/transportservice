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
                  <h4 class="card-title">{{ $packersMover->exists ? 'Edit Branch' : 'Add Branch' }}</h4>
                  <a href="{{ route('admin.packers_movers') }}" class="btn btn-secondary btn-sm">Back to Packers &amp; Movers</a>
              </div>
              <div class="card-body">
                  <form action="{{ route('admin.save.packers_mover', $packersMover->id ?? '') }}" method="POST">
                      @csrf
                      <div class="row">
                          <div class="col-md-6 mb-3">
                              <label class="form-label">Branch Name</label>
                              <input type="text" name="name" class="form-control" value="{{ old('name', $packersMover->name) }}" placeholder="Enter branch name">
                          </div>

                          <div class="col-md-6 mb-3">
                              <label class="form-label">City</label>
                              <input type="text" name="city" class="form-control" value="{{ old('city', $packersMover->city) }}" placeholder="Enter city">
                          </div>
                      </div>

                      <div class="row">
                          <div class="col-md-12 mb-3">
                              <label class="form-label">Address</label>
                              <textarea name="address" class="form-control" rows="3" placeholder="Enter branch address">{{ old('address', $packersMover->address) }}</textarea>
                          </div>
                      </div>

                      <div class="row">
                          <div class="col-md-4 mb-3">
                              <label class="form-label">Price per KM per KG</label>
                              <input type="number" step="0.01" name="price_per_km_per_kg" class="form-control" value="{{ old('price_per_km_per_kg', $packersMover->price_per_km_per_kg) }}" placeholder="Enter rate">
                          </div>

                          <div class="col-md-4 mb-3">
                              <label class="form-label">Minimum Charge</label>
                              <input type="number" step="0.01" name="min_charge" class="form-control" value="{{ old('min_charge', $packersMover->min_charge) }}" placeholder="Enter minimum charge">
                          </div>
                      </div>

                      <div class="row">
                          <div class="col-md-6 mb-3">
                              <div class="form-check mt-3">
                                  <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $packersMover->is_active) ? 'checked' : '' }}>
                                  <label class="form-check-label" for="is_active">Active</label>
                              </div>
                          </div>
                      </div>

                      <div class="text-end d-flex justify-content-end gap-2">
                          <button type="reset" class="btn btn-secondary">Reset</button>
                          <button type="submit" class="btn btn-primary">{{ $packersMover->exists ? 'Update Branch' : 'Add Branch' }}</button>
                      </div>
                  </form>
              </div>
          </div>
      </div>
  </div>
@endsection
