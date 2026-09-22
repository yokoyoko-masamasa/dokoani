<?php

namespace Database\Seeders;

use App\Models\StreamingService;
use Illuminate\Database\Seeder;

class StreamingServiceSeeder extends Seeder
{
    /**
     * 対応する4社の配信サービスを登録する。
     */
    public function run(): void
    {
        $logoBase = 'https://image.tmdb.org/t/p/w92';

        $services = [
            [
                'name' => 'Netflix',
                'tmdb_provider_id' => 8,
                'logo_image_url' => $logoBase.'/rK1KljqmbvO9HQa1PBFLILWah72.png',
                'service_url' => 'https://www.netflix.com/jp/',
            ],
            [
                'name' => 'Amazon Prime Video',
                'tmdb_provider_id' => 9,
                'logo_image_url' => $logoBase.'/gMZdpavHmxFNnLpMHwVxfqeux2g.png',
                'service_url' => 'https://www.amazon.co.jp/gp/video/storefront',
            ],
            [
                'name' => 'Disney+',
                'tmdb_provider_id' => 337,
                'logo_image_url' => $logoBase.'/5eZ872CghnHFLB1j8grszbrx0dx.png',
                'service_url' => 'https://disneyplus.disney.co.jp/',
            ],
            [
                'name' => 'U-NEXT',
                'tmdb_provider_id' => 84,
                'logo_image_url' => $logoBase.'/3DGpCh83CR9lDzyKbNWlmUBexBF.png',
                'service_url' => 'https://video.unext.jp/',
            ],
        ];

        foreach ($services as $service) {
            StreamingService::updateOrCreate(
                ['tmdb_provider_id' => $service['tmdb_provider_id']],
                $service
            );
        }
    }
}
