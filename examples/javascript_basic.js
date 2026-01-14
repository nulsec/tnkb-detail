#!/usr/bin/env node

/**
 * TNKB Client - JavaScript Basic Example
 */

const { TNKBClient, InvalidPlateError, APIError } = require('tnkb-client');

async function main() {
    const client = new TNKBClient({
        timeout: 10000,
        maxRetries: 3
    });
    
    console.log('='.repeat(60));
    console.log('TNKB Client - JavaScript Examples');
    console.log('='.repeat(60));
    
    // Example 1: Check a single plate
    console.log('\n[Example 1] Check Single Plate');
    console.log('-'.repeat(40));
    try {
        const vehicle = await client.checkPlate('B 1234 ABC');
        console.log(`Plate: ${vehicle.plateNumber}`);
        console.log(`Region: ${vehicle.regionName}`);
        console.log(`Region Code: ${vehicle.regionCode}`);
        console.log(`Vehicle Type: ${vehicle.vehicleType}`);
        console.log(`Valid: ${vehicle.isValid}`);
    } catch (error) {
        console.error(`Error: ${error.message}`);
    }
    
    // Example 2: Validate plate format
    console.log('\n[Example 2] Validate Plate Format');
    console.log('-'.repeat(40));
    const platesToTest = ['B 1234 ABC', 'A 123 DEF', 'INVALID', 'XYZ'];
    platesToTest.forEach(plate => {
        const isValid = client.validatePlate(plate);
        const status = isValid ? '✓ Valid' : '✗ Invalid';
        console.log(`${plate.padEnd(15)} -> ${status}`);
    });
    
    // Example 3: Get region information
    console.log('\n[Example 3] Get Region Information');
    console.log('-'.repeat(40));
    const regionCodes = ['B', 'A', 'AB', 'AA', 'ZZ'];
    regionCodes.forEach(code => {
        const region = client.getRegionInfo(code);
        if (region) {
            console.log(`${code}: ${region.name} (${region.province})`);
        } else {
            console.log(`${code}: Not found`);
        }
    });
    
    // Example 4: List all regions
    console.log('\n[Example 4] List All Regions (first 10)');
    console.log('-'.repeat(40));
    const regions = client.listRegions();
    regions.slice(0, 10).forEach(region => {
        console.log(`${region.code.padEnd(3)} - ${region.name.padEnd(20)} (${region.province})`);
    });
    console.log(`... and ${regions.length - 10} more regions`);
    
    // Example 5: Bulk check multiple plates
    console.log('\n[Example 5] Bulk Check Multiple Plates');
    console.log('-'.repeat(40));
    const plates = [
        'B 1234 ABC',
        'A 123 DEF',
        'AB 5 CDE',
        'INVALID'
    ];
    
    try {
        const vehicles = await client.bulkCheck(plates);
        vehicles.forEach(vehicle => {
            const status = vehicle.isValid ? '✓' : '✗';
            console.log(`${status} ${vehicle.plateNumber.padEnd(15)} -> ${vehicle.regionName}`);
        });
    } catch (error) {
        console.error(`Error: ${error.message}`);
    }
    
    // Example 6: Handle errors
    console.log('\n[Example 6] Error Handling');
    console.log('-'.repeat(40));
    const invalidPlates = ['INVALID', '12345', 'ABC', ''];
    for (const plate of invalidPlates) {
        try {
            const vehicle = await client.checkPlate(plate);
        } catch (error) {
            console.log(`✗ ${plate.padEnd(15)} -> ${error.message}`);
        }
    }
    
    console.log('\n' + '='.repeat(60));
    console.log('Examples completed!');
    console.log('='.repeat(60));
}

main().catch(console.error);
