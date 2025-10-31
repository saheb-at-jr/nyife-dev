<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller as BaseController;
use App\Models\AutoReply;
use App\Models\Chat;
use App\Models\ChatLog;
use App\Models\ChatTicket;
use App\Models\ChatTicketLog;
use App\Models\Contact;
use App\Models\Organization;
use App\Models\Team;
use App\Models\User;
use App\Services\ChatService;
use App\Services\WhatsappService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Redirect;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ChatTicketController extends BaseController
{
    public function index(Request $request, $uuid = null)
    {
         Log::info("This is request index");
        Log::info($request  );
    }

    public function update(Request $request, $uuid)
    { 
        Log::info("This is request update");
        Log::info($request  );
        $contact = Contact::where('uuid', $uuid)->first();

        ChatTicket::where('contact_id', $contact->id)->update([
            'status' => $request->status,
            'assigned_to' => auth()->user()->id
        ]);

        $statusDescription = $request->status == 'closed' ? 'opened to closed' : 'closed to open';

        $ticketId = ChatTicketLog::insertGetId([
            'contact_id' => $contact->id,
            'description' => 'Conversation was moved from ' . $statusDescription,
            'created_at' => now()
        ]);

        ChatLog::insert([
            'contact_id' => $contact->id,
            'entity_type' => 'ticket',
            'entity_id' => $ticketId,
            'created_at' => now()
        ]);

        return Redirect::back()->with(
            'status', [
                'type' => 'success', 
                'message' => __('Status updated successfully!')
            ]
        );
    }

    public function assign(Request $request, $uuid)
    {
        Log::info("This is id from session");

        Log::info("This is request assign");
        Log::info($request);

        $contact = Contact::where('uuid', $uuid)->first();
        $team = Team::where('organization_id', session()->get('current_organization'))
                    ->where('user_id', $request->id)
                    ->first();

        Log::info("team");
        Log::info($team);

        $user = User::where('id', $request->id)->first();
        
        if (!$team) {
            return Redirect::back()->with(
                'status', [
                    'type' => 'error',
                    'message' => __('Team not found!')
                ]
            );
        }

        // ✅ Check if team is active
        if ($team->status !== 'active') {
            return Redirect::back()->with(
                'status', [
                    'type' => 'error',
                    'message' => __('Team is not active, cannot assign ticket.')
                ]
            );
        }

        if ($user) {
            ChatTicket::where('contact_id', $contact->id)->update([
                'assigned_to' => $request->id
            ]);

            $ticketId = ChatTicketLog::insertGetId([
                'contact_id' => $contact->id,
                'description' => 'Conversation was assigned to ' . $user->first_name . ' ' . $user->last_name,
                'created_at' => now()
            ]);

            ChatLog::insert([
                'contact_id' => $contact->id,
                'entity_type' => 'ticket',
                'entity_id' => $ticketId,
                'created_at' => now()
            ]);

            return Redirect::back()->with(
                'status', [
                    'type' => 'success', 
                    'message' => __('Ticket assigned successfully!')
                ]
            );
        }

        return Redirect::back()->with(
            'status', [
                'type' => 'error',
                'message' => __('User not found!')
            ]
        );
    }


     public function updateTeamStatus(Request $request, $uuid)
    {
        Log::info("This is request updateTeamStatus");
        Log::info($request);

        $team = Team::where('uuid', $uuid)
                    ->where('organization_id', session()->get('current_organization'))
                    ->first();

        Log::info("team");
        Log::info($team);

        if (!$team) {
            return Redirect::back()->with(
                'status', [
                    'type' => 'error',
                    'message' => __('Team not found!')
                ]
            );
        }

        // ✅ Toggle status
        $newStatus = $team->status === 'active' ? 'suspended' : 'active';
        Log::info($newStatus);

        $team->update(['status' => $newStatus]);

        Log::info("Team status updated to: " . $newStatus);

        return Redirect::back()->with(
            'status', [
                'type' => 'success',
                'message' => __('Team status updated to ' . ucfirst($newStatus) . ' successfully!')
            ]
        );
    }


}