<?php

namespace App\Jobs;

use App\Models\CrawelLog;
use App\Models\CrawlLog;
use App\Models\Product;
use App\Models\Rate;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

class CrawlingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private $page)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $page = $this->page;
            $log = CrawlLog::whereUrl($page['loc'])->first();
            if ($log) {
                if ($log['last_modified_at']->timestamp >= strtotime($page['lastmod'])) {
                    return;
                }
            }
            $client = new Client();
            $req = $client->get($page['loc']);
            $res = $req->getBody();
            if (preg_match('/<body.*class.*profile.*>/', $res)) {
                $symbol = '';
                $price = 0;
                if (preg_match('/<div.*line.*header-tag.*>\s<div>(.*)<\/div>/', $res, $symbol)) {
                    if (preg_match('/<span.*PDrCotVal.*>(.*)<\/span>/', $res, $price)) {
                        $symbol = preg_replace('/<div.*>\s<div>/', '', $symbol[0]);
                        $symbol = str_replace('</div>', '', $symbol);
                        $price = preg_replace('/<\/span>/', '', $price[0]);
                        $price = preg_replace('/<span.*>/', '', $price);
                        $price = str_replace(',', '', $price);
                        $price = floatval($price);
                        Rate::create([
                            'symbol' => $symbol,
                            'price' => $price
                        ]);
                    }
                }
            }

            CrawlLog::create([
                'last_modified_at' => new Carbon($page['lastmod']),
                'url' => $page['loc']
            ]);
        } catch (ClientException $err) {
            if ($err->getCode() == 410) {
                Cache::put('queue_stop', now()->addMinutes(5));
            }
            Log::info($err->getMessage());
        }
    }
}