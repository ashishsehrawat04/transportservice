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
                  <h4 class="card-title">{{ $transportPrice->exists ? 'Edit Transport Price' : 'Add Transport Price' }}</h4>
                  <a href="{{ route('admin.transport_prices') }}" class="btn btn-secondary btn-sm">Back to Transport Prices</a>
              </div>
              <div class="card-body">
                  <form action="{{ route('admin.save.transport_price', $transportPrice->id ?? '') }}" method="POST">
                      @csrf
                      <div class="row">
                          <div class="col-md-6 mb-3">
                              <label class="form-label">Item Type</label>
                              <input type="text" name="item_type" class="form-control" value="{{ old('item_type', $transportPrice->item_type) }}" placeholder="Enter item type">
                          </div>

                          <div class="col-md-6 mb-3">
                              <label class="form-label">Description</label>
                              <input type="text" name="description" class="form-control" value="{{ old('description', $transportPrice->description) }}" placeholder="Enter description (optional)">
                          </div>
                      </div>

                      <div class="row">
                          <div class="col-md-4 mb-3">
                              <label class="form-label">Calculate Price By</label>
                              <select name="calculation_type" id="calculation_type" class="form-control">
                                  @foreach(['distance' => 'Distance', 'volume' => 'Volume'] as $value => $label)
                                      <option value="{{ $value }}" {{ old('calculation_type', $transportPrice->calculation_type ?? 'distance') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                  @endforeach
                              </select>
                              <small class="text-muted">Current checkout auto-selects Weight or Volume, whichever charge is higher.</small>
                          </div>

                          <div class="col-md-4 mb-3">
                              <label class="form-label">Weight Rate / KG</label>
                              <input type="number" step="0.01" name="weight_rate_per_kg" class="form-control" value="{{ old('weight_rate_per_kg', $transportPrice->weight_rate_per_kg ?? 0) }}" placeholder="Enter weight rate per KG">
                          </div>

                          <div class="col-md-4 mb-3 price-rate-field" data-rate-type="distance">
                              <label class="form-label">Distance Rate / KM</label>
                              <input type="number" step="0.01" name="distance_rate_per_km" class="form-control" value="{{ old('distance_rate_per_km', $transportPrice->distance_rate_per_km ?? 0) }}" placeholder="Enter distance rate per KM">
                          </div>

                      </div>

                      <div class="row">
                          <div class="col-md-4 mb-3 price-rate-field" data-rate-type="volume">
                              <label class="form-label">Volume Rate / CFT</label>
                              <input type="number" step="0.01" name="volume_rate_per_cft" class="form-control" value="{{ old('volume_rate_per_cft', $transportPrice->volume_rate_per_cft ?? 0) }}" placeholder="Enter volume rate per CFT">
                          </div>

                          <div class="col-md-4 mb-3">
                              <label class="form-label">Minimum Charge</label>
                              <input type="number" step="0.01" name="min_charge" class="form-control" value="{{ old('min_charge', $transportPrice->min_charge ?? 0.00) }}" placeholder="Enter minimum charge">
                          </div>
                          <div class="col-md-4 mb-3">
                              <div class="form-check mt-4 pt-2">
                                  <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $transportPrice->is_active) ? 'checked' : '' }}>
                                  <label class="form-check-label" for="is_active">Active</label>
                              </div>
                          </div>
                      </div>

                      <div class="text-end d-flex justify-content-end gap-2">
                          <button type="reset" class="btn btn-secondary">Reset</button>
                          <button type="submit" class="btn btn-primary">{{ $transportPrice->exists ? 'Update Transport Price' : 'Add Transport Price' }}</button>
                      </div>
                  </form>
              </div>
          </div>
      </div>
  </div>

  <script>
      document.addEventListener('DOMContentLoaded', function () {
          const calculationType = document.getElementById('calculation_type');
          const rateFields = document.querySelectorAll('.price-rate-field');

          function syncRateFields() {
              rateFields.forEach(function (field) {
                  const input = field.querySelector('input');
                  const isActive = field.dataset.rateType === calculationType.value;

                  field.style.opacity = isActive ? '1' : '.45';
                  input.required = false;
              });
          }

          calculationType.addEventListener('change', syncRateFields);
          syncRateFields();
      });
  </script>
@endsection
