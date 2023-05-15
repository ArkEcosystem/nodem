<?php

declare(strict_types=1);

namespace App\Services\Client\Resources;

use App\Enums\Constants;

final class Log extends AbstractResource
{
    public function search(array $params): array
    {
        return $this->client
            ->withTimeout(Constants::LOG_TIMEOUT)
            ->send('log.search', $params);
    }

    /**
     * Retrieve all of the log archives from the server.
     *
     * @return array
     */
    public function archived() : array
    {
        return $this->client->send('log.archived');
    }

    /**
     * Generate the log archive for the given parameters and retrieve the URL for downloading newly created log archive.
     *
     * @param array $params
     *
     * @return array
     */
    public function download(array $params): array
    {
        $filename = $this->client->sendRaw('log.download', $params);

        return [
            'url'      => collect($this->archived())->firstWhere('name', $filename)['downloadLink'],
            'filename' => $filename,
        ];
    }
}
