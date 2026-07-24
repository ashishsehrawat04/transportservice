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

    .pm-select + .select2-container {
        width: 100% !important;
    }

    .pm-select + .select2-container .select2-selection--single {
        height: 48px;
        border: 1px solid var(--ot-line);
        border-radius: 8px;
    }

    .pm-select + .select2-container .select2-selection__rendered {
        line-height: 48px;
        padding-left: 12px;
        padding-right: 34px;
    }

    .pm-select + .select2-container .select2-selection__arrow {
        height: 48px;
        right: 8px;
    }

    .select2-dropdown.pm-dropdown {
        border-color: var(--ot-line);
    }

    .select2-dropdown.pm-dropdown .select2-search__field {
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

    .estimate-panel {
        margin-top: 18px;
        background: var(--ot-panel-tint);
        border: 1px solid rgba(14, 143, 122, .25);
        border-radius: 14px;
        padding: 20px 22px;
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
    <symbol id="pm-calc" viewBox="0 0 24 24" fill="none" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2"></rect><path d="M8 6h8M8 11h1M12 11h1M16 11h1M8 15h1M12 15h1M16 15h1M8 19h1M12 19h1"></path></symbol>
    <symbol id="pm-plus" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"></path></symbol>
    <symbol id="pm-trash" viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16"></path><path d="M9 7V4h6v3"></path><path d="M6 7l1 13h10l1-13"></path></symbol>
    <symbol id="pm-box" viewBox="0 0 32 32" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 10l12-6 12 6-12 6z"></path><path d="M4 10v12l12 6 12-6V10"></path><path d="M16 16v12"></path></symbol>
    <symbol id="pm-sparkle" viewBox="0 0 24 24" fill="none" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8z"></path></symbol>
</svg>

<section class="booking-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10">
                <div class="booking-panel">
                    <div class="booking-head">
                        <div>
                            <span class="booking-eyebrow">Packers &amp; Movers</span>
                            <h2>Book Your Move</h2>
                        </div>
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

                    <form action="{{ route('packers_movers.save_items') }}" method="POST" id="packersMoverItemForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Packers &amp; Movers Branch</label>
                                <select name="packers_mover_id" class="form-control pm-select" data-placeholder="Select a branch" required>
                                    <option value="">select branch</option>
                                    @foreach($packersMovers as $packersMover)
                                        <option value="{{ $packersMover->id }}" {{ (string) old('packers_mover_id') === (string) $packersMover->id ? 'selected' : '' }}>
                                            {{ $packersMover->name }} — {{ $packersMover->city }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Pickup Date</label>
                                <input type="date" name="pickup_date" class="form-control" value="{{ old('pickup_date') }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Moving Distance (KM)</label>
                                <input type="number" step="0.1" name="distance_km" class="form-control" value="{{ old('distance_km') }}" min="0.1" required>
                            </div>
                            <div class="col-12">
                                <div class="text-danger small mb-3 d-none" id="packersMoverError">
                                    Please select a valid active packers &amp; movers branch.
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pickup Address</label>
                                <textarea name="pickup_address" class="form-control" rows="3" placeholder="Address to pick items up from">{{ old('pickup_address') }}</textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Drop Address</label>
                                <textarea name="drop_address" class="form-control" rows="3" placeholder="Address to move items to">{{ old('drop_address') }}</textarea>
                            </div>
                        </div>

                        <div class="booking-section-title">Item Details</div>
                        <div id="packersMoverItems">
                            <div class="pm-item-row item-card">
                                <div class="item-card-head">
                                    <div class="item-card-title">
                                        <span class="item-card-icon"><svg viewBox="0 0 32 32"><use href="#pm-box"></use></svg></span>
                                        <h5>Item 1</h5>
                                    </div>
                                    <button type="button" class="btn-remove-item remove-item" style="display:none;">
                                        <svg viewBox="0 0 24 24"><use href="#pm-trash"></use></svg> Delete
                                    </button>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Item Name</label>
                                        <input type="text" name="items[0][item_name]" class="form-control" placeholder="Sofa, wardrobe, boxes..." required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Item Type</label>
                                        <input type="text" name="items[0][item_type]" class="form-control" placeholder="Furniture, appliance...">
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
                                <svg class="btn-icon" viewBox="0 0 24 24"><use href="#pm-calc"></use></svg>
                                <span class="btn-spinner"></span>
                                <span id="calculateEstimateLabel">Calculate Estimate</span>
                            </button>
                        </div>

                         <div class="d-flex justify-content-between flex-wrap gap-3 mt-4">
                            <button type="button" class="btn-neutral-outline" id="addMoreItem">
                                <svg viewBox="0 0 24 24"><use href="#pm-plus"></use></svg> Add More
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
                            <div class="estimate-panel-header"><svg viewBox="0 0 24 24"><use href="#pm-sparkle"></use></svg> Estimated Price</div>
                            <div id="estimateItemsList"></div>
                            <div class="estimate-summary">
                                <div class="estimate-summary-line">
                                    <span>Rate</span>
                                    <span id="estimateRate">-</span>
                                </div>
                                <div class="estimate-summary-line">
                                    <span>Min Charge (per request)</span>
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
        const itemsWrapper = document.getElementById('packersMoverItems');
        const addButton = document.getElementById('addMoreItem');
        const pmForm = document.getElementById('packersMoverItemForm');
        const pmSelect = pmForm.querySelector('[name="packers_mover_id"]');
        const distanceInput = pmForm.querySelector('[name="distance_km"]');
        const pmError = document.getElementById('packersMoverError');
        const activePackersMovers = @json($packersMovers->pluck('id')->values());

        if (window.jQuery && jQuery.fn.select2) {
            jQuery('.pm-select').select2({
                width: '100%',
                allowClear: true,
                dropdownCssClass: 'pm-dropdown',
                placeholder: function () {
                    return jQuery(this).data('placeholder');
                }
            });
        }

        function refreshRows() {
            const rows = itemsWrapper.querySelectorAll('.pm-item-row');
            rows.forEach((row, index) => {
                row.querySelector('h5').textContent = 'Item ' + (index + 1);
                row.querySelector('.remove-item').style.display = rows.length > 1 ? 'inline-flex' : 'none';
            });
        }

        addButton.addEventListener('click', function () {
            const firstRow = itemsWrapper.querySelector('.pm-item-row');
            const newRow = firstRow.cloneNode(true);

            newRow.querySelectorAll('input').forEach(function (input) {
                input.name = input.name.replace(/items\[\d+\]/, 'items[' + itemIndex + ']');
                if (input.name.includes('[quantity]')) {
                    input.value = 1;
                } else {
                    input.value = '';
                }
            });

            itemsWrapper.appendChild(newRow);
            itemIndex++;
            refreshRows();
            newRow.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            autoRecalcIfOpen();
        });

        itemsWrapper.addEventListener('click', function (event) {
            if (event.target.closest('.remove-item')) {
                event.target.closest('.pm-item-row').remove();
                refreshRows();
                autoRecalcIfOpen();
            }
        });

        function hasActivePackersMover() {
            return activePackersMovers.some(function (id) {
                return String(id) === String(pmSelect.value);
            });
        }

        pmSelect.addEventListener('change', function () {
            pmError.classList.add('d-none');
            autoRecalcIfOpen();
        });

        pmForm.addEventListener('submit', function (event) {
            if (!hasActivePackersMover()) {
                event.preventDefault();
                pmError.classList.remove('d-none');

                if (window.jQuery && jQuery.fn.select2) {
                    jQuery(pmSelect).select2('open');
                } else {
                    pmSelect.focus();
                }
            }
        });

        const calculateEstimateBtn = document.getElementById('calculateEstimateBtn');
        const calculateEstimateLabel = document.getElementById('calculateEstimateLabel');
        const estimateError = document.getElementById('estimateError');
        const estimateSkeleton = document.getElementById('estimateSkeleton');
        const estimateResult = document.getElementById('estimateResult');
        const estimateItemsList = document.getElementById('estimateItemsList');
        const estimateRate = document.getElementById('estimateRate');
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

            if (!hasActivePackersMover()) {
                estimateError.textContent = 'Please select a valid active packers & movers branch first.';
                estimateError.classList.remove('d-none');
                estimateResult.classList.add('d-none');
                return;
            }

            calculateEstimateBtn.disabled = true;
            calculateEstimateBtn.classList.add('is-loading');
            calculateEstimateLabel.textContent = 'Calculating...';

            const items = [];
            itemsWrapper.querySelectorAll('.pm-item-row').forEach(function (row) {
                items.push({
                    quantity: parseInt(row.querySelector('input[name$="[quantity]"]').value, 10) || 1,
                    length_cm: parseFloat(row.querySelector('input[name$="[length_cm]"]').value) || 0,
                    width_cm: parseFloat(row.querySelector('input[name$="[width_cm]"]').value) || 0,
                    height_cm: parseFloat(row.querySelector('input[name$="[height_cm]"]').value) || 0,
                    weight_kg: parseFloat(row.querySelector('input[name$="[weight_kg]"]').value) || 0,
                });
            });

            const payload = {
                packers_mover_id: pmSelect.value,
                distance_km: parseFloat(distanceInput.value) || 0.1,
                items: items,
            };

            estimateSkeleton.classList.remove('d-none');

            try {
                const response = await fetch("{{ route('packers_movers.estimate_items') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': pmForm.querySelector('input[name="_token"]').value,
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
                    const figure = Number(item.charge_weight_kg).toFixed(2) + ' kg × ' + Number(item.distance_km).toFixed(1) + ' km';

                    const row = document.createElement('div');
                    row.className = 'estimate-item-row';
                    row.innerHTML =
                        '<span class="estimate-item-index">Item ' + (index + 1) + '</span>' +
                        '<span class="' + chipClass + '">' + chipLabel + '</span>' +
                        '<span class="estimate-item-figure">' + figure + '</span>' +
                        '<span class="estimate-item-charge">₹' + Number(item.item_charge).toFixed(2) + '</span>';
                    estimateItemsList.appendChild(row);
                });

                const rate = data.packers_mover && data.packers_mover.price_per_km_per_kg;
                estimateRate.textContent = rate ? '₹' + Number(rate).toFixed(2) + ' / kg / km' : '-';
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

        distanceInput.addEventListener('input', debouncedAutoRecalc);

        calculateEstimateBtn.addEventListener('click', function (event) {
            event.preventDefault();
            runEstimate();
        });
    });
</script>

@include('web.footer')
