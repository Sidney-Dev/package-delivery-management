<?php

namespace App\Jobs;

use App\Actions\Reporting\MarkEndOfDayReturnsAction;
use App\Models\Package;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MarkEndOfDayReturnsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $cityId, public string $eodTime, public ?string $reason = null)
    {
    }

    public function handle(MarkEndOfDayReturnsAction $action): void
    {
        $packages = Package::query()
            ->where('city_id', $this->cityId)
            ->get(['id','city_id','status','return_reason'])
            ->map(fn($p) => $p->toArray())
            ->all();

        $result = $action($packages, [
            'city_id' => $this->cityId,
            'eod_time' => $this->eodTime,
            'reason' => $this->reason,
        ]);

        // Persist updates
        foreach ($result['updated_packages'] as $pkg) {
            if (($pkg['status'] ?? null) === 'return') {
                Package::whereKey($pkg['id'])->update([
                    'status' => 'return',
                    'return_reason' => $pkg['return_reason'] ?? $this->reason,
                ]);
            }
        }
    }
}
