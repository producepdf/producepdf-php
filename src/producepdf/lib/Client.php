<?php

namespace ProducePdf;

use ProducePdf\BaseClient;
use ProducePdf\Exception\InvalidArgumentException;
use ProducePdf\Exception\RateLimitException;
use ProducePdf\Exception\UnknownApiErrorException;

class Client extends BaseClient
{

    /**
     * @throws RateLimitException
     * @throws UnknownApiErrorException
     */
    public function generatePdfFromHtml($data = []): ?\Psr\Http\Message\StreamInterface
    {
        if (\is_string($data)) {
            $data = ['html' => $data];
        } elseif (!\is_array($data)) {
            throw new InvalidArgumentException('$data must be a string(html) or an array');
        }

        return $this->generatePdf($data);
    }

    /**
     * @throws RateLimitException
     * @throws UnknownApiErrorException
     */
    public function generatePdfFromUrl($data = []): ?\Psr\Http\Message\StreamInterface
    {
        if (\is_string($data)) {
            $data = ['url' => $data];
        } elseif (!\is_array($data)) {
            throw new InvalidArgumentException('$data must be a string(url) or an array');
        }

        return $this->generatePdf($data);
    }
}
