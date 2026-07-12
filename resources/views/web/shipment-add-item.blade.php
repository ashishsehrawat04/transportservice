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
        background-color: var(--ot-green);
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
        height: 48px;
        border: 1px solid var(--ot-line);
        border-radius: 8px;
    }

    .shipment-city-select + .select2-container .select2-selection__rendered {
        line-height: 48px;
        padding-left: 12px;
        padding-right: 34px;
    }

    .shipment-city-select + .select2-container .select2-selection__arrow {
        height: 48px;
        right: 8px;
    }

    .select2-dropdown.shipment-city-dropdown {
        border-color: var(--ot-line);
    }

    .select2-dropdown.shipment-city-dropdown .select2-search__field {
        min-height: 40px;
        border: 1px solid var(--ot-line);
        border-radius: 4px;
        outline: none;
    }
</style>

<style>
    .booking-section {
        padding: 110px 0 80px;
        background:
            radial-gradient(ellipse 900px 400px at 88% 0%, rgba(255, 122, 69, .06), transparent 60%),
            var(--ot-bg);
    }

    .booking-panel {
        background: var(--ot-panel);
        border: 1px solid var(--ot-line);
        border-radius: 16px;
        box-shadow: var(--ot-shadow);
        padding: 30px;
    }

    .booking-head {
        align-items: center;
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        justify-content: space-between;
        margin-bottom: 24px;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--ot-line);
    }

    .booking-eyebrow {
        color: var(--ot-amber-dark);
        display: block;
        font-size: 12.5px;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
    }

    .booking-head h2 {
        font-size: 27px;
        font-weight: 700;
        margin: 5px 0 0;
    }

    .booking-panel .form-label {
        color: var(--ot-ink-soft);
        font-size: 12.5px;
        font-weight: 700;
        letter-spacing: .01em;
    }

    .booking-panel .form-control,
    .booking-panel select.form-control {
        border: 1px solid var(--ot-line);
        border-radius: 8px;
        min-height: 48px;
        background: #fbfdfc;
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    .booking-panel textarea.form-control {
        min-height: 92px;
    }

    .booking-panel .form-control:focus {
        border-color: var(--ot-green);
        box-shadow: 0 0 0 3px rgba(14, 143, 122, .12);
        outline: none;
        background: #fff;
    }

    .booking-section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .07em;
        text-transform: uppercase;
        color: var(--ot-muted);
        margin: 30px 0 16px;
    }

    .booking-section-title::after {
        content: "";
        flex: 1;
        height: 1px;
        background: var(--ot-line);
    }

    .item-card {
        background: var(--ot-panel);
        border: 1px solid var(--ot-line);
        border-radius: 14px;
        margin-bottom: 14px;
        padding: 18px 20px;
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    .item-card:hover {
        border-color: rgba(14, 143, 122, .35);
        box-shadow: var(--ot-shadow-sm);
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
        gap: 12px;
        justify-content: space-between;
        margin-bottom: 16px;
    }

    .item-card-title {
        display: flex;
        align-items: center;
        gap: 11px;
    }

    .item-card-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: var(--ot-panel-tint);
        border: 1px solid var(--ot-line);
        display: grid;
        place-items: center;
        flex: none;
    }

    .item-card-icon svg { width: 19px; height: 19px; stroke: var(--ot-green-dark); fill: none; }

    .item-card-head h5 {
        font-family: var(--ot-display);
        font-size: 15px;
        font-weight: 600;
        margin: 0;
    }

    .btn-outline-teal {
        align-items: center;
        background: transparent;
        border: 1px solid var(--ot-green);
        border-radius: 9px;
        color: var(--ot-green-dark);
        display: inline-flex;
        font-family: var(--ot-display);
        font-size: 13px;
        font-weight: 600;
        gap: 8px;
        min-height: 46px;
        padding: 0 18px;
        transition: background .15s ease, color .15s ease;
    }

    .btn-outline-teal svg { width: 15px; height: 15px; stroke: currentColor; }

    .btn-outline-teal:hover {
        background: var(--ot-green);
        color: #fff;
    }

    .btn-spinner {
        display: none;
        width: 15px;
        height: 15px;
        border: 2px solid currentColor;
        border-right-color: transparent;
        border-radius: 50%;
        opacity: .75;
        flex: none;
    }

    @media (prefers-reduced-motion: no-preference) {
        .btn-spinner { animation: btnSpin .7s linear infinite; }
    }

    @keyframes btnSpin {
        to { transform: rotate(360deg); }
    }

    #calculateEstimateBtn.is-loading {
        cursor: default;
        opacity: .85;
    }

    #calculateEstimateBtn.is-loading .btn-spinner { display: inline-block; }
    #calculateEstimateBtn.is-loading .btn-icon { display: none; }

    .btn-remove-item {
        align-items: center;
        background: #fef3f2;
        border: 1px solid #fecdca;
        border-radius: 8px;
        color: #b42318;
        display: inline-flex;
        font-size: 11.5px;
        font-weight: 700;
        gap: 5px;
        padding: 6px 12px;
    }

    .btn-remove-item svg { width: 11px; height: 11px; stroke: currentColor; }

    .btn-remove-item:hover {
        background: #b42318;
        border-color: #b42318;
        color: #fff;
    }

    .btn-neutral-outline {
        align-items: center;
        background: var(--ot-panel);
        border: 1px solid var(--ot-line);
        border-radius: 9px;
        color: var(--ot-ink);
        display: inline-flex;
        font-family: var(--ot-display);
        font-weight: 600;
        font-size: 13.5px;
        gap: 8px;
        justify-content: center;
        min-height: 46px;
        padding: 0 18px;
    }

    .btn-neutral-outline svg { width: 14px; height: 14px; stroke: currentColor; }

    .btn-neutral-outline:hover {
        background: var(--ot-panel-tint);
        color: var(--ot-green-dark);
    }
</style>

<style>
    .estimate-panel {
        margin-top: 18px;
        background: var(--ot-panel-tint);
        border: 1px solid rgba(14, 143, 122, .25);
        border-radius: 14px;
        padding: 20px 22px;
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
        border-radius: 10px;
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
        display: flex;
        align-items: center;
        gap: 8px;
        font-family: var(--ot-display);
        font-size: 14.5px;
        font-weight: 600;
        color: var(--ot-amber-dark);
        margin-bottom: 14px;
    }

    .estimate-panel-header svg { width: 16px; height: 16px; stroke: currentColor; }

    .estimate-item-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        padding: 9px 0;
        border-bottom: 1px dashed rgba(14, 143, 122, .2);
        font-size: 13.5px;
    }

    .estimate-item-row:last-child {
        border-bottom: none;
    }

    .estimate-item-index {
        font-weight: 700;
        min-width: 56px;
        font-size: 12.5px;
        color: var(--ot-ink-soft);
    }

    .estimate-item-figure {
        color: var(--ot-ink-soft);
        font-family: var(--ot-mono);
        font-size: 12.5px;
    }

    .estimate-item-charge {
        font-family: var(--ot-mono);
        font-weight: 700;
    }

    .estimate-summary {
        margin-top: 14px;
        padding-top: 14px;
        border-top: 1px solid rgba(14, 143, 122, .25);
    }

    .estimate-summary-line {
        display: flex;
        justify-content: space-between;
        font-size: 13px;
        color: var(--ot-muted);
        padding: 4px 0;
    }

    .estimate-summary-line span:last-child {
        font-family: var(--ot-mono);
        color: var(--ot-ink-soft);
    }

    .estimate-summary-total {
        margin-top: 4px;
        padding-top: 8px;
        border-top: 1px dashed rgba(14, 143, 122, .25);
    }

    .estimate-summary-total span:first-child {
        font-weight: 700;
        color: var(--ot-ink);
        font-size: 14px;
    }

    .estimate-summary-total span:last-child {
        color: var(--ot-green-dark) !important;
        font-weight: 700;
        font-size: 17px;
    }

    .basis-chip {
        display: inline-flex;
        align-items: center;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .04em;
        padding: 3px 9px;
        border-radius: 999px;
        white-space: nowrap;
    }

    .basis-chip.weight {
        color: var(--ot-sky);
        background: rgba(47, 143, 224, .12);
        border: 1px solid rgba(47, 143, 224, .3);
    }

    .basis-chip.volume {
        color: var(--ot-green-dark);
        background: rgba(14, 143, 122, .1);
        border: 1px solid rgba(14, 143, 122, .3);
    }
</style>

<svg style="position:absolute; width:0; height:0; overflow:hidden" aria-hidden="true">
    <symbol id="ai-calc" viewBox="0 0 24 24" fill="none" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2"></rect><path d="M8 6h8M8 11h1M12 11h1M16 11h1M8 15h1M12 15h1M16 15h1M8 19h1M12 19h1"></path></symbol>
    <symbol id="ai-plus" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"></path></symbol>
    <symbol id="ai-trash" viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16"></path><path d="M9 7V4h6v3"></path><path d="M6 7l1 13h10l1-13"></path></symbol>
    <symbol id="ai-box" viewBox="0 0 32 32" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 10l12-6 12 6-12 6z"></path><path d="M4 10v12l12 6 12-6V10"></path><path d="M16 16v12"></path></symbol>
    <symbol id="ai-bike" viewBox="0 0 32 32" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="22" r="5"></circle><circle cx="24" cy="22" r="5"></circle><path d="M8 22l6-11h6l4 11"></path><path d="M14 11h6"></path><path d="M14 22h10"></path></symbol>
    <symbol id="ai-electronics" viewBox="0 0 32 32" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="6" width="24" height="16" rx="1.6"></rect><path d="M12 26h8M16 22v4"></path></symbol>
    <symbol id="ai-sparkle" viewBox="0 0 24 24" fill="none" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8z"></path></symbol>
</svg>

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
                        <!-- <a href="{{ route('shipment.cart') }}" class="primary-btn1 btn-hover">
                            My Cart
                            <span></span>
                        </a> -->
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
                                    <div class="item-card-title">
                                        <span class="item-card-icon"><svg viewBox="0 0 32 32"><use href="#ai-box" class="item-type-icon-use"></use></svg></span>
                                        <h5>Item 1</h5>
                                    </div>
                                    <button type="button" class="btn-remove-item remove-item" style="display:none;">
                                        <svg viewBox="0 0 24 24"><use href="#ai-trash"></use></svg> Delete
                                    </button>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label"> Shipment Item Name</label>
                                        <input type="text" name="items[0][item_name]" class="form-control" placeholder="Box, chair, fridge..." required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label"> Shipment Item Type</label>
                                        <select name="items[0][item_type]" class="form-control item-type-select" required>
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
                                <svg class="btn-icon" viewBox="0 0 24 24"><use href="#ai-calc"></use></svg>
                                <span class="btn-spinner"></span>
                                <span id="calculateEstimateLabel">Calculate Estimate</span>
                            </button>
                        </div>

                         <div class="d-flex justify-content-between flex-wrap gap-3 mt-4">
                            <button type="button" class="btn-neutral-outline" id="addMoreItem">
                                <svg viewBox="0 0 24 24"><use href="#ai-plus"></use></svg> Add More
                            </button>
                            <button type="submit" class="primary-btn1 btn-hover">
                                Add To Cart
                                <span></span>
                            </button>
                        </div>
                        <div class="text-danger small mt-2 d-none" id="estimateError"></div>

                        <div class="estimate-skeleton d-none" id="estimateSkeleton">
                            <div class="estimate-skeleton-row"></div>
                            <div class="estimate-skeleton-row"></div>
                        </div>

                        <div class="estimate-panel d-none" id="estimateResult">
                            <div class="estimate-panel-header"><svg viewBox="0 0 24 24"><use href="#ai-sparkle"></use></svg> Estimated Price</div>
                            <div id="estimateItemsList"></div>
                            <div class="estimate-summary">
                                <div class="estimate-summary-line">
                                    <span>Transit Time</span>
                                    <span id="estimateTransitTime">-</span>
                                </div>
                                <div class="estimate-summary-line">
                                    <span>Min Charge (per item)</span>
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

        const typeIconMap = { bike: 'ai-bike', electronics: 'ai-electronics' };

        function iconForType(type) {
            return typeIconMap[type] || 'ai-box';
        }

        function syncTypeIcon(row) {
            const select = row.querySelector('.item-type-select');
            const use = row.querySelector('.item-type-icon-use');
            if (select && use) {
                use.setAttribute('href', '#' + iconForType(select.value));
            }
        }

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
                row.querySelector('.remove-item').style.display = rows.length > 1 ? 'inline-flex' : 'none';
            });
        }

        itemsWrapper.addEventListener('change', function (event) {
            if (event.target.classList.contains('item-type-select')) {
                syncTypeIcon(event.target.closest('.shipment-item-row'));
            }
        });

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
            syncTypeIcon(newRow);

            newRow.classList.add('item-card-enter');
            itemsWrapper.appendChild(newRow);
            itemIndex++;
            refreshRows();
            newRow.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            autoRecalcIfOpen();
        });

        itemsWrapper.addEventListener('click', function (event) {
            if (event.target.closest('.remove-item')) {
                event.target.closest('.shipment-item-row').remove();
                refreshRows();
                autoRecalcIfOpen();
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
                autoRecalcIfOpen();
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
        const calculateEstimateLabel = document.getElementById('calculateEstimateLabel');
        const estimateError = document.getElementById('estimateError');
        const estimateSkeleton = document.getElementById('estimateSkeleton');
        const estimateResult = document.getElementById('estimateResult');
        const estimateItemsList = document.getElementById('estimateItemsList');
        const estimateTransitTime = document.getElementById('estimateTransitTime');
        const estimateMinCharge = document.getElementById('estimateMinCharge');
        const estimateItemsSubtotal = document.getElementById('estimateItemsSubtotal');
        const estimateGrandTotal = document.getElementById('estimateGrandTotal');

        function isEstimateOpen() {
            return !estimateResult.classList.contains('d-none');
        }

        function debounce(fn, delay) {
            let timer;
            return function () {
                clearTimeout(timer);
                timer = setTimeout(fn, delay);
            };
        }

        async function runEstimate() {
            estimateError.classList.add('d-none');

            if (!hasActiveRoute()) {
                estimateError.textContent = 'Please select a valid active route first.';
                estimateError.classList.remove('d-none');
                estimateResult.classList.add('d-none');
                return;
            }

            calculateEstimateBtn.disabled = true;
            calculateEstimateBtn.classList.add('is-loading');
            calculateEstimateLabel.textContent = 'Calculating...';

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
                    const figure = Number(item.charge_weight_kg).toFixed(2) + ' kg';

                    const row = document.createElement('div');
                    row.className = 'estimate-item-row';
                    row.innerHTML =
                        '<span class="estimate-item-index">Item ' + (index + 1) + '</span>' +
                        '<span class="' + chipClass + '">' + chipLabel + '</span>' +
                        '<span class="estimate-item-figure">' + figure + '</span>' +
                        '<span class="estimate-item-charge">₹' + Number(item.item_charge).toFixed(2) + '</span>';
                    estimateItemsList.appendChild(row);
                });

                const transitDays = data.route && data.route.transit_days;
                estimateTransitTime.textContent = transitDays ? transitDays + (transitDays > 1 ? ' Days' : ' Day') : '-';
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
                calculateEstimateBtn.classList.remove('is-loading');
                calculateEstimateLabel.textContent = 'Calculate Estimate';
            }
        }

        const debouncedAutoRecalc = debounce(function () {
            if (isEstimateOpen()) {
                runEstimate();
            }
        }, 500);

        function autoRecalcIfOpen() {
            if (isEstimateOpen()) {
                runEstimate();
            }
        }

        itemsWrapper.addEventListener('input', function (event) {
            if (event.target.matches('input[name$="[length_cm]"], input[name$="[width_cm]"], input[name$="[height_cm]"], input[name$="[weight_kg]"], input[name$="[quantity]"]')) {
                debouncedAutoRecalc();
            }
        });

        calculateEstimateBtn.addEventListener('click', function (event) {
            event.preventDefault();
            runEstimate();
        });

        function resetShipmentForm() {
            shipmentForm.reset();

            if (window.jQuery && jQuery.fn.select2) {
                jQuery('.shipment-city-select').val('').trigger('change');
            }

            itemsWrapper.querySelectorAll('.shipment-item-row').forEach(function (row, index) {
                if (index > 0) {
                    row.remove();
                }
            });
            itemIndex = 1;

            const firstRow = itemsWrapper.querySelector('.shipment-item-row');
            if (firstRow) {
                firstRow.querySelectorAll('input').forEach(function (input) {
                    input.value = input.name.includes('[quantity]') ? 1 : '';
                });
                firstRow.querySelectorAll('select').forEach(function (select) {
                    select.value = '';
                });
                syncTypeIcon(firstRow);
            }
            refreshRows();

            estimateResult.classList.add('d-none');
            estimateSkeleton.classList.add('d-none');
            estimateError.classList.add('d-none');
            cityRouteError.classList.add('d-none');
        }

        function isBackForwardNavigation() {
            if (window.performance && typeof performance.getEntriesByType === 'function') {
                const entries = performance.getEntriesByType('navigation');
                if (entries.length && entries[0].type === 'back_forward') {
                    return true;
                }
            }
            return false;
        }

        // Using the browser's Back button to return here — whether the page
        // is served from bfcache or the browser just restores old field
        // values on a fresh load — must not leave stale item data behind,
        // or a stray "Add to Cart" click resubmits the same shipment again.
        // Chrome applies its own field-value restoration late (after load,
        // not at DOMContentLoaded), so the reset has to happen after that —
        // hooking `load` plus a short fallback delay covers it reliably.
        if (isBackForwardNavigation()) {
            resetShipmentForm();
            window.addEventListener('load', resetShipmentForm);
            setTimeout(resetShipmentForm, 300);
        }

        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                resetShipmentForm();
            }
        });
    });
</script>

@include('web.footer')
