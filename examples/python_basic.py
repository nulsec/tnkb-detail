#!/usr/bin/env python3
"""
TNKB Client - Python Basic Example
"""

from tnkb import TNKBClient
from tnkb.exceptions import InvalidPlateError, APIError

def main():
    # Initialize client with default settings
    client = TNKBClient()
    
    print("=" * 60)
    print("TNKB Client - Python Examples")
    print("=" * 60)
    
    # Example 1: Check a single plate
    print("\n[Example 1] Check Single Plate")
    print("-" * 40)
    try:
        vehicle = client.check_plate('B 1234 ABC')
        print(f"Plate: {vehicle.plate_number}")
        print(f"Region: {vehicle.region_name}")
        print(f"Region Code: {vehicle.region_code}")
        print(f"Vehicle Type: {vehicle.vehicle_type}")
        print(f"Valid: {vehicle.is_valid}")
    except InvalidPlateError as e:
        print(f"Error: {e}")
    except APIError as e:
        print(f"API Error: {e}")
    
    # Example 2: Validate plate format
    print("\n[Example 2] Validate Plate Format")
    print("-" * 40)
    plates_to_test = ['B 1234 ABC', 'A 123 DEF', 'INVALID', 'XYZ']
    for plate in plates_to_test:
        is_valid = client.validate_plate(plate)
        print(f"{plate:15} -> {'✓ Valid' if is_valid else '✗ Invalid'}")
    
    # Example 3: Get region information
    print("\n[Example 3] Get Region Information")
    print("-" * 40)
    region_codes = ['B', 'A', 'AB', 'AA', 'ZZ']
    for code in region_codes:
        region = client.get_region_info(code)
        if region:
            print(f"{code}: {region['name']} ({region['province']})")
        else:
            print(f"{code}: Not found")
    
    # Example 4: List all regions
    print("\n[Example 4] List All Regions (first 10)")
    print("-" * 40)
    regions = client.list_regions()
    for region in regions[:10]:
        print(f"{region['code']:3} - {region['name']:20} ({region['province']})")
    print(f"... and {len(regions) - 10} more regions")
    
    # Example 5: Bulk check multiple plates
    print("\n[Example 5] Bulk Check Multiple Plates")
    print("-" * 40)
    plates = [
        'B 1234 ABC',
        'A 123 DEF',
        'AB 5 CDE',
        'INVALID'
    ]
    
    vehicles = client.bulk_check(plates)
    for vehicle in vehicles:
        status = '✓' if vehicle.is_valid else '✗'
        print(f"{status} {vehicle.plate_number:15} -> {vehicle.region_name}")
    
    # Example 6: Handle errors
    print("\n[Example 6] Error Handling")
    print("-" * 40)
    invalid_plates = ['INVALID', '12345', 'ABC', '']
    for plate in invalid_plates:
        try:
            vehicle = client.check_plate(plate)
        except InvalidPlateError as e:
            print(f"✗ {plate:15} -> {e}")
        except Exception as e:
            print(f"✗ {plate:15} -> Unexpected error: {e}")
    
    print("\n" + "=" * 60)
    print("Examples completed!")
    print("=" * 60)

if __name__ == '__main__':
    main()
