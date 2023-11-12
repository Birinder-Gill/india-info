<?php

namespace App\Http\Controllers;

use App\Jobs\seedBusinessContacts;
use App\Models\CategoryName;
use Illuminate\Http\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\BusinessContact;
use App\Models\PaginationLog;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Http;

class BusinessContactController extends Controller
{
    protected $cat;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    function getCategoryContacts(Request $request)
    {
        // $fileName = CategoryName::first()->name;
        // return response()->download(storage_path("app/".$fileName));
        // $catId = ($request->cat);
        // $cat = CategoryName::find($catId);
        seedBusinessContacts::dispatch();
        // $this->handle();
    }

    function handle()
    {
        $id = CategoryName::first();
        //    foreach ($ids as $id) {
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
        $this->parseHtml($this->cat->link,'',1, $fileHandle);

        //    }
    }

    function parseHtml(string $url, $webPage = '', $realPage = 1,$fileHandle)
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
                if ($row->getAttribute('class') === "border-bottom") {
                    $result = array_values($this->extractInformation($row, $webPage, $realPage));
                    $this->addRowToCSV($fileHandle, $result) ;
                }
            }


        } catch (\Throwable $th) {
         dd($th);
        }
        $aTags = $lastRow->getElementsByTagName('a');
        if ($aTags->length) {
            $href = $aTags[1]->getAttribute('href');
            $rp = $realPage + 1;
            $ep = explode("/", $href);
            $wp = end($ep);
            $this->parseHtml($href, $wp, $rp,$fileHandle);
        } else {
            fclose($fileHandle);
            dd("We are done");
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
            "cat_id" => $this->cat->id."",
            "cat_name" => $this->cat->name,
            "web_page" => $webPage."",
            "real_page" => $realPage."",
        ];
        // );
    }



    /**
     * Adds a row to the CSV file.
     */
    private function addRowToCSV($fileHandle, $row)
    {
        fputcsv($fileHandle, $row);
    }
}
