<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ApiTest extends TestCase
{
    /**
     * Test rates for 2021
     */
    public function testRates1()
    {
        $response = $this->postJson('/api/rates', [
            'date' => '2021-04-15',
            'codes' => ['USD', 'eur']
        ]);

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'rates' => [
                    'EUR' => '90,5391',
                    'USD' => '75,6826'
                ]
            ]);
    }

    /**
     * Test rates for 2011
     */
    public function testRates2()
    {
        $response = $this->postJson('/api/rates', [
            'date' => '2011-04-15',
            'codes' => ['USD', 'EUR']
        ]);

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'rates' => [
                    'EUR' => '40,8791',
                    'USD' => '28,1886'
                ]
            ]);
    }

    /**
     * Test for missing 'date' parameter error
     */
    public function testRatesDateMissing()
    {
        $response = $this->postJson('/api/rates', [
            'codes' => ['USD', 'EUR']
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'errors' => [
                    'date'
                ]
            ]);
    }

    /**
     * Test for wrong format 'date' parameter error
     */
    public function testRatesDateWrongFormat()
    {
        $response = $this->postJson('/api/rates', [
            'date' => '15.04.2021',
            'codes' => ['USD', 'EUR']
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'errors' => [
                    'date'
                ]
            ]);
    }

    /**
     * Test for 'date' in future
     */
    public function testRatesDateInFuture()
    {
        $response = $this->postJson('/api/rates', [
            'date' => '2080-04-15',
            'codes' => ['USD', 'EUR']
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'errors' => [
                    'date'
                ]
            ]);
    }

    /**
     * Test for wrong format 'date' parameter error
     */
    public function testSavePreset()
    {
        $response = $this->postJson('/api/save-preset', [
            'codes' => ['USD', 'EUR']
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'preset' => [
                    'key'
                ]
            ]);
        return $response->json('preset.key');
    }

    /**
     * Test rates by preset
     * @depends testSavePreset
     */
    public function testRatesByPreset($key)
    {
        $response = $this->postJson('/api/rates', [
            'date' => '2021-04-15',
            'preset' => $key
        ]);

        $response
            ->assertStatus(200)
            ->assertExactJson([
                'rates' => [
                    'EUR' => '90,5391',
                    'USD' => '75,6826'
                ]
            ]);
    }

    /**
     * Test rates by preset with wrong preset
     */
    public function testRatesByPresetWrongPreset()
    {
        $wrongPreset = Str::random(16);
        $response = $this->postJson('/api/rates', [
            'date' => '2021-04-15',
            'preset' => $wrongPreset
        ]);

        $response
            ->assertStatus(404);
    }

    /**
     * Test preset comment
     * @depends testSavePreset
     */
    public function testPresetComment($key)
    {
        $presetComment = Str::random(255);

        $response = $this->postJson('/api/save-preset-comment', [
            'preset' => $key,
            'comment' => $presetComment
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonPath('preset.comment', $presetComment);
    }

    /**
     * Test preset comment without comment parameter
     * @depends testSavePreset
     */
    public function testPresetCommentCommentMissing($key)
    {
        $response = $this->postJson('/api/save-preset-comment', [
            'preset' => $key
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'errors' => [
                    'comment'
                ]
            ]);
    }

    /**
     * Test preset comment without preset parameter
     */
    public function testPresetCommentPresetMissing()
    {
        $presetComment = Str::random(255);

        $response = $this->postJson('/api/save-preset-comment', [
            'comment' => $presetComment
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'errors' => [
                    'preset'
                ]
            ]);
    }

    /**
     * Test preset comment with wrong preset
     */
    public function testPresetCommentWrongPreset()
    {
        $wrongPreset = Str::random(16);
        $presetComment = Str::random(255);

        $response = $this->postJson('/api/save-preset-comment', [
            'preset' => $wrongPreset,
            'comment' => $presetComment
        ]);

        $response
            ->assertStatus(404);
    }




}
