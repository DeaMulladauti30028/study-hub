<?php

namespace App\Http\Controllers;

use App\Models\{StudyGroup, GroupMaterial};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class GroupMaterialController extends Controller
{
    use AuthorizesRequests;
    // List materials for a group (members only)
    // app/Http/Controllers/GroupMaterialController.php
public function index(Request $request, StudyGroup $group)
{
    $q    = $request->string('q')->toString();          // search term
    $type = $request->string('type')->toString();       // image|pdf|video|slides|sheets|docs

    $materials = GroupMaterial::query()
        ->where('study_group_id', $group->id)
        ->searchTitle($q)
        ->ofBasicType($type)
        ->latest('id')
        ->paginate(15)
        ->withQueryString();

    return view('groups.materials.index', compact('group','materials','q','type'));
}


    // Upload a file into the group's materials (members only)
    public function store(Request $request, StudyGroup $group)
{
    $this->authorize('create', [GroupMaterial::class, $group]);

    $data = $request->validate([
        'title' => ['nullable','string','max:255'],
        // Friendlier, extension-based validation:
        'file'  => ['required','file','max:40960', 'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,txt,md,jpg,jpeg,png,webp,zip,7z,rar'],
    ]);

    $file = $request->file('file');

    // store RELATIVE path on a private disk (configure 'private' in filesystems.php)
    $path = $file->store("groups/{$group->id}/materials", 'private');

    GroupMaterial::create([
        'study_group_id' => $group->id,
        'user_id'        => $request->user()->id,
        'title'          => $data['title'] ?? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
        'file_path'      => $path,                          // relative
        'file_size'      => $file->getSize(),
        'mime_type'      => $file->getMimeType(),           // best-effort MIME
        'original_name'  => $file->getClientOriginalName(),
        // 'disk'        => 'private', // add if you later add a 'disk' column
    ]);

    return redirect()
        ->route('groups.materials.index', $group)
        ->with('status', 'Material uploaded.');
}

    // Download (members only)
    public function download(StudyGroup $group, GroupMaterial $material)
{
    abort_unless($material->study_group_id === $group->id, 404);
    $this->authorize('download', $material);

    return Storage::disk('private')->download(
        $material->file_path,
        $material->original_name ?? basename($material->file_path)
    );
}


    public function destroy(StudyGroup $group, GroupMaterial $material)
{
    abort_if($material->study_group_id !== $group->id, 404);
    $this->authorize('delete', $material);

    if ($material->file_path) {
        Storage::disk('public')->delete($material->file_path);
    }
    $material->delete();

    return back()->with('status', 'Material deleted.');
}

public function preview(StudyGroup $group, GroupMaterial $material)
{
    abort_unless($material->study_group_id === $group->id, 404);
    $this->authorize('view', $material);

    $absPath = Storage::disk('private')->path($material->file_path);

    return response()->file($absPath, [
        'Content-Type'        => $material->mime_type ?: (Storage::mimeType($absPath) ?: 'application/octet-stream'),
        'Content-Disposition' => 'inline; filename="'.($material->original_name ?? basename($absPath)).'"',
    ]);
}
}
