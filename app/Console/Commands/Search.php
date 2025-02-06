<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
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
    }

    function searchPageContent(string $usrSearch): Collection
    {//DB::raw()
        return DB::table('pages')
            ->select('URL_Path', DB::raw("MATCH(Plain_Text) AGAINST ('$usrSearch' IN NATURAL LANGUAGE MODE) AS relevance"))
            ->whereRaw("MATCH(Plain_Text) AGAINST (? IN NATURAL LANGUAGE MODE)", [$usrSearch])
            ->orderByDesc('relevance')
            ->get();
    }

}
//SELECT URL_Path,
//       (MATCH(Plain_Text) AGAINST (:searchTerm IN NATURAL LANGUAGE MODE)) AS relevance
//    FROM Pages
//    WHERE MATCH(Plain_Text) AGAINST (:searchTerm IN NATURAL LANGUAGE MODE)
//    ORDER BY relevance DESC

//INSERT INTO Pages (URL_Path, Plain_Text) VALUES (:URL_Path, :Plain_Text)
