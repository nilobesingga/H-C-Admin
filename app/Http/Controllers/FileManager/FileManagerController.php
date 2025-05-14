<?php

namespace App\Http\Controllers\FileManager;

use App\Http\Controllers\Controller;
use App\Models\Bitrix\BitrixListsSageCompanyMapping;
use App\Services\UserServices;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FileManagerController extends Controller
{
    use ApiResponser;

    protected $userService;
    protected $user;
    protected $userCategoryIds;

    public function __construct(UserServices $userService)
    {
        $this->userService = $userService;
        $this->user = $userService->getAuthUserModulesAndCategories();
        $this->userCategoryIds = $userService->getUserCategoryIds();
    }

    /**
     * Display the file manager view
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $bitrixListCategories = BitrixListsSageCompanyMapping::select('category_id', 'bitrix_list_id', 'bitrix_category_id', 'bitrix_category_name')
            ->where('bitrix_list_id', 1)
            ->whereNotNull('bitrix_category_id')
            ->whereIn('category_id', $this->userCategoryIds)
            ->distinct()
            ->get();

        $page = (object) [
            'title' => 'File Manager',
            'identifier' => 'file-manager',
            'user' => $this->user,
            'bitrix_list_categories' => $bitrixListCategories
        ];

        return view('filemanager.file-manager', compact('page'));
    }

    /**
     * Test SFTP connection
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function testConnection()
    {
        try {
            $disk = Storage::disk('file_server');

            // Try a simple operation to test the connection
            $disk->listContents('/', false);

            return $this->successResponse('Successfully connected to SFTP server', [
                'status' => 'connected',
                'message' => 'Successfully connected to SFTP server'
            ]);
        } catch (\Exception $e) {
            Log::error('SFTP connection test failed: ' . $e->getMessage());

            return $this->errorResponse('Failed to connect to SFTP server', [
                'status' => 'failed',
                'message' => 'Failed to connect to SFTP server',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Get file data from the SFTP server
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getData(Request $request)
    {
        try {
            // Validate incoming request
            $request->validate([
                'path' => 'nullable|string',
                'action' => 'required|string|in:list,download,info',
                'filename' => 'nullable|string',
                'disk' => 'nullable|string|in:file_server,holding_smb',
            ]);

            $path = $request->input('path', '/');
            $action = $request->input('action');
            $filename = $request->input('filename');
            $diskName = $request->input('disk', 'holding_smb');

            // Initialize the storage disk
            $disk = Storage::disk($diskName);

            // Check connection before performing operations
            try {
                // Simple test operation
                if (!$disk->exists($path)) {
                    // If path doesn't exist but it's root, that's a connection issue
                    if ($path === '/') {
                        throw new \Exception("Unable to connect to file server");
                    }
                    return $this->errorResponse("Directory not found: {$path}", null, 404);
                }
            } catch (\Exception $e) {
                Log::error('File server connection failed: ' . $e->getMessage());
                return $this->errorResponse('Error connecting to file server: ' . $e->getMessage(), null, 500);
            }

            // Handle different actions
            switch ($action) {
                case 'list':
                    // List directories and files
                    $directories = [];
                    $files = [];

                    // Get all contents
                    $contents = $disk->listContents($path, false);

                    foreach ($contents as $item) {
                        $itemData = [
                            'name' => $item['basename'] ?? basename($item['path']),
                            'path' => $item['path'],
                            'size' => $item['size'] ?? 0,
                            'last_modified' => $item['timestamp'] ?? null,
                        ];

                        if ($item['type'] === 'dir') {
                            $directories[] = $itemData;
                        } else {
                            $extension = pathinfo($item['path'], PATHINFO_EXTENSION);
                            $itemData['extension'] = $extension;
                            $files[] = $itemData;
                        }
                    }

                    // Log the results for debugging
                    Log::info("Listing directory '{$path}' on disk '{$diskName}'", [
                        'directories_count' => count($directories),
                        'files_count' => count($files)
                    ]);

                    return $this->successResponse('File list retrieved successfully', [
                        'directories' => $directories,
                        'files' => $files,
                        'current_path' => $path,
                    ]);

                case 'download':
                    // Download a specific file
                    if (!$filename) {
                        return $this->errorResponse('Filename is required for download action', null, 400);
                    }

                    $filePath = rtrim($path, '/') . '/' . $filename;

                    if (!$disk->exists($filePath)) {
                        return $this->errorResponse('File not found', null, 404);
                    }

                    $fileContent = $disk->get($filePath);
                    $mimeType = $disk->mimeType($filePath);

                    return response($fileContent)
                        ->header('Content-Type', $mimeType)
                        ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

                case 'info':
                    // Get file or directory info
                    if (!$filename) {
                        return $this->errorResponse('Filename is required for info action', null, 400);
                    }

                    $filePath = rtrim($path, '/') . '/' . $filename;

                    if (!$disk->exists($filePath)) {
                        return $this->errorResponse('File not found', null, 404);
                    }

                    // Get file information using individual methods instead of getMetadata()
                    $fileSize = null;
                    $lastModified = null;
                    $mimeType = null;
                    $type = null;

                    try {
                        $fileSize = $disk->size($filePath);
                    } catch (\Exception $e) {
                        $fileSize = 0;
                        Log::warning("Could not get file size for info: {$e->getMessage()}");
                    }

                    try {
                        $lastModified = $disk->lastModified($filePath);
                    } catch (\Exception $e) {
                        $lastModified = time();
                        Log::warning("Could not get last modified time for info: {$e->getMessage()}");
                    }

                    try {
                        $mimeType = $disk->mimeType($filePath);
                        // Determine if it's a file or directory based on mime type
                        $type = $mimeType === 'directory' ? 'dir' : 'file';
                    } catch (\Exception $e) {
                        $mimeType = 'application/octet-stream';
                        // Try to determine type by path
                        $type = substr($filePath, -1) === '/' ? 'dir' : 'file';
                        Log::warning("Could not get mime type for info: {$e->getMessage()}");
                    }

                    return $this->successResponse('File info retrieved successfully', [
                        'name' => basename($filePath),
                        'path' => $filePath,
                        'size' => $fileSize,
                        'last_modified' => $lastModified,
                        'type' => $type,
                        'mime_type' => $mimeType,
                    ]);

                default:
                    return $this->errorResponse('Invalid action specified', null, 400);
            }
        } catch (\Exception $e) {
            Log::error('File manager error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return $this->errorResponse('Error accessing file server: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Perform a deep search through the file system
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deepSearch(Request $request)
    {
        try {
            // Validate incoming request
            $request->validate([
                'query' => 'required|string|min:3',
                'path' => 'nullable|string',
                'disk' => 'nullable|string|in:file_server,holding_smb',
            ]);

            $searchQuery = $request->input('query');
            $startPath = $request->input('path', '/');
            $diskName = $request->input('disk', 'holding_smb');

            // Initialize the storage disk
            $disk = Storage::disk($diskName);

            // Check connection
            try {
                if (!$disk->exists($startPath)) {
                    if ($startPath === '/') {
                        throw new \Exception("Unable to connect to file server");
                    }
                    return $this->errorResponse("Directory not found: {$startPath}", null, 404);
                }
            } catch (\Exception $e) {
                Log::error('File server connection failed: ' . $e->getMessage());
                return $this->errorResponse('Error connecting to file server: ' . $e->getMessage(), null, 500);
            }

            // Perform recursive search
            $results = $this->recursiveSearch($disk, $searchQuery, $startPath);

            return $this->successResponse('Search completed', $results);
        } catch (\Exception $e) {
            Log::error('Deep search error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return $this->errorResponse('Error performing search: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Recursively search through directories
     *
     * @param \Illuminate\Contracts\Filesystem\Filesystem $disk
     * @param string $searchQuery
     * @param string $path
     * @param int $maxDepth
     * @param int $currentDepth
     * @param int $maxResults
     * @return array
     */
    private function recursiveSearch($disk, $searchQuery, $path = '/', $maxDepth = 5, $currentDepth = 0, $maxResults = 5)
    {
        $results = [];
        $searchQuery = strtolower($searchQuery);

        // Stop if we've gone too deep or have too many results
        if ($currentDepth > $maxDepth || count($results) >= $maxResults) {
            return $results;
        }

        try {
            $contents = $disk->listContents($path, false);

            foreach ($contents as $item) {
                // Skip if we've reached max results
                if (count($results) >= $maxResults) {
                    break;
                }

                $name = $item['basename'] ?? basename($item['path']);
                $itemPath = $item['path'];

                // Check if the current item matches the search
                if (stripos(strtolower($name), $searchQuery) !== false) {
                    $result = [
                        'name' => $name,
                        'path' => $itemPath,
                        'type' => $item['type'],
                    ];

                    if ($item['type'] === 'file') {
                        $result['extension'] = pathinfo($itemPath, PATHINFO_EXTENSION);
                        $result['size'] = $item['size'] ?? 0;
                        $result['last_modified'] = $item['timestamp'] ?? null;
                    }

                    $results[] = $result;
                }

                // Recursively search directories
                if ($item['type'] === 'dir') {
                    $subResults = $this->recursiveSearch(
                        $disk,
                        $searchQuery,
                        $itemPath,
                        $maxDepth,
                        $currentDepth + 1,
                        $maxResults - count($results)
                    );

                    $results = array_merge($results, $subResults);
                }
            }
        } catch (\Exception $e) {
            Log::error("Error searching directory {$path}: " . $e->getMessage());
        }

        return $results;
    }

    /**
     * Handle file uploads
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadFile(Request $request)
    {
        try {
            // Validate incoming request
            $request->validate([
                'file' => 'required|file|max:10240', // 10MB max file size
                'path' => 'required|string',
                'disk' => 'nullable|string|in:file_server,holding_smb',
            ]);

            $path = $request->input('path', '/');
            $diskName = $request->input('disk', 'holding_smb');
            $file = $request->file('file');

            if (!$file) {
                return $this->errorResponse('No file provided', null, 400);
            }

            // Initialize the storage disk
            $disk = Storage::disk($diskName);

            // Check if directory exists
            if (!$disk->exists($path)) {
                if ($path === '/') {
                    throw new \Exception("Unable to connect to file server");
                }
                return $this->errorResponse("Directory not found: {$path}", null, 404);
            }

            // Generate target path - use the current path from request
            $targetPath = rtrim($path, '/') . '/' . $file->getClientOriginalName();

            // Check if file already exists
            if ($disk->exists($targetPath)) {
                // Generate a unique filename by appending a timestamp
                $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $newFilename = $filename . '_' . time() . '.' . $extension;
                $targetPath = rtrim($path, '/') . '/' . $newFilename;
            }

            // Store the file on the disk
            $fileStream = fopen($file->getRealPath(), 'r');
            $disk->put($targetPath, $fileStream);
            fclose($fileStream);

            // Get file information using individual methods instead of getMetadata()
            $fileSize = null;
            $lastModified = null;

            try {
                $fileSize = $disk->size($targetPath);
            } catch (\Exception $e) {
                $fileSize = $file->getSize();
                Log::warning("Could not get file size from storage: {$e->getMessage()}");
            }

            try {
                $lastModified = $disk->lastModified($targetPath);
            } catch (\Exception $e) {
                $lastModified = time();
                Log::warning("Could not get last modified time: {$e->getMessage()}");
            }

            // Log successful upload
            Log::info("File uploaded successfully", [
                'path' => $targetPath,
                'disk' => $diskName,
                'size' => $fileSize
            ]);

            return $this->successResponse('File uploaded successfully', [
                'name' => basename($targetPath),
                'path' => $targetPath,
                'size' => $fileSize,
                'last_modified' => $lastModified,
                'type' => 'file',
                'extension' => $file->getClientOriginalExtension(),
            ]);

        } catch (\Exception $e) {
            Log::error('File upload error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return $this->errorResponse('Error uploading file: ' . $e->getMessage(), null, 500);
        }
    }
}
