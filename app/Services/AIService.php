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
           Anda adalah asisten pribadi yang cerdas. 
            Tanggal/Waktu Saat Ini: {$today}.

            Analisis input pengguna dan ekstrak detail tugas menjadi objek JSON yang valid.

            Aturan ekstraksi:
            1. Title: Ringkasan singkat dari tugas.
            2. Description: Informasi detail (jika ada).
            3. Due Date: Format 'YYYY-MM-DD HH:mm:ss'. Jika waktu tidak disebutkan, gunakan 09:00:00. Jika tidak ada tanggal, set null.
            4. Priority: Analisis urgensi berdasarkan kata seperti 'urgent', 'penting', 'segera', 'asap', 'darurat'.
            - Jika mendesak/penting -> 'High'
             - Jika standar/biasa -> 'Middle'
             - Jika sepele/nanti saja -> 'Low'
              - Default 'Low' jika tidak yakin.
              - Nilai yang diperbolehkan: 'Low', 'Middle', 'High'.

            Kembalikan HANYA struktur JSON berikut:
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