<div>
    <h1 class="h3 mb-3">
        {{ $title ?? '' }}
    </h1>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">{{ $title ?? '' }} Data</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="float-start">
                        <div>

                        </div>
                        <button class="btn btn-primary" wire:click="getPool()" wire:loading.attr="disabled">
                            <i class="fa fa-check"></i>
                            Check All
                        </button>
                        <button class="btn btn-danger" wire:click="" wire:loading.attr="disabled">
                            <i class="fa fa-x"></i>
                            Uncheck All
                        </button>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="row">
                </div>
            </div>
        </div>
    </div>
</div>
