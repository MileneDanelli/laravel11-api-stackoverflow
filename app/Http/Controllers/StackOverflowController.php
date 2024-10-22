<?php

namespace App\Http\Controllers;

use App\Models\StackOverflowQuery;
use Carbon\Carbon;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

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

        $response = $this->client->get($url);
        $data = json_decode($response->getBody()->getContents(), true);

        StackOverflowQuery::create([
            'query' => json_encode($queryParams),
            'response' => json_encode($data),
        ]);

        return $data;
    }
}
