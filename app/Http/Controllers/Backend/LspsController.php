<?php

namespace App\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Backend\BackendBaseController;
use App\Models\Lsp;
use App\Models\Status;
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
        $statuses = Status::pluck("name","id");
        $status_colors =[
            "success" => "green",
            "warning" => "yellow",
            "danger" => "red",
            "primary" => "blue",
            "secondary" => "gray"
        ];

        logUserAccess($module_title.' '.$module_action);

        return view(
            "{$module_path}.{$module_name}.index_datatable",
            compact('module_title', 'module_name', "{$module_name}", 'module_icon', 'module_name_singular', 'module_action','status_colors','statuses')
        );
    }
    public function index_data()
    {
        $module_model = $this->module_model;
        $query = $module_model::withCount("skemas", "tuks", "asesors");

        $request = request();

        if ($request->filled('name')) {
            $query->where('name', 'LIKE', '%' . $request->input('name') . '%');
        }

        if ($request->filled('jenis')) {
            $query->whereIn('jenis', $request->input('jenis'));
        }

        if ($request->filled('status_fu')) {
            $query->whereHas('status', function ($q) use ($request) {
                $q->whereIn('id', $request->input('status_fu'));
            });
        }

        if ($request->filled('status_fu_color')) {
            $query->whereHas('status', function ($q) use ($request) {
                $q->whereIn('color', $request->input('status_fu_color'));
            });
        }

        if ($request->filled('status_lisensi')) {
            $query->where('status_lisensi', $request->input('status_lisensi'));
        }

        if ($request->filled('alamat')) {
            $query->where('alamat', 'LIKE', '%' . $request->input('alamat') . '%');
        }

        // Valid operators
        $validOperators = ['=', '<', '<=', '>', '>='];

        // Filter by Skema Count using HAVING
        if ($request->filled('skema_count_value')) {
            $operator = $request->input('skema_count_operator', '=');
            if (!in_array($operator, $validOperators)) {
                $operator = '=';
            }
            $query->having('skemas_count', $operator, $request->input('skema_count_value'));
        }

        // Filter by TUK Count using HAVING
        if ($request->filled('tuk_count_value')) {
            $operator = $request->input('tuk_count_operator', '=');
            if (!in_array($operator, $validOperators)) {
                $operator = '=';
            }
            $query->having('tuks_count', $operator, $request->input('tuk_count_value'));
        }

        // Filter by Asesor Count using HAVING
        if ($request->filled('asesor_count_value')) {
            $operator = $request->input('asesor_count_operator', '=');
            if (!in_array($operator, $validOperators)) {
                $operator = '=';
            }
            $query->having('asesors_count', $operator, $request->input('asesor_count_value'));
        }

        // Return DataTables response
        return DataTables::of($query)
            ->addColumn('action', function ($data) {
                $module_name = $this->module_name;
                return view('backend.includes.action_column_lsp', compact('module_name', 'data'));
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
 /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     * @return \Illuminate\Contracts\View\View
     */
    public function editFollowup($id)
    {
        $module_title = $this->module_title;
        $module_name = $this->module_name;
        $module_path = $this->module_path;
        $module_icon = $this->module_icon;
        $module_model = $this->module_model;
        $module_name_singular = Str::singular($module_name);

        $module_action = 'Edit';

        $$module_name_singular = $module_model::findOrFail($id);

        logUserAccess($module_title.' '.$module_action.' | Id: '.$$module_name_singular->id);

        return view(
            "{$module_path}.{$module_name}.editFollowup",
            compact('module_title', 'module_name', 'module_path', 'module_icon', 'module_action', 'module_name_singular', "{$module_name_singular}")
        );
    }

    public function updateFollowup(Request $request, $id)
    {
        $module_title = $this->module_title;
        $module_name = $this->module_name;
        $module_path = $this->module_path;
        $module_icon = $this->module_icon;
        $module_model = $this->module_model;
        $module_name_singular = Str::singular($module_name);

        $module_action = 'Update';

        $validated_request = $request->validate([
            'notes' => 'nullable|max:191',
            'status_id' => 'nullable|max:191',
        ]);

        $$module_name_singular = $module_model::findOrFail($id);

        $$module_name_singular->update($request->except('logo_image', 'logo_image_remove'));

        flash(Str::singular($module_title) . "' Updated Successfully")->success()->important();

        logUserAccess($module_title . ' ' . $module_action . ' | Id: ' . $$module_name_singular->id);

        return redirect()->route("backend.{$module_name}.show", $$module_name_singular->id);
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
