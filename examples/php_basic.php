<?php
/**
 * TNKB Client - PHP Basic Example
 */

require_once __DIR__ . '/../php/vendor/autoload.php';

use TNKB\TNKBClient;
use TNKB\ClientConfig;
use TNKB\InvalidPlateException;
use TNKB\APIException;

$client = new TNKBClient();

echo str_repeat('=', 60) . "\n";
echo "TNKB Client - PHP Examples\n";
echo str_repeat('=', 60) . "\n";

// Example 1: Check a single plate
echo "\n[Example 1] Check Single Plate\n";
echo str_repeat('-', 40) . "\n";
try {
    $vehicle = $client->checkPlate('B 1234 ABC');
    echo "Plate: " . $vehicle->plateNumber . "\n";
    echo "Region: " . $vehicle->regionName . "\n";
    echo "Region Code: " . $vehicle->regionCode . "\n";
    echo "Vehicle Type: " . $vehicle->vehicleType . "\n";
    echo "Valid: " . ($vehicle->isValid ? "Yes" : "No") . "\n";
} catch (InvalidPlateException $e) {
    echo "Error: " . $e->getMessage() . "\n";
} catch (APIException $e) {
    echo "API Error: " . $e->getMessage() . "\n";
}

// Example 2: Validate plate format
echo "\n[Example 2] Validate Plate Format\n";
echo str_repeat('-', 40) . "\n";
$platesToTest = ['B 1234 ABC', 'A 123 DEF', 'INVALID', 'XYZ'];
foreach ($platesToTest as $plate) {
    $isValid = $client->validatePlate($plate);
    $status = $isValid ? '✓ Valid' : '✗ Invalid';
    printf("%s -> %s\n", str_pad($plate, 15), $status);
}

// Example 3: Get region information
echo "\n[Example 3] Get Region Information\n";
echo str_repeat('-', 40) . "\n";
$regionCodes = ['B', 'A', 'AB', 'AA', 'ZZ'];
foreach ($regionCodes as $code) {
    $region = $client->getRegionInfo($code);
    if ($region) {
        printf("%s: %s (%s)\n", $code, $region->name, $region->province);
    } else {
        printf("%s: Not found\n", $code);
    }
}

// Example 4: List all regions
echo "\n[Example 4] List All Regions (first 10)\n";
echo str_repeat('-', 40) . "\n";
$regions = $client->listRegions();
$count = 0;
foreach ($regions as $region) {
    if ($count >= 10) break;
    printf("%s - %-20s (%s)\n", 
        str_pad($region->code, 3), 
        $region->name, 
        $region->province
    );
    $count++;
}
printf("... and %d more regions\n", count($regions) - 10);

// Example 5: Bulk check multiple plates
echo "\n[Example 5] Bulk Check Multiple Plates\n";
echo str_repeat('-', 40) . "\n";
$plates = [
    'B 1234 ABC',
    'A 123 DEF',
    'AB 5 CDE',
    'INVALID'
];

try {
    $vehicles = $client->bulkCheck($plates);
    foreach ($vehicles as $vehicle) {
        $status = $vehicle->isValid ? '✓' : '✗';
        printf("%s %s -> %s\n", 
            $status, 
            str_pad($vehicle->plateNumber, 15), 
            $vehicle->regionName
        );
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Example 6: Export to JSON
echo "\n[Example 6] Export to JSON\n";
echo str_repeat('-', 40) . "\n";
try {
    $vehicle = $client->checkPlate('B 1234 ABC');
    echo $vehicle->toJson() . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Example 7: Handle errors
echo "\n[Example 7] Error Handling\n";
echo str_repeat('-', 40) . "\n";
$invalidPlates = ['INVALID', '12345', 'ABC', ''];
foreach ($invalidPlates as $plate) {
    try {
        $vehicle = $client->checkPlate($plate);
    } catch (InvalidPlateException $e) {
        printf("✗ %s -> %s\n", str_pad($plate ?: '(empty)', 15), $e->getMessage());
    } catch (\Exception $e) {
        printf("✗ %s -> Unexpected error: %s\n", str_pad($plate ?: '(empty)', 15), $e->getMessage());
    }
}

echo "\n" . str_repeat('=', 60) . "\n";
echo "Examples completed!\n";
echo str_repeat('=', 60) . "\n";
