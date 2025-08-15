<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\MarkEndOfDayReturnsJob;
use App\Models\City;
use Illuminate\Http\Request;

class ReportingController extends Controller
{
    public function runEndOfDay(Request $request, City $city)
    {
        $data = $request->validate([
            'eod_time' => 'nullable|string',
            'reason' => 'nullable|string',
        ]);

        MarkEndOfDayReturnsJob::dispatch($city->id, $data['eod_time'] ?? now()->format('H:i'), $data['reason'] ?? null);

        return response()->json(['queued' => true]);
    }
}
