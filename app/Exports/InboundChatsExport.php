<?php

namespace App\Exports;

use App\Models\Chat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class InboundChatsExport implements FromCollection, WithHeadings
{
    protected $days;
    protected $organizationId;

    public function __construct($days, $organizationId)
    {
        $this->days = $days;
        $this->organizationId = $organizationId;

        Log::info('InboundChatsExport initialized', [
            'days' => $days,
            'organization_id' => $organizationId,
        ]);
    }

    public function collection()
    {
        try {
            $startDate = Carbon::now()->subDays($this->days)->startOfDay();
            $endDate   = Carbon::now()->endOfDay();

            Log::info('Fetching inbound chats', [
                'organization_id' => $this->organizationId,
                'start_date' => $startDate->toDateTimeString(),
                'end_date' => $endDate->toDateTimeString(),
            ]);

            $chats = Chat::where('organization_id', $this->organizationId)
                ->where('type', 'inbound')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->with('contact')
                ->get();

            Log::info('Inbound chats fetched', [
                'total_chats' => $chats->count(),
            ]);

            $mapped = $chats->map(function ($chat) {
                $metadata = json_decode($chat->metadata, true);

                $row = [
                    'Chat ID'    => $chat->id,
                    'Contact ID' => $chat->contact_id,
                    'Contact'    => $metadata['from'] ?? '',
                    'Message'    => $metadata['button']['payload'] ?? ($metadata['text']['body'] ?? ''),
                    'Created At' => Carbon::parse($chat->created_at)->toDateTimeString(),
                ];

                Log::debug('Mapped chat row', $row);

                return $row;
            });

            Log::info('Final mapped chat data ready for export', [
                'export_count' => $mapped->count(),
            ]);

            return $mapped;
        } catch (\Exception $e) {
            Log::error('Error during InboundChatsExport', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return collect(); // Return empty collection if an error occurs
        }
    }

    public function headings(): array
    {
        return ['Chat ID', 'Contact ID', 'Contact', 'Message', 'Created At'];
    }
}
