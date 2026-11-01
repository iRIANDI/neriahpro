<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosOrganizationWorkspace extends Model
{
    use HasUlids;

    protected $table = 'pos_organization_workspaces';

    protected $fillable = [
        'owner_user_id',
        'parent_organization_id',
        'name',
        'slug',
        'plan_tier',
        'entity_level',
        'elimination_account_code',
        'seat_limit',
        'white_label_config_json',
    ];

    protected function casts(): array
    {
        return [
            'seat_limit' => 'integer',
            'white_label_config_json' => 'array',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function parentOrganization(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_organization_id');
    }

    public function childBranches(): HasMany
    {
        return $this->hasMany(self::class, 'parent_organization_id')->orderBy('id', 'asc');
    }

    public function members(): HasMany
    {
        return $this->hasMany(PosOrganizationMember::class, 'workspace_id');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(PosProject::class, 'organization_workspace_id');
    }

    /**
     * Bagian 2 Feature #6 (v11.0): Multi-Entity Holding, Branch Consolidation & Inter-Company Elimination.
     *
     * @param  array<string, int|float>  $intercompanyEliminationsByBranchId
     * @return array<string, mixed>
     */
    public function buildHoldingConsolidationReport(array $intercompanyEliminationsByBranchId = []): array
    {
        $branches = $this->childBranches()->withCount(['projects', 'members'])->get();
        $ownProjects = $this->projects()->get(['id', 'app_name', 'commercial_quotation_json', 'architectural_freeze_score']);

        $calculateGrossVal = static function ($projects): int {
            $sum = 0;
            foreach ($projects as $proj) {
                $q = is_array($proj->commercial_quotation_json) ? $proj->commercial_quotation_json : [];
                $sum += (int) ($q['quotation_idr'] ?? 0);
            }

            return $sum;
        };

        $hqGrossIdr = $calculateGrossVal($ownProjects);
        $branchBreakdown = [];
        $branchesGrossTotal = 0;
        $totalIntercompanyEliminationIdr = 0;
        $totalProjectsCount = $ownProjects->count();
        $totalSeatsAllocated = (int) $this->seat_limit;

        foreach ($branches as $branch) {
            $bProjects = $branch->projects()->get(['id', 'app_name', 'commercial_quotation_json']);
            $bGross = $calculateGrossVal($bProjects);
            $elim = max(0, (int) ($intercompanyEliminationsByBranchId[$branch->id] ?? 0));
            $bNet = max(0, $bGross - $elim);

            $branchesGrossTotal += $bGross;
            $totalIntercompanyEliminationIdr += $elim;
            $totalProjectsCount += $bProjects->count();
            $totalSeatsAllocated += (int) $branch->seat_limit;

            $branchBreakdown[] = [
                'workspace_id' => $branch->id,
                'name' => $branch->name,
                'slug' => $branch->slug,
                'entity_level' => $branch->entity_level ?: 'BRANCH_OUTLET',
                'elimination_account_code' => $branch->elimination_account_code ?: '3900-INTERCO-ELIM',
                'projects_count' => $bProjects->count(),
                'gross_ledger_idr' => $bGross,
                'intercompany_elimination_idr' => $elim,
                'net_consolidated_idr' => $bNet,
                'isolation_policy' => 'STRICT_BRANCH_DATA_WALL',
            ];
        }

        $consolidatedGross = $hqGrossIdr + $branchesGrossTotal;
        $consolidatedNet = max(0, $consolidatedGross - $totalIntercompanyEliminationIdr);

        return [
            'holding_workspace_id' => $this->id,
            'holding_name' => $this->name,
            'entity_level' => $this->entity_level ?: 'HOLDING_HQ',
            'branches_count' => count($branchBreakdown),
            'total_consolidated_projects' => $totalProjectsCount,
            'total_consolidated_seats' => $totalSeatsAllocated,
            'hq_gross_ledger_idr' => $hqGrossIdr,
            'branches_gross_ledger_idr' => $branchesGrossTotal,
            'consolidated_gross_idr' => $consolidatedGross,
            'intercompany_elimination_idr' => $totalIntercompanyEliminationIdr,
            'consolidated_net_idr' => $consolidatedNet,
            'formatted_consolidated_net' => 'Rp '.number_format($consolidatedNet, 0, ',', '.'),
            'branches' => $branchBreakdown,
        ];
    }
}
