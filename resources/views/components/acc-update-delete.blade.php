@props([
    'id' => 0,
    'modal' => 'acc-modal',
    'edit' => true,
    'delete' => true,
    'editFunction' => 'edit',
    'deleteFunction' => 'confirmDelete',
    'originRoute' => '',
])

<td>
    {{ $slotOutside ?? '' }}
    <div class="dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" id="update-delete-dropdown-{{ $id }}" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fa fa-ellipsis-v"></i>
        </button>
        <ul class="dropdown-menu" aria-labelledby="update-delete-dropdown-{{ $id }}">
            @if($edit)
                <li>
                    <button
                        class="dropdown-item"
                        wire:click="{{ $editFunction }}('{{ $id }}')"
                        @cannot('update.' . $originRoute) disabled @endcannot
                    >
                        <i class="fa fa-pencil"></i> Edit
                    </button>
                </li>
            @endif
            @if($delete)
                <li>
                    <button
                        class="dropdown-item"
                        wire:click="{{ $deleteFunction }}('{{ $id }}')"
                        @cannot('delete.' . $originRoute) disabled @endcannot
                    >
                        <i class="fa fa-trash"></i> Delete
                    </button>
                </li>
            @endif
            {{ $slot ?? '' }}
        </ul>
    </div>
</td>
