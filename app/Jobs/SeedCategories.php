<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\CategoryName;
use App\Models\PaginationLog;
use DOMDocument;
use Illuminate\Support\Facades\Http;

class SeedCategories implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $gpage = 0;
        try {
            for ($page=0; $page < 81; $page++) {
                $gpage = $page;
                $url = 'https://www.indiainfo.net/cat/places-in-india/page/'.strval($page);
                $this->parseHtml($url,$page);
                PaginationLog::create([
                    'table_name' => 'CategoryName' ,
                    'job_name' =>  'SeedCategories',
                    'at_page' =>  $page,
                    'success_code' => 1 ,
                ]);
            }
        } catch (\Throwable $th) {
            PaginationLog::create([
                'table_name' => 'CategoryName' ,
                'job_name' =>  'SeedCategories',
                'at_page' =>  $gpage,
                'success_code' => 0 ,
            ]);
        }

    }
    function parseHtml(string $url,int $page) {
        $response = Http::get($url);
        $body = $response->body();
        $dom = new DOMDocument();
        @$dom->loadHTML((string)$body);
        $elements = $dom->getElementsByTagName('ul');
        $list = $elements[2];
        $this->fillList($list,$page);
    }
    function fillList($list,$page) {
        foreach ($list->getElementsByTagName('li') as $li) {
            $a = $li->getElementsByTagName('a')->item(0);
            $name = trim($li->nodeValue);
           CategoryName::create([
                'name' => $name,
                'link' => $a->getAttribute('href'),
                'at_page' => $page,
            ]);
        }
    }
}
