<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Incident Reports Export</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; margin-bottom: 5px; }
        p { text-align: center; margin-top: 0; }
    </style>
</head>
<body>
    <h2>Incident Reports Summary</h2>
    <p>Generated on {{ now()->format('F d, Y h:i A') }}</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Incident ID</th>
                <th>Student</th>
                <th>Category</th>
                <th>Incident Date</th>
                <th>Location</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $index => $report)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $report->incident_id }}</td>
                    <td>{{ $report->student->fullname ?? 'N/A' }}</td>
                    <td>VC00{{ $report->category->sanction_id ?? $report->category_id }} -
                                {{ $report->category->offense ?? 'Unknown Category' }}</td>
                    <td>{{ \Carbon\Carbon::parse($report->incident_date)->format('M d, Y') }}</td>
                    <td>{{ $report->location }}</td>
                    <td>{{ ucfirst($report->status) }}</td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center;">No data found for the selected filters.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>