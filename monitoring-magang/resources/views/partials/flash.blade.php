@if (session('success'))
    <div class="p-alert p-alert-success">{{ session('success') }}</div>
@endif
@if (session('warning'))
    <div class="p-alert p-alert-warning">{{ session('warning') }}</div>
@endif
@if ($errors->any())
    <div class="p-alert p-alert-danger">
        <strong>Periksa kembali isian berikut:</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
