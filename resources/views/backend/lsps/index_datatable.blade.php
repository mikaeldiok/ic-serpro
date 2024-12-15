@extends('backend.layouts.app')

@section('title')
    {{ __($module_action) }} {{ __($module_title) }}
@endsection

@section('breadcrumbs')
    <x-backend.breadcrumbs>
        <x-backend.breadcrumb-item type="active"
            icon='{{ $module_icon }}'>{{ __($module_title) }}</x-backend.breadcrumb-item>
    </x-backend.breadcrumbs>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">

            <x-backend.section-header :module_name="$module_name" :module_title="$module_title" :module_icon="$module_icon" :module_action="$module_action" />
            <div class="row mt-4 mb-3">
                <div class="col d-flex justify-content-start">
                    <div class="dropdown float-start">
                        <button class="btn btn-secondary dropdown-toggle mr-2" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            Toggle Columns
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <li><input type="checkbox" class="toggle-column" data-column="0"> Action</li>
                            <li><input type="checkbox" class="toggle-column" data-column="1"> ID</li>
                            <li><input type="checkbox" class="toggle-column" data-column="2" checked> Name</li>
                            <li><input type="checkbox" class="toggle-column" data-column="3"> No Lisensi</li>
                            <li><input type="checkbox" class="toggle-column" data-column="4" checked> Email</li>
                            <li><input type="checkbox" class="toggle-column" data-column="5" checked> Jenis</li>
                            <li><input type="checkbox" class="toggle-column" data-column="6"> No Telp</li>
                            <li><input type="checkbox" class="toggle-column" data-column="7" checked> No HP</li>
                            <li><input type="checkbox" class="toggle-column" data-column="8"> No Fax</li>
                            <li><input type="checkbox" class="toggle-column" data-column="9" checked> Website</li>
                            <li><input type="checkbox" class="toggle-column" data-column="10"> Masa Berlaku Sertifikat</li>
                            <li><input type="checkbox" class="toggle-column" data-column="11" checked> Status Lisensi</li>
                            <li><input type="checkbox" class="toggle-column" data-column="12"> Alamat</li>
                            <li><input type="checkbox" class="toggle-column" data-column="13"> Logo Image</li>
                        </ul>
                    </div>

                    <button type="button" class="btn btn-primary mb-3 mx-2" data-bs-toggle="modal" data-bs-target="#filterModal">
                        Filter Data
                    </button>

                    <button type="button" id="clearFilterBtn" class="btn btn-outline-secondary mb-3 ">
                        Clear Filters
                    </button>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-hover w-100" id="datatable">
                            <thead>
                                <tr>
                                    <th>Action</th>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>No Lisensi</th>
                                    <th>Email</th>
                                    <th>Jenis</th>
                                    <th>No Telp</th>
                                    <th>No HP</th>
                                    <th>No Fax</th>
                                    <th>Website</th>
                                    <th>Masa Berlaku Sertifikat</th>
                                    <th>Status Lisensi</th>
                                    <th>Alamat</th>
                                    <th>Logo Image</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-7">
                    <div class="float-left"></div>
                </div>
                <div class="col-5">
                    <div class="float-end"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Filter Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="filterForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="filterAlamat" class="form-label">Name</label>
                                    <input type="text" id="filterName" class="form-control" placeholder="Name">
                                </div>
                                <div class="mb-3">
                                    <label for="filterJenis" class="form-label">Jenis</label>
                                    <select id="filterJenis" class="form-select select2" multiple>
                                        <option value="LSP Pihak Ketiga">LSP Pihak Ketiga</option>
                                        <option value="LSP Pihak Kedua">LSP Pihak Kedua</option>
                                        <option value="LSP Pihak Kesatu">LSP Pihak Kesatu</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="filterAlamat" class="form-label">Alamat</label>
                                    <input type="text" id="filterAlamat" class="form-control" placeholder="Search Address">
                                </div>
                                <div class="mb-3">
                                    <label for="filterStatusLisensi" class="form-label">Status Lisensi</label>
                                    <select id="filterStatusLisensi" class="form-select select2">
                                        <option value="Aktif">Aktif</option>
                                        <option value="Masa Berlaku Habis">Masa Berlaku Habis</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="filterTukCountOperator" class="form-label">TUK Count</label>
                                    <div class="d-flex align-items-center">
                                        <select id="filterTukCountOperator" class="form-select me-2" style="width: 80px;">
                                            <option value="=">=</option>
                                            <option value="<"><</option>
                                            <option value="<="><=</option>
                                            <option value=">">></option>
                                            <option value=">=">>=</option>
                                        </select>
                                        <input type="number" id="filterTukCount" class="form-control" min="0" placeholder="Enter TUK Count">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="filterSkemaCountOperator" class="form-label">Skema Count</label>
                                    <div class="d-flex align-items-center">
                                        <select id="filterSkemaCountOperator" class="form-select me-2" style="width: 80px;">
                                            <option value="=">=</option>
                                            <option value="<"><</option>
                                            <option value="<="><=</option>
                                            <option value=">">></option>
                                            <option value=">=">>=</option>
                                        </select>
                                        <input type="number" id="filterSkemaCount" class="form-control" min="0" placeholder="Enter Skema Count">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="filterAsesorCountOperator" class="form-label">Asesor Count</label>
                                    <div class="d-flex align-items-center">
                                        <select id="filterAsesorCountOperator" class="form-select me-2" style="width: 80px;">
                                            <option value="=">=</option>
                                            <option value="<"><</option>
                                            <option value="<="><=</option>
                                            <option value=">">></option>
                                            <option value=">=">>=</option>
                                        </select>
                                        <input type="number" id="filterAsesorCount" class="form-control" min="0" placeholder="Enter Asesor Count">
                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="applyFilterBtn" class="btn btn-primary">Apply Filters</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('after-scripts')
<script type="module" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="module">
    // Initialize DataTable
    let table = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("backend.$module_name.index_data") }}',
            data: function (d) {
                d.jenis = $('#filterJenis').val();
                d.status_lisensi = $('#filterStatusLisensi').val();
                d.alamat = $('#filterAlamat').val();
                d.name = $('#filterName').val();

                // Pass operator and value for Skema, TUK, and Asesor counts
                d.skema_count_operator = $('#filterSkemaCountOperator').val();
                d.skema_count_value = $('#filterSkemaCount').val();
                d.tuk_count_operator = $('#filterTukCountOperator').val();
                d.tuk_count_value = $('#filterTukCount').val();
                d.asesor_count_operator = $('#filterAsesorCountOperator').val();
                d.asesor_count_value = $('#filterAsesorCount').val();
            }
        },

        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false, visible: true },
            { data: 'id', name: 'id', visible: false },
            { data: 'name', name: 'name', visible: true },
            { data: 'no_lisensi', name: 'no_lisensi', visible: false },
            { data: 'email', name: 'email', visible: true },
            { data: 'jenis', name: 'jenis', visible: true },
            { data: 'no_telp', name: 'no_telp', visible: false },
            { data: 'no_hp', name: 'no_hp', visible: true },
            { data: 'no_fax', name: 'no_fax', visible: false },
            { data: 'website', name: 'website', visible: true },
            { data: 'masa_berlaku_sert', name: 'masa_berlaku_sert', visible: false },
            { data: 'status_lisensi', name: 'status_lisensi', visible: true },
            { data: 'alamat', name: 'alamat', visible: false },
            { data: 'logo_image', name: 'logo_image', visible: false }
        ]
    });

    $('#clearFilterBtn').on('click', function () {
        $('#filterModal').find('input, select, textarea').each(function () {
            const element = $(this);

            if (element.is('select')) {
                element.val(null).trigger('change');
            } else if (element.is('input[type="text"], input[type="number"], textarea')) {
                element.val('');
            } else if (element.is('input[type="checkbox"], input[type="radio"]')) {
                element.prop('checked', false);
            }
        });

        table.ajax.reload();
    });

    $('#applyFilterBtn').on('click', function () {
        $('#filterModal').modal('hide');
        table.ajax.reload();
    });

    $('#filterJenis, #filterStatusLisensi').select2({
        placeholder: 'All',
        allowClear: true,
        width: '100%',
        dropdownParent: $('#filterModal')
    });

    $('#filterModal').on('shown.bs.modal', function () {
        $('#filterJenis, #filterStatusLisensi').select2({
            placeholder: 'All',
            allowClear: true,
            width: '100%',
            dropdownParent: $(this)
        });
    });

    $('.toggle-column').each(function() {
        let column = table.column($(this).attr('data-column'));
        $(this).prop('checked', column.visible());
    });

    $('.toggle-column').on('change', function() {
        let column = table.column($(this).attr('data-column'));
        column.visible(!column.visible());
        table.columns.adjust().draw();
    });
</script>

@endpush

