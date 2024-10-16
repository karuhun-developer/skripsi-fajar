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
                    <label class="form-label">Ip Address</label>
                    <x-acc-input type="text" model="form.ipaddress" placeholder="Ip Address" />
                </div>
            </div>
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <x-acc-input type="text" model="form.username" placeholder="Username" />
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <x-acc-input type="text" model="form.password" placeholder="Password" />
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <x-acc-input type="textarea" model="form.description" placeholder="Description" />
                </div>
            </div>
        </x-acc-form>
    </x-acc-modal>

</x-acc-with-alert>
