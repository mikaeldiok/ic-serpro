<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BnspScraper;

class ScrapeLsp extends Command
{
    protected $signature = 'scrape:lsp {hal}';
    protected $description = 'Scrape LSP data from a specific hal (page)';
    protected $scraper;

    public function __construct(BnspScraper $scraper)
    {
        parent::__construct();
        $this->scraper = $scraper;
    }

    public function handle()
    {
        $hal = $this->argument('hal');

        try {
            $this->scraper->scrapePage($hal);
            $this->info("\nPage {$hal} scraped successfully.");
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
