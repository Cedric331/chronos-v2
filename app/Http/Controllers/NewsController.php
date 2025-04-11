<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use SimpleXMLElement;

class NewsController extends Controller
{
    /**
     * Récupère les actualités depuis Universfreebox.com
     */
    public function fetchNews(Request $request)
    {
        $request->validate([
            'source' => 'required|string',
            'maxItems' => 'nullable|integer|min:1|max:20',
        ]);

        $source = $request->source;
        $maxItems = $request->maxItems ?? 10;
        $cacheKey = "news_{$source}_{$maxItems}";
        $cacheDuration = 60; // minutes (1 heure)
        $lastRequestKey = "news_last_request_global";
        $now = now();

        // Vérifier si une requête a été faite récemment (moins de 10 minutes)
        $lastRequestTime = Cache::get($lastRequestKey);

        if ($lastRequestTime && $now->diffInMinutes($lastRequestTime) < 10) {
            // Si une requête a été faite il y a moins de 10 minutes, utiliser les données en cache
            if (Cache::has($cacheKey)) {
                return response()->json(Cache::get($cacheKey));
            }
        }

        // Vérifier si les données sont en cache
        if (Cache::has($cacheKey)) {
            return response()->json(Cache::get($cacheKey));
        }

        try {
            // URL du flux RSS d'Universfreebox
            $rssUrl = 'https://www.universfreebox.com/rss';

            // Récupérer le contenu du flux RSS
            $response = Http::timeout(5)->get($rssUrl);

            if ($response->successful()) {
                $xml = new SimpleXMLElement($response->body());
                $items = [];

                // Parcourir les éléments du flux RSS
                $count = 0;
                foreach ($xml->channel->item as $item) {
                    if ($count >= $maxItems) break;

                    $items[] = [
                        'id' => md5((string)$item->link),
                        'title' => (string)$item->title,
                        'link' => (string)$item->link,
                        'description' => (string)$item->description,
                        'pubDate' => (string)$item->pubDate,
                        'source' => 'universfreebox'
                    ];

                    $count++;
                }

                $result = [
                    'items' => $items,
                    'source' => $source,
                    'timestamp' => now()->toIso8601String()
                ];

                // Mettre en cache les résultats
                Cache::put($cacheKey, $result, $cacheDuration * 60);

                // Mettre à jour le timestamp de la dernière requête globale
                Cache::put($lastRequestKey, $now, 60 * 10); // Expire après 10 minutes

                return response()->json($result);
            } else {
                return response()->json(['error' => 'Impossible de récupérer le flux RSS'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la récupération des actualités: ' . $e->getMessage()], 500);
        }
    }
}
