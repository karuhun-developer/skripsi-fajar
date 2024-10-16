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
                        <th>Name</th>
                        <th>Local IP</th>
                        <th>Range IP</th>
                        <th>Star</th>
                        <th>Router</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($get as $d)
                        <tr>
                            <td>{{ $d->name }}</td>
                            <td>{{ $d->local_ip }}</td>
                            <td>{{ $d->range_ip }}</td>
                            <td>{{ $d->star ?? '' }}</td>
                            <td>{{ $d->router->name }}</td>
                            <x-acc-update-delete :id="$d->id" :$originRoute>
                                @if($d->type != 'header')
                                    <a
                                        href="{{ route('cms.management.menu.child', ['menu' => $d->id]) }}"
                                        class="dropdown-item"
                                        wire:navigate
                                    >
                                        <i class="fa fa-list"></i> Child Menu
                                    </a>
                                @endif
                            </x-acc-update-delete>
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
