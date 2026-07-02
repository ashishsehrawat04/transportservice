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
                  <h4 class="card-title">{{ $cityRoute->exists ? 'Edit City Route' : 'Add City Route' }}</h4>
                  <a href="{{ route('admin.city_routes') }}" class="btn btn-secondary btn-sm">Back to City Routes</a>
              </div>
              <div class="card-body">
                  <form action="{{ route('admin.save.city_route', $cityRoute->id ?? '') }}" method="POST">
                      @csrf
                      <div class="row">
                          <div class="col-md-6 mb-3">
                              <label class="form-label">From City</label>
                              <input type="text" name="from_city" class="form-control" value="{{ old('from_city', $cityRoute->from_city) }}" placeholder="Enter start city">
                          </div>

                          <div class="col-md-6 mb-3">
                              <label class="form-label">To City</label>
                              <input type="text" name="to_city" class="form-control" value="{{ old('to_city', $cityRoute->to_city) }}" placeholder="Enter destination city">
                          </div>
                      </div>

                      <div class="row">
                          <div class="col-md-4 mb-3">
                              <label class="form-label">Distance (KM)</label>
                              <input type="number" step="0.01" name="distance_km" class="form-control" value="{{ old('distance_km', $cityRoute->distance_km) }}" placeholder="Enter distance in kilometers">
                          </div>
                          <div class="col-md-4 mb-3">
                              <label class="form-label">Fair charges</label>
                              <input type="number" step="0.01" name="min_charge" class="form-control" value="{{ old('min_charge', $cityRoute->min_charge) }}" placeholder="Enter Charge">
                          </div>

                          <div class="col-md-4 mb-3">
                              <label class="form-label">Weight rate per KM</label>
                              <input type="number" step="0.01" name="base_rate_per_km" class="form-control" value="{{ old('base_rate_per_km', $cityRoute->base_rate_per_km) }}" placeholder="Enter weight rate per KM">
                          </div>

                          <div class="col-md-4 mb-3">
                              <label class="form-label">Volume rate per unit</label>
                              <input type="number" step="0.01" name="base_rate_per_volume" class="form-control" value="{{ old('base_rate_per_volume', $cityRoute->base_rate_per_volume) }}" placeholder="Enter volume rate per unit">
                          </div>
                      </div>

                      <div class="row">
                          <div class="col-md-6 mb-3">
                              <div class="form-check mt-3">
                                  <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $cityRoute->is_active) ? 'checked' : '' }}>
                                  <label class="form-check-label" for="is_active">Active</label>
                              </div>
                          </div>
                      </div>

                      <div class="text-end d-flex justify-content-end gap-2">
                          <button type="reset" class="btn btn-secondary">Reset</button>
                          <button type="submit" class="btn btn-primary">{{ $cityRoute->exists ? 'Update City Route' : 'Add City Route' }}</button>
                      </div>
                  </form>
              </div>
          </div>
      </div>
  </div>
@endsection
