<div class="text-end">
    @can("edit_" . $module_name)
        <x-backend.buttons.edit
            route='{!! route("backend.$module_name.editFollowup", $data) !!}'
            title="{{ __('Edit') }} {{ ucwords(Str::singular($module_name)) }}"
            small="true"
            icon="fas fa-pencil"
        />
    @endcan

    <x-backend.buttons.show
        route='{!! route("backend.$module_name.show", $data) !!}'
        title="{{ __('Show') }} {{ ucwords(Str::singular($module_name)) }}"
        small="true"
        icon="fas fa-eye"
    />
</div>
