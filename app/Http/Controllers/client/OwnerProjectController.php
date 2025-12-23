<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnerProjectController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $projects = Project::where('owner_id', $user->id)
            ->with(['contracts', 'sites'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('client.owner.projects.index', compact('projects'));
    }
    
    public function show(Project $project)
    {
        $user = Auth::user();
        
        // Kiểm tra quyền sở hữu
        if ($project->owner_id !== $user->id) {
            abort(403, 'Unauthorized access');
        }
        
        $project->load([
            'contracts.contractor',
            'sites.tasks.progress',
            'payments'
        ]);
        
        return view('client.owner.projects.show', compact('project'));
    }
}