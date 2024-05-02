<?php

namespace Makaira\HttpClient;

use Makaira\HttpClient;

class SigningTest extends \PHPUnit_Framework_TestCase
{
    public function testSignEmptyBody()
    {
        $aggregate = $this->getMock(HttpClient::class);
        $signer = new Signing($aggregate, 'secret');

        mt_srand(42);
        $aggregate
            ->expects($this->once())
            ->method('request')
            ->with('GET', 'http://example.com/', null, [
                'X-Makaira-Nonce: 1608637542',
                'X-Makaira-Hash: f5b12e9f076a338e6da032750c6fa8f09d97e15523198027b66227b16ba7365c'
            ]);

        $signer->request('GET', 'http://example.com/');
    }

    public function testSignContentBody()
    {
        $aggregate = $this->getMock(HttpClient::class);
        $signer = new Signing($aggregate, 'secret');

        mt_srand(42);
        $aggregate
            ->expects($this->once())
            ->method('request')
            ->with('GET', 'http://example.com/', '{}', [
                'Content-Type: application/json',
                'X-Makaira-Nonce: 1608637542',
                'X-Makaira-Hash: a1dbdee6e7fb519e9d8ae88c31b73d517cacb27d1ae7610bf794d258dde99515'
            ]);

        $signer->request('GET', 'http://example.com/', '{}', ['Content-Type: application/json']);
    }
}
