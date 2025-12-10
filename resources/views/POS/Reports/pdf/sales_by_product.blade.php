<!DOCTYPE html>
<html>
<head>
    <title>Sales by Product</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: center; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <h2>Sales by Product</h2>
    <p>From: {{ $from }} To: {{ $to }}</p>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Total Quantity Sold</th>
                <th>Total Sales</th>
           
            </tr>
        </thead>
        <tbody>
            @foreach($salesByProduct as $row)
            <tr>
                <td>{{ $row->product_name }}</td>
                <td>{{ $row->total_quantity }}</td>
                <td>{{ number_format($row->total_sales,2) }}</td>
            
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
