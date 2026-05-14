<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Folder;
use App\Models\FileEntry;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DriveController extends Controller
{
    public function index(Request $request)
    {
        $parentId = $request->query('folder', null);
        $search = $request->query('search', null);
        
        $folderQuery = Folder::where('user_id', Auth::id())->withCount('files');
        $fileQuery = FileEntry::where('user_id', Auth::id());
        
        if ($search) {
            $folders = $folderQuery->where('name', 'like', "%{$search}%")->get();
            $files = $fileQuery->where('name', 'like', "%{$search}%")->get();
            $breadcrumbs = [];
        } else {
            $folders = $folderQuery->where('parent_id', $parentId)->get();
            $files = $fileQuery->where('folder_id', $parentId)->get();
            
            $breadcrumbs = [];
            if ($parentId) {
                $folder = Folder::find($parentId);
                while ($folder) {
                    array_unshift($breadcrumbs, $folder);
                    $folder = $folder->parent;
                }
            }
        }
        $currentTab = 'my_drive';
        return view('dashboard', compact('folders', 'files', 'parentId', 'breadcrumbs', 'search', 'currentTab'));
    }

    public function recent()
    {
        $folders = collect();
        $files = FileEntry::where('user_id', Auth::id())->orderBy('updated_at', 'desc')->take(20)->get();
        $breadcrumbs = [];
        $parentId = null;
        $currentTab = 'recent';
        return view('dashboard', compact('folders', 'files', 'parentId', 'breadcrumbs', 'currentTab'));
    }

    public function starred()
    {
        $folders = Folder::where('user_id', Auth::id())->where('is_starred', true)->withCount('files')->get();
        $files = FileEntry::where('user_id', Auth::id())->where('is_starred', true)->get();
        $breadcrumbs = [];
        $parentId = null;
        $currentTab = 'starred';
        return view('dashboard', compact('folders', 'files', 'parentId', 'breadcrumbs', 'currentTab'));
    }

    public function trash()
    {
        $folders = Folder::onlyTrashed()->where('user_id', Auth::id())->withCount('files')->get();
        $files = FileEntry::onlyTrashed()->where('user_id', Auth::id())->get();
        $breadcrumbs = [];
        $parentId = null;
        $currentTab = 'trash';
        return view('dashboard', compact('folders', 'files', 'parentId', 'breadcrumbs', 'currentTab'));
    }

    public function createFolder(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        
        Folder::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'user_id' => Auth::id(),
        ]);
        
        if ($request->expectsJson()) return response()->json(['success' => true, 'message' => 'Folder created successfully']);
        return back()->with('success', 'Folder created successfully');
    }

    public function renameFolder(Request $request, Folder $folder)
    {
        if ($folder->user_id !== Auth::id()) abort(403);
        $request->validate(['name' => 'required|string|max:255']);
        $folder->update(['name' => $request->name]);
        if ($request->expectsJson()) return response()->json(['success' => true, 'message' => 'Folder renamed']);
        return back()->with('success', 'Folder renamed');
    }

    public function deleteFolder(Folder $folder)
    {
        if ($folder->user_id !== Auth::id()) abort(403);
        $folder->delete();
        if (request()->expectsJson()) return response()->json(['success' => true, 'message' => 'Folder moved to trash']);
        return back()->with('success', 'Folder moved to trash');
    }

    public function restoreFolder($id)
    {
        $folder = Folder::onlyTrashed()->where('user_id', Auth::id())->findOrFail($id);
        $folder->restore();
        if (request()->expectsJson()) return response()->json(['success' => true, 'message' => 'Folder restored']);
        return back()->with('success', 'Folder restored');
    }

    public function forceDeleteFolder($id)
    {
        $folder = Folder::onlyTrashed()->where('user_id', Auth::id())->findOrFail($id);
        $folder->forceDelete();
        if (request()->expectsJson()) return response()->json(['success' => true, 'message' => 'Folder permanently deleted']);
        return back()->with('success', 'Folder permanently deleted');
    }

    public function toggleStarFolder(Request $request, Folder $folder)
    {
        if ($folder->user_id !== Auth::id()) abort(403);
        $folder->update(['is_starred' => !$folder->is_starred]);
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'is_starred' => $folder->is_starred, 'message' => 'Folder ' . ($folder->is_starred ? 'starred' : 'unstarred')]);
        }
        return back()->with('success', 'Folder star updated');
    }

    public function uploadFile(Request $request)
    {
        $request->validate(['files' => 'required|array', 'files.*' => 'file|max:51200']); // max 50MB per file
        
        foreach($request->file('files') as $file) {
            $path = $file->store('drive', 'public');
            
            FileEntry::create([
                'name' => $file->getClientOriginalName(),
                'path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'folder_id' => $request->parent_id,
                'user_id' => Auth::id(),
            ]);
        }
        
        if ($request->expectsJson()) return response()->json(['success' => true, 'message' => 'Files uploaded successfully']);
        return back()->with('success', 'Files uploaded successfully');
    }

    public function downloadFile(FileEntry $file)
    {
        if ($file->user_id !== Auth::id()) abort(403);
        return response()->download(storage_path('app/public/' . $file->path), $file->name);
    }

    public function renameFile(Request $request, FileEntry $file)
    {
        if ($file->user_id !== Auth::id()) abort(403);
        $request->validate(['name' => 'required|string|max:255']);
        $file->update(['name' => $request->name]);
        if ($request->expectsJson()) return response()->json(['success' => true, 'message' => 'File renamed']);
        return back()->with('success', 'File renamed');
    }

    public function deleteFile(FileEntry $file)
    {
        if ($file->user_id !== Auth::id()) abort(403);
        $file->delete();
        if (request()->expectsJson()) return response()->json(['success' => true, 'message' => 'File moved to trash']);
        return back()->with('success', 'File moved to trash');
    }

    public function restoreFile($id)
    {
        $file = FileEntry::onlyTrashed()->where('user_id', Auth::id())->findOrFail($id);
        $file->restore();
        if (request()->expectsJson()) return response()->json(['success' => true, 'message' => 'File restored']);
        return back()->with('success', 'File restored');
    }

    public function forceDeleteFile($id)
    {
        $file = FileEntry::onlyTrashed()->where('user_id', Auth::id())->findOrFail($id);
        Storage::disk('public')->delete($file->path);
        $file->forceDelete();
        if (request()->expectsJson()) return response()->json(['success' => true, 'message' => 'File permanently deleted']);
        return back()->with('success', 'File permanently deleted');
    }

    public function toggleStarFile(Request $request, FileEntry $file)
    {
        if ($file->user_id !== Auth::id()) abort(403);
        $file->update(['is_starred' => !$file->is_starred]);
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'is_starred' => $file->is_starred, 'message' => 'File ' . ($file->is_starred ? 'starred' : 'unstarred')]);
        }
        return back()->with('success', 'File star updated');
    }

    public function liveSearch(Request $request)
    {
        $query = $request->query('query', '');
        
        if (strlen($query) < 2) {
            return response()->json(['folders' => [], 'files' => []]);
        }

        $folders = Folder::where('user_id', Auth::id())
            ->where('name', 'like', "%{$query}%")
            ->take(5)
            ->get()
            ->map(function($folder) {
                return [
                    'id' => $folder->id,
                    'name' => $folder->name,
                    'type' => 'folder',
                    'owner' => $folder->user->name,
                    'updated_at' => $folder->updated_at->format('d M'),
                    'url' => route('dashboard', ['folder' => $folder->id])
                ];
            });

        $files = FileEntry::where('user_id', Auth::id())
            ->where('name', 'like', "%{$query}%")
            ->take(10)
            ->get()
            ->map(function($file) {
                return [
                    'id' => $file->id,
                    'name' => $file->name,
                    'type' => 'file',
                    'mime_type' => $file->mime_type,
                    'owner' => $file->user->name,
                    'updated_at' => $file->updated_at->format('d M'),
                    'url' => asset('storage/' . $file->path)
                ];
            });

        return response()->json([
            'folders' => $folders,
            'files' => $files
        ]);
    }
}
