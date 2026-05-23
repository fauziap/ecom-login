@extends('v_layouts.app')

@section('content')
<div class="col-md-12">

    <div class="section-title">
        <p>HISTORY</p>
        <h3 class="title">History Pesanan</h3>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="shopping-cart-table table">
        <thead>
            <tr>
                <th>ID PESANAN</th>
                <th>TANGGAL</th>
                <th>TOTAL BAYAR</th>
                <th>STATUS</th>
                <th>DETAIL</th>
            </tr>
        </thead>

        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>

                    <td>
                        {{ $order->created_at->format('d M Y H:i') }}
                    </td>

                    <td>
                        Rp {{ number_format($order->total_harga, 0, ',', '.') }}
                    </td>

                    <td>
                        {{ ucfirst($order->status) }}
                    </td>

                    <td>
                        <a href="#" class="primary-btn btn-sm">
                            LIHAT DETAIL
                        </a>

                        <a href="#" class="primary-btn btn-sm">
                            INVOICE
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        Belum ada pesanan
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>
@endsection
