<div class="row mb-3">
    <div class="col-12 col-sm-4 mb-3">
        <div class="form-group">
            <?php
            $field_name = "status_id";
            $field_lable = "Status";
            $field_placeholder = "-- Select a status --";
            $required = "";

            $select_options = [null => "-- None --"] + \App\Models\Status::pluck("name", "id")->toArray();

            $default_value = old($field_name, $data->status_id ?? null);
            ?>
            {{ html()->label($field_lable, $field_name)->class("form-label") }} {!! field_required($required) !!}
            {{ html()->select($field_name, $select_options, $default_value)->class("form-select")->attributes(["$required"]) }}
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-12 col-sm-12 mb-3">
        <div class="form-group">
            <?php
            $field_name = "notes";
            $field_lable = "notes";
            $field_placeholder = "notes";
            $required = "";
            ?>
            {{ html()->label($field_lable, $field_name)->class("form-label") }} {!! field_required($required) !!}
            {{ html()->textArea($field_name)->placeholder($field_placeholder)->class("form-control")->attributes(["$required"]) }}
        </div>
    </div>
</div>
<hr />
