import { TNKBClient, InvalidPlateError, APIError } from '../src/index';

describe('TNKBClient', () => {
  let client: TNKBClient;

  beforeEach(() => {
    client = new TNKBClient({
      timeout: 5000,
      maxRetries: 2,
    });
  });

  describe('validatePlate', () => {
    it('should validate correct plate format', () => {
      expect(client.validatePlate('B 1234 ABC')).toBe(true);
      expect(client.validatePlate('AB 123 CDE')).toBe(true);
    });

    it('should reject invalid plate format', () => {
      expect(client.validatePlate('INVALID')).toBe(false);
      expect(client.validatePlate('1234567')).toBe(false);
      expect(client.validatePlate('')).toBe(false);
    });
  });

  describe('getRegionInfo', () => {
    it('should return correct region info', () => {
      const info = client.getRegionInfo('B');
      expect(info).not.toBeNull();
      expect(info?.name).toBe('Jawa Barat');
      expect(info?.code).toBe('B');
    });

    it('should return null for unknown region', () => {
      const info = client.getRegionInfo('ZZ');
      expect(info).toBeNull();
    });

    it('should be case insensitive', () => {
      const info1 = client.getRegionInfo('b');
      const info2 = client.getRegionInfo('B');
      expect(info1?.code).toBe(info2?.code);
    });
  });

  describe('listRegions', () => {
    it('should return all regions', () => {
      const regions = client.listRegions();
      expect(regions.length).toBeGreaterThan(30);
    });

    it('should have correct structure', () => {
      const regions = client.listRegions();
      regions.forEach(region => {
        expect(region.code).toBeDefined();
        expect(region.name).toBeDefined();
        expect(region.province).toBeDefined();
      });
    });

    it('should be sorted by code', () => {
      const regions = client.listRegions();
      const codes = regions.map(r => r.code);
      const sorted = [...codes].sort();
      expect(codes).toEqual(sorted);
    });
  });

  describe('checkPlate', () => {
    it('should handle valid plates', async () => {
      const result = await client.checkPlate('B 1234 ABC');
      expect(result.plateNumber).toBeDefined();
      expect(result.regionCode).toBeDefined();
      expect(result.regionName).toBeDefined();
      expect(result.isValid).toBeDefined();
    });

    it('should reject invalid plate format', async () => {
      try {
        await client.checkPlate('INVALID');
        expect(true).toBe(false); // Should not reach here
      } catch (error) {
        expect(error).toBeInstanceOf(InvalidPlateError);
      }
    });

    it('should normalize plate format', async () => {
      const result = await client.checkPlate('b 1234 abc');
      expect(result.plateNumber).toMatch(/^[A-Z]+\s?\d+\s?[A-Z]+$/);
    });
  });

  describe('bulkCheck', () => {
    it('should check multiple plates', async () => {
      const plates = ['B 1234 ABC', 'A 123 DEF'];
      const results = await client.bulkCheck(plates);
      expect(results.length).toBe(2);
      expect(results[0].plateNumber).toBeDefined();
      expect(results[1].plateNumber).toBeDefined();
    });

    it('should handle invalid plates in bulk', async () => {
      const plates = ['B 1234 ABC', 'INVALID'];
      const results = await client.bulkCheck(plates);
      expect(results.length).toBe(2);
      expect(results[1].isValid).toBe(false);
    });
  });
});
