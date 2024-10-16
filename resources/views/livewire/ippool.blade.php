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
                        <th>
                            Action
                        </th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </x-acc-table>
            </div>

        </div>
    </div>

</x-acc-with-alert>
