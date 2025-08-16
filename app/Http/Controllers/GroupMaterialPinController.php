<?php

namespace App\Http\Controllers;

use App\Models\{StudyGroup, GroupMaterial};
use Illuminate\Support\Facades\DB;

class GroupMaterialPinController extends Controller
{
    public function store(StudyGroup $group, GroupMaterial $material)
    {
        // If you haven't enabled scopeBindings(), keep this guard:
        abort_unless((int)$material->study_group_id === (int)$group->id, 404);

        $this->authorize('pin', $material);

        // bypass mass-assignment safely
        $material->forceFill(['pinned_at' => now()])->save();

        return back()->with('status', 'Material pinned.');
    }

    public function destroy(StudyGroup $group, GroupMaterial $material)
    {
        abort_unless((int)$material->study_group_id === (int)$group->id, 404);

        $this->authorize('unpin', $material);

        $material->forceFill(['pinned_at' => null])->save();

        return back()->with('status', 'Material unpinned.');
    }
}
