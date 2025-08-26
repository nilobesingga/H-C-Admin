<?php

use App\Models\Bitrix\UserProfile;
use App\Models\Contact;
use App\Models\TaskModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

function getCurrentDateAndTime()
{
    return Carbon::now()->format('Y-m-d H:i:s');
}

function getDateOFLast60Days()
{
    return Carbon::now()->subDays(60)->format('Y-m-d');
}
function getLastDateOfMonthAfterThreeYears()
{
    return Carbon::now()->addYears(3)->month(12)->day(31)->format('Y-m-d');
}

function profile()
{
    $profile = UserProfile::where('user_id', Auth::id())->first();
    $profile['profilePhoto'] = $profile->photo ? Storage::url($profile->photo) : '/storage/images/logos/CRESCO_icon.png';
    return $profile;
}

function getFirstChars($string, $uppercase = false) {
    // Split the string into words
    $words = explode(' ', trim($string));

    // Extract the first character from each word
    $firstChars = '';
    foreach ($words as $word) {
        if (!empty($word)) {
            $firstChars .= mb_substr($word, 0, 1); // Using mb_substr for UTF-8 support
        }
    }

    // Convert to uppercase if requested
    if ($uppercase) {
        $firstChars = mb_strtoupper($firstChars);
    }

    return $firstChars;
}

function generateRequestNo()
{
    $prefix = 'REQ';
    $currentYear = date('y');

    // Get the latest reference number for the current year
    $lastRef = DB::table('request')
        ->select(DB::raw('MAX(SUBSTRING_INDEX(request_no, "-", -1)) as last_count'))
        ->whereRaw('request_no LIKE ?', ["$prefix-$currentYear-%"])
        ->first();

    // Start counter at 0 if no previous references or start a new year
    $count = $lastRef && isset($lastRef->last_count) ? (int)$lastRef->last_count : 0;

    // Increment counter
    $count++;

    // Format counter to 3 digits
    $formattedCount = str_pad($count, 3, '0', STR_PAD_LEFT);

    // Generate reference number
    $referenceNumber = sprintf('%s-%s-%s', $prefix, $currentYear, $formattedCount);

    // No need to store in the table here as this would typically be
    // done when creating the actual statement record

    return $referenceNumber;
}

function getChangeRequestCount($type='document_type')
{
    // if($company_id == 0) {
    //     return 0; // Return 0 if user is not authenticated
    // }
    // $contactId = Auth::user()->bitrix_contact_id;
    $pendingCount = DB::table('request')
        ->where('status', 'pending')
        ->where('type', $type)
        // ->where('contact_id', $contactId)
        // ->where('company_id', $company_id)
        ->count();
    return $pendingCount;
}

function getOpenTaskCount()
{
    $tasks = TaskModel::whereExists(function ($query) {
            $query->select('user_id')
                    ->from('task_users')
                    ->whereColumn('task_users.task_id', 'task.id')
                    ->where('task_users.user_id', Auth::id());
        })
        ->where('user_id', Auth::id())
        ->where('status', 'open')
        ->count();
    return $tasks;
}

function getSetupRequestCount()
{
    $setupRequestCount = DB::table('company_setup')

        ->count();
    return $setupRequestCount;
}

function getDataChangeRequestCount()
{
    $changeRequestCount = DB::table('change_request')
        ->where('status', 'pending')
        ->count();
    return $changeRequestCount;
}


function getUserModule($title = null, $company_id = null)
{
    $user = User::with([
        'modules' => function ($q) {
            $q->where('parent_id', 0)->orderBy('order', 'ASC');
        },
        'modules.children' => function ($q) {
            $q->join('user_module_permission as ump', function ($join) {
                $join->on('modules.id', '=', 'ump.module_id')
                    ->where('modules.admin', 1)
                    ->where('ump.user_id', Auth::id());
            })
            ->orderBy('modules.order', 'ASC');
        },
        'categories',
        'userprofile'
    ])->whereId(Auth::id())->first();

    $module = [];
    if ($user) {
        $module = $user->modules->map(function ($module) {
            $count = 0;
            if ($module->route == 'setup-request') {
                $count = getSetupRequestCount();
            }
            elseif ($module->route == 'task') {
                $count = getOpenTaskCount();
            }
            elseif ($module->route == 'change-request') {
                $count = getChangeRequestCount('change_request');
            }
            elseif ($module->route == 'document-request') {
                $count = getChangeRequestCount('document_request');
            }
            elseif ($module->route == 'data-request') {
                $count = getDataChangeRequestCount();
            }
            return [
                'id' => $module->id,
                'name' => $module->name,
                'route' => $module->route,
                'icon' => $module->icon,
                'count' => $count,
                'children' => $module->children->map(function ($child) {
                    $count = 0;
                    if ($child->route == 'task') {
                        $count = getOpenTaskCount();
                    }
                    elseif ($child->route == 'document-request') {
                        $count = getChangeRequestCount('document_request');
                    }
                     elseif ($child->route == 'change-request') {
                        $count = getChangeRequestCount('change_request');
                    }

                    return [
                        'id' => $child->id,
                        'name' => $child->name,
                        'icon' => $child->icon,
                        'route' => $child->route,
                        'count' => $count,
                    ];
                })->toArray(),
            ];
        })->toArray();
    }
    $page = (object) [
        'title' => $title,
        'identifier' => 'dashboard',
        'user' => $user,
    ];
    return compact('page', 'module');
}

