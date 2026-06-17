<?php

namespace App\Http\Controllers;

use App\Services\ChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatbotController extends Controller
{
    public function __construct(private ChatbotService $chatbot) {}

    // show the chat UI page
    public function index(): View
    {
        return view('chatbot.index');
    }

    // handle an incoming chat message and return a JSON response.
    public function message(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $response = $this->chatbot->reply($request->message);

        return response()->json([
            'success' => true,
            'response' => $response,
        ]);
    }
}
