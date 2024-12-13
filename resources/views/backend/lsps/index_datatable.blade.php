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
                <div class="col">
                    <div class="dropdown float-start">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
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
@endsection

@push('after-styles')
    <link href="{{ asset('vendor/datatable/datatables.min.css') }}" rel="stylesheet">
    <style>
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100% !important;
        }
    </style>
@endpush

@push('after-scripts')
    <script type="module" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>

    <script type="module">
        let table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("backend.$module_name.index_data") }}',
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
