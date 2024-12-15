@extends("backend.layouts.app")

@section("title")
    {{ $lsp->name }} - {{ __($module_action) }} {{ __($module_title) }}
@endsection

@section("breadcrumbs")
    <x-backend.breadcrumbs>
        <x-backend.breadcrumb-item route='{{ route("backend.$module_name.index") }}' icon="{{ $module_icon }}">
            {{ __($module_title) }}
        </x-backend.breadcrumb-item>
        <x-backend.breadcrumb-item type="active">{{ __($module_action) }}</x-backend.breadcrumb-item>
    </x-backend.breadcrumbs>
@endsection

@section("content")
<div class="card">
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-2 col-12">
                <button id="updateLspDataBtn" class="btn btn-primary">Update LSP Data</button>
            </div>
        </div>
        <div class="row mb-2">
            <h3 class="d-inline">
                Profile LSP
                <span class="fs-6 ms-2">
                    <a class="text-sm" target="_blank" href="https://bnsp.go.id/lsp/{{ $lsp->encrypted_id }}"><i class="fas fa-link"></i> source</a>
                </span>
            </h3>
        <hr>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="row mb-5">
                    <div class="col-12 text-center">
                        <img src="{{ $lsp->logo_image }}" alt="{{ $lsp->name }}" class="img-fluid rounded-3 shadow-lg ">
                    </div>
                </div>
                <div class="row mt-5">
                    <div class="col-12">
                        <table class="table table-sm table-bordered">
                            <tbody>
                                <tr>
                                    <td><a href="#skemaSection" class="text-primary">Skema</a></td>
                                    <td>{{ $lsp->skemas->count() }}</td>
                                </tr>
                                <tr>
                                    <td><a href="#tukSection" class="text-primary">TUK</a></td>
                                    <td>{{ $lsp->tuks->count() }}</td>
                                </tr>
                                <tr>
                                    <td><a href="#asesorSection" class="text-primary">Asesor</a></td>
                                    <td>{{ $lsp->asesors->count() }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <table class="table table-sm ">
                    <tbody>
                        <tr>
                            <th>Name</th>
                            <td>{{ $lsp->name }}</td>
                        </tr>
                        <tr>
                            <th>No. SK Lisensi</th>
                            <td>{{ $lsp->sk_lisensi }}</td>
                        </tr>
                        <tr>
                            <th>No Lisensi</th>
                            <td>{{ $lsp->no_lisensi }}</td>
                        </tr>
                        <tr>
                            <th>Jenis</th>
                            <td>{{ $lsp->jenis }}</td>
                        </tr>
                        <tr>
                            <th>No Telp</th>
                            <td>{{ $lsp->no_telp }}</td>
                        </tr>
                        <tr>
                            <th>No HP</th>
                            <td>{{ $lsp->no_hp }}</td>
                        </tr>
                        <tr>
                            <th>No Fax</th>
                            <td>{{ $lsp->no_fax }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $lsp->email }}</td>
                        </tr>
                        <tr>
                            <th>Website</th>
                            <td>
                                @php
                                    $website = $lsp->website;
                                    if ($website && !preg_match('/^http(s)?:\/\//', $website)) {
                                        $website = 'http://' . $website;
                                    }
                                @endphp
                                <a href="{{ $website }}" target="_blank">{{ $lsp->website }}</a>
                            </td>
                        </tr>
                        <tr>
                            <th>Masa Berlaku Sertifikat</th>
                            <td>{{ $lsp->masa_berlaku_sert }}</td>
                        </tr>
                        <tr>
                            <th>Status Lisensi</th>
                            <td>
                                <span class="badge {{ $lsp->status_lisensi === 'Aktif' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $lsp->status_lisensi }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Alamat</th>
                            <td>{{ $lsp->alamat }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <h5  id="tukSection">TUK List</h5>
                <hr>
                <div  style="max-height: 500px; overflow-y: auto;">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lsp->tuks as $index => $tuk)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $tuk->tuk_code }}</td>
                                    <td>{{ $tuk->name }}</td>
                                    <td>{{ $tuk->type }}</td>
                                    <td>{{ $tuk->address }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-6" >
                <h5 id="asesorSection">Asesor List</h5>
                <hr>
                <div  style="max-height: 500px; overflow-y: auto;">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Registration ID</th>
                                <th>Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lsp->asesors as $index => $asesor)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $asesor->name }}</td>
                                    <td>{{ $asesor->registration_id }}</td>
                                    <td>{{ $asesor->address }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row mt-4" id="skemaSection">
            <div class="col-12">
                <h5  id="tukSection">Skema List</h5>
                <hr>
                <div class="overflow-auto" style="max-height: 2400px;">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Skema Name</th>
                                <th>Units</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lsp->skemas as $index => $skema)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $skema->name }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#unitsCollapse{{ $skema->id }}" aria-expanded="false" aria-controls="unitsCollapse{{ $skema->id }}">
                                            View ({{ $skema->units->count() }})
                                        </button>
                                    </td>
                                </tr>
                                <tr class="collapse-row">
                                    <td colspan="3" class="p-0">
                                        <div id="unitsCollapse{{ $skema->id }}" class="collapse">
                                            <div class="overflow-auto p-2" style="max-height: 1200px;"> <!-- Adjust height for units -->
                                                <table class="table table-sm table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Unit Code</th>
                                                            <th>Name</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($skema->units as $unitIndex => $unit)
                                                            <tr>
                                                                <td>{{ $unitIndex + 1 }}</td>
                                                                <td>{{ $unit->unit_code }}</td>
                                                                <td>{{ $unit->name }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('after-scripts')
<script>
    $(document).ready(function () {
        $('#updateLspDataBtn').on('click', function () {
            const button = $(this);
            const lspId = {{ $lsp->id }};
            const url = '{{ route("backend.lsps.updateLspDataAjax", ":id") }}'.replace(':id', lspId);

            // Disable button and show loading state with spinner
            button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...');

            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    if (response.success) {
                        alert(response.message);
                        location.reload(); // Reload page to reflect updated data
                    } else {
                        alert(response.message);
                    }
                },
                error: function (xhr, status, error) {
                    alert('An error occurred while updating the LSP data.');
                    console.error(error);
                },
                complete: function () {
                    // Re-enable button and reset text
                    button.prop('disabled', false).html('Update LSP Data');
                }
            });
        });
    });
</script>
@endpush

