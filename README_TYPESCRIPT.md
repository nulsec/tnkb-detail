# TNKB Client - TypeScript/JavaScript

Indonesian Vehicle Registration Number (TNKB) decoder for Node.js and TypeScript projects.

## Installation

```bash
npm install tnkb-client
```

## Quick Start

### JavaScript

```javascript
const { TNKBClient } = require('tnkb-client');

async function main() {
  const client = new TNKBClient();
  const vehicle = await client.checkPlate('B 1234 ABC');
  console.log(`Region: ${vehicle.regionName}`);
}

main();
```

### TypeScript

```typescript
import { TNKBClient, VehicleInfo } from 'tnkb-client';

async function main(): Promise<void> {
  const client = new TNKBClient();
  const vehicle: VehicleInfo = await client.checkPlate('B 1234 ABC');
  console.log(`Region: ${vehicle.regionName}`);
}

main();
```

## Features

- ✅ Validate Indonesian plate numbers
- ✅ Decode region information (40+ regions)
- ✅ Check multiple plates in batch
- ✅ Automatic retry with exponential backoff
- ✅ TypeScript support with full type definitions
- ✅ Zero external dependencies (uses axios)

## API Reference

### TNKBClient

#### Constructor Options

```typescript
interface TNKBClientConfig {
  timeout?: number;           // Request timeout in ms (default: 10000)
  maxRetries?: number;        // Max retry attempts (default: 3)
  apiKey?: string;            // Optional API authentication
  baseUrl?: string;           // Custom API endpoint
}
```

#### Methods

##### `checkPlate(plateNumber: string): Promise<VehicleInfo>`

Check and decode a vehicle plate number.

```typescript
const vehicle = await client.checkPlate('B 1234 ABC');
console.log(vehicle.regionName);  // "Jawa Barat"
console.log(vehicle.isValid);     // true
```

##### `validatePlate(plateNumber: string): boolean`

Validate plate format without making API call.

```typescript
if (client.validatePlate('B 1234 ABC')) {
  console.log('Valid format');
}
```

##### `getRegionInfo(regionCode: string): RegionInfo | null`

Get information about a region.

```typescript
const info = client.getRegionInfo('B');
// { code: 'B', name: 'Jawa Barat', province: 'West Java' }
```

##### `listRegions(): RegionInfo[]`

List all supported regions.

```typescript
const regions = client.listRegions();
regions.forEach(r => {
  console.log(`${r.code}: ${r.name}`);
});
```

##### `bulkCheck(plateNumbers: string[]): Promise<VehicleInfo[]>`

Check multiple plates efficiently.

```typescript
const results = await client.bulkCheck([
  'B 1234 ABC',
  'A 123 DEF',
  'AB 5 CDE'
]);
```

## Error Handling

```typescript
import { 
  TNKBClient, 
  InvalidPlateError, 
  APIError 
} from 'tnkb-client';

try {
  const vehicle = await client.checkPlate('INVALID');
} catch (error) {
  if (error instanceof InvalidPlateError) {
    console.error('Invalid plate format');
  } else if (error instanceof APIError) {
    console.error('API error:', error.message);
  }
}
```

## Configuration

```typescript
const client = new TNKBClient({
  timeout: 15000,           // 15 seconds
  maxRetries: 5,            // Try up to 5 times
  apiKey: process.env.API_KEY,
  baseUrl: 'https://custom-api.com/api'
});
```

## Examples

### Check Single Plate

```typescript
const client = new TNKBClient();
const result = await client.checkPlate('B 1234 ABC');
console.log(JSON.stringify(result, null, 2));
```

### Batch Processing

```typescript
const plates = ['B 1234 ABC', 'A 123 DEF', 'AB 5 CDE'];
const results = await client.bulkCheck(plates);

results.forEach(vehicle => {
  console.log(`${vehicle.plateNumber}: ${vehicle.regionName}`);
});
```

### List All Regions

```typescript
const regions = client.listRegions();
console.table(regions);
```

### Error Handling

```typescript
async function checkPlate(plate: string) {
  try {
    const result = await client.checkPlate(plate);
    if (result.isValid) {
      return `✓ ${result.regionName}`;
    }
  } catch (error) {
    console.error(`✗ ${error.message}`);
  }
}
```

## Testing

```bash
# Run tests
npm test

# Run with coverage
npm run test:coverage

# Watch mode
npm test -- --watch
```

## Types

The package includes full TypeScript definitions:

```typescript
interface VehicleInfo {
  plateNumber: string;
  regionCode: string;
  regionName: string;
  vehicleType: string;
  isValid: boolean;
  details?: Record<string, unknown>;
  createdAt?: Date;
}

interface RegionInfo {
  code: string;
  name: string;
  province?: string;
}
```

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

See `src/index.ts` for complete region mapping.

## Performance Tips

1. **Reuse client instance**: Create once, use multiple times
2. **Batch checking**: Use `bulkCheck()` for multiple plates
3. **Local validation**: Use `validatePlate()` before `checkPlate()`
4. **Handle retries**: Configure `maxRetries` based on your needs

## Troubleshooting

### Connection Timeout
```typescript
const client = new TNKBClient({
  timeout: 20000  // Increase timeout to 20 seconds
});
```

### Invalid Credentials
```typescript
const client = new TNKBClient({
  apiKey: 'your-valid-api-key'
});
```

### Rate Limiting
Implement request throttling:
```typescript
import pLimit from 'p-limit';

const limit = pLimit(5);  // Max 5 concurrent requests
const plates = [...];
const results = await Promise.all(
  plates.map(p => limit(() => client.checkPlate(p)))
);
```

## Development

```bash
# Install dependencies
npm install

# Build
npm run build

# Lint
npm run lint

# Format
npm run format

# Run tests
npm test
```

## Dependencies

- **axios**: HTTP client for making requests
- **dotenv**: Environment variable management

## Related Packages

- **Python**: `pip install tnkb-client`
- **PHP**: `composer require nulsec/tnkb-client-php`

## License

MIT

## Support

- Issues: GitHub Issues
- Email: security@nulsec.com
