<div class="space-y-3">

    @if (session('success'))
        <x-ui.alert type="success" :message="session('success')" dismissible />
    @endif

    @if (session('info'))
        <x-ui.alert type="info" :message="session('info')" dismissible />
    @endif

    @if (session('warning'))
        <x-ui.alert type="warning" :message="session('warning')" dismissible />
    @endif

    @if (session('error'))
        <x-ui.alert type="danger" :message="session('error')" dismissible />
    @endif

</div>
