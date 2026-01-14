# TNKB Detail - Multi-Language Client Libraries

**Indonesian Vehicle Registration Number (TNKB) Decoder**

Professional client libraries for decoding and validating Indonesian vehicle registration numbers. Available in **Python**, **JavaScript/TypeScript**, and **PHP**.

## 🚀 Quick Start

### Choose Your Language

<details open>
<summary><b>Python</b></summary>

```bash
pip install tnkb-client
```

```python
from tnkb import TNKBClient

client = TNKBClient()
vehicle = await client.check_plate('B 1234 ABC')
print(vehicle.region_name)  # "Jawa Barat"
```

📖 [Full Python Documentation](README_PYTHON.md)

</details>

<details>
<summary><b>JavaScript/TypeScript</b></summary>

```bash
npm install tnkb-client
```

```typescript
import { TNKBClient } from 'tnkb-client';

const client = new TNKBClient();
const vehicle = await client.checkPlate('B 1234 ABC');
console.log(vehicle.regionName);  // "Jawa Barat"
```

📖 [Full TypeScript Documentation](README_TYPESCRIPT.md)

</details>

<details>
<summary><b>PHP</b></summary>

```bash
composer require nulsec/tnkb-client-php
```

```php
use TNKB\TNKBClient;

$client = new TNKBClient();
$vehicle = $client->checkPlate('B 1234 ABC');
echo $vehicle->regionName;  // "Jawa Barat"
```

📖 [Full PHP Documentation](README_PHP.md)

</details>

## ✨ Features

All implementations provide:

- ✅ **Decode TNKB Numbers**: Extract region information from plate numbers
- ✅ **Plate Validation**: Regex-based format validation
- ✅ **Region Lookup**: Complete mapping of 40+ Indonesian regions
- ✅ **Bulk Operations**: Check multiple plates efficiently
- ✅ **Error Handling**: Comprehensive exception handling
- ✅ **Automatic Retries**: Exponential backoff for network failures
- ✅ **Fallback Parsing**: Works offline with local data
- ✅ **Type Safety**: Full type hints/annotations
- ✅ **Zero Heavy Dependencies**: Minimal external libraries

## 📋 Supported Regions

Complete coverage of Indonesian vehicle registration codes:

| Code | Region | Province |
|------|--------|----------|
| A | DKI Jakarta | Jakarta |
| B | Jawa Barat | West Java |
| C | Jawa Tengah | Central Java |
| AB | Padang | West Sumatra |
| AA | Medan | North Sumatra |
| ... | 40+ regions | All provinces |

## 🔧 Common Operations

### Validate Plate Format

```python
# Python
if client.validate_plate('B 1234 ABC'):
    print("Valid format")
```

```javascript
// JavaScript/TypeScript
if (client.validatePlate('B 1234 ABC')) {
  console.log('Valid format');
}
```

```php
// PHP
if ($client->validatePlate('B 1234 ABC')) {
    echo "Valid format";
}
```

### Check Single Plate

```python
vehicle = client.check_plate('B 1234 ABC')
# vehicle.region_name, vehicle.region_code, vehicle.is_valid
```

```javascript
const vehicle = await client.checkPlate('B 1234 ABC');
// vehicle.regionName, vehicle.regionCode, vehicle.isValid
```

```php
$vehicle = $client->checkPlate('B 1234 ABC');
// $vehicle->regionName, $vehicle->regionCode, $vehicle->isValid
```

### Get Region Info

```python
region = client.get_region_info('B')
# {'code': 'B', 'name': 'Jawa Barat', 'province': 'West Java'}
```

```javascript
const region = client.getRegionInfo('B');
// { code: 'B', name: 'Jawa Barat', province: 'West Java' }
```

```php
$region = $client->getRegionInfo('B');
// RegionInfo object with code, name, province
```

### Bulk Check Plates

```python
vehicles = client.bulk_check(['B 1234 ABC', 'A 123 DEF', 'AB 5 CDE'])
for v in vehicles:
    print(f"{v.plate_number}: {v.region_name}")
```

```javascript
const vehicles = await client.bulkCheck(['B 1234 ABC', 'A 123 DEF']);
vehicles.forEach(v => console.log(`${v.plateNumber}: ${v.regionName}`));
```

```php
$vehicles = $client->bulkCheck(['B 1234 ABC', 'A 123 DEF']);
foreach ($vehicles as $v) {
    echo $v->plateNumber . ': ' . $v->regionName . "\n";
}
```

### List All Regions

```python
regions = client.list_regions()
for region in regions:
    print(f"{region['code']}: {region['name']}")
```

```javascript
const regions = client.listRegions();
regions.forEach(r => console.log(`${r.code}: ${r.name}`));
```

```php
foreach ($client->listRegions() as $region) {
    echo $region->code . ': ' . $region->name . "\n";
}
```

## ⚙️ Configuration

All clients support customization:

```python
# Python
client = TNKBClient(
    timeout=15,           # seconds
    max_retries=5,
    verify_ssl=True,
    api_key='optional-key'
)
```

```typescript
// TypeScript
const client = new TNKBClient({
  timeout: 15000,        // milliseconds
  maxRetries: 5,
  apiKey: 'optional-key',
  baseUrl: 'https://custom-api.com/api'
});
```

```php
// PHP
$config = new ClientConfig(
    timeout: 15,
    maxRetries: 5,
    apiKey: 'optional-key',
    baseUrl: 'https://jojapi.com/api'
);
$client = new TNKBClient($config);
```

## 🧪 Testing

Each implementation includes comprehensive tests:

```bash
# Python
python -m pytest tests/

# JavaScript/TypeScript
npm test

# PHP
composer test
```

## 📚 Examples

Full working examples are available in the `examples/` directory:

- [Python Basic](examples/python_basic.py)
- [JavaScript Basic](examples/javascript_basic.js)
- [PHP Basic](examples/php_basic.php)

Run examples:

```bash
# Python
python examples/python_basic.py

# JavaScript
node examples/javascript_basic.js

# PHP
php examples/php_basic.php
```

## 🌐 API Endpoints

The client uses:
- **Public API**: `https://jojapi.com/api`
- **Endpoints**:
  - `/check-kendaraan-indonesia?nopol=B%201234%20ABC`
  - `/cek-nopol-indonesia?nopol=B%201234%20ABC`

## 📖 Documentation Structure

- [CLIENT_LIBRARIES.md](CLIENT_LIBRARIES.md) - Comprehensive overview
- [README_PYTHON.md](README_PYTHON.md) - Python-specific documentation
- [README_TYPESCRIPT.md](README_TYPESCRIPT.md) - JavaScript/TypeScript documentation
- [README_PHP.md](README_PHP.md) - PHP-specific documentation
- `examples/` - Working code examples for each language

## 🔌 Framework Integration

### Django Integration (Python)

```python
from django.views import View
from tnkb import TNKBClient

class VehicleCheckView(View):
    def __init__(self):
        self.client = TNKBClient()
    
    def post(self, request):
        plate = request.POST.get('plate')
        vehicle = self.client.check_plate(plate)
        return JsonResponse(vehicle.to_dict())
```

### Express Integration (Node.js)

```javascript
const { TNKBClient } = require('tnkb-client');
const client = new TNKBClient();

app.post('/check', async (req, res) => {
  try {
    const vehicle = await client.checkPlate(req.body.plate);
    res.json(vehicle);
  } catch (error) {
    res.status(400).json({ error: error.message });
  }
});
```

### Laravel Integration (PHP)

```php
Route::post('/vehicle/check', function (Request $request) {
    $client = app(TNKBClient::class);
    $vehicle = $client->checkPlate($request->plate);
    return response()->json($vehicle->toArray());
});
```

## 🛠️ Development Setup

### Python

```bash
cd /workspaces/tnkb-detail
python -m venv venv
source venv/bin/activate
pip install -r requirements.txt
pip install -e .
```

### Node.js/TypeScript

```bash
cd /workspaces/tnkb-detail
npm install
npm run build
npm test
```

### PHP

```bash
cd /workspaces/tnkb-detail/php
composer install
composer test
```

## 📦 Package Distribution

### Python (PyPI)

```bash
pip install tnkb-client
```

### JavaScript/TypeScript (npm)

```bash
npm install tnkb-client
```

### PHP (Composer)

```bash
composer require nulsec/tnkb-client-php
```

## 🚨 Error Handling

All implementations provide specific exceptions for different error scenarios:

```python
# Python
try:
    vehicle = client.check_plate('INVALID')
except InvalidPlateError as e:
    print(f"Format error: {e}")
except APIError as e:
    print(f"API error: {e}")
except NetworkError as e:
    print(f"Network error: {e}")
```

```typescript
// TypeScript
try {
  const vehicle = await client.checkPlate('INVALID');
} catch (error) {
  if (error instanceof InvalidPlateError) {
    console.error('Format error:', error.message);
  } else if (error instanceof APIError) {
    console.error('API error:', error.message);
  }
}
```

```php
// PHP
try {
    $vehicle = $client->checkPlate('INVALID');
} catch (InvalidPlateException $e) {
    echo "Format error: " . $e->getMessage();
} catch (APIException $e) {
    echo "API error: " . $e->getMessage();
}
```

## 🔄 Migration Guide

If you're migrating from another library, here's how to adapt:

### From Another Python Library

```python
# Old code
# result = old_library.decode('B 1234 ABC')

# New code
from tnkb import TNKBClient
client = TNKBClient()
vehicle = client.check_plate('B 1234 ABC')
print(vehicle.region_name)
```

### From Another JavaScript Library

```javascript
// Old code
// const result = await oldLibrary.decode('B 1234 ABC');

// New code
const { TNKBClient } = require('tnkb-client');
const client = new TNKBClient();
const vehicle = await client.checkPlate('B 1234 ABC');
console.log(vehicle.regionName);
```

## 📊 Performance

- **Latency**: < 500ms for API calls
- **Fallback**: < 10ms for local parsing
- **Retry Logic**: Exponential backoff (1s, 2s, 4s)
- **Connection Pooling**: Automatic (all languages)
- **Memory**: < 5MB for library + data

## 🐛 Troubleshooting

### Common Issues

**Q: Getting connection timeout?**
```python
# Increase timeout
client = TNKBClient(timeout=30)
```

**Q: Too many retries?**
```python
# Reduce retries
client = TNKBClient(max_retries=1)
```

**Q: Invalid SSL certificate?**
```python
# Disable SSL verification (not recommended for production)
client = TNKBClient(verify_ssl=False)
```

## 📝 License

MIT License - See LICENSE file for details

## 🤝 Contributing

Contributions welcome! Please:
1. Fork the repository
2. Create a feature branch
3. Add tests for new functionality
4. Submit a pull request

## 📞 Support

- **Issues**: GitHub Issues
- **Email**: security@nulsec.com
- **Documentation**: See language-specific README files

## 🎯 Roadmap

- [ ] GraphQL API support
- [ ] WebSocket support for real-time checks
- [ ] Caching layer (Redis)
- [ ] Rate limiting support
- [ ] Batch import from CSV
- [ ] Web UI for testing

---

**Multi-Language TNKB Client Libraries** | Built by Security Research Team | © 2024 NulSec
