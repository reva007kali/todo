<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AIService
{
    public function parseTask(string $prompt)
    {
        $apiKey = env('OPENAI_API_KEY');
        $today = now()->format('Y-m-d H:i:s');

        // Instruksi kita perjelas untuk Priority
        $systemPrompt = "
            You are a smart personal assistant. 
            Current Date/Time: {$today}.
            
            Analyze the user input and extract task details into a valid JSON object.
            
            Rules for extraction:
            1. Title: A short summary of the task.
            2. Description: Detailed info (if any).
            3. Due Date: Format 'YYYY-MM-DD HH:mm:ss'. If time is not specified, assume 09:00:00. If no date, set null.
            4. Priority: Analyze the urgency based on words like 'urgent', 'penting', 'segera', 'asap', 'darurat'.
               - If urgent/important -> 'High'
               - If standard/casual -> 'Middle'
               - If trivial/later -> 'Low'
               - Default to 'Low' if unsure.
               - Allowed values: 'Low', 'Middle', 'High'.
            
            Return ONLY this JSON structure:
            {
                'title': 'string',
                'description': 'string',
                'priority': 'string',
                'due_date': 'string or null'
            }
        ";

        $response = Http::withToken($apiKey)->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o-mini',
            'response_format' => ['type' => 'json_object'],
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.3, // Kita turunkan supaya hasilnya lebih konsisten/tegas
        ]);

        return $response->json()['choices'][0]['message']['content'];
    }

    public function generateBriefing($tasksList)
    {
        $apiKey = env('OPENAI_API_KEY');
        $today = now()->format('l, d F Y');

        // PERBAIKAN DI SINI:
        $systemPrompt = "
        You are a friendly assistant. Today is {$today}.
        
        Your Goal: Read the list of user's pending tasks and generate a 'Daily Briefing'in bahasa indonesia.
        
        Output format: 
        - Use simple HTML tags only (<span>, <ul>, <li>, <strong>, <br>).
        - Do NOT use <html>, <head>, <body>, or <!DOCTYPE>.
        - Do NOT use Markdown (no # or *).
        - Keep it motivating, concise, and professional.
        
        Structure:
        1. <span>Short Motivating opening.</span><br>
        2. <ul>List of key focus areas.</ul><br>
        3. <span><strong>Estimated effort:</strong> [Time]</span>
        
        If the list is empty, return: '<span>You are all caught up! Enjoy your day.</span>'
    ";

        $response = Http::withToken($apiKey)->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => "Here are my pending tasks:\n" . $tasksList],
            ],
        ]);

        return $response->json()['choices'][0]['message']['content'];
    }
}