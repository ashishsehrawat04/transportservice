@include('web.header')

<section class="shipment-form-section" style="padding: 110px 0 80px; background:#f7f7f7;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10">
                <div style="background:#fff; border:1px solid #e8e8e8; padding:30px; border-radius:8px;">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                        <div>
                            <span style="color:#ff7a00; font-weight:600;">Shipment</span>
                            <h2 class="mb-0">Add Items</h2>
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
                            <div class="col-md-12 mb-3">
                                <label class="form-label">City Route</label>
                                <select name="city_route_id" class="form-control" required>
                                    <option value="">Select city route</option>
                                    @foreach($cityRoutes as $route)
                                        <option value="{{ $route->id }}" {{ old('city_route_id') == $route->id ? 'selected' : '' }}>
                                            {{ $route->from_city }} to {{ $route->to_city }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pickup Date</label>
                                <input type="date" name="pickup_date" class="form-control" value="{{ old('pickup_date') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Delivery Date</label>
                                <input type="date" name="delivery_date" class="form-control" value="{{ old('delivery_date') }}" required>
                            </div>
                        </div>

                        <div id="shipmentItems">
                            <div class="shipment-item-row" style="border:1px solid #e7e7e7; border-radius:8px; padding:20px; margin-bottom:16px;">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Item 1</h5>
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-item" style="display:none;">Delete</button>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Item Name</label>
                                        <input type="text" name="items[0][item_name]" class="form-control" placeholder="Box, chair, fridge..." required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Item Type</label>
                                        <input type="text" name="items[0][item_type]" class="form-control" placeholder="Furniture, electronics...">
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
                            <button type="button" class="btn btn-secondary" id="addMoreItem">Add More Item</button>
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
    });
</script>

@include('web.footer')
