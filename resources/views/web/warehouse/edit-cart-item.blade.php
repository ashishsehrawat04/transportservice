@include('web.header')

<style>
    .shipment-edit-section {
        background: #f5f7fb;
        padding: 110px 0 80px;
    }

    .edit-panel {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: 0 14px 35px rgba(15, 23, 42, .06);
        padding: 28px;
    }

    .edit-head {
        align-items: center;
        display: flex;
        gap: 18px;
        justify-content: space-between;
        margin-bottom: 24px;
    }

    .edit-head span {
        color: #ff7a45;
        font-size: 13px;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .edit-head h2 {
        color: #101820;
        font-size: 30px;
        font-weight: 800;
        margin: 4px 0 0;
    }

    .edit-card {
        border: 1px solid #eef2f7;
        border-radius: 8px;
        padding: 18px;
    }

    .edit-card-title {
        color: #101820;
        font-size: 17px;
        font-weight: 800;
        margin-bottom: 16px;
    }

    .form-label {
        color: #374151;
        font-size: 13px;
        font-weight: 800;
    }

    .form-control {
        border-color: #d1d5db;
        border-radius: 6px;
        min-height: 46px;
    }

    textarea.form-control {
        min-height: 92px;
    }

    .edit-actions {
        align-items: center;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 22px;
    }

    .edit-back-btn {
        align-items: center;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        color: #101820;
        display: inline-flex;
        font-weight: 800;
        gap: 8px;
        min-height: 46px;
        padding: 0 16px;
        text-decoration: none;
    }

    .edit-back-btn:hover {
        background: #f9fafb;
        color: #101820;
    }

    @media (max-width: 575px) {
        .edit-head,
        .edit-actions {
            align-items: stretch;
            flex-direction: column;
        }

        .edit-head h2 {
            font-size: 26px;
        }
    }
</style>

@php
    $warehouseAddress = $cartItem->warehouseAddress;
@endphp

<section class="shipment-edit-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10">
                <div class="edit-panel">
                    <div class="edit-head">
                        <div>
                            <span>Warehouse Cart</span>
                            <h2>Edit Cart Item</h2>
                        </div>
                        <a href="{{ route('warehouse.cart') }}" class="edit-back-btn">
                            <i class="bi bi-arrow-left"></i>
                            Back To Cart
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

                    <form action="{{ route('warehouse.cart.update', $cartItem->id) }}" method="POST" id="warehouseEditForm">
                        @csrf

                        <div class="edit-card mb-3">
                            <div class="edit-card-title">Storage Details</div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Warehouse</label>
                                    <select name="warehouse_id" class="form-control" required>
                                        <option value="">Select warehouse</option>
                                        @foreach($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}" {{ (string) old('warehouse_id', $cartItem->warehouse_id) === (string) $warehouse->id ? 'selected' : '' }}>
                                                {{ $warehouse->name }} — {{ $warehouse->city }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Pickup Date</label>
                                    <input type="date" name="pickup_date" class="form-control" value="{{ old('pickup_date', optional($cartItem->pickup_date)->format('Y-m-d')) }}" required>
                                </div>
                                <div class="col-12">
                                    <div class="text-danger small mb-3 d-none" id="warehouseError">
                                        Please select a valid active warehouse.
                                    </div>
                                </div>
                                <div class="col-md-12 mb-0">
                                    <label class="form-label">Pickup Address</label>
                                    <textarea name="pickup_address" class="form-control" placeholder="Address where the item should be picked up from">{{ old('pickup_address', optional($warehouseAddress)->pickup_address) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="edit-card mt-3">
                            <div class="edit-card-title">Item Details</div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Item Name</label>
                                    <input type="text" name="item_name" class="form-control" value="{{ old('item_name', $cartItem->item_name) }}" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Item Type</label>
                                    <input type="text" name="item_type" class="form-control" value="{{ old('item_type', $cartItem->item_type) }}" placeholder="Furniture, electronics...">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Quantity</label>
                                    <input type="number" name="quantity" class="form-control" value="{{ old('quantity', $cartItem->quantity) }}" min="1" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Length (CM)</label>
                                    <input type="number" step="0.01" name="length_cm" class="form-control" value="{{ old('length_cm', $cartItem->length_cm) }}" min="0">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Width (CM)</label>
                                    <input type="number" step="0.01" name="width_cm" class="form-control" value="{{ old('width_cm', $cartItem->width_cm) }}" min="0">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Height (CM)</label>
                                    <input type="number" step="0.01" name="height_cm" class="form-control" value="{{ old('height_cm', $cartItem->height_cm) }}" min="0.01" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Weight (KG)</label>
                                    <input type="number" step="0.01" name="weight_kg" class="form-control" value="{{ old('weight_kg', $cartItem->weight_kg) }}" min="0.01" required>
                                </div>
                                <div class="col-md-3 mb-0">
                                    <label class="form-label">Storage Days</label>
                                    <input type="number" name="storage_days" class="form-control" value="{{ old('storage_days', $cartItem->storage_days) }}" min="1" required>
                                </div>
                            </div>
                        </div>

                        <div class="edit-actions">
                            <a href="{{ route('warehouse.cart') }}" class="edit-back-btn">Cancel</a>
                            <button type="submit" class="primary-btn1 btn-hover">
                                Update Item
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
        const warehouseForm = document.getElementById('warehouseEditForm');
        const warehouseSelect = warehouseForm.querySelector('[name="warehouse_id"]');
        const warehouseError = document.getElementById('warehouseError');
        const activeWarehouses = @json($warehouses->pluck('id')->values());

        function hasActiveWarehouse() {
            return activeWarehouses.some(function (id) {
                return String(id) === String(warehouseSelect.value);
            });
        }

        warehouseSelect.addEventListener('change', function () {
            warehouseError.classList.add('d-none');
        });

        warehouseForm.addEventListener('submit', function (event) {
            if (!hasActiveWarehouse()) {
                event.preventDefault();
                warehouseError.classList.remove('d-none');
                warehouseSelect.focus();
            }
        });
    });
</script>

@include('web.footer')
