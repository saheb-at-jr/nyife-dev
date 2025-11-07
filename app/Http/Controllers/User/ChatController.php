<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller as BaseController;
use App\Services\ChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Redirect;

class ChatController extends BaseController
{
    public function downloadInboundChat(Request $request, $uuid = null)
    {
        Log::info('ChatController@downloadInboundChat called', [
            'uuid' => $uuid,
            'organization_id' => session()->get('current_organization'),
            'query' => $request->query(),
        ]);

        return $this->chatService()->getInboundChats($request, $uuid, $request->query('search'));
    }

    private function chatService()
    {
        Log::debug('Instantiating ChatService', [
            'organization_id' => session()->get('current_organization'),
        ]);

        return new ChatService(session()->get('current_organization'));
    }

    public function index(Request $request, $uuid = null)
    {
        Log::info('ChatController@index called', [
            'uuid' => $uuid,
            'query' => $request->query(),
        ]);

        return $this->chatService()->getChatList($request, $uuid, $request->query('search'));
    }

    public function updateChatSortDirection(Request $request)
    {
        Log::debug('ChatController@updateChatSortDirection', [
            'sort' => $request->sort,
        ]);

        $request->session()->put('chat_sort_direction', $request->sort);

        return Redirect::back();
    }

    public function sendMessage(Request $request)
    {
        Log::info('ChatController@sendMessage called', [
            'payload' => $request->all(),
        ]);

        return $this->chatService()->sendMessage($request);
    }

    public function sendTemplateMessage(Request $request, $uuid)
    {
        Log::info('ChatController@sendTemplateMessage called', [
            'uuid' => $uuid,
            'payload' => $request->all(),
        ]);

        $res = $this->chatService()->sendTemplateMessage($request, $uuid);

        Log::debug('Template message response', [
            'uuid' => $uuid,
            'response' => $res,
        ]);

        return Redirect::back()->with(
            'status', [
                'type' => $res->success === true ? 'success' : 'error',
                'message' => $res->success === true ? __('Message sent successfully!') : $res->message,
                'res' => $res,
            ]
        );
    }

    public function deleteChats($uuid)
    {
        Log::warning('ChatController@deleteChats called', [
            'uuid' => $uuid,
        ]);

        $this->chatService()->clearContactChat($uuid);

        return Redirect::back()->with(
            'status', [
                'type' => 'success',
                'message' => __('Chat cleared successfully!'),
            ]
        );
    }

    public function loadMoreMessages(Request $request, $contactId)
    {
        $page = $request->query('page', 1);
        Log::debug('ChatController@loadMoreMessages called', [
            'contact_id' => $contactId,
            'page' => $page,
        ]);

        $messages = $this->chatService()->getChatMessages($contactId, $page);

        Log::debug('Messages fetched', [
            'count' => count($messages ?? []),
            'contact_id' => $contactId,
            'page' => $page,
        ]);

        return response()->json($messages);
    }
}
