<x-acc-with-alert>
    <h1 class="h3 mb-3">
        {{ $title ?? '' }}
    </h1>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">{{ $title ?? '' }} Data</h5>
        </div>
        <div class="card-body">
            <x-acc-header :$originRoute/>
            <div class="table-responsive">
                <x-acc-table>
                    <thead>
                    <tr>
                        <x-acc-loop-th :$searchBy :$orderBy :$order/>
                        <th>Plan</th>
                        <th>Start At</th>
                        <th>Expired At</th>
                        <th>Status</th>
                        <th>Validity</th>
                        <th>Type</th>
                        <th>Action</th>

                    </tr>
                    </thead>
                    <tbody>
                    @forelse($get as $d)
                        <tr>
                            <td>{{ $d->name }}</td>
                            <td>{{ $d->email }}</td>
                            <td>{{ $d->roles[0]?->name }}</td>
                            <td>{{ $d->plan->name ?? 'N/A' }}</td>
                            <td>{{ $d->plan->subscriptions[0]->start_at ?? 'N/A' }}</td>
                            <td>{{ $d->plan->subscriptions[0]->expired_at ?? 'N/A' }}</td>
                            <td>{{ $d->plan->subscriptions[0]->status ?? 'N/A' }}</td>
                            <td>{{ $d->plan->validity ?? 'N/A'}} {{ $d->plan->validity_unit ?? '' }}</td>
                            <td>{{ $d->plan->type ?? 'N/A'}}</td>


                            <x-acc-update-delete :id="$d->id" :$originRoute>
                                <button class="dropdown-item"
                                        wire:click="editPassword('{{ $d->id }}')">
                                    <i class="fa fa-key"></i> Change Password
                                </button>
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
            <div class="float-end">
                {{ $get->links() }}
            </div>
        </div>
    </div>

    {{-- Create / Update Modal --}}
    <x-acc-modal title="{{ $isUpdate ? 'Update' : 'Create' }} {{ $title }}" :isModaOpen="$modals['defaultModal']">
        <x-acc-form submit="customSave">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <x-acc-input type="select" :live="true" model="form.role">
                        <option value="">--Select Role--</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                        @endforeach
                    </x-acc-input>
                </div>
            </div>
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <x-acc-input type="text" model="form.name" placeholder="Name"/>
                </div>
            </div>
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <x-acc-input type="email" model="form.email" placeholder="Email"/>
                </div>
            </div>

            @if ($form->role == 'customer')
                <div class="col-md-12">
                    <hr>
                    <div class="mb-3">
{{--                        <label class="form-label">Plan</label>--}}
{{--                        <x-acc-input type="select" :live="true" model="form.planId">--}}
{{--                            <option value="">--Select Plan--</option>--}}
{{--                            @foreach($plans as $plan)--}}
{{--                                <option value="{{ $plan->id }}">{{ $plan->name }}</option>--}}
{{--                            @endforeach--}}
{{--                        </x-acc-input>--}}
                        <label class="form-label">Plan</label>
                        <select class="form-control" wire:model="form.planId">
                            <option value="">Select Plan</option>
                            @foreach($plans as $plan)
                                <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">PPPOE Username</label>
                        <x-acc-input type="text" model="form.pppoeUsername" placeholder="PPPOE Username"/>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">PPPOE Password</label>
                        <x-acc-input type="password" model="form.pppoePassword" placeholder="PPPOE Password"/>
                    </div>
                </div>

            @endif

            @if(!$isUpdate)
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <x-acc-input type="password" model="form.password" placeholder="Password"/>
                    </div>
                </div>
            @endif
        </x-acc-form>
    </x-acc-modal>

    {{-- Change password --}}
    <x-acc-modal title="Change Password {{ $form->name }}" :isModaOpen="$modals['updatePasswordModal']"
                 closeModalFunction="closeModalUpdatePassword">
        <x-acc-form submit="changePassword">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <x-acc-input type="password" model="form.password" placeholder="Password"/>
                </div>
            </div>
        </x-acc-form>
    </x-acc-modal>
</x-acc-with-alert>
