<?php

namespace App\Console\Commands;

use App\Models\CrawlLog;
use App\Models\Rate;
use DivisionByZeroError;
use ErrorException;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpParser\Error;

class Transaction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaction';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $i = 0;
        // try {
        //     DB::Transaction(function () use ($i) {
        //         Rate::create([
        //             'symbol' => 'ریال قودرت مند',
        //             'price' => 1
        //         ]);
        //         Log::info('Goodbye world');
        //         $b = 1 / $i++;
        //         // if ($i++ == 1) {
        //         //     throw new Exception('test');
        //         // }
        //         CrawlLog::create([
        //             // 
        //             'last_modified_at' => now(),
        //             'url' => 'https://google.com'
        //         ]);
        //     }, 2);
        // } catch (DivisionByZeroError $err) {
        // }
        DB::beginTransaction();
        for ($i = 0; $i < 2; $i++) {
            try {
                Rate::create([
                    'symbol' => 'ریال قودرت مند',
                    'price' => 1
                ]);
                if ($i == 0) {
                    throw new Exception("ridam");
                }
                CrawlLog::create([
                    'last_modified_at' => now(),
                    'url' => 'https://google.com'
                ]);
                DB::commit();
                break;
            } catch (Exception $err) {
                DB::rollBack();
            }
        }
    }
}