<style>
    body {
        font-family: sans-serif;
        font-size: 11px;
    }

    h2 {
        text-align: center;
        margin-bottom: 5px;
    }

    .header {
        margin-bottom: 15px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 8px;
    }

    th,
    td {
        border: 1px solid #000;
        padding: 5px;
    }

    th {
        background: #f5f5f5;
    }

    .text-right {
        text-align: right;
    }

    .category-title {
        background: #eaeaea;
        font-weight: bold;
        padding: 6px;
    }

    .summary-table td {
        border: none;
        padding: 4px;
    }

    .footer {
        margin-top: 40px;
    }
</style>

<h2>INVOICE</h2>

<div class="header">
    <strong>No Bill:</strong> {{ $bill->bill_no }} <br>
    <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($bill->bill_date)->format('d-m-Y H:i') }} <br>
    <strong>MR Code:</strong> {{ $bill->mr_code }} <br>
    <strong>Visit No:</strong> {{ $bill->visit_no }}
</div>

@foreach($groupedItems as $type => $items)

<div class="category-title">
    {{ strtoupper($type) }}
</div>

<table>
    <thead>
        <tr>
            <th>Deskripsi</th>
            <th width="60">Qty</th>
            <th width="100">Harga</th>
            <th width="120">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $item)
        <tr>
            <td>{{ $item->description }}</td>
            <td class="text-right">{{ $item->qty }}</td>
            <td class="text-right">{{ number_format($item->price, 0) }}</td>
            <td class="text-right">{{ number_format($item->subtotal, 0) }}</td>
        </tr>
        @endforeach

        <tr>
            <td colspan="3" class="text-right">
                <strong>Subtotal {{ strtoupper($type) }}</strong>
            </td>
            <td class="text-right">
                <strong>{{ number_format($subtotalPerCategory[$type], 0) }}</strong>
            </td>
        </tr>
    </tbody>
</table>

@endforeach

<hr>

<table class="summary-table" width="40%" align="right">
    <tr>
        <td>Subtotal</td>
        <td class="text-right">{{ number_format($grandSubtotal, 0) }}</td>
    </tr>
    <tr>
        <td>Diskon</td>
        <td class="text-right">{{ number_format($discount, 0) }}</td>
    </tr>
    <tr>
        <td>Pajak</td>
        <td class="text-right">{{ number_format($tax, 0) }}</td>
    </tr>
    <tr>
        <td><strong>Grand Total</strong></td>
        <td class="text-right">
            <strong>{{ number_format($grandTotal, 0) }}</strong>
        </td>
    </tr>
</table>

<div class="footer">
    <br><br><br>
    <div style="text-align:right;">
        ___________________________<br>
        Kasir
    </div>
</div>