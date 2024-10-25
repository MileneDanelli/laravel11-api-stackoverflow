<?php

namespace App\Http\Controllers;

use App\Models\StackOverflowQuery;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Encryption\DecryptException;

class StackOverflowController extends Controller
{
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getQuestions(Request $request)
    {
        $request->validate([
            'tagged' => 'nullable|string',
            'fromdate' => 'nullable|date',
            'todate' => 'nullable|date|after_or_equal:fromdate',
        ]);

        $queryParams = $this->buildQueryParams($request);
        $cacheKey = 'stackoverflow_' . md5(json_encode($queryParams));

        try {
            if (Cache::has($cacheKey)) {
                $cachedResponse = Cache::get($cacheKey);
                return response()->json(json_decode($cachedResponse, true), 200);
            }

            $cachedQuery = StackOverflowQuery::where('query', json_encode($queryParams))->first();

            if ($cachedQuery) {
                $decryptedResponse = json_decode($cachedQuery->response, true);
                Cache::put($cacheKey, json_encode($decryptedResponse), 3600);
                return response()->json($decryptedResponse, 200);
            }

            $url = $this->buildUrlWithQuery('https://api.stackexchange.com/2.3/questions', $queryParams);
            $response = $this->client->get($url);
            $data = json_decode($response->getBody()->getContents(), true);

            StackOverflowQuery::create([
                'query' => json_encode($queryParams),
                'response' => json_encode($data),
            ]);

            Cache::put($cacheKey, json_encode($data), 3600);
            return response()->json($data, 200);

        } catch (GuzzleException $e) {
            Log::error('StackOverflow API request error', ['message' => $e->getMessage()]);
            return response()->json([
                'error' => 'StackOverflow API request error',
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 500);
        } catch (\Exception $e) {
            Log::critical('Unexpected error in StackOverflowController', ['message' => $e->getMessage()]);
            return response()->json([
                'error' => 'Unexpected error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function buildQueryParams(Request $request): array
    {
        $queryParams = [
            'order' => 'desc',
            'sort' => 'activity',
            'site' => 'stackoverflow',
        ];

        if ($request->filled('tagged')) {
            $queryParams['tagged'] = $request->input('tagged');
        }

        if ($request->filled('fromdate')) {
            $queryParams['fromdate'] = Carbon::parse($request->input('fromdate'))->timestamp;
        }

        if ($request->filled('todate')) {
            $queryParams['todate'] = Carbon::parse($request->input('todate'))->endOfDay()->timestamp;
        }

        return $queryParams;
    }

    private function buildUrlWithQuery(string $baseUrl, array $queryParams): string
    {
        return $baseUrl . '?' . http_build_query($queryParams);
    }
}
