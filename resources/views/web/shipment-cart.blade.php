@include('web.header')

<section class="shipment-cart-section" style="padding: 110px 0 80px; background:#f7f7f7;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div>
                <span style="color:#ff7a00; font-weight:600;">Shipment</span>
                <h2 class="mb-0">My Cart</h2>
            </div>
            <a href="{{ route('shipment.add_item') }}" class="primary-btn1 btn-hover">
                Add Item
                <span></span>
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="table-responsive" style="background:#fff; border:1px solid #e8e8e8; border-radius:8px; padding:20px;">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Height</th>
                        <th>Weight</th>
                        <th>Route</th>
                        <th>Price</th>
                        <th>Pickup</th>
                        <th>Delivery</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cartItems as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $item->item_name }}</strong>
                                @if($item->item_type)
                                    <br><small>{{ $item->item_type }}</small>
                                @endif
                            </td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->height_cm, 2) }} CM</td>
                            <td>{{ number_format($item->weight_kg, 2) }} KG</td>
                            <td>{{ optional($item->cityRoute)->from_city ?? '-' }} to {{ optional($item->cityRoute)->to_city ?? '-' }}</td>
                            <td>
                                @if($item->price_error)
                                    <span class="text-danger">{{ $item->price_error }}</span>
                                @else
                                    <strong>{{ number_format($item->calculated_price, 2) }}</strong>
                                @endif
                            </td>
                            <td>{{ $item->pickup_date->format('d M Y') }}</td>
                            <td>{{ $item->delivery_date->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('shipment.cart.delete', $item->id) }}" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove this item?')">Delete</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">Your cart is empty.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-end align-items-center flex-wrap gap-3 mt-4">
                <div style="font-size:18px; font-weight:700;">Total: {{ number_format($cartTotal, 2) }}</div>
                <form action="{{ route('shipment.cart.checkout') }}" method="POST">
                    @csrf
                    <button type="submit" class="primary-btn1 btn-hover" {{ $cartItems->isEmpty() || !$price ? 'disabled' : '' }} onclick="return confirm('Save cart items to transport leads?')">
                        Save To Leads
                        <span></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

@include('web.footer')
