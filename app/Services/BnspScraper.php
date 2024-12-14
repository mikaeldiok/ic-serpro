<?php

namespace App\Services;

use App\Models\Lsp;
use App\Models\LspAsesor;
use App\Models\LspSkema;
use App\Models\LspSkemaUnit;
use App\Models\LspTuk;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class BnspScraper
{
    public function scrapeAllPages()
    {
        $currentPage = 1;

        while (true) {
            echo "Scraping page {$currentPage}...\n";

            $baseUrl = "https://bnsp.go.id/lsp?hal={$currentPage}";

            try {
                $response = Http::retry(5, 5000)->get($baseUrl);

                if ($response->status() !== 200) {
                    throw new \Exception("Failed to fetch the page. Status code: {$response->status()}");
                }

                $crawler = new Crawler($response->body());

                $titles = $crawler->filter('h4.trending__title a');

                if ($titles->count() === 0) {
                    echo "No titles found on page {$currentPage}. Stopping scrape.\n";
                    break;
                }

                $titles->each(function (Crawler $node) {
                    $detailUrl = $node->attr('href');
                    $encryptedId = basename($detailUrl);

                    $this->scrapeDetailPage($detailUrl, $encryptedId);
                });

                $currentPage++;

            } catch (\Exception $e) {
                echo "Error scraping page {$currentPage}: " . $e->getMessage() . "\n";
                break;
            }
        }

        echo "Scraping completed.\n";
    }

    public function checkNewLspAllPages()
    {
        $currentPage = 1;

        while (true) {
            echo "Checking page {$currentPage} for updates...\n";

            $baseUrl = "https://bnsp.go.id/lsp?hal={$currentPage}";

            try {
                $response = Http::retry(5, 5000)->get($baseUrl);

                if ($response->status() !== 200) {
                    throw new \Exception("Failed to fetch the page. Status code: {$response->status()}");
                }

                $crawler = new Crawler($response->body());

                $titles = $crawler->filter('h4.trending__title a');

                if ($titles->count() === 0) {
                    echo "No more pages to process. Scraping completed.\n";
                    break;
                }

                $titles->each(function (Crawler $node) {
                    $detailUrl = $node->attr('href');
                    $encryptedId = basename($detailUrl);

                    $name = $node->text();

                    // echo "Checking LSP by name: {$name}...\n";

                    $lsp = Lsp::where('name', $name)->first();

                    if ($lsp) {
                        // echo "Skipping already existing LSP with name: {$name} and ID: {$lsp->id}\n";
                        return;
                    }

                    // echo "Scraping new LSP with name: {$name} and encrypted ID: {$encryptedId}...\n";

                    $this->scrapeDetailPage($detailUrl, $encryptedId);
                });


                $currentPage++;
            } catch (\Exception $e) {
                echo "Error checking page {$currentPage}: " . $e->getMessage() . "\n";
                break;
            }
        }

        echo "Check and update process completed.\n";
    }

    public function scrapePage($hal)
    {
        $baseUrl = "https://bnsp.go.id/lsp?hal={$hal}";

        try {
            $response = Http::retry(5, 5000)->get($baseUrl);

            if ($response->status() !== 200) {
                throw new \Exception("Failed to fetch the page. Status code: {$response->status()}");
            }

            $crawler = new Crawler($response->body());

            $crawler->filter('h4.trending__title a')->each(function (Crawler $node) {
                $detailUrl = $node->attr('href');
                $encryptedId = basename($detailUrl);

                $this->scrapeDetailPage($detailUrl, $encryptedId);
            });
        } catch (\Exception $e) {
            echo "Error scraping page {$hal}: " . $e->getMessage();
        }
    }


    public function scrapeDetailPage($url, $encryptedId)
    {
        $attempts = 0;
        $maxAttempts = 5;
        $response = null;

        while ($attempts < $maxAttempts) {
            try {
                $response = Http::get($url);

                if ($response->status() === 200) {
                    break;
                }

                throw new \Exception("Failed to fetch detail page: {$url}. Status code: {$response->status()}");
            } catch (\Exception $e) {
                $attempts++;
                echo "\nAttempt {$attempts} failed: {$e->getMessage()}";

                if ($attempts >= $maxAttempts) {
                    echo "\nMax retry attempts reached for URL: {$url}. Skipping...\n";
                    return;
                }
                sleep(2);
            }
        }

        $crawler = new Crawler($response->body());

        $nameNode = $crawler->filter('.page__title');
        $name = $nameNode->count() > 0 ? $nameNode->text() : null;

        echo "\nExtracting: ".$name."... ";

        $profileData = [];
        $crawler->filter('.product__wrapper table tr')->each(function (Crawler $node) use (&$profileData) {
            $key = $node->filter('td')->eq(0)->text();
            $value = $node->filter('td')->eq(2)->text();
            $profileData[$key] = $value;
        });

        $alamatNode = $crawler->filter('.product__wrapper h5:contains("Alamat")');
        $address = null;

        if ($alamatNode->count() > 0) {
            $addressNode = $alamatNode->getNode(0)->nextSibling;
            if ($addressNode && $addressNode->nodeType === XML_TEXT_NODE) {
                $address = trim($addressNode->textContent);
            }
        }

        $logoNode = $crawler->filter('img.rounded-3.shadow-lg.d-inline-block');
        $logoImage = $logoNode->count() > 0 ? $logoNode->attr('src') : null;

        $lspData = [
            'encrypted_id' => $encryptedId,
            'name' => $name,
            'sk_lisensi' => $profileData['No. SK Lisensi'] ?? null,
            'no_lisensi' => $profileData['No Lisensi'] ?? null,
            'jenis' => $profileData['Jenis'] ?? null,
            'no_telp' => $profileData['No Telp'] ?? null,
            'no_hp' => $profileData['No Hp'] ?? null,
            'no_fax' => $profileData['No Fax'] ?? null,
            'email' => $profileData['Email'] ?? null,
            'website' => $profileData['Website'] ?? null,
            'masa_berlaku_sert' => $profileData['Masa Berlaku Sertifikat'] ?? null,
            'status_lisensi' => $profileData['Status Lisensi'] ?? null,
            'alamat' => $address,
            'logo_image' => $logoImage,
        ];

        $lsp = Lsp::updateOrCreate(
            ['no_lisensi' => $lspData['no_lisensi']],
            $lspData
        );

        $this->scrapeSkemas($crawler, $lsp->id);
        $this->scrapeAsesors($crawler, $lsp->id);
        $this->scrapeTuks($crawler, $lsp->id);
        echo "Done!";
    }

    protected function scrapeSkemas($crawler, $lspId)
    {
        $skemaRows = $crawler->filter('#skema table tr');
        if ($skemaRows->count() === 0) {
            return;
        }

        $skemas = [];
        $skemaRows->each(function ($node, $index) use ($lspId, &$skemas) {
            if ($index === 0) return; // Skip header row

            $columns = $node->filter('td');
            if ($columns->count() < 3) return; // Ensure there are enough columns

            $onclick = $columns->eq(2)->filter('span')->attr('onclick');
            preg_match("/fetchDataUnitSkema\('.*?',\s*'(\d+)'\)/", $onclick, $matches);
            $skemaId = $matches[1] ?? null;

            if ($skemaId) {
                $skemas[] = [
                    'lsp_id' => $lspId,
                    'order' => trim($columns->eq(0)->text() ?? ''),
                    'name' => trim($columns->eq(1)->text() ?? ''),
                    'skema_id' => $skemaId,
                ];
            }
        });

        foreach ($skemas as $skema) {
            $skemaRecord = LspSkema::updateOrCreate(
                [
                    'lsp_id' => $skema['lsp_id'],
                    'name' => $skema['name'],
                ],
                $skema
            );

            $this->fetchSkemaUnits($skema['skema_id'], $skemaRecord->id);
        }
    }

    protected function fetchSkemaUnits($skemaId, $lspSkemaId)
    {
        $url = "https://bnsp.go.id/lsp/unit-skema/{$skemaId}";

        try {
            $response = Http::retry(3, 5000)->get($url);

            $data = $response->json();
            $units = $data['units'] ?? [];

            foreach ($units as $index => $unit) {
                LspSkemaUnit::updateOrCreate(
                    [
                        'skema_id' => $lspSkemaId,
                        'unit_code' => $unit['kodeunit'],
                    ],
                    [
                        'order' => $index + 1,
                        'name' => $unit['keterangan'],
                    ]
                );
            }

        } catch (\Exception $e) {
            echo "Error fetching units for Skema ID {$skemaId}: " . $e->getMessage();
        }
    }


    protected function scrapeAsesors($crawler, $lspId)
    {
        $rows = $crawler->filter('#asesor table tr');
        if ($rows->count() === 0) {
            return;
        }

        $crawler->filter('#asesor table tr')->each(function ($node, $index) use ($lspId) {
            if ($index === 0) return;

            $columns = $node->filter('td');
            LspAsesor::updateOrCreate(
                [
                    'lsp_id' => $lspId,
                    'registration_id' => trim($columns->eq(2)->text()),
                ],
                [
                    'order' => trim($columns->eq(0)->text()),
                    'name' => trim($columns->eq(1)->text()),
                    'address' => trim($columns->eq(3)->text()),
                ]
            );
        });
    }

    protected function scrapeTuks($crawler, $lspId)
    {
        $rows = $crawler->filter('#tuk table tr');
        if ($rows->count() === 0) {
            return;
        }

        $crawler->filter('#tuk table tr')->each(function ($node, $index) use ($lspId) {
            if ($index === 0) return;

            $columns = $node->filter('td');
            LspTuk::updateOrCreate(
                [
                    'lsp_id' => $lspId,
                    'tuk_code' => trim($columns->eq(1)->text()),
                ],
                [
                    'order' => trim($columns->eq(0)->text()),
                    'type' => trim($columns->eq(2)->text()),
                    'name' => trim($columns->eq(3)->text()),
                    'address' => trim($columns->eq(4)->text()),
                ]
            );
        });
    }
}
