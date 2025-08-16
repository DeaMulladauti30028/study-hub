<?php
namespace App\Http\Controllers;

use App\Models\{StudyGroup, Contribution};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContributionEndorseController extends Controller
{
    public function store(StudyGroup $group, Contribution $contribution)
    {
        // If you didn't enable scopeBindings() on the route group, keep this:
        abort_unless((int) $contribution->study_group_id === (int) $group->id, 404);

        $this->authorize('endorse', $contribution);


        // Single write (bypass mass-assignment)
        $contribution->forceFill([
            'accepted_by' => Auth::id(),
            'accepted_at' => now(),
        ])->save();

        $after = $contribution->fresh();
        logger()->info('ENDORSE.AFTER', [
            'by' => $after->accepted_by,
            'at' => $after->accepted_at,
        ]);

        return back()->with('status', 'Contribution endorsed.');
    }

    public function destroy(StudyGroup $group, Contribution $contribution)
    {
        // If you didn't enable scopeBindings() on the route group, keep this:
        abort_unless((int) $contribution->study_group_id === (int) $group->id, 404);

        $this->authorize('endorse', $contribution);

        // Single write (bypass mass-assignment)
        $contribution->forceFill([
            'accepted_by' => null,
            'accepted_at' => null,
        ])->save();

        return back()->with('status', 'Endorsement removed.');
    }
}
