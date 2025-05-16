<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Request as ItemRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class MatchingService
{
    /**
     * Encontrar ítems que coincidan con una solicitud.
     *
     * @param ItemRequest $request
     * @return Collection
     */
    public function findMatchingItems(ItemRequest $request)
    {
        // Comenzar búsqueda por categoría
        $query = Item::where('status', 'available')
            ->where('user_id', '!=', $request->user_id); // No mostrar los propios ítems del usuario

        // Misma categoría (factor principal de coincidencia)
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Si hay ubicación, preferir ítems de la misma zona
        if ($request->location) {
            // Primero intentamos con la misma ubicación exacta
            $exactLocationItems = clone $query;
            $exactLocationItems->where('location', $request->location);

            // Si hay resultados exactos, los devolvemos
            if ($exactLocationItems->count() > 0) {
                return $exactLocationItems->get();
            }

            // Si no hay coincidencia exacta, buscamos parcial
            $query->where(function($q) use ($request) {
                $locationParts = explode(' ', $request->location);
                foreach ($locationParts as $part) {
                    if (strlen($part) >= 4) { // Ignoramos palabras muy cortas
                        $q->orWhere('location', 'like', "%$part%");
                    }
                }
            });
        }

        // Extraer palabras clave del título y descripción de la solicitud
        $keywords = $this->extractKeywords(
            $request->title . ' ' . $request->description
        );

        // Ordenamos por relevancia basada en palabras clave
        $items = $query->get();

        // Calcular puntuación de coincidencia para cada ítem
        $scoredItems = $items->map(function($item) use ($keywords) {
            $score = 0;
            $itemContent = strtolower($item->title . ' ' . $item->description);

            // Buscar cada palabra clave en el contenido del ítem
            foreach ($keywords as $keyword) {
                // Mayor puntuación en el título que en la descripción
                $titleWeight = substr_count(strtolower($item->title), $keyword) * 3;
                $descWeight = substr_count(strtolower($item->description), $keyword);

                $score += $titleWeight + $descWeight;
            }

            $item->match_score = $score;
            return $item;
        });

        // Devolver solo ítems con alguna coincidencia y ordenar por puntuación
        return $scoredItems->filter(function($item) {
            return $item->match_score > 0;
        })->sortByDesc('match_score');
    }

    /**
     * Encontrar solicitudes que coincidan con un ítem.
     *
     * @param Item $item
     * @return Collection
     */
    public function findMatchingRequests(Item $item)
    {
        // Comenzar búsqueda por categoría
        $query = ItemRequest::where('status', 'active')
            ->where('user_id', '!=', $item->user_id); // No mostrar las propias solicitudes del usuario

        // Misma categoría (factor principal de coincidencia)
        if ($item->category_id) {
            $query->where('category_id', $item->category_id);
        }

        // Si hay ubicación, preferir solicitudes de la misma zona
        if ($item->location) {
            // Filtrado por ubicación similar
            $query->where(function($q) use ($item) {
                $locationParts = explode(' ', $item->location);
                foreach ($locationParts as $part) {
                    if (strlen($part) >= 4) { // Ignoramos palabras muy cortas
                        $q->orWhere('location', 'like', "%$part%");
                    }
                }
            });
        }

        // Extraer palabras clave del título y descripción del ítem
        $keywords = $this->extractKeywords(
            $item->title . ' ' . $item->description
        );

        // Ordenamos por relevancia basada en palabras clave
        $requests = $query->get();

        // Calcular puntuación de coincidencia para cada solicitud
        $scoredRequests = $requests->map(function($request) use ($keywords) {
            $score = 0;
            $requestContent = strtolower($request->title . ' ' . $request->description);

            // Buscar cada palabra clave en el contenido de la solicitud
            foreach ($keywords as $keyword) {
                // Mayor puntuación en el título que en la descripción
                $titleWeight = substr_count(strtolower($request->title), $keyword) * 3;
                $descWeight = substr_count(strtolower($request->description), $keyword);

                $score += $titleWeight + $descWeight;
            }

            $request->match_score = $score;
            return $request;
        });

        // Devolver solo solicitudes con alguna coincidencia y ordenar por puntuación
        return $scoredRequests->filter(function($request) {
            return $request->match_score > 0;
        })->sortByDesc('match_score');
    }

    /**
     * Extraer palabras clave de un texto.
     *
     * @param string $text
     * @return array
     */
    protected function extractKeywords($text)
    {
        // Convertir a minúsculas
        $text = strtolower($text);

        // Eliminar caracteres especiales
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);

        // Dividir en palabras
        $words = preg_split('/\s+/', $text);

        // Filtrar palabras vacías o muy cortas
        $words = array_filter($words, function($word) {
            return strlen($word) >= 4;
        });

        // Palabras comunes a eliminar (stopwords)
        $stopwords = [
            'para', 'esta', 'este', 'esto', 'estos', 'estas', 'esos', 'esas', 'aquellos', 'aquellas',
            'como', 'cuando', 'donde', 'cual', 'quien', 'quienes', 'porque', 'pero', 'sino', 'aunque',
            'desde', 'hasta', 'entre', 'hacia', 'sobre', 'bajo', 'durante', 'mediante', 'según', 'contra'
        ];

        // Eliminar stopwords
        $keywords = array_diff($words, $stopwords);

        // Devolver palabras únicas
        return array_unique($keywords);
    }
}
