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
        background-color: #ff7a45;
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

<style>
    .estimate-panel {
        margin-top: 16px;
        background: #F8FAFC;
        border: 1px solid #E4E8F0;
        border-radius: 8px;
        padding: 20px;
    }

    @media (prefers-reduced-motion: no-preference) {
        .estimate-panel:not(.d-none) {
            animation: estimateReveal .45s cubic-bezier(.22, .9, .3, 1);
        }
    }

    @keyframes estimateReveal {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .estimate-skeleton {
        margin-top: 16px;
    }

    .estimate-skeleton-row {
        height: 44px;
        border-radius: 8px;
        margin-bottom: 10px;
        background: linear-gradient(90deg, #EEF1F6 25%, #E4E8F0 37%, #EEF1F6 63%);
        background-size: 400% 100%;
    }

    .estimate-skeleton-row:last-child {
        width: 70%;
        margin-bottom: 0;
    }

    @media (prefers-reduced-motion: no-preference) {
        .estimate-skeleton-row {
            animation: estimateShimmer 1.4s ease infinite;
        }
    }

    @keyframes estimateShimmer {
        0% { background-position: 100% 50%; }
        100% { background-position: 0 50%; }
    }

    .estimate-panel-header {
        font-size: 14px;
        margin-bottom: 12px;
    }

    .estimate-item-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        padding: 8px 0;
        border-bottom: 1px solid #E4E8F0;
        font-size: 13.5px;
        color: #101820;
    }

    .estimate-item-row:last-child {
        border-bottom: none;
    }

    .estimate-item-index {
        font-weight: 600;
        min-width: 60px;
    }

    .estimate-item-figure {
        color: #667085;
        font-size: 12.5px;
    }

    .estimate-item-charge {
        font-weight: 700;
    }

    .estimate-summary {
        margin-top: 14px;
        padding-top: 14px;
        border-top: 1px solid #E4E8F0;
    }

    .estimate-summary-line {
        display: flex;
        justify-content: space-between;
        font-size: 13.5px;
        color: #667085;
        padding: 3px 0;
    }

    .estimate-summary-total {
        color: #101820;
        font-weight: 700;
        font-size: 15px;
    }

    .basis-chip {
        display: inline-flex;
        align-items: center;
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .02em;
        padding: 2px 7px;
        border-radius: 20px;
        border: 1px solid;
        white-space: nowrap;
    }

    .basis-chip.weight {
        color: #175CD3;
        background: #EAF2FF;
        border-color: #C8DDFF;
    }

    .basis-chip.volume {
        color: #6941C6;
        background: #F4EBFF;
        border-color: #E2D2FB;
    }
</style>

<style>
    .booking-section {
        background: #f5f7fb;
        padding: 110px 0 80px;
    }

    .booking-panel {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: 0 14px 35px rgba(15, 23, 42, .06);
        padding: 28px;
    }

    .booking-head {
        align-items: center;
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        justify-content: space-between;
        margin-bottom: 22px;
    }

    .booking-eyebrow {
        color: #ff7a45;
        display: block;
        font-size: 13px;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .booking-head h2 {
        color: #101820;
        font-size: 26px;
        font-weight: 800;
        margin: 4px 0 0;
    }

    .booking-panel .form-label {
        color: #344054;
        font-size: 13px;
        font-weight: 700;
    }

    .booking-panel .form-control,
    .booking-panel select.form-control {
        border: 1px solid #d1d5db;
        border-radius: 6px;
        min-height: 46px;
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    .booking-panel textarea.form-control {
        min-height: 92px;
    }

    .booking-panel .form-control:focus {
        border-color: #0e8f7a;
        box-shadow: 0 0 0 3px rgba(14, 143, 122, .12);
        outline: none;
    }

    .booking-section-title {
        color: #101820;
        font-size: 15px;
        font-weight: 800;
        margin: 26px 0 14px;
    }

    .item-card {
        background: #fbfbfd;
        border: 1px solid #eef1f6;
        border-radius: 10px;
        margin-bottom: 14px;
        padding: 18px;
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    .item-card:hover {
        border-color: #d6dce8;
        box-shadow: 0 4px 14px rgba(18, 33, 60, .05);
    }

    @media (prefers-reduced-motion: no-preference) {
        .item-card.item-card-enter {
            animation: itemCardEnter .35s cubic-bezier(.22, .9, .3, 1);
        }
    }

    @keyframes itemCardEnter {
        from { opacity: 0; transform: translateY(-8px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .item-card-head {
        align-items: center;
        display: flex;
        justify-content: space-between;
        margin-bottom: 14px;
    }

    .item-card-head h5 {
        color: #101820;
        font-size: 14.5px;
        font-weight: 800;
        margin: 0;
    }

    .btn-outline-teal {
        background: transparent;
        border: 1px solid #0e8f7a;
        border-radius: 6px;
        color: #0a6c5c;
        font-size: 13px;
        font-weight: 800;
        min-height: 46px;
        padding: 0 18px;
        transition: background .15s ease, color .15s ease;
    }

    .btn-outline-teal:hover {
        background: #0e8f7a;
        color: #fff;
    }

    .btn-remove-item {
        background: #fef3f2;
        border: 1px solid #fecdca;
        border-radius: 6px;
        color: #b42318;
        font-size: 12px;
        font-weight: 700;
        padding: 5px 12px;
    }

    .btn-remove-item:hover {
        background: #b42318;
        border-color: #b42318;
        color: #fff;
    }

    .btn-neutral-outline {
        align-items: center;
        background: #fff;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        color: #101820;
        display: inline-flex;
        font-weight: 700;
        gap: 8px;
        justify-content: center;
        min-height: 46px;
        padding: 0 18px;
    }

    .btn-neutral-outline:hover {
        background: #f9fafb;
        color: #101820;
    }
</style>

<section class="booking-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10">
                <div class="booking-panel">
                    <div class="booking-head">
                        <div>
                            <span class="booking-eyebrow">Shipment</span>
                            <h2>Add Shipment</h2>
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

                        <div class="booking-section-title">Item Details</div>
                        <div id="shipmentItems">
                            <div class="shipment-item-row item-card">
                                <div class="item-card-head">
                                    <h5>Item 1</h5>
                                    <button type="button" class="btn-remove-item remove-item" style="display:none;">Delete</button>
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

                        <div class="d-flex justify-content-start">
                            <button type="button" class="btn-outline-teal" id="calculateEstimateBtn">
                                <i class="bi bi-calculator"></i> Calculate Estimate
                            </button>
                        </div>
                        <div class="text-danger small mt-2 d-none" id="estimateError"></div>

                        <div class="estimate-skeleton d-none" id="estimateSkeleton">
                            <div class="estimate-skeleton-row"></div>
                            <div class="estimate-skeleton-row"></div>
                        </div>

                        <div class="estimate-panel d-none" id="estimateResult">
                            <div class="estimate-panel-header" style="color:#ff7a45; font-weight:600;">Estimated Price</div>
                            <div id="estimateItemsList"></div>
                            <div class="estimate-summary">
                                <div class="estimate-summary-line">
                                    <span>Route Fair Charges</span>
                                    <span id="estimateMinCharge">₹0.00</span>
                                </div>
                                <div class="estimate-summary-line">
                                    <span>Items subtotal</span>
                                    <span id="estimateItemsSubtotal">₹0.00</span>
                                </div>
                                <div class="estimate-summary-line estimate-summary-total">
                                    <span>Estimated Total</span>
                                    <span id="estimateGrandTotal">₹0.00</span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between flex-wrap gap-3 mt-4">
                            <button type="button" class="btn-neutral-outline" id="addMoreItem">Add More</button>
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

            newRow.classList.add('item-card-enter');
            itemsWrapper.appendChild(newRow);
            itemIndex++;
            refreshRows();
            newRow.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
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

        const calculateEstimateBtn = document.getElementById('calculateEstimateBtn');
        const estimateError = document.getElementById('estimateError');
        const estimateSkeleton = document.getElementById('estimateSkeleton');
        const estimateResult = document.getElementById('estimateResult');
        const estimateItemsList = document.getElementById('estimateItemsList');
        const estimateMinCharge = document.getElementById('estimateMinCharge');
        const estimateItemsSubtotal = document.getElementById('estimateItemsSubtotal');
        const estimateGrandTotal = document.getElementById('estimateGrandTotal');

        calculateEstimateBtn.addEventListener('click', async function (event) {
            event.preventDefault();
            estimateError.classList.add('d-none');
            estimateResult.classList.add('d-none');

            if (!hasActiveRoute()) {
                estimateError.textContent = 'Please select a valid active route first.';
                estimateError.classList.remove('d-none');
                return;
            }

            const items = [];
            itemsWrapper.querySelectorAll('.shipment-item-row').forEach(function (row) {
                items.push({
                    quantity: parseInt(row.querySelector('input[name$="[quantity]"]').value, 10) || 1,
                    length_cm: parseFloat(row.querySelector('input[name$="[length_cm]"]').value) || 0,
                    width_cm: parseFloat(row.querySelector('input[name$="[width_cm]"]').value) || 0,
                    height_cm: parseFloat(row.querySelector('input[name$="[height_cm]"]').value) || 0,
                    weight_kg: parseFloat(row.querySelector('input[name$="[weight_kg]"]').value) || 0,
                });
            });

            const payload = {
                from_city: fromCitySelect.value,
                to_city: toCitySelect.value,
                items: items,
            };

            const originalLabel = calculateEstimateBtn.innerHTML;
            calculateEstimateBtn.disabled = true;
            calculateEstimateBtn.textContent = 'Calculating...';
            estimateSkeleton.classList.remove('d-none');

            try {
                const response = await fetch("{{ route('shipment.estimate_items') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': shipmentForm.querySelector('input[name="_token"]').value,
                    },
                    body: JSON.stringify(payload),
                });

                const data = await response.json();

                if (response.status === 422 || !data.success) {
                    estimateSkeleton.classList.add('d-none');
                    estimateError.textContent = data.message || 'Could not calculate estimate. Please try again.';
                    estimateError.classList.remove('d-none');
                    return;
                }

                estimateItemsList.innerHTML = '';
                data.items.forEach(function (item, index) {
                    const isVolume = item.charge_basis === 'volume';
                    const chipClass = isVolume ? 'basis-chip volume' : 'basis-chip weight';
                    const chipLabel = isVolume ? 'Volume' : 'Weight';
                    const figure = isVolume
                        ? Number(item.volume_cft).toFixed(2) + ' cft'
                        : Number(item.charge_weight_kg).toFixed(2) + ' kg';

                    const row = document.createElement('div');
                    row.className = 'estimate-item-row';
                    row.innerHTML =
                        '<span class="estimate-item-index">Item ' + (index + 1) + '</span>' +
                        '<span class="' + chipClass + '">' + chipLabel + '</span>' +
                        '<span class="estimate-item-figure">' + figure + '</span>' +
                        '<span class="estimate-item-charge">₹' + Number(item.item_charge).toFixed(2) + '</span>';
                    estimateItemsList.appendChild(row);
                });

                estimateMinCharge.textContent = '₹' + Number(data.min_charge).toFixed(2);
                estimateItemsSubtotal.textContent = '₹' + Number(data.items_total).toFixed(2);
                estimateGrandTotal.textContent = '₹' + Number(data.grand_total).toFixed(2);

                estimateSkeleton.classList.add('d-none');
                estimateResult.classList.remove('d-none');
            } catch (error) {
                estimateSkeleton.classList.add('d-none');
                estimateError.textContent = 'Could not calculate estimate. Please try again.';
                estimateError.classList.remove('d-none');
            } finally {
                calculateEstimateBtn.disabled = false;
                calculateEstimateBtn.innerHTML = originalLabel;
            }
        });
    });
</script>

@include('web.footer')
