# TNKB Client - PHP

Indonesian Vehicle Registration Number (TNKB) decoder for PHP projects.

## Installation

```bash
composer require nulsec/tnkb-client-php
```

## Quick Start

```php
<?php

require_once 'vendor/autoload.php';

use TNKB\TNKBClient;
use TNKB\ClientConfig;

$client = new TNKBClient();
$vehicle = $client->checkPlate('B 1234 ABC');

echo "Region: " . $vehicle->regionName . "\n";
echo "Valid: " . ($vehicle->isValid ? "Yes" : "No") . "\n";
```

## Features

- ✅ Validate Indonesian plate numbers
- ✅ Decode region information (40+ regions)
- ✅ Check multiple plates in batch
- ✅ Automatic retry with exponential backoff
- ✅ Full PHP 7.4+ type safety
- ✅ Guzzle HTTP client integration

## Installation

### Via Composer

```bash
composer require nulsec/tnkb-client-php
```

### Requirements

- PHP >= 7.4
- Composer
- `guzzlehttp/guzzle` ^7.0

## API Reference

### TNKBClient

#### Constructor

```php
use TNKB\TNKBClient;
use TNKB\ClientConfig;

// Default configuration
$client = new TNKBClient();

// Custom configuration
$config = new ClientConfig(
    timeout: 15,        // seconds
    maxRetries: 5,
    apiKey: 'api-key',
    baseUrl: 'https://jojapi.com/api'
);
$client = new TNKBClient($config);
```

#### Methods

##### `checkPlate(string $plateNumber): VehicleInfo`

Check and decode a vehicle plate number.

```php
$vehicle = $client->checkPlate('B 1234 ABC');
echo $vehicle->regionName;   // "Jawa Barat"
echo $vehicle->isValid;      // true
```

##### `validatePlate(string $plateNumber): bool`

Validate plate format.

```php
if ($client->validatePlate('B 1234 ABC')) {
    echo "Valid format\n";
}
```

##### `getRegionInfo(string $regionCode): ?RegionInfo`

Get region information.

```php
$region = $client->getRegionInfo('B');
if ($region) {
    echo $region->code;      // 'B'
    echo $region->name;      // 'Jawa Barat'
    echo $region->province;  // 'West Java'
}
```

##### `listRegions(): array`

List all supported regions.

```php
$regions = $client->listRegions();
foreach ($regions as $region) {
    echo $region->code . ': ' . $region->name . "\n";
}
```

##### `bulkCheck(array $plateNumbers): array`

Check multiple plates.

```php
$vehicles = $client->bulkCheck([
    'B 1234 ABC',
    'A 123 DEF',
    'AB 5 CDE'
]);

foreach ($vehicles as $vehicle) {
    echo $vehicle->plateNumber . ' -> ' . $vehicle->regionName . "\n";
}
```

## Data Models

### VehicleInfo

```php
class VehicleInfo {
    public string $plateNumber;
    public string $regionCode;
    public string $regionName;
    public string $vehicleType;
    public bool $isValid;
    public array $details;
    public ?DateTimeImmutable $createdAt;
    
    public function toArray(): array;
    public function toJson(): string;
}
```

### RegionInfo

```php
class RegionInfo {
    public string $code;
    public string $name;
    public ?string $province;
}
```

## Error Handling

```php
use TNKB\TNKBClient;
use TNKB\InvalidPlateException;
use TNKB\APIException;
use TNKB\NetworkException;

try {
    $vehicle = $client->checkPlate('B 1234 ABC');
} catch (InvalidPlateException $e) {
    echo "Invalid plate: " . $e->getMessage();
} catch (APIException $e) {
    echo "API error: " . $e->getMessage();
} catch (NetworkException $e) {
    echo "Network error: " . $e->getMessage();
}
```

### Exception Hierarchy

```
Exception
└── TNKBException
    ├── InvalidPlateException
    ├── APIException
    │   ├── NetworkException
    │   └── TimeoutException
    └── ValidationException
```

## Configuration

```php
use TNKB\ClientConfig;

$config = new ClientConfig(
    timeout: 15,           // seconds (default: 10)
    maxRetries: 5,         // default: 3
    apiKey: 'your-key',    // optional
    baseUrl: 'https://api.example.com'  // optional
);

$client = new TNKBClient($config);
```

## Examples

### Basic Usage

```php
<?php
require_once 'vendor/autoload.php';

use TNKB\TNKBClient;

$client = new TNKBClient();

// Single plate check
$vehicle = $client->checkPlate('B 1234 ABC');
echo json_encode($vehicle->toArray(), JSON_UNESCAPED_UNICODE) . "\n";
```

### Batch Processing

```php
$plates = ['B 1234 ABC', 'A 123 DEF', 'AB 5 CDE'];
$results = $client->bulkCheck($plates);

foreach ($results as $vehicle) {
    if ($vehicle->isValid) {
        printf(
            "%s -> %s (%s)\n",
            $vehicle->plateNumber,
            $vehicle->regionName,
            $vehicle->vehicleType
        );
    }
}
```

### Export to JSON

```php
foreach ($client->listRegions() as $region) {
    echo json_encode($region->toArray(), JSON_UNESCAPED_UNICODE) . "\n";
}
```

### Database Storage

```php
function storeVehicleInfo(VehicleInfo $vehicle, PDO $db): void {
    $stmt = $db->prepare(
        'INSERT INTO vehicles (plate, region, valid, data) VALUES (?, ?, ?, ?)'
    );
    $stmt->execute([
        $vehicle->plateNumber,
        $vehicle->regionCode,
        $vehicle->isValid,
        $vehicle->toJson()
    ]);
}
```

### Caching Results

```php
function checkWithCache(string $plate, Redis $redis): VehicleInfo {
    $cached = $redis->get("tnkb:$plate");
    if ($cached) {
        return unserialize($cached);
    }
    
    $vehicle = $client->checkPlate($plate);
    $redis->setex("tnkb:$plate", 86400, serialize($vehicle));
    return $vehicle;
}
```

## Testing

```bash
# Install dev dependencies
composer install

# Run tests
./vendor/bin/phpunit

# Run with coverage
./vendor/bin/phpunit --coverage-html coverage/

# Code quality
./vendor/bin/phpstan analyse src
php-cs-fixer fix src --allow-risky=yes
```

## Laravel Integration

### Service Provider

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use TNKB\TNKBClient;
use TNKB\ClientConfig;

class TNKBServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TNKBClient::class, function ($app) {
            $config = new ClientConfig(
                timeout: (int)env('TNKB_TIMEOUT', 10),
                maxRetries: (int)env('TNKB_RETRIES', 3),
                apiKey: env('TNKB_API_KEY'),
                baseUrl: env('TNKB_BASE_URL', 'https://jojapi.com/api')
            );
            
            return new TNKBClient($config);
        });
    }
}
```

### Usage in Controller

```php
<?php

namespace App\Http\Controllers;

use TNKB\TNKBClient;
use TNKB\InvalidPlateException;

class VehicleController extends Controller
{
    public function __construct(private TNKBClient $tnkb) {}
    
    public function checkPlate(Request $request)
    {
        try {
            $vehicle = $this->tnkb->checkPlate($request->plate);
            return response()->json($vehicle->toArray());
        } catch (InvalidPlateException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
```

## Performance

- **Connection Pooling**: Guzzle handles connection reuse
- **Automatic Retries**: Exponential backoff for transient failures
- **Timeout Handling**: Prevents hanging requests
- **Local Fallback**: Offline plate parsing

## Supported Regions

Complete list of 40+ Indonesian provinces and cities:

```
A   - DKI Jakarta
B   - Jawa Barat
C   - Jawa Tengah
D   - Bandung
...
AA  - Medan
AB  - Padang
...
```

## Development

```bash
# Install dependencies
composer install --dev

# Run linter
./vendor/bin/phpstan analyse src

# Format code
php-cs-fixer fix src --allow-risky=yes

# Run tests
./vendor/bin/phpunit tests/
```

## Troubleshooting

### Connection Timeout

```php
$config = new ClientConfig(timeout: 30);  // Increase to 30 seconds
$client = new TNKBClient($config);
```

### Certificate Issues

```php
$httpClient = new \GuzzleHttp\Client([
    'verify' => false  // Not recommended for production
]);
$client = new TNKBClient($config, $httpClient);
```

### Memory Issues with Large Batches

```php
// Process in chunks
$plates = [...];
$chunkSize = 100;

foreach (array_chunk($plates, $chunkSize) as $chunk) {
    $results = $client->bulkCheck($chunk);
    // Process results
    unset($results);
}
```

## Requirements

- PHP 7.4 or higher
- `guzzlehttp/guzzle` ^7.0
- `vlucas/phpdotenv` ^5.4

## Related Packages

- **Python**: `pip install tnkb-client`
- **JavaScript/TypeScript**: `npm install tnkb-client`

## License

MIT

## Support

- Issues: GitHub Issues
- Email: security@nulsec.com
