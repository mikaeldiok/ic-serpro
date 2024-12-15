@extends("backend.layouts.app")

@section("title")
    {{ __($module_action) }} {{ __($module_title) }}
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
    <x-backend.layouts.edit
        :data="$$module_name_singular"
        :module_name="$module_name"
        :module_path="$module_path"
        :module_title="$module_title"
        :module_icon="$module_icon"
        :module_action="$module_action"
    >
        <div class="card-body">
            <x-backend.section-header
                :data="$$module_name_singular"
                :module_name="$module_name"
                :module_title="$module_title"
                :module_icon="$module_icon"
                :module_action="$module_action"
            />

            <div class="row mt-4">
                <div class="col">
                    {{ html()->modelForm($$module_name_singular, "PATCH", route("backend.$module_name.updateFollowup", $$module_name_singular))->acceptsFiles()->open() }}

                    @include("$module_path.$module_name.formFollowup")

                    <div class="row">
                        <div class="col-4 mt-4">
                            <x-backend.buttons.save />
                        </div>

                        <div class="col-8 mt-4">
                            <div class="float-end">
                                @can("delete_" . $module_name)
                                    <a
                                        href="{{ route("backend.$module_name.destroy", $$module_name_singular) }}"
                                        class="btn btn-danger"
                                        data-method="DELETE"
                                        data-token="{{ csrf_token() }}"
                                        data-toggle="tooltip"
                                        title="{{ __("Delete") }}"
                                    >
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                @endcan

                                <x-backend.buttons.cancel />
                            </div>
                        </div>
                    </div>

                    {{ html()->closeModelForm() }}
                </div>
            </div>
        </div>
    </x-backend.layouts.edit>
@endsection
