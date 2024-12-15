<div class="row mb-3">
    <!-- Status ID Field -->
    <div class="col-12 col-sm-4 mb-3">
        <div class="form-group">
            <?php
            $field_name = "status_id";
            $field_lable = "Status ID";
            $field_placeholder = "Enter a unique Status ID";
            $required = "required";
            ?>
            {{ html()->label($field_lable, $field_name)->class("form-label") }} {!! field_required($required) !!}
            {{ html()->text($field_name)->placeholder($field_placeholder)->class("form-control")->attributes(["$required"]) }}
        </div>
    </div>

    <!-- Name Field -->
    <div class="col-12 col-sm-4 mb-3">
        <div class="form-group">
            <?php
            $field_name = "name";
            $field_lable = "Name";
            $field_placeholder = "Enter Status Name";
            $required = "required";
            ?>
            {{ html()->label($field_lable, $field_name)->class("form-label") }} {!! field_required($required) !!}
            {{ html()->text($field_name)->placeholder($field_placeholder)->class("form-control")->attributes(["$required"]) }}
        </div>
    </div>

    <!-- Color Field -->
    <div class="col-12 col-sm-4 mb-3">
        <div class="form-group">
            <?php
            $field_name = "color";
            $field_lable = "Color";
            $field_placeholder = "-- Select a Color --";
            $required = "required";
            $select_options = ["primary" => "Primary", "success" => "Success", "warning" => "Warning", "danger" => "Danger", "secondary" => "Secondary"];
            ?>
            {{ html()->label($field_lable, $field_name)->class("form-label") }} {!! field_required($required) !!}
            {{ html()->select($field_name, $select_options)->class("form-select")->attributes(["$required"]) }}
        </div>
    </div>
</div>

<div class="row mb-3">
    <!-- Description Field -->
    <div class="col-12">
        <div class="form-group">
            <?php
            $field_name = "description";
            $field_lable = "Description";
            $field_placeholder = "Enter Status Description";
            $required = "";
            ?>
            {{ html()->label($field_lable, $field_name)->class("form-label") }} {!! field_required($required) !!}
            {{ html()->textarea($field_name)->placeholder($field_placeholder)->class("form-control")->attributes(["$required"]) }}
        </div>
    </div>
</div>

<hr />
