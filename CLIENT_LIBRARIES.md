# TNKB Client Libraries

**Indonesian Vehicle Registration Number (TNKB/Tanda Nomor Kendaraan Bermotor) Decoder**

Multi-language client libraries for decoding and validating Indonesian vehicle registration numbers. Available in Python, JavaScript/TypeScript, and PHP.

## Overview

TNKB (Tanda Nomor Kendaraan Bermotor) is the Indonesian vehicle registration system. This project provides ready-to-use client libraries to:

- ✅ Decode vehicle plates to extract region information
- ✅ Validate Indonesian plate number formats
- ✅ Get detailed region metadata
- ✅ Check multiple plates in bulk
- ✅ Handle network failures gracefully with retry logic
- ✅ Work offline with local parsing fallback
- ✅ Support API key authentication

## Features

### All Implementations Include:
- **Plate Validation**: Regex-based validation for Indonesian plate formats
- **Region Lookup**: Complete mapping of 40+ Indonesian provinces and cities
- **Error Handling**: Comprehensive exception handling with meaningful error messages
- **Retry Logic**: Automatic retry with exponential backoff for network failures
- **Timeout Handling**: Configurable request timeouts
- **Fallback Parsing**: Local plate parsing when API is unavailable
- **Type Safety**: Full type hints/annotations (TypeScript, PHP 7.4+)
- **Zero Dependencies** (Python): Uses only standard library for core functionality

## Supported Plate Format

Indonesian vehicle plates follow this pattern:
```
[Region Code] [Number] [Letters]
Examples: B 1234 ABC, AB 123 CDE, A 1 XYZ
```

## Installation & Usage

### Python

```bash
pip install tnkb-client
```

**Basic Usage:**
```python
from tnkb import TNKBClient

# Initialize client
client = TNKBClient(timeout=10, max_retries=3)

# Check a single plate
vehicle = client.check_plate('B 1234 ABC')
print(f"Region: {vehicle.region_name}")
print(f"Valid: {vehicle.is_valid}")

# Validate plate format
if client.validate_plate('B 1234 ABC'):
    print("Valid plate format")

# Get region information
region = client.get_region_info('B')
print(f"Region: {region['name']}")

# List all regions
regions = client.list_regions()
for region in regions:
    print(f"{region['code']}: {region['name']}")

# Check multiple plates
vehicles = client.bulk_check(['B 1234 ABC', 'A 123 DEF'])
```

**Advanced Usage:**
```python
from tnkb import TNKBClient
from tnkb.exceptions import InvalidPlateError, APIError

# Custom configuration
client = TNKBClient(
    timeout=15,
    max_retries=5,
    verify_ssl=True,
    api_key='your-api-key'
)

# Error handling
try:
    vehicle = client.check_plate('invalid')
except InvalidPlateError as e:
    print(f"Invalid plate: {e}")
except APIError as e:
    print(f"API error: {e}")

# Context manager for resource cleanup
with TNKBClient() as client:
    result = client.check_plate('B 1234 ABC')
```

### JavaScript/TypeScript

```bash
npm install tnkb-client
```

**Basic Usage (JavaScript):**
```javascript
const { TNKBClient } = require('tnkb-client');

const client = new TNKBClient({
  timeout: 10000,
  maxRetries: 3
});

// Check a single plate
client.checkPlate('B 1234 ABC')
  .then(vehicle => {
    console.log(`Region: ${vehicle.regionName}`);
    console.log(`Valid: ${vehicle.isValid}`);
  })
  .catch(error => console.error(error));
```

**TypeScript Usage:**
```typescript
import { TNKBClient, VehicleInfo, InvalidPlateError } from 'tnkb-client';

const client = new TNKBClient({
  timeout: 10000,
  maxRetries: 3,
  apiKey: 'your-api-key'
});

// Async/await
async function checkVehicle() {
  try {
    const vehicle: VehicleInfo = await client.checkPlate('B 1234 ABC');
    console.log(`Region: ${vehicle.regionName}`);
    console.log(`Type: ${vehicle.vehicleType}`);
  } catch (error) {
    if (error instanceof InvalidPlateError) {
      console.error('Invalid plate format');
    } else {
      console.error('API error:', error.message);
    }
  }
}

checkVehicle();

// Bulk checking
async function bulkCheck() {
  const plates = ['B 1234 ABC', 'A 123 DEF', 'AB 5 CDE'];
  const results = await client.bulkCheck(plates);
  results.forEach(vehicle => {
    console.log(`${vehicle.plateNumber}: ${vehicle.regionName}`);
  });
}

bulkCheck();

// List all regions
const regions = client.listRegions();
regions.forEach(region => {
  console.log(`${region.code}: ${region.name} (${region.province})`);
});
```

### PHP

```bash
composer require nulsec/tnkb-client-php
```

**Basic Usage:**
```php
<?php

require_once 'vendor/autoload.php';

use TNKB\TNKBClient;
use TNKB\ClientConfig;

// Initialize client
$config = new ClientConfig(timeout: 10, maxRetries: 3);
$client = new TNKBClient($config);

// Check a single plate
$vehicle = $client->checkPlate('B 1234 ABC');
echo "Region: " . $vehicle->regionName . "\n";
echo "Valid: " . ($vehicle->isValid ? "Yes" : "No") . "\n";

// Validate plate format
if ($client->validatePlate('B 1234 ABC')) {
    echo "Valid plate format\n";
}

// Get region information
$region = $client->getRegionInfo('B');
echo "Region: " . $region->name . "\n";

// List all regions
foreach ($client->listRegions() as $region) {
    echo $region->code . ": " . $region->name . "\n";
}

// Check multiple plates
$vehicles = $client->bulkCheck(['B 1234 ABC', 'A 123 DEF']);
foreach ($vehicles as $vehicle) {
    echo $vehicle->plateNumber . " -> " . $vehicle->regionName . "\n";
}
```

**Advanced PHP Usage:**
```php
<?php

use TNKB\TNKBClient;
use TNKB\ClientConfig;
use TNKB\InvalidPlateException;
use TNKB\APIException;
use TNKB\NetworkException;

// Custom configuration
$config = new ClientConfig(
    timeout: 15,
    maxRetries: 5,
    apiKey: 'your-api-key',
    baseUrl: 'https://jojapi.com/api'
);

$client = new TNKBClient($config);

// Error handling
try {
    $vehicle = $client->checkPlate('B 1234 ABC');
    echo "Result: " . $vehicle->toJson() . "\n";
} catch (InvalidPlateException $e) {
    echo "Invalid plate: " . $e->getMessage() . "\n";
} catch (APIException $e) {
    echo "API error: " . $e->getMessage() . "\n";
} catch (NetworkException $e) {
    echo "Network error: " . $e->getMessage() . "\n";
}

// Batch processing
$plates = ['B 1234 ABC', 'A 123 DEF', 'AB 5 CDE'];
$results = $client->bulkCheck($plates);

foreach ($results as $vehicle) {
    if ($vehicle->isValid) {
        echo json_encode($vehicle->toArray(), JSON_UNESCAPED_UNICODE) . "\n";
    }
}

// Export data
foreach ($client->listRegions() as $region) {
    echo sprintf(
        "%s: %s (%s)\n",
        $region->code,
        $region->name,
        $region->province
    );
}
```

## API Endpoints

The client uses the following public API:
- **Base URL**: `https://jojapi.com/api`
- **Check Vehicle**: `/check-kendaraan-indonesia?nopol=B%201234%20ABC`
- **Alternative**: `/cek-nopol-indonesia?nopol=B%201234%20ABC`

## Supported Regions

The library supports all Indonesian provinces and major cities:

| Code | Region | Province |
|------|--------|----------|
| A | DKI Jakarta | Jakarta |
| B | Jawa Barat | West Java |
| C | Jawa Tengah | Central Java |
| D | Bandung | West Java |
| ... | ... | ... |
| AA | Medan | North Sumatra |
| AB | Padang | West Sumatra |

See `REGION_CODES` in each implementation for the complete list (40+ regions).

## Error Handling

All implementations provide specific exception types:

### Python
```python
from tnkb.exceptions import (
    TNKBError,           # Base exception
    InvalidPlateError,   # Invalid plate format
    APIError,            # API returned error
    NetworkError,        # Network connection issue
    TimeoutError,        # Request timeout
    ValidationError      # Validation failed
)
```

### JavaScript/TypeScript
```typescript
TNKBError        // Base error
InvalidPlateError
APIError
NetworkError
```

### PHP
```php
TNKBException           // Base exception
InvalidPlateException
APIException
NetworkException
TimeoutException
ValidationException
```

## Configuration Options

All implementations support:

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `timeout` | int | 10 | Request timeout in seconds |
| `max_retries` | int | 3 | Max retry attempts on failure |
| `api_key` | string | null | Optional API authentication key |
| `base_url` | string | See above | Custom API endpoint |
| `verify_ssl` | bool | true | SSL certificate verification (Python) |

## Testing

### Python
```bash
python -m pytest tests/
python -m pytest tests/ -v --cov=tnkb
```

### JavaScript/TypeScript
```bash
npm test
npm run test:coverage
```

### PHP
```bash
php vendor/bin/phpunit
php vendor/bin/phpunit --coverage-html coverage/
```

## Performance & Reliability

- **Automatic Retries**: Exponential backoff for transient failures
- **Connection Pooling**: Reuses HTTP connections (Python requests, JS axios)
- **Timeout Handling**: Prevents hanging requests
- **Fallback Parsing**: Works offline with local region mapping
- **Type Safety**: Compile-time checks (TypeScript, PHP 7.4+)

## Examples

See the `examples/` directory for complete working examples:
- `examples/python/basic.py` - Basic Python usage
- `examples/python/advanced.py` - Advanced features
- `examples/javascript/basic.js` - JavaScript examples
- `examples/typescript/advanced.ts` - TypeScript with error handling
- `examples/php/basic.php` - PHP usage
- `examples/php/batch_processing.php` - Batch operations

## Contributing

Contributions welcome! Please:
1. Fork the repository
2. Create a feature branch
3. Add tests for new functionality
4. Submit a pull request

## License

MIT License - See LICENSE file for details

## Support

- **Issues**: GitHub Issues
- **Email**: security@nulsec.com
- **Documentation**: See README files in each language directory

## Changelog

### v1.0.0
- Initial release
- Python, JavaScript/TypeScript, PHP implementations
- Full region mapping (40+ Indonesian provinces/cities)
- Comprehensive error handling
- Automatic retry logic
- Offline fallback parsing

## Related Resources

- [Indonesian Vehicle Registration System](https://en.wikipedia.org/wiki/Vehicle_registration_plates_of_Indonesia)
- [TNKB Format Specification](https://jojapi.com/api/docs)
- [Region Code Reference](./REGION_CODES.md)

---

**Made by Security Research Team** | © 2024 NulSec
