# TNKB Client Libraries - Complete Documentation Index

## 📚 Documentation Files

### Main Documentation
- **[README_MAIN.md](README_MAIN.md)** - Start here! Overview of all three implementations
- **[CLIENT_LIBRARIES.md](CLIENT_LIBRARIES.md)** - Comprehensive feature overview and API reference

### Language-Specific Guides
- **[README_PYTHON.md](README_PYTHON.md)** - Python package documentation
- **[README_TYPESCRIPT.md](README_TYPESCRIPT.md)** - JavaScript/TypeScript documentation
- **[README_PHP.md](README_PHP.md)** - PHP package documentation

### Code Examples
- **[examples/python_basic.py](examples/python_basic.py)** - Python usage examples
- **[examples/javascript_basic.js](examples/javascript_basic.js)** - JavaScript examples
- **[examples/php_basic.php](examples/php_basic.php)** - PHP examples

## 🎯 Quick Navigation

### I want to...

**Use Python**
1. Install: `pip install tnkb-client`
2. Read: [README_PYTHON.md](README_PYTHON.md)
3. Example: [examples/python_basic.py](examples/python_basic.py)

**Use JavaScript/TypeScript**
1. Install: `npm install tnkb-client`
2. Read: [README_TYPESCRIPT.md](README_TYPESCRIPT.md)
3. Example: [examples/javascript_basic.js](examples/javascript_basic.js)

**Use PHP**
1. Install: `composer require nulsec/tnkb-client-php`
2. Read: [README_PHP.md](README_PHP.md)
3. Example: [examples/php_basic.php](examples/php_basic.php)

**Understand all features at once**
- Read: [CLIENT_LIBRARIES.md](CLIENT_LIBRARIES.md)

**See an overview**
- Read: [README_MAIN.md](README_MAIN.md)

## 🗂️ Project Structure

```
tnkb-detail/
├── README_MAIN.md                 # Main overview (start here)
├── CLIENT_LIBRARIES.md            # Comprehensive documentation
├── README_PYTHON.md               # Python-specific guide
├── README_TYPESCRIPT.md           # TypeScript-specific guide
├── README_PHP.md                  # PHP-specific guide
├── DOCUMENTATION_INDEX.md         # This file
│
├── Python Implementation
├── setup.py                       # Python package setup
├── tnkb/
│   ├── __init__.py               # Package initialization
│   ├── client.py                 # Main Python client (400+ lines)
│   ├── models.py                 # Data models (VehicleInfo, RegionInfo)
│   └── exceptions.py             # Custom exceptions
├── tests/
│   └── test_client.py            # Python unit tests
├── requirements.txt
│
├── JavaScript/TypeScript Implementation
├── package.json                   # NPM package configuration
├── jest.config.js                # Jest test configuration
├── src/
│   └── index.ts                  # TypeScript implementation (450+ lines)
├── tests/
│   └── client.test.ts            # TypeScript unit tests
├── tsconfig.json                 # TypeScript configuration
│
├── PHP Implementation
├── php/
│   ├── composer.json             # Composer package config
│   ├── src/
│   │   ├── Exceptions.php        # Exception classes
│   │   ├── Models.php            # VehicleInfo, RegionInfo classes
│   │   └── TNKBClient.php        # Main PHP client (400+ lines)
│   └── tests/
│       └── TNKBClientTest.php    # PHPUnit tests
│
├── Examples
├── examples/
│   ├── python_basic.py           # Python example
│   ├── javascript_basic.js       # JavaScript example
│   └── php_basic.php             # PHP example
│
└── Version Control
    └── .git/
```

## 📖 Content Guide

### By Topic

**Getting Started**
- [README_MAIN.md](README_MAIN.md) - Quick start for each language
- Example files in [examples/](examples/) directory

**API Reference**
- [CLIENT_LIBRARIES.md](CLIENT_LIBRARIES.md) - Complete API documentation
- Language-specific READMEs for details

**Error Handling**
- [README_PYTHON.md](README_PYTHON.md#error-handling)
- [README_TYPESCRIPT.md](README_TYPESCRIPT.md#error-handling)
- [README_PHP.md](README_PHP.md#error-handling)

**Configuration**
- [README_MAIN.md](README_MAIN.md#-configuration)
- [CLIENT_LIBRARIES.md](CLIENT_LIBRARIES.md#configuration-options)

**Testing**
- [README_MAIN.md](README_MAIN.md#-testing)
- Each language has its own test runner

**Framework Integration**
- [README_MAIN.md](README_MAIN.md#-framework-integration)
- Django, Express, Laravel examples

**Performance & Optimization**
- [CLIENT_LIBRARIES.md](CLIENT_LIBRARIES.md#performance--reliability)
- [README_MAIN.md](README_MAIN.md#-performance)

## 🚀 Implementation Details

### Python Client
- **File**: [tnkb/client.py](tnkb/client.py)
- **Lines**: 400+
- **Features**:
  - `TNKBClient` class with 5 main methods
  - Automatic retry logic with exponential backoff
  - Request timeout handling
  - SSL verification options
  - Optional API key authentication
  - Local parsing fallback
  - Context manager support
  - Complete error handling (6 exception types)

### TypeScript Client
- **File**: [src/index.ts](src/index.ts)
- **Lines**: 450+
- **Features**:
  - `TNKBClient` class with async methods
  - Full TypeScript type definitions
  - Axios HTTP client integration
  - Retry interceptor
  - Error handling with 3 exception types
  - Region mapping with 40+ entries
  - Bulk operations support

### PHP Client
- **File**: [php/src/TNKBClient.php](php/src/TNKBClient.php)
- **Lines**: 400+
- **Features**:
  - `TNKBClient` class with 5 main methods
  - Guzzle HTTP client integration
  - Automatic retries with exponential backoff
  - Configuration via `ClientConfig` class
  - Data models: `VehicleInfo`, `RegionInfo`
  - Exception hierarchy (5 exception types)
  - JSON serialization support

## 📊 Feature Comparison

| Feature | Python | TypeScript | PHP |
|---------|--------|-----------|-----|
| Check Plate | ✅ | ✅ | ✅ |
| Validate Format | ✅ | ✅ | ✅ |
| Get Region Info | ✅ | ✅ | ✅ |
| List Regions | ✅ | ✅ | ✅ |
| Bulk Check | ✅ | ✅ | ✅ |
| Retry Logic | ✅ | ✅ | ✅ |
| Type Safety | ✅ | ✅ | ✅ |
| Error Handling | ✅ | ✅ | ✅ |
| Tests | ✅ | ✅ | ✅ |
| Examples | ✅ | ✅ | ✅ |

## 🔍 API Methods (All Languages)

### Core Methods
1. **checkPlate(plateNumber)** - Decode vehicle plate
2. **validatePlate(plateNumber)** - Validate format only
3. **getRegionInfo(regionCode)** - Get region details
4. **listRegions()** - List all supported regions
5. **bulkCheck(plateNumbers)** - Check multiple plates

### Return Types
- **VehicleInfo**: Decoded plate information
  - plateNumber
  - regionCode
  - regionName
  - vehicleType
  - isValid
  - details (API response)
  - createdAt (timestamp)

- **RegionInfo**: Region metadata
  - code
  - name
  - province

## 🛠️ Configuration Options

All three implementations support:

| Option | Type | Default | Purpose |
|--------|------|---------|---------|
| timeout | int | 10s | Request timeout |
| maxRetries | int | 3 | Retry attempts |
| apiKey | string | null | API authentication |
| baseUrl | string | jojapi.com | API endpoint |
| verifySsl | bool | true | SSL verification (Python) |

## 📦 Dependencies

### Python
- **Core**: requests, python-dotenv
- **Optional**: pytest (testing)

### TypeScript/JavaScript
- **Core**: axios
- **Dev**: typescript, jest, eslint, prettier

### PHP
- **Core**: guzzlehttp/guzzle, vlucas/phpdotenv
- **Dev**: phpunit, phpstan, php-cs-fixer

## 🧪 Testing

### Python
```bash
python -m pytest tests/ -v --cov=tnkb
```

### TypeScript
```bash
npm test
npm run test:coverage
```

### PHP
```bash
./vendor/bin/phpunit
./vendor/bin/phpunit --coverage-html coverage/
```

## 🌍 Supported Regions

All implementations include mapping for 40+ Indonesian provinces and major cities:

**Major Regions:**
- A: DKI Jakarta
- B: Jawa Barat (West Java)
- C: Jawa Tengah (Central Java)
- AA: Medan (North Sumatra)
- AB: Padang (West Sumatra)
- And 35+ more...

See [CLIENT_LIBRARIES.md](CLIENT_LIBRARIES.md#supported-regions) for complete list.

## 🔒 Security Considerations

- **SSL Verification**: Enabled by default
- **Timeout Prevention**: Prevents hanging connections
- **Error Messages**: Safe error messaging without data leaks
- **No Data Logging**: Sensitive plate numbers not logged by default
- **Input Validation**: Regex validation before API calls

## 💡 Best Practices

1. **Reuse Clients**: Create once, use multiple times
2. **Error Handling**: Always catch exceptions
3. **Timeouts**: Set appropriate timeouts for your use case
4. **Retries**: Use automatic retry logic
5. **Bulk Operations**: Use bulkCheck for multiple plates
6. **Caching**: Cache results to reduce API calls
7. **Environment Variables**: Use `.env` for configuration

## 🐛 Common Issues & Solutions

See language-specific README files for detailed troubleshooting:
- [Python Troubleshooting](README_PYTHON.md#troubleshooting)
- [TypeScript Troubleshooting](README_TYPESCRIPT.md#troubleshooting)
- [PHP Troubleshooting](README_PHP.md#troubleshooting)

## 📞 Support & Contributing

- **Issues**: GitHub Issues
- **Email**: security@nulsec.com
- **Pull Requests**: Welcome!

## 📋 Checklist for New Users

- [ ] Read [README_MAIN.md](README_MAIN.md)
- [ ] Choose your language
- [ ] Read language-specific README
- [ ] Run example file
- [ ] Install package
- [ ] Try basic example
- [ ] Handle errors properly
- [ ] Configure for your needs
- [ ] Run tests
- [ ] Check documentation for advanced features

## 📄 License

MIT License - See LICENSE file in repository

## 🎉 What's Included

- ✅ Complete client implementations (Python, TypeScript, PHP)
- ✅ Full type safety and annotations
- ✅ Comprehensive error handling
- ✅ Automatic retry logic
- ✅ 40+ region mappings
- ✅ Unit tests for all implementations
- ✅ Working code examples
- ✅ Framework integration guides
- ✅ Complete documentation

---

**Last Updated**: January 2024
**Status**: Production Ready
**Versions**: 
- Python: 1.0.0
- TypeScript/JavaScript: 1.0.0
- PHP: 1.0.0
