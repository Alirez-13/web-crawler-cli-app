<?php

namespace App\Console\Commands;

use App\Scraper\WebScraper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExtractData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:extract-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $name = $this->ask('What is your name?');
        echo 'Hello ' . $name;

        $user = $this->choice('What you wanna do?', ['Main domain data', 'Subdomain data']);

        if ($user == 'Main domain data') {
            $this->output->progressStart(100);

            for ($i = 0; $i < 100; $i++) {
                $this->output->progressAdvance();
            }

            $this->extractMainPageData();
            $this->output->progressFinish();

        } elseif ($user == 'Subdomain data') {
            $this->output->progressStart(100);

            for ($i = 0; $i < 100; $i++) {
                $this->output->progressAdvance();
            }
            $this->extractPageData();
            echo "Data Extracted successfully\n";
        }

    }

function extractMainPageData(): false|string
{
    $url = 'https://www.entekhab.ir';

    $webScraper = WebScraper::getInstance();
    $html = $webScraper->scrape($url);

    if ($html === false) {
        return false;
    }
    $cleanHtmlPage = strip_tags($html);
    $cleanHtmlPage = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $cleanHtmlPage);

    DB::table('pages')->insert([
        'URL_Path' => $url,
        'Plain_Text' => $cleanHtmlPage]);

    return "Main domain data saved successfully!";
}

function extractPageData()
{
    $url = 'https://www.entekhab.ir';

    $webScraper = WebScraper::getInstance();
    $html = $webScraper->scrape($url);

    if ($html === false) {
        return false;
    }
    $html = str_get_html($html);
    $subDomains = $html->find('a');
    $tempURL = array();

    foreach ($subDomains as $link) {
        if (str_contains($link->href, '/fa/news/')) {
            $tempURL[] = 'https://www.entekhab.ir' . $link->href;
        }
    }
    $removedDuplicationUrl = array_unique($tempURL);

    foreach ($removedDuplicationUrl as $uniqueUrl) {

        $tempContent = $webScraper->scrape($uniqueUrl);

        $cleaned_string = preg_replace('/<[^>]+>/', '', $tempContent);
        $cleaned_string = preg_replace('/<script\b[^>]*>(.*?)<\/script>/', '', $cleaned_string);

        if (DB::table('pages')->insert([
            'URL_Path' => $uniqueUrl,
            'Plain_Text' => $cleaned_string])) {
            print "Added successfully";
        }

    }
}

}
