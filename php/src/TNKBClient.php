<?php

declare(strict_types=1);

namespace TNKB;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TransferException;

/**
 * TNKB Client Configuration
 */
class ClientConfig
{
    public ?int $timeout;
    public int $maxRetries;
    public ?string $apiKey;
    public string $baseUrl;

    public function __construct(
        ?int $timeout = 10,
        int $maxRetries = 3,
        ?string $apiKey = null,
        string $baseUrl = 'https://cek-nopol-kendaraan.p.rapidapi.com'
    ) {
        $this->timeout = $timeout;
        $this->maxRetries = $maxRetries;
        $this->apiKey = $apiKey;
        $this->baseUrl = $baseUrl;
    }
}

/**
 * TNKB Client for Indonesian Vehicle Registration Numbers
 */
class TNKBClient
{
    private ClientInterface $httpClient;
    private ClientConfig $config;
    private string $platePattern = '/^([A-Z]{1,2})\s?(\d{1,4})\s?([A-Z]{1,3})$/';

    public function __construct(?ClientConfig $config = null, ?ClientInterface $httpClient = null)
    {
        $this->config = $config ?? new ClientConfig();
        $this->httpClient = $httpClient ?? new Client([
            'timeout' => $this->config->timeout ?? 10,
            'http_errors' => false,
        ]);
    }

    /**
     * Check and decode Indonesian vehicle plate number
     */
    public function checkPlate(string $plateNumber): VehicleInfo
    {
        $normalized = $this->normalizePlate($plateNumber);
        [$regionCode] = $this->parsePlate($normalized);

        try {
            $response = $this->callApi(
                $this->config->baseUrl . '/check',
                ['nopol' => $normalized]
            );

            return $this->buildVehicleInfo($response, $normalized);
        } catch (\Exception $e) {
            \error_log('API call failed, falling back to local parsing: ' . $e->getMessage());

            return $this->parseLocally($normalized, $regionCode);
        }
    }

    /**
     * Validate Indonesian vehicle plate format
     */
    public function validatePlate(string $plateNumber): bool
    {
        try {
            $this->validatePlateFormat($plateNumber);

            return true;
        } catch (InvalidPlateException) {
            return false;
        }
    }

    /**
     * Get information about a registration region
     */
    public function getRegionInfo(string $regionCode): ?RegionInfo
    {
        return RegionCodes::getRegion($regionCode);
    }

    /**
     * List all Indonesian vehicle registration regions
     */
    public function listRegions(): array
    {
        return RegionCodes::getAllRegions();
    }

    /**
     * Check multiple plate numbers
     */
    public function bulkCheck(array $plateNumbers): array
    {
        $results = [];

        foreach ($plateNumbers as $plate) {
            try {
                $results[] = $this->checkPlate($plate);
            } catch (\Exception $e) {
                $results[] = $this->createInvalidVehicle($plate, $e->getMessage());
            }
        }

        return $results;
    }

    /**
     * Normalize plate number format
     */
    private function normalizePlate(string $plateNumber): string
    {
        if (empty($plateNumber)) {
            throw new InvalidPlateException('Plate number cannot be empty');
        }

        $normalized = \strtoupper(\trim(\preg_replace('/\s+/', ' ', $plateNumber)));
        $this->validatePlateFormat($normalized);

        return $normalized;
    }

    /**
     * Validate plate number format
     */
    private function validatePlateFormat(string $plateNumber): void
    {
        if (!\preg_match($this->platePattern, $plateNumber)) {
            throw new InvalidPlateException(
                \sprintf(
                    'Invalid plate format: %s. Expected format: \'[A-Z] [1-4 DIGITS] [A-Z]\'',
                    $plateNumber
                )
            );
        }
    }

    /**
     * Parse plate into components
     */
    private function parsePlate(string $plateNumber): array
    {
        if (!\preg_match($this->platePattern, $plateNumber, $matches)) {
            throw new InvalidPlateException(\sprintf('Cannot parse plate: %s', $plateNumber));
        }

        return [$matches[1], $matches[2], $matches[3]];
    }

    /**
     * Make API request with error handling and retries
     */
    private function callApi(string $endpoint, array $params): array
    {
        $attempt = 0;

        while ($attempt < $this->config->maxRetries) {
            try {
                $response = $this->httpClient->request('GET', $endpoint, [
                    'query' => $params,
                    'headers' => $this->getHeaders(),
                    'timeout' => $this->config->timeout ?? 10,
                ]);

                $data = \json_decode((string)$response->getBody(), true, 512, JSON_THROW_ON_ERROR);

                if (!isset($data['success']) || !$data['success']) {
                    throw new APIException(
                        'API error: ' . ($data['message'] ?? 'Unknown error')
                    );
                }

                return $data['data'] ?? $data;
            } catch (ConnectException $e) {
                $attempt++;

                if ($attempt >= $this->config->maxRetries) {
                    throw new NetworkException('Network connection error after retries');
                }

                \usleep(1000000 * $attempt); // Exponential backoff
            } catch (RequestException $e) {
                if ($e->getCode() === 28) { // CURL timeout
                    throw new TimeoutException('Network timeout');
                }

                throw new APIException('API request failed: ' . $e->getMessage());
            } catch (\Exception $e) {
                throw new APIException('API error: ' . $e->getMessage());
            }
        }

        throw new NetworkException('Maximum retries exceeded');
    }

    /**
     * Get default HTTP headers
     */
    private function getHeaders(): array
    {
        $headers = [
            'User-Agent' => 'TNKBClient/1.0.0 (PHP)',
            'Content-Type' => 'application/json',
            'X-RapidAPI-Host' => 'cek-nopol-kendaraan.p.rapidapi.com',
        ];

        if ($this->config->apiKey) {
            $headers['X-RapidAPI-Key'] = $this->config->apiKey;
        }

        return $headers;
    }

    /**
     * Build VehicleInfo from API response
     */
    private function buildVehicleInfo(array $apiResponse, string $plateNumber): VehicleInfo
    {
        $regionCode = \strtoupper($apiResponse['region_code'] ?? '');
        $regionInfo = RegionCodes::getRegion($regionCode);

        return new VehicleInfo(
            $plateNumber,
            $regionCode,
            $regionInfo?->name ?? 'Unknown',
            (string)($apiResponse['vehicle_type'] ?? 'Unknown'),
            true,
            $apiResponse,
            new \DateTimeImmutable()
        );
    }

    /**
     * Parse plate locally when API is unavailable
     */
    private function parseLocally(string $plateNumber, string $regionCode): VehicleInfo
    {
        $code = \strtoupper($regionCode);
        $regionInfo = RegionCodes::getRegion($code);

        return new VehicleInfo(
            $plateNumber,
            $code,
            $regionInfo?->name ?? 'Unknown Region',
            'Car',
            true,
            [
                'source' => 'local_parsing',
                'note' => 'Parsed locally without API',
            ],
            new \DateTimeImmutable()
        );
    }

    /**
     * Create invalid vehicle info object
     */
    private function createInvalidVehicle(string $plate, string $error): VehicleInfo
    {
        return new VehicleInfo(
            $plate,
            '',
            'Unknown',
            'Unknown',
            false,
            ['error' => $error],
            new \DateTimeImmutable()
        );
    }
}
