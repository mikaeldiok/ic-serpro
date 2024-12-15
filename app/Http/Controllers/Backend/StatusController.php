<?php

namespace App\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Backend\BackendBaseController;
use App\Models\Status;
use App\Services\BnspScraper;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class StatusController extends BackendBaseController
{
    use Authorizable;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'Status';

        $this->module_name = 'status';

        $this->module_path = 'backend';

        $this->module_icon = 'fa-solid fa-diagram-project';

        // module model name, path
        $this->module_model = "App\Models\Status";
    }

    public function index()
    {
        $module_title = $this->module_title;
        $module_name = $this->module_name;
        $module_path = $this->module_path;
        $module_icon = $this->module_icon;
        $module_model = $this->module_model;
        $module_name_singular = Str::singular($module_name);

        $module_action = 'List';

        $$module_name = $module_model::paginate(15);

        logUserAccess($module_title.' '.$module_action);

        return view(
            "{$module_path}.{$module_name}.index_datatable",
            compact('module_title', 'module_name', "{$module_name}", 'module_icon', 'module_name_singular', 'module_action')
        );
    }
    public function index_data(): JsonResponse
    {
        $module_model = $this->module_model;
        $query = $module_model::query();

        // Return DataTables response
        return DataTables::of($query)
            ->addColumn('action', function ($data) {
                $module_name = $this->module_name;
                return view('backend.includes.action_column', compact('module_name', 'data'));
            })
            ->editColumn('name', function ($data) {
                return '<strong>' . e($data->name) . '</strong>';
            })
            ->rawColumns(['name', 'action'])
            ->make(true);
    }



    public function store(Request $request)
    {
        $module_title = $this->module_title;
        $module_name = $this->module_name;
        $module_path = $this->module_path;
        $module_icon = $this->module_icon;
        $module_model = $this->module_model;
        $module_name_singular = Str::singular($module_name);

        $module_action = 'Store';

        $validated_request = $request->validate([
            'status_id' => 'required|max:191|unique:' . $module_model . ',status_id',
            'name' => 'required|max:191|unique:' . $module_model . ',name',
            'color' => 'nullable|max:191',
            'description' => 'nullable',
        ]);

        $$module_name_singular = $module_model::create($validated_request);

        // Handle logo image
        if ($request->hasFile('logo_image')) {
            $media = $$module_name_singular->addMedia($request->file('logo_image'))->toMediaCollection($module_name);
            $$module_name_singular->logo_image = $media->getUrl();
            $$module_name_singular->save();
        }

        flash("New '" . Str::singular($module_title) . "' Added Successfully")->success()->important();

        logUserAccess($module_title . ' ' . $module_action . ' | Id: ' . $$module_name_singular->id);

        return redirect("admin/{$module_name}");
    }

    public function show($id)
    {
        $module_title = $this->module_title;
        $module_name = $this->module_name;
        $module_path = $this->module_path;
        $module_icon = $this->module_icon;
        $module_model = $this->module_model;
        $module_name_singular = Str::singular($module_name);

        $module_action = 'Show';

        $$module_name_singular = $module_model::findOrFail($id);

        logUserAccess($module_title . ' ' . $module_action . ' | Id: ' . $$module_name_singular->id);

        return view(
            "{$module_path}.{$module_name}.show",
            compact('module_title', 'module_name', 'module_path', 'module_icon', 'module_name_singular', 'module_action', "{$module_name_singular}")
        );
    }

    public function update(Request $request, $id)
    {
        $module_title = $this->module_title;
        $module_name = $this->module_name;
        $module_path = $this->module_path;
        $module_icon = $this->module_icon;
        $module_model = $this->module_model;
        $module_name_singular = Str::singular($module_name);

        $module_action = 'Update';

        $validated_request = $request->validate([
            'status_id' => 'required|max:191',
            'name' => 'required|max:191',
            'color' => 'nullable|max:191',
            'description' => 'nullable',
        ]);

        $$module_name_singular = $module_model::findOrFail($id);

        $$module_name_singular->update($validated_request);

        // Handle logo image
        if ($request->hasFile('logo_image')) {
            if ($$module_name_singular->getMedia($module_name)->first()) {
                $$module_name_singular->getMedia($module_name)->first()->delete();
            }
            $media = $$module_name_singular->addMedia($request->file('logo_image'))->toMediaCollection($module_name);
            $$module_name_singular->logo_image = $media->getUrl();
            $$module_name_singular->save();
        }

        if ($request->logo_image_remove === 'logo_image_remove') {
            if ($$module_name_singular->getMedia($module_name)->first()) {
                $$module_name_singular->getMedia($module_name)->first()->delete();
                $$module_name_singular->logo_image = '';
                $$module_name_singular->save();
            }
        }

        flash(Str::singular($module_title) . "' Updated Successfully")->success()->important();

        logUserAccess($module_title . ' ' . $module_action . ' | Id: ' . $$module_name_singular->id);

        return redirect()->route("backend.{$module_name}.show", $$module_name_singular->id);
    }
    public function updateStatusDataAjax(Request $request, $id)
    {
        $status = Status::findOrFail($id);

        $encryptedId = $status->encrypted_id;

        $bnspService = app(BnspScraper::class);

        try {
            $bnspService->scrapeDetailPage("https://bnsp.go.id/status/{$encryptedId}", $encryptedId);

            return response()->json(['success' => true, 'message' => 'STATUS data updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update STATUS data: ' . $e->getMessage()]);
        }
    }

}
