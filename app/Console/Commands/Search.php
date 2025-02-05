<?php

namespace App\Console\Commands;

use App\Models\Pages;
use Illuminate\Console\Command;
use App\Scraper\WebScraper;
use Illuminate\Support\Facades\DB;

class Search extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:search';

    // make hidden this command
    protected $hidden = true;
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Search over (انتخاب) website';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $userSearch = $this->ask('Start search everything you want');
        $result = $this->searchPageContent($userSearch);
        $this->info("List of relevance pages:");

        print_r($result);
//        foreach ($result as $page) {
//        }
    }

    function searchPageContent(string $usrSearch)
    {//DB::raw()
        $results = DB::table('pages')
            ->select('URL_Path', DB::raw("MATCH(Plain_Text) AGAINST ('$usrSearch' IN NATURAL LANGUAGE MODE) AS relevance"))
            ->whereRaw("MATCH(Plain_Text) AGAINST (? IN NATURAL LANGUAGE MODE)", [$usrSearch])
            ->orderByDesc('relevance')
            ->get();
        return $results;
    }

}
//SQLSTATE[HY093]: Invalid parameter number (Connection: mysql, SQL: select `URL_Path`, MATCH(Plain_Text) AGAINST (????? ????? IN NATURAL LANGUAGE MODE) AS relevance from `pages` where MATCH(Plain_Text) AGAINST (? IN NATURAL LANGUAGE MODE) order by `relevance` desc)
//SELECT URL_Path,
//       (MATCH(Plain_Text) AGAINST (:searchTerm IN NATURAL LANGUAGE MODE)) AS relevance
//    FROM Pages
//    WHERE MATCH(Plain_Text) AGAINST (:searchTerm IN NATURAL LANGUAGE MODE)
//    ORDER BY relevance DESC

//INSERT INTO Pages (URL_Path, Plain_Text) VALUES (:URL_Path, :Plain_Text)
