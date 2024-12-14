<?php

namespace App\Console\Commands;

use App\Services\BnspScraper;
use Illuminate\Console\Command;

class CheckNewLsp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:check-new-lsp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for new LSP entries and update only new records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $scraperService = new BnspScraper();

        $this->info("Checking for new LSP entries...");
        $scraperService->checkNewLspAllPages();
        $this->info("New LSP entries checked and updated successfully!");
    }
}
