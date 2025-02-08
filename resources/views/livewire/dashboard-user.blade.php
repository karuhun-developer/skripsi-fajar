<div class="container mt-5">
    <h1 class="mb-4">Customer Dashboard</h1>

    <!-- Personal Information -->
    <div class="card mb-4">
        <div class="card-header">Personal Information</div>
        <div class="card-body">
            <p><strong>Name:</strong> John Doe</p>
            <p><strong>Email:</strong> john.doe@example.com</p>
            <p><strong>Phone:</strong> +1234567890</p>
            <p><strong>Address:</strong> 123 Main St, Anytown, USA</p>
        </div>
    </div>

    <!-- Subscription Status -->
    <div class="card mb-4">
        <div class="card-header">Subscription Status</div>
        <div class="card-body">
            <p><strong>Package:</strong> Premium Plan</p>
            <p><strong>Status:</strong> Active</p>
            <p><strong>Start Date:</strong> 2023-01-01</p>
            <p><strong>End Date:</strong> 2023-12-31</p>
            <p><strong>Invoice Date:</strong> 2023-01-01</p>
        </div>
    </div>

    <!-- Payment History -->
    <div class="card mb-4">
        <div class="card-header">Payment History</div>
        <div class="card-body">
            <table class="table">
                <thead>
                <tr>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>2023-01-01</td>
                    <td>$100</td>
                    <td>Credit Card</td>
                    <td>Success</td>
                </tr>
                <tr>
                    <td>2023-02-01</td>
                    <td>$100</td>
                    <td>Credit Card</td>
                    <td>Pending</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Total Data Usage</div>
        <div class="card-body">
            <p><strong>Total Data Used:</strong> {{ number_format(($response[0]['rx-byte'] + $response[0]['tx-byte']) / (1024 * 1024 * 1024), 2) ?? 'N/A' }} GB</p>
            @if(!empty($response))
                <p><strong>Interface Name:</strong> {{ $response[0]['name'] }}</p>
                <p><strong>Received Bytes:</strong> {{ $response[0]['rx-byte'] ?? 'N/A' }}</p>
                <p><strong>Transmitted Bytes:</strong> {{ $response[0]['tx-byte'] ?? 'N/A' }}</p>
            @else
                <p>No data available.</p>
            @endif
        </div>
    </div>

    <!-- Notifications and Messages -->
    <div class="card mb-4">
        <div class="card-header">Notifications and Messages</div>
        <div class="card-body">
            <p>No new notifications.</p>
        </div>
    </div>
</div>
