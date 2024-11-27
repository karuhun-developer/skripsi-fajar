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
                            <th>Router</th>
                            <th>Star</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($get as $d)
                            <tr>
                                <td>{{ $d->name }}</td>
                                <td>{{ $d->local_ip }}</td>
                                <td>{{ $d->range_ip }}</td>
                                <td>{{ $d->router->name }}</td>
                                <td>{{ $d->star }}</td>
                                <x-acc-update-delete :id="$d->id" :$originRoute />
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


    {{-- Create / Update Modal --}}
    <x-acc-modal title="{{ $isUpdate ? 'Update' : 'Create' }} {{ $title }}" :isModaOpen="$modals['defaultModal']">
        <x-acc-form submit="customSave">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <x-acc-input type="text" model="form.name" placeholder="Name" />
                </div>
            </div>
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">Local IP</label>
                    <x-acc-input type="text" model="form.localIp" placeholder="192.168.10.1" />
                </div>
            </div>
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">Range IP</label>
                    <x-acc-input type="text" model="form.rangeIp" placeholder="192.168.10.2-192.168.10.254" />
                </div>
            </div>

            @if (!$isUpdate)
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">Router</label>
                        <select class="form-control" wire:model="form.routerId">
                            <option value="">Select Router</option>
                            @foreach ($routers as $router)
                                <option value="{{ $router->id }}">{{ $router->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif


        </x-acc-form>
    </x-acc-modal>

</x-acc-with-alert>
