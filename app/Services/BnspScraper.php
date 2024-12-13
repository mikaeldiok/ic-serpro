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
    public function scrapePage($hal)
    {
        $baseUrl = "https://bnsp.go.id/lsp?hal={$hal}";
        $response = Http::get($baseUrl);

        if ($response->status() !== 200) {
            throw new \Exception("Failed to fetch the page. Status code: {$response->status()}");
        }

        $crawler = new Crawler($response->body());

        $crawler->filter('h4.trending__title a')->each(function (Crawler $node) {
            $detailUrl = $node->attr('href');
            $encryptedId = basename($detailUrl);

            $this->scrapeDetailPage($detailUrl, $encryptedId);
        });
    }

    public function scrapeDetailPage($url, $encryptedId)
    {
        $response = Http::get($url);

        if ($response->status() !== 200) {
            throw new \Exception("Failed to fetch detail page: {$url}");
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
        $skemas = [];

        $crawler->filter('#skema table tr')->each(function ($node, $index) use ($lspId, &$skemas) {
            if ($index === 0) return;

            $columns = $node->filter('td');
            $onclick = $columns->eq(2)->filter('span')->attr('onclick');
            preg_match("/fetchDataUnitSkema\('.*?',\s*'(\d+)'\)/", $onclick, $matches);
            $skemaId = $matches[1] ?? null;

            if ($skemaId) {
                $skemas[] = [
                    'lsp_id' => $lspId,
                    'order' => trim($columns->eq(0)->text()),
                    'name' => trim($columns->eq(1)->text()),
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
        $response = Http::get($url);

        if ($response->status() !== 200) {
            throw new \Exception("Failed to fetch units for Skema ID: {$skemaId}");
        }

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
    }

    protected function scrapeAsesors($crawler, $lspId)
    {
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
