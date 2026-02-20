<div>
    <div class="row">
        <!-- [ sample-page ] start -->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>Hello {{ auth()->user()->name}}</h5>
                </div>
                <div class="card-body">
                    <button wire:click="increment" class="btn btn-info">+</button>
                    <h1>{{ $count }}</h1>
                </div>
            </div>
        </div>
        <!-- [ sample-page ] end -->
    </div>
</div>