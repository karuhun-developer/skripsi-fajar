<div class="container mt-5">
    <h1 class="mb-4">Customer Dashboard</h1>

    <!-- Subscription Status -->
    <div class="card mb-4">
        <div class="card-header">Subscription Status</div>
        <div class="card-body">
            <p><strong>Package:</strong> {{ auth()->user()->plan->bandwidth->name }}</p>
            <p><strong>Status:</strong> {{ auth()->user()->status }}</p>
            <p><strong>Start Date:</strong> {{ auth()->user()->plan->subscriptions[0]->start_at }}</p>
            <p><strong>Expired Date:</strong> {{ auth()->user()->plan->subscriptions[0]->expired_at }}</p>
            <p><strong>Invoice Date:</strong> 2023-01-01</p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Total Data Usage</div>
        <div class="card-body">
            <p><strong>Total Data Used:</strong> {{ number_format(($response[0]['rx-byte'] ?? 0 + $response[0]['tx-byte'] ?? 0 ) / (1024 * 1024 * 1024), 2) ?? 'N/A' }} GB</p>
            @if(!empty($response))
                <p><strong>Interface Name:</strong> {{ $response[0]['name'] ?? 'N/A'}}</p>
                <p><strong>Received Bytes:</strong> {{ $response[0]['rx-byte'] ?? 'N/A' }}</p>
                <p><strong>Transmitted Bytes:</strong> {{ $response[0]['tx-byte'] ?? 'N/A' }}</p>
            @else
                <p>No data available.</p>
            @endif
        </div>
    </div>

</div>
