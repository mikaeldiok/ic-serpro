<?php

namespace App\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Backend\BackendBaseController;
use App\Models\Lsp;
use App\Services\BnspScraper;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class LspsController extends BackendBaseController
{
    use Authorizable;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'Lsps';

        $this->module_name = 'lsps';

        $this->module_path = 'backend';

        $this->module_icon = 'fa-solid fa-diagram-project';

        // module model name, path
        $this->module_model = "App\Models\Lsp";
    }

    public function index_data(): JsonResponse
    {
        $module_title = $this->module_title;
        $module_name = $this->module_name;
        $module_path = $this->module_path;
        $module_icon = $this->module_icon;
        $module_model = $this->module_model;
        $module_name_singular = Str::singular($module_name);

        $module_action = 'List';

        $page_heading = label_case($module_title);
        $title = $page_heading.' '.label_case($module_action);

        // $$module_name = $module_model::select('id', 'name','jenis','no_telp','no_hp','no_fax','no_lisensi','email','website','status_lisensi','logo_image');
        $$module_name = $module_model::query();

        $data = $$module_name;

        return DataTables::of($$module_name)
            ->addColumn('action', function ($data) {
                $module_name = $this->module_name;

                return view('backend.includes.action_column', compact('module_name', 'data'));
            })
            ->editColumn('name', '<strong>{{$name}}</strong>')
            ->rawColumns(['name', 'action'])
            ->orderColumns(['id'], '-:column $1')
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
            'encrypted_id' => 'required|max:191|unique:' . $module_model . ',encrypted_id',
            'name' => 'required|max:191|unique:' . $module_model . ',name',
            'sk_lisensi' => 'nullable|max:191',
            'no_lisensi' => 'nullable|max:191',
            'jenis' => 'nullable|max:191',
            'no_telp' => 'nullable|max:15',
            'no_hp' => 'nullable|max:15',
            'no_fax' => 'nullable|max:15',
            'email' => 'nullable|email|max:191',
            'website' => 'nullable|url|max:191',
            'masa_berlaku_sert' => 'nullable|date',
            'status_lisensi' => 'nullable|max:191',
            'alamat' => 'nullable|string',
            'logo_image' => 'nullable|image|max:2048',
        ]);

        $$module_name_singular = $module_model::create($request->except('logo_image'));

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
            'encrypted_id' => 'required|max:191|unique:' . $module_model . ',encrypted_id,' . $id,
            'name' => 'required|max:191|unique:' . $module_model . ',name,' . $id,
            'sk_lisensi' => 'nullable|max:191',
            'no_lisensi' => 'nullable|max:191',
            'jenis' => 'nullable|max:191',
            'no_telp' => 'nullable|max:15',
            'no_hp' => 'nullable|max:15',
            'no_fax' => 'nullable|max:15',
            'email' => 'nullable|email|max:191',
            'website' => 'nullable|url|max:191',
            'masa_berlaku_sert' => 'nullable|date',
            'status_lisensi' => 'required|max:191',
            'alamat' => 'nullable|string',
            'logo_image' => 'nullable|image|max:2048',
        ]);

        $$module_name_singular = $module_model::findOrFail($id);

        $$module_name_singular->update($request->except('logo_image', 'logo_image_remove'));

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
    public function updateLspDataAjax(Request $request, $id)
    {
        $lsp = Lsp::findOrFail($id);

        $encryptedId = $lsp->encrypted_id;

        $bnspService = app(BnspScraper::class);

        try {
            $bnspService->scrapeDetailPage("https://bnsp.go.id/lsp/{$encryptedId}", $encryptedId);

            return response()->json(['success' => true, 'message' => 'LSP data updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update LSP data: ' . $e->getMessage()]);
        }
    }

}
