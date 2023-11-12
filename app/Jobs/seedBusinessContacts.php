<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\BusinessContact;
use App\Models\CategoryName;
use App\Models\PaginationLog;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Http;

class seedBusinessContacts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $cat;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $ids = CategoryName::all();
        foreach ($ids as $id) {
            $this->cat = $id;
            $fileName = $this->cat->name . '.csv';
            $fileHandle = fopen(storage_path("app/public/" . $fileName), 'w');
            // Optional: Add CSV headers (if needed)
            fputcsv($fileHandle, [
                "title",
                "link",
                "address",
                "contact",
                "cat_id",
                "cat_name",
                "web_page",
                "real_page",
            ]);
            $this->parseHtml($this->cat->link, '', 1, $fileHandle);
        }
    }

    function parseHtml(string $url, $webPage = '', $realPage = 1, $fileHandle)
    {
        $response = Http::get($url);
        $body = $response->body();
        $dom = new DOMDocument();
        @$dom->loadHTML((string)$body);
        $elements = $dom->getElementsByTagName('table');
        $table = $elements[0];
        $rows = $table->getElementsByTagName('tr');

        $firstRow = $rows->item(0);
        $firstRow->parentNode->removeChild($firstRow);

        if ($rows->length > 0) {
            $lastRow = $rows->item($rows->length - 1);
            $lastRow->parentNode->removeChild($lastRow);
        }
        try {
            foreach ($rows as $row) {
                if ($row->getAttribute('class') === "border-bottom")
                    fputcsv($fileHandle, $this->extractInformation($row, $webPage, $realPage));
            }

            PaginationLog::create([
                'table_name' => 'BusinessContacts',
                'job_name' =>  storage_path("app/public/" . "3d.csv"),
                'at_page' =>  $realPage,
                'success_code' => 1,
            ]);
        } catch (\Throwable $th) {
            PaginationLog::create([
                'table_name' => 'BusinessContacts',
                'job_name' =>  'SeedBusinessContacts',
                'at_page' =>  $realPage,
                'success_code' => 0,
            ]);
        }
        $aTags = $lastRow->getElementsByTagName('a');
        if ($aTags->length) {
            $href = $aTags[1]->getAttribute('href');
            $rp = $realPage + 1;
            $ep = explode("/", $href);
            $wp = end($ep);
            $this->parseHtml($href, $wp, $rp, $fileHandle);
        } else {
            //We are done
            fclose($fileHandle);
        }
    }

    function extractInformation($element, $webPage, $realPage)
    {
        $xpath = new DOMXPath($element->ownerDocument);

        // Extracting the h3 title
        $titleNode = $xpath->evaluate('.//h3/a', $element)->item(0);
        $title = $titleNode ? $titleNode->nodeValue : 'Not Found';

        // Extracting the href attribute
        $href = $titleNode ? $titleNode->getAttribute('href') : 'Not Found';

        // Extracting the contact information
        $contactNode = $xpath->evaluate('.//p[contains(.,"Contact")]', $element)->item(0);
        $contact = $contactNode ? explode(":", $contactNode->nodeValue)[1] : 'Not Found';

        // Extracting the address
        $addressNode = $xpath->evaluate('.//p[contains(.,"Address")]', $element)->item(0);
        $address = $addressNode ? explode(":", $addressNode->nodeValue)[1] : 'Not Found';

        // Printing the extracted information
        // BusinessContact::create(
        return [
            "title" => $title,
            "link" => $href,
            "address" => $address,
            "contact" => $contact,
            "cat_id" => $this->cat->id,
            "cat_name" => $this->cat->name,
            "web_page" => $webPage,
            "real_page" => $realPage,
        ];
        // );
    }
}
