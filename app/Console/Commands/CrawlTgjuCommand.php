<?php

namespace App\Console\Commands;

use App\Jobs\CrawlingJob;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use League\CommonMark\Xml\XmlRenderer;

class CrawlTgjuCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawl:tgju';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawling the tgju site data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $client = new Client();
        $req = $client->get('https://www.tgju.org/sitemap.xml');
        $res = $req->getBody();
        $xml = simplexml_load_string($res);
        $json = json_encode($xml);
        $sitemapData = json_decode($json, true);
        $urls = $sitemapData['url'];
        $bar = $this->output->createProgressBar(count($urls));
        foreach ($urls as $page) {
            // Log::info(json_encode($page));
            CrawlingJob::dispatchSync($page);
            $bar->advance();
        }
        $bar->finish();
    }
}