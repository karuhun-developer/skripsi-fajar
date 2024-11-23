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
                        <th>Rate Download</th>
                        <th>Rate Upload</th>
                        <th>Burst</th>
                        <th>Downgraded Bandwidth</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($get as $d)
                        <tr>
                            <td>{{ $d->name }}</td>
                            <td>{{ $d->rate_download . " " . $d->rate_download_unit}}</td>
                            <td>{{ $d->rate_upload . " " . $d->rate_upload_unit }}</td>
                            <td>{{ $d->burst }}</td>
                            <td>{{ $d->downgradedBandwidth->name  }}</td>

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
                    <label class="form-label">Rate Download</label>
                    <div class="input-group">
                        <x-acc-input type="number" model="form.rateDownload" placeholder="Rate Download" min="1" />
                        <select class="form-control" wire:model="form.rateDownloadUnit">
                            <option value="">--Select Unit--</option>
                            <option value="Kbps">Kbps</option>
                            <option value="Mbps">Mbps</option>
                            <option value="Gbps">Gbps</option>
                        </select>
                    </div>
                </div>
            </div>


            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">Rate Upload</label>
                    <div class="input-group">
                        <x-acc-input type="number" model="form.rateUpload" placeholder="Rate Download" min="1" />
                        <select class="form-control" wire:model="form.rateUploadUnit">
                            <option value="">--Select Unit--</option>
                            <option value="Kbps">Kbps</option>
                            <option value="Mbps">Mbps</option>
                            <option value="Gbps">Gbps</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">Burst</label>
                    <x-acc-input type="text" model="form.burst" placeholder="Burst" />
                </div>
            </div>

        </x-acc-form>
    </x-acc-modal>

</x-acc-with-alert>
