<!DOCTYPE html>
<html>
<head>
    <title>Invoice Details</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 3px; text-align: center; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <h2>Invoice Details</h2>
    <p>From: {{ $from }} To: {{ $to }}</p>
    <table>
        <thead>
            <tr>
                <th>Invoice No</th>
                <th>Status</th>
                <th>Total Amount</th>
                <th>Amount Received</th>
                <th>Change</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Subtotal</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoiceDetails as $row)
            <tr>
                <td>{{ $row->invoice_no }}</td>
                <td>{{ $row->status }}</td>
                <td>{{ $row->total_amount }}</td>
                <td>{{ $row->amount_received }}</td>
                <td>{{ $row->change_amount }}</td>
                <td>{{ $row->product_name }}</td>
                <td>{{ $row->quantity }}</td>
                <td>{{ $row->price }}</td>
                <td>{{ $row->subtotal }}</td>
                <td>{{ $row->created_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
