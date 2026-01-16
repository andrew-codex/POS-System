<!DOCTYPE html>
<html>
<head>
    <title>Sales by Date</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: center; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <h2>Sales by Date</h2>
    <p>From: {{ $from }} To: {{ $to }}</p>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Total Invoices</th>
                <th>Total Items Sold</th>
                <th>Total Sales</th>
            </tr>
        </thead>
        <tbody>
            @foreach($salesByDate as $row)
            <tr>
                <td>{{ $row->sale_date }}</td>
                <td>{{ $row->total_invoices }}</td>
                <td>{{ $row->total_items_sold }}</td>
                <td>{{ $row->total_sales }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
