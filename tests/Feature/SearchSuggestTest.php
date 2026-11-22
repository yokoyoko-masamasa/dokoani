<?php

namespace Tests\Feature;

use Tests\TestCase;

class SearchSuggestTest extends TestCase
{
    public function test_suggest_returns_429_after_60_requests_in_a_minute(): void
    {
        // q が空なら DB に問い合わせないので、DB の準備は要らない
        // 60回目までは通常どおり返る
        for ($i = 0; $i < 60; $i++) {
            $this->get(route('search.suggest', ['q' => '']))->assertOk();
        }

        // 61回目は回数制限で 429 になる
        $this->get(route('search.suggest', ['q' => '']))->assertTooManyRequests();
    }
}
