@include('web.header')

<style>
    .select2-container {
        box-sizing: border-box;
        display: inline-block;
        margin: 0;
        position: relative;
        vertical-align: middle;
    }

    .select2-container .select2-selection--single {
        box-sizing: border-box;
        cursor: pointer;
        display: block;
        user-select: none;
    }

    .select2-container .select2-selection__rendered {
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .select2-container .select2-selection__arrow {
        position: absolute;
        top: 0;
        width: 20px;
    }

    .select2-dropdown {
        background-color: #fff;
        border: 1px solid #aaa;
        box-sizing: border-box;
        display: block;
        left: -100000px;
        position: absolute;
        width: 100%;
        z-index: 1051;
    }

    .select2-container--open .select2-dropdown {
        left: 0;
    }

    .select2-results {
        display: block;
    }

    .select2-results__options {
        list-style: none;
        margin: 0;
        max-height: 220px;
        overflow-y: auto;
        padding: 0;
    }

    .select2-results__option {
        cursor: pointer;
        padding: 8px 10px;
        user-select: none;
    }

    .select2-results__option--highlighted[aria-selected] {
        background-color: #ff7a00;
        color: #fff;
    }

    .select2-search--dropdown {
        display: block;
        padding: 8px;
    }

    .select2-search--dropdown .select2-search__field {
        box-sizing: border-box;
        width: 100%;
    }

    .select2-hidden-accessible {
        border: 0 !important;
        clip: rect(0 0 0 0) !important;
        height: 1px !important;
        margin: -1px !important;
        overflow: hidden !important;
        padding: 0 !important;
        position: absolute !important;
        width: 1px !important;
    }

    .shipment-city-select + .select2-container {
        width: 100% !important;
    }

    .shipment-city-select + .select2-container .select2-selection--single {
        height: 50px;
        border: 1px solid #dee2e6;
        border-radius: 6px;
    }

    .shipment-city-select + .select2-container .select2-selection__rendered {
        line-height: 50px;
        padding-left: 12px;
        padding-right: 34px;
    }

    .shipment-city-select + .select2-container .select2-selection__arrow {
        height: 50px;
        right: 8px;
    }

    .select2-dropdown.shipment-city-dropdown {
        border-color: #dee2e6;
    }

    .select2-dropdown.shipment-city-dropdown .select2-search__field {
        min-height: 40px;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        outline: none;
    }
</style>

<section class="shipment-form-section" style="padding: 110px 0 80px; background:#f7f7f7;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10">
                <div style="background:#fff; border:1px solid #e8e8e8; padding:30px; border-radius:8px;">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                        <div>
                            <span style="color:#ff7a00; font-weight:600;">Shipment</span>
                            <h2 class="mb-0">Add Shipment</h2>
                        </div>
                        <a href="{{ route('shipment.cart') }}" class="primary-btn1 btn-hover">
                            My Cart
                            <span></span>
                        </a>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('shipment.save_items') }}" method="POST" id="shipmentItemForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">From City</label>
                                <select name="from_city" class="form-control shipment-city-select" data-placeholder="Select from city" required>
                                    <option value="">select city</option>
                                    @foreach($fromCities as $city)
                                        <option value="{{ $city }}" {{ old('from_city') === $city ? 'selected' : '' }}>
                                            {{ $city }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Deliver To</label>
                                <select name="to_city"  class="form-control shipment-city-select"  data-placeholder="Select delivery city" required>
                                    <option value="">select city</option>

                                    @foreach($toCities as $city)
                                        <option value="{{ $city }}" {{ old('to_city') === $city ? 'selected' : '' }}>
                                            {{ $city }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <div class="text-danger small mb-3 d-none" id="cityRouteError">
                                    Please select a valid active route.
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Transport Pickup Address</label>
                                <textarea name="pickup_address" class="form-control" rows="3" placeholder="Enter pickup address">{{ old('pickup_address') }}</textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Transport Delivery Address</label>
                                <textarea name="delivery_address" class="form-control" rows="3" placeholder="Enter delivery address">{{ old('delivery_address') }}</textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pickup Date</label>
                                <input type="date" name="pickup_date" class="form-control" value="{{ old('pickup_date') }}" required>
                            </div>
                            <!-- <div class="col-md-6 mb-3">
                                <label class="form-label">Delivery Date</label>
                                <input type="date" name="delivery_date" class="form-control" value="{{ old('delivery_date') }}" required>
                            </div> -->
                        </div>

                        <div id="shipmentItems">
                            <div class="shipment-item-row" style="border:1px solid #e7e7e7; border-radius:8px; padding:20px; margin-bottom:16px;">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Item 1</h5>
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-item" style="display:none;">Delete</button>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label"> Shipment Item Name</label>
                                        <input type="text" name="items[0][item_name]" class="form-control" placeholder="Box, chair, fridge..." required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label"> Shipment Item Type</label>
                                        <select name="items[0][item_type]" class="form-control" required>
                                            <option value="">Select item type</option>
                                            @foreach($itemTypes as $itemType)
                                                <option value="{{ $itemType }}" {{ old('items.0.item_type') == $itemType ? 'selected' : '' }}>
                                                    {{ $itemType }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Quantity</label>
                                        <input type="number" name="items[0][quantity]" class="form-control" value="1" min="1" required>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Length (CM)</label>
                                        <input type="number" step="0.01" name="items[0][length_cm]" class="form-control" min="0">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Width (CM)</label>
                                        <input type="number" step="0.01" name="items[0][width_cm]" class="form-control" min="0">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Height (CM)</label>
                                        <input type="number" step="0.01" name="items[0][height_cm]" class="form-control" min="0.01" required>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Weight (KG)</label>
                                        <input type="number" step="0.01" name="items[0][weight_kg]" class="form-control" min="0.01" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between flex-wrap gap-3 mt-4">
                            <button type="button" class="btn btn-secondary" id="addMoreItem">Add More</button>
                            <button type="submit" class="primary-btn1 btn-hover">
                                Save To Cart
                                <span></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let itemIndex = 1;
        const itemsWrapper = document.getElementById('shipmentItems');
        const addButton = document.getElementById('addMoreItem');
        const shipmentForm = document.getElementById('shipmentItemForm');
        const fromCitySelect = shipmentForm.querySelector('[name="from_city"]');
        const toCitySelect = shipmentForm.querySelector('[name="to_city"]');
        const cityRouteError = document.getElementById('cityRouteError');
        const activeRoutes = @json($cityRoutes->map(fn ($route) => [
            'from_city' => $route->from_city,
            'to_city' => $route->to_city,
        ])->values());

        if (window.jQuery && jQuery.fn.select2) {
            jQuery('.shipment-city-select').select2({
                width: '100%',
                allowClear: true,
                dropdownCssClass: 'shipment-city-dropdown',
                placeholder: function () {
                    return jQuery(this).data('placeholder');
                }
            });
        }

        function refreshRows() {
            const rows = itemsWrapper.querySelectorAll('.shipment-item-row');
            rows.forEach((row, index) => {
                row.querySelector('h5').textContent = 'Item ' + (index + 1);
                row.querySelector('.remove-item').style.display = rows.length > 1 ? 'inline-block' : 'none';
            });
        }

        addButton.addEventListener('click', function () {
            const firstRow = itemsWrapper.querySelector('.shipment-item-row');
            const newRow = firstRow.cloneNode(true);

            newRow.querySelectorAll('input').forEach(function (input) {
                input.name = input.name.replace(/items\[\d+\]/, 'items[' + itemIndex + ']');
                input.value = input.type === 'number' && input.name.includes('[quantity]') ? 1 : '';
            });
            newRow.querySelectorAll('select').forEach(function (select) {
                select.name = select.name.replace(/items\[\d+\]/, 'items[' + itemIndex + ']');
                select.value = '';
            });

            itemsWrapper.appendChild(newRow);
            itemIndex++;
            refreshRows();
        });

        itemsWrapper.addEventListener('click', function (event) {
            if (event.target.classList.contains('remove-item')) {
                event.target.closest('.shipment-item-row').remove();
                refreshRows();
            }
        });

        function hasActiveRoute() {
            const fromCity = fromCitySelect.value.trim().toLowerCase();
            const toCity = toCitySelect.value.trim().toLowerCase();

            return activeRoutes.some(function (route) {
                return route.from_city.toLowerCase() === fromCity && route.to_city.toLowerCase() === toCity;
            });
        }

        [fromCitySelect, toCitySelect].forEach(function (select) {
            select.addEventListener('change', function () {
                cityRouteError.classList.add('d-none');
            });
        });

        shipmentForm.addEventListener('submit', function (event) {
            if (!hasActiveRoute()) {
                event.preventDefault();
                cityRouteError.classList.remove('d-none');

                if (window.jQuery && jQuery.fn.select2) {
                    jQuery(toCitySelect).select2('open');
                } else {
                    toCitySelect.focus();
                }
            }
        });
    });
</script>

@include('web.footer')
