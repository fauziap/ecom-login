@extends('v_layouts.app')

@section('content')
<div class="col-md-12">
    <div class="order-summary clearfix">

        <div class="section-title">
            <p>KERANJANG</p>
            <h3 class="title">Keranjang Belanja</h3>
        </div>

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

        @if($order && $order->orderItems->count() > 0)

            @php
                $totalBerat = 0;
            @endphp

            <table class="shopping-cart-table table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Detail</th>
                        <th>Harga</th>
                        <th>Qty</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($order->orderItems as $item)
                        @php
                            $totalBerat += ($item->produk->berat * $item->quantity);
                        @endphp

                        <tr>
                            <td>
                                <img src="{{ asset('storage/img-produk/thumb_sm_' . $item->produk->foto) }}" width="80">
                            </td>

                            <td>
                                {{ $item->produk->nama_produk }} <br>
                                Berat: {{ $item->produk->berat }} gram <br>
                                Stok: {{ $item->produk->stok }}
                            </td>

                            <td>
                                Rp {{ number_format($item->harga, 0, ',', '.') }}
                            </td>

                            <td>
                                <form action="{{ route('order.updateCart', $item->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <input type="number"
                                           name="quantity"
                                           value="{{ $item->quantity }}"
                                           min="1"
                                           style="width:70px;">

                                    <button type="submit" class="btn btn-warning btn-sm">
                                        Update
                                    </button>
                                </form>
                            </td>

                            <td>
                                Rp {{ number_format($item->harga * $item->quantity, 0, ',', '.') }}
                            </td>

                            <td>
                                <form action="{{ route('order.removeFromCart', $item->produk->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-danger btn-sm">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <hr>

            <div class="section-title">
                <h3 class="title">Pilih Pengiriman</h3>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <select id="province" class="form-control mb-3">
                        <option value="">Pilih Provinsi</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <select id="city" class="form-control mb-3">
                        <option value="">Pilih Kota</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <select id="courier" class="form-control mb-3">
                        <option value="jne">JNE</option>
                        <option value="tiki">TIKI</option>
                        <option value="pos">POS Indonesia</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <input type="number"
                           id="weight"
                           class="form-control"
                           value="{{ $totalBerat }}"
                           readonly>
                </div>

                <div class="col-md-12 mt-3">
                    <button id="cekOngkir" class="primary-btn">
                        Cek Ongkir
                    </button>
                </div>
            </div>

            <hr>

            <div id="shippingResult"></div>

            <hr>

            @php
                $shipping = session('shipping');
                $ongkir = $shipping['cost'] ?? 0;
                $totalBayar = $order->total_harga + $ongkir;
            @endphp

            <div class="pull-right">
                <table class="table">
                    <tr>
                        <th>Subtotal</th>
                        <td>Rp {{ number_format($order->total_harga, 0, ',', '.') }}</td>
                    </tr>

                    <tr>
                        <th>Ongkos Kirim</th>
                        <td>
                            Rp {{ number_format($ongkir, 0, ',', '.') }}

                            @if($shipping)
                                <br>
                                {{ strtoupper($shipping['service']) }}
                                ({{ $shipping['etd'] }} hari)
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <th>Total Bayar</th>
                        <td>
                            <strong style="color:red;">
                                Rp {{ number_format($totalBayar, 0, ',', '.') }}
                            </strong>
                        </td>
                    </tr>
                </table>

                <form action="{{ route('order.checkout') }}" method="POST">
                    @csrf
                    <button type="submit" class="primary-btn">
                        Bayar Sekarang
                    </button>
                </form>
            </div>

        @else
            <p>Keranjang kosong.</p>
        @endif
    </div>
</div>

<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
document.addEventListener('DOMContentLoaded', function() {

    fetch('/provinces')
        .then(res => res.json())
        .then(data => {
            let province = document.getElementById('province');

            if (data.data) {
                data.data.forEach(item => {
                    province.innerHTML += `
                        <option value="${item.id}">
                            ${item.name}
                        </option>
                    `;
                });
            }
        });

    document.getElementById('province').addEventListener('change', function() {
        let provinceId = this.value;

        fetch(`/cities?province_id=${provinceId}`)
            .then(res => res.json())
            .then(data => {
                let city = document.getElementById('city');
                city.innerHTML = '<option>Pilih Kota</option>';

                if (data.data) {
                    data.data.forEach(item => {
                        city.innerHTML += `
                            <option value="${item.id}">
                                ${item.name}
                            </option>
                        `;
                    });
                }
            });
    });

    document.getElementById('cekOngkir').addEventListener('click', function() {
        let city = document.getElementById('city').value;
        let courier = document.getElementById('courier').value;
        let weight = document.getElementById('weight').value;

        fetch('/cost', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                destination: city,
                courier: courier,
                weight: weight
            })
        })
        .then(res => res.json())
        .then(data => {
            let html = '';

            if (data.data && data.data.length > 0) {
                data.data.forEach(service => {
                    html += `
                        <div class="card p-3 mb-2">
                            <strong>${service.service}</strong><br>
                            Rp ${service.cost.toLocaleString()}<br>
                            Estimasi ${service.etd} hari<br><br>

                            <button onclick="pilihShipping(
                                '${service.service}',
                                '${service.cost}',
                                '${service.etd}'
                            )" class="primary-btn">
                                Pilih Pengiriman
                            </button>
                        </div>
                    `;
                });
            }

            document.getElementById('shippingResult').innerHTML = html;
        });
    });

});

function pilihShipping(service, cost, etd)
{
    fetch('/save-shipping', {
        method: 'POST',
        headers: {
            'Content-Type':'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            service: service,
            cost: cost,
            etd: etd
        })
    })
    .then(res => res.json())
    .then(() => {
        location.reload();
    });
}
</script>
@endsection
