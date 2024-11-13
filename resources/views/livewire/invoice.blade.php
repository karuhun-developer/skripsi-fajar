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
                        <th>Order Id</th>
                        <th>Bill Amount</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Status</th>
                        <th>Action</th>

                    </tr>
                    </thead>
                    <tbody>
                    @forelse($get as $d)
                        <tr>
                            <td>{{ $d->order_id }}</td>
                            <td>{{ $d->bill_amount }}</td>
                            <td>{{ $d->created_at }}</td>
                            <td>{{ $d->updated_at }}</td>
                            <td>{{ $d->status }}</td>
                            <th>
                                <a href="{{ $d->payment_link }}" class="btn btn-primary" target="_blank" wire:loading.attr="disabled">
                                    <i class="fa fa-check"></i>
                                    Pay
                                </a>
                            </th>

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

        </x-acc-form>
    </x-acc-modal>

</x-acc-with-alert>
