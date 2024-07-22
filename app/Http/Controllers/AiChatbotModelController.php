<?php

namespace App\Http\Controllers;

use App\Models\AiModel;
use Illuminate\Http\Request;

class AiChatbotModelController extends Controller
{
    public function index()
    {
        $aiModels = AiModel::with('tokens')
            ->whereHas('tokens', function ($query) {
                $query->where('type', 'word');
            })
            ->whereIn('ai_engine', ['openai', 'anthropic', 'gemini'])
            ->whereNotIn('key', ['tts-1', 'tts-1-hd', 'whisper-1'])
            ->where('is_active', true)->get();
        $groupedAiModels = $aiModels->groupBy('ai_engine');

        return view('panel.admin.chatbot.ai-models', compact('groupedAiModels'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'selected_title.*' => 'required',
        ]);

        foreach ($data['selected_title'] as $key => $value) {
            AiModel::query()
                ->where('id', $key)
                ->update([
                    'selected_title' => $value,
                ]);
        }

        AiModel::query()->update([
            'is_selected' => false,
        ]);

        $is_selected = $request->input('is_selected');

        if ($is_selected) {
            foreach ($request['is_selected'] as $key => $value) {
                AiModel::query()
                    ->where('id', $key)
                    ->update([
                        'is_selected' => true,
                    ]);
            }
        }


        return redirect()->back()->with([
            'message' => 'AI Models updated successfully',
            'type' => 'success'
        ]);
    }
}
