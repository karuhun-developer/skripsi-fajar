<x-acc-with-alert>
    <h1 class="h3 mb-3">
        {{ $title ?? '' }}
    </h1>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">{{ $title ?? '' }} Data</h5>
        </div>
        <div class="card-body">
            <x-acc-header :$originRoute>

            </x-acc-header>
            <div class="table-responsive">
                <x-acc-table>
                    <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Name</th>
                        <th>Bill Amount</th>
                        <th>Status</th>
                        <th>Payment Link</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($get as $d)
                        <tr>
                            <td>{{ $d->order_id }}</td>
                            <td>{{ $d->subscription->user->name }}</td>
                            <td>{{ $d->bill_amount }}</td>
                            <td>{{ $d->status }}</td>
                            <td>{{ $d->payment_link }}</td>
                            <td>{{ $d->created_at }}
                        </tr>
                    @empty
                        <tr>
                            <td colspan="100" class="text-center">
                                No Data Found
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </x-acc-table>
            </div>

        </div>
    </div>
</x-acc-with-alert>
