<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\VisionBlueprint;
use App\Models\ProjectIssue;
use App\Models\ProjectComment;
use Illuminate\Support\Facades\Schema;

class ProjectThreadController extends Controller
{
    public function storeIssue(Request $request, $slug)
    {
        if (!Schema::hasTable('project_issues')) {
            return response()->json([
                'success' => false,
                'message' => 'Layanan tiket sedang dalam pemeliharaan (tabel database belum aktif).'
            ], 503);
        }

        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        
        $validated = $request->validate([
            'feature_id' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'nullable|string|in:low,medium,high,critical',
            'type' => 'nullable|string|in:bug,feature_request,change_request,general',
        ]);

        $issue = $blueprint->issues()->create([
            'user_id' => auth()->id(), // assuming user is authenticated
            'feature_id' => $validated['feature_id'],
            'title' => ['id' => $validated['title'], 'en' => $validated['title']], // Tier 1 Multilang Format
            'description' => ['id' => $validated['description'], 'en' => $validated['description']], // Tier 1 Multilang Format
            'priority' => $validated['priority'] ?? 'medium',
            'type' => $validated['type'] ?? 'bug',
            'status' => 'open',
        ]);

        return response()->json([
            'success' => true,
            'issue' => $issue,
            'message' => app()->getLocale() === 'en' ? 'Feedback ticket submitted.' : 'Tiket masukan berhasil dibuat.'
        ]);
    }

    public function storeComment(Request $request, $slug, $issueId)
    {
        if (!Schema::hasTable('project_comments') || !Schema::hasTable('project_issues')) {
            return response()->json([
                'success' => false,
                'message' => 'Layanan komentar sedang dalam pemeliharaan (tabel database belum aktif).'
            ], 503);
        }

        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $issue = ProjectIssue::where('id', $issueId)->where('vision_blueprint_id', $blueprint->id)->firstOrFail();

        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $comment = $issue->comments()->create([
            'vision_blueprint_id' => $blueprint->id,
            'user_id' => auth()->id(),
            'content' => ['id' => $validated['content'], 'en' => $validated['content']], // Tier 1 Multilang
        ]);

        return response()->json([
            'success' => true,
            'comment' => $comment,
            'message' => app()->getLocale() === 'en' ? 'Comment added.' : 'Komentar ditambahkan.'
        ]);
    }
}
