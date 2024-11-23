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
                        <th>Bandwidth</th>
                        <th>Price</th>
                        <th>IP Pool</th>
                        <th>Router</th>
                        <th>Validity</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($get as $d)
                        <tr>
                            <td>{{ $d->name }}</td>
                            <td>{{ $d->bandwidth->name }}</td>
                            <td>{{ $d->price }}</td>
                            <td>{{ $d->pool->name }}</td>
                            <td>{{ $d->pool->router->name }}</td>
                            <td>{{ $d->validity . ' ' . $d->validity_unit}}</td>
                            <x-acc-update-delete :id="$d->id" :$originRoute/>
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
        <x-acc-form submit="save">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <x-acc-input type="text" model="form.name" placeholder="Name" />
                </div>
            </div>
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">Bandwidth</label>
                    <select class="form-control" wire:model="form.bandwidthId">
                        <option value="">Select Bandwidth</option>
                        @foreach($bandwidths as $bandwidth)
                            <option value="{{ $bandwidth->id }}">{{ $bandwidth->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">Price</label>
                    <x-acc-input type="number" model="form.price" placeholder="Price" />
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">Validity</label>
                    <div class="input-group">
                        <x-acc-input type="number" model="form.validity" placeholder="Validity" min="1" />
                        <select class="form-control" wire:model="form.validityUnit">
                            <option value="day">day</option>
                            <option value="month">month</option>
                            <option value="year">year</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">Router</label>
                    <select wire:model.live="routerId" class="form-control" wire:change="getPools">
                        <option value="">Pilih Router</option>
                        @foreach($routers as $router)
                            <option value="{{ $router->id }}">{{ $router->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">Pool</label>
                    <select wire:model="form.poolId" class="form-control">
                        <option value="">Pilih Pool</option>
                        @foreach($pools as $pool)
                            <option value="{{ $pool->id }}">{{ $pool->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div> --}}

        </x-acc-form>
    </x-acc-modal>

</x-acc-with-alert>
