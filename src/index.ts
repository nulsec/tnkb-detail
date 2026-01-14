/**
 * TNKB Client - TypeScript/JavaScript Implementation
 * Indonesian Vehicle Registration Number Decoder
 */

import axios, { AxiosInstance, AxiosError } from 'axios';

/**
 * Vehicle information from TNKB
 */
export interface VehicleInfo {
  plateNumber: string;
  regionCode: string;
  regionName: string;
  vehicleType: string;
  isValid: boolean;
  details?: Record<string, unknown>;
  createdAt?: Date;
}

/**
 * Region information
 */
export interface RegionInfo {
  code: string;
  name: string;
  province?: string;
  description?: string;
}

/**
 * TNKB Client configuration
 */
export interface TNKBClientConfig {
  timeout?: number;
  maxRetries?: number;
  verifySsl?: boolean;
  apiKey?: string;
  baseUrl?: string;
}

/**
 * Custom errors
 */
export class TNKBError extends Error {
  constructor(message: string) {
    super(message);
    this.name = 'TNKBError';
  }
}

export class InvalidPlateError extends TNKBError {
  constructor(message: string) {
    super(message);
    this.name = 'InvalidPlateError';
  }
}

export class APIError extends TNKBError {
  constructor(message: string) {
    super(message);
    this.name = 'APIError';
  }
}

export class NetworkError extends APIError {
  constructor(message: string) {
    super(message);
    this.name = 'NetworkError';
  }
}

/**
 * Indonesian region codes mapping
 */
const REGION_CODES: Record<string, { name: string; province: string }> = {
  'A': { name: 'DKI Jakarta', province: 'Jakarta' },
  'B': { name: 'Jawa Barat', province: 'West Java' },
  'C': { name: 'Jawa Tengah', province: 'Central Java' },
  'D': { name: 'Bandung', province: 'West Java' },
  'E': { name: 'Pekalongan', province: 'Central Java' },
  'F': { name: 'Purwokerto', province: 'Central Java' },
  'G': { name: 'Yogyakarta', province: 'Yogyakarta' },
  'H': { name: 'Surabaya', province: 'East Java' },
  'K': { name: 'Semarang', province: 'Central Java' },
  'L': { name: 'Surabaya', province: 'East Java' },
  'M': { name: 'Madura', province: 'East Java' },
  'N': { name: 'Malang', province: 'East Java' },
  'P': { name: 'Banyumas', province: 'Central Java' },
  'R': { name: 'Pati', province: 'Central Java' },
  'S': { name: 'Karanganyar', province: 'Central Java' },
  'T': { name: 'Sidoarjo', province: 'East Java' },
  'U': { name: 'Tegal', province: 'Central Java' },
  'W': { name: 'Mataram', province: 'West Nusa Tenggara' },
  'AA': { name: 'Medan', province: 'North Sumatra' },
  'AB': { name: 'Padang', province: 'West Sumatra' },
  'AD': { name: 'Aceh', province: 'Aceh' },
  'AE': { name: 'Pekanbaru', province: 'Riau' },
  'AG': { name: 'Jambi', province: 'Jambi' },
  'BA': { name: 'Bengkulu', province: 'Bengkulu' },
  'BB': { name: 'Lampung', province: 'Lampung' },
  'BC': { name: 'Palembang', province: 'South Sumatra' },
  'BD': { name: 'Pangkal Pinang', province: 'Bangka Belitung' },
  'BE': { name: 'Bandar Lampung', province: 'Lampung' },
  'BK': { name: 'Batam', province: 'Riau Islands' },
  'BM': { name: 'Banjarmasin', province: 'South Kalimantan' },
  'BN': { name: 'Balikpapan', province: 'East Kalimantan' },
  'BP': { name: 'Pontianak', province: 'West Kalimantan' },
  'BR': { name: 'Samarinda', province: 'East Kalimantan' },
  'BS': { name: 'Palangka Raya', province: 'Central Kalimantan' },
  'BT': { name: 'Banjarmasin', province: 'South Kalimantan' },
  'CC': { name: 'Manado', province: 'North Sulawesi' },
  'CD': { name: 'Gorontalo', province: 'Gorontalo' },
  'CT': { name: 'Palu', province: 'Central Sulawesi' },
  'DA': { name: 'Makassar', province: 'South Sulawesi' },
  'DB': { name: 'Pare-Pare', province: 'South Sulawesi' },
  'DC': { name: 'Kendari', province: 'Southeast Sulawesi' },
  'DD': { name: 'Baubau', province: 'Southeast Sulawesi' },
  'DE': { name: 'Ambon', province: 'Maluku' },
  'EB': { name: 'Manado', province: 'North Sulawesi' },
  'ED': { name: 'Denpasar', province: 'Bali' },
  'EE': { name: 'Mataram', province: 'West Nusa Tenggara' },
  'EF': { name: 'Kupang', province: 'East Nusa Tenggara' },
  'KB': { name: 'Jayapura', province: 'Papua' },
  'PA': { name: 'Pontianak', province: 'West Kalimantan' },
};

/**
 * TNKB Client for Indonesian Vehicle Registration Numbers
 */
export class TNKBClient {
  private axiosInstance: AxiosInstance;
  private readonly apiBaseUrl: string;
  private readonly checkKendaraanEndpoint: string;
  private readonly cekNopolEndpoint: string;
  private readonly platePattern: RegExp = /^([A-Z]{1,2})\s?(\d{1,4})\s?([A-Z]{1,3})$/;

  constructor(config: TNKBClientConfig = {}) {
    const {
      timeout = 10000,
      maxRetries = 3,
      apiKey,
      baseUrl = 'https://jojapi.com/api',
    } = config;

    this.apiBaseUrl = baseUrl;
    this.checkKendaraanEndpoint = `${this.apiBaseUrl}/check-kendaraan-indonesia`;
    this.cekNopolEndpoint = `${this.apiBaseUrl}/cek-nopol-indonesia`;

    this.axiosInstance = axios.create({
      timeout,
      headers: {
        'User-Agent': 'TNKBClient/1.0.0 (JavaScript)',
        'Content-Type': 'application/json',
      },
    });

    if (apiKey) {
      this.axiosInstance.defaults.headers.common['Authorization'] = `Bearer ${apiKey}`;
    }

    // Add retry interceptor
    this.axiosInstance.interceptors.response.use(
      response => response,
      async error => {
        const config = error.config;
        if (!config) throw error;

        config.retryCount = config.retryCount || 0;
        if (config.retryCount >= maxRetries) {
          return Promise.reject(error);
        }

        config.retryCount += 1;
        await new Promise(resolve => setTimeout(resolve, 1000 * config.retryCount));
        return this.axiosInstance(config);
      }
    );
  }

  /**
   * Check and decode Indonesian vehicle plate number
   */
  async checkPlate(plateNumber: string): Promise<VehicleInfo> {
    const normalized = this.normalizePlate(plateNumber);
    const [regionCode] = this.parsePlate(normalized);

    try {
      const response = await this.callApi(this.checkKendaraanEndpoint, {
        nopol: normalized,
      });
      return this.buildVehicleInfo(response, normalized);
    } catch (error) {
      console.warn('API call failed, falling back to local parsing', error);
      return this.parseLocally(normalized, regionCode);
    }
  }

  /**
   * Validate Indonesian vehicle plate format
   */
  validatePlate(plateNumber: string): boolean {
    try {
      this.validatePlateFormat(plateNumber);
      return true;
    } catch {
      return false;
    }
  }

  /**
   * Get information about a registration region
   */
  getRegionInfo(regionCode: string): RegionInfo | null {
    const code = regionCode.toUpperCase();
    const info = REGION_CODES[code];
    if (!info) return null;
    return { code, ...info };
  }

  /**
   * List all Indonesian vehicle registration regions
   */
  listRegions(): RegionInfo[] {
    return Object.entries(REGION_CODES)
      .sort(([codeA], [codeB]) => codeA.localeCompare(codeB))
      .map(([code, info]) => ({
        code,
        name: info.name,
        province: info.province,
      }));
  }

  /**
   * Check multiple plate numbers
   */
  async bulkCheck(plateNumbers: string[]): Promise<VehicleInfo[]> {
    const results: VehicleInfo[] = [];
    for (const plate of plateNumbers) {
      try {
        results.push(await this.checkPlate(plate));
      } catch (error) {
        results.push(this.createInvalidVehicle(plate, String(error)));
      }
    }
    return results;
  }

  /**
   * Normalize plate number format
   */
  private normalizePlate(plateNumber: string): string {
    if (!plateNumber) {
      throw new InvalidPlateError('Plate number cannot be empty');
    }

    const normalized = plateNumber
      .toUpperCase()
      .split(/\s+/)
      .join(' ')
      .trim();

    this.validatePlateFormat(normalized);
    return normalized;
  }

  /**
   * Validate plate number format
   */
  private validatePlateFormat(plateNumber: string): void {
    if (!this.platePattern.test(plateNumber)) {
      throw new InvalidPlateError(
        `Invalid plate format: ${plateNumber}. Expected format: '[A-Z] [1-4 DIGITS] [A-Z]'`
      );
    }
  }

  /**
   * Parse plate into components
   */
  private parsePlate(plateNumber: string): [string, string, string] {
    const match = plateNumber.match(this.platePattern);
    if (!match) {
      throw new InvalidPlateError(`Cannot parse plate: ${plateNumber}`);
    }
    return [match[1], match[2], match[3]];
  }

  /**
   * Make API request with error handling
   */
  private async callApi(
    endpoint: string,
    params: Record<string, unknown>
  ): Promise<Record<string, unknown>> {
    try {
      const response = await this.axiosInstance.get(endpoint, { params });
      const data = response.data;

      if (!data.success) {
        throw new APIError(`API error: ${data.message || 'Unknown error'}`);
      }

      return data.data || data;
    } catch (error) {
      if (axios.isAxiosError(error)) {
        if (error.code === 'ECONNABORTED') {
          throw new NetworkError(`Network timeout`);
        }
        if (error.message === 'Network Error') {
          throw new NetworkError(`Network connection error`);
        }
        throw new APIError(`API request failed: ${error.message}`);
      }
      throw error;
    }
  }

  /**
   * Build VehicleInfo from API response
   */
  private buildVehicleInfo(
    apiResponse: Record<string, unknown>,
    plateNumber: string
  ): VehicleInfo {
    const regionCode = (apiResponse.region_code || '').toString().toUpperCase();
    const regionInfo = REGION_CODES[regionCode];

    return {
      plateNumber,
      regionCode,
      regionName: regionInfo?.name || 'Unknown',
      vehicleType: (apiResponse.vehicle_type || 'Unknown').toString(),
      isValid: true,
      details: apiResponse,
      createdAt: new Date(),
    };
  }

  /**
   * Parse plate locally when API is unavailable
   */
  private parseLocally(
    plateNumber: string,
    regionCode: string
  ): VehicleInfo {
    const code = regionCode.toUpperCase();
    const regionInfo = REGION_CODES[code];

    return {
      plateNumber,
      regionCode: code,
      regionName: regionInfo?.name || 'Unknown Region',
      vehicleType: 'Car',
      isValid: true,
      details: {
        source: 'local_parsing',
        note: 'Parsed locally without API',
      },
      createdAt: new Date(),
    };
  }

  /**
   * Create invalid vehicle info object
   */
  private createInvalidVehicle(plate: string, error: string): VehicleInfo {
    return {
      plateNumber: plate,
      regionCode: '',
      regionName: 'Unknown',
      vehicleType: 'Unknown',
      isValid: false,
      details: { error },
    };
  }
}

export default TNKBClient;
