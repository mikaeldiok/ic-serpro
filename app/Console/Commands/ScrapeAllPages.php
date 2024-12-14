<?php

namespace App\Console\Commands;

use App\Services\BnspScraper;
use Illuminate\Console\Command;

class ScrapeAllPages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:all-pages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape all pages for LSP data and update the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $scraperService = new BnspScraper();

        $this->info("Starting to scrape all pages...");
        $scraperService->scrapeAllPages();
        $this->info("All pages scraped successfully!");
    }
}
