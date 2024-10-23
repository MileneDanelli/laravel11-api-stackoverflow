<?php

namespace App\Http\Controllers;

use App\Models\StackOverflowQuery;
use Carbon\Carbon;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;

class StackOverflowController extends Controller
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function getQuestions(Request $request)
    {
        $tagged = $request->input('tagged');
        $todate = $request->input('todate');
        $fromdate = $request->input('fromdate');

        $url = 'https://api.stackexchange.com/2.3/questions?order=desc&sort=activity&site=stackoverflow';

        $queryParams = [];
        if ($tagged) {
            $queryParams['tagged'] = $tagged;
        }
        if ($fromdate) {
            $queryParams['fromdate'] = Carbon::parse($fromdate)->timestamp;
        }
        if ($todate) {
            $queryParams['todate'] = Carbon::parse($todate)->endOfDay()->timestamp;
        }

        if (!empty($queryParams)) {
            $url .= '&' . http_build_query($queryParams);
        }

        try {
            $response = $this->client->get($url);
            $data = json_decode($response->getBody()->getContents(), true);

            StackOverflowQuery::create([
                'query' => json_encode($queryParams),
                'response' => json_encode($data),
            ]);

            return response()->json($data, 200);
        } catch (RequestException $e) {
            $errorMessage = $e->hasResponse()
                ? $e->getResponse()->getBody()->getContents()
                : $e->getMessage();

            return response()->json([
                'error' => 'Request error to StackOverflow API',
                'message' => $errorMessage,
            ], $e->getCode() ?: 500);
        } catch (ConnectException $e) {
            return response()->json([
                'error' => 'Connection error',
                'message' => 'Failed to connect to the StackOverflow API',
            ], 503);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Unexpected error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
