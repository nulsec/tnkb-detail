"""
TNKB Client - Main API Client
"""

import re
import logging
from typing import Optional, Dict, Any, List
from urllib.parse import quote

import requests
from requests.adapters import HTTPAdapter
from urllib3.util.retry import Retry

from .exceptions import (
    TNKBError, InvalidPlateError, APIError, 
    NetworkError, TimeoutError as TNKBTimeoutError, ValidationError
)
from .models import VehicleInfo, REGION_CODES

logger = logging.getLogger(__name__)


class TNKBClient:
    """
    Client for Indonesian Vehicle Registration Number (TNKB) API
    
    Example:
        >>> client = TNKBClient()
        >>> result = client.check_plate('B 1234 ABC')
        >>> print(result.region_name)
    """
    
    # API Endpoints
    API_BASE_URL = "https://cek-nopol-kendaraan.p.rapidapi.com"
    CHECK_KENDARAAN_ENDPOINT = f"{API_BASE_URL}/check"
    CEK_NOPOL_ENDPOINT = f"{API_BASE_URL}/check"
    
    # Plate validation regex
    # Format: [LETTER] [1-4 DIGITS] [LETTER(S)]
    PLATE_PATTERN = re.compile(r'^([A-Z]{1,2})\s?(\d{1,4})\s?([A-Z]{1,3})$')
    
    def __init__(
        self,
        timeout: int = 10,
        max_retries: int = 3,
        verify_ssl: bool = True,
        rapidapi_key: Optional[str] = None,
    ):
        """
        Initialize TNKB Client
        
        Args:
            timeout: Request timeout in seconds (default: 10)
            max_retries: Number of retries for failed requests (default: 3)
            verify_ssl: Verify SSL certificates (default: True)
            api_key: Optional API key for authenticated requests
        """
        self.timeout = timeout
        self.verify_ssl = verify_ssl
        self.api_key = api_key
        
        # Setup session with retries
        self.session = requests.Session()
        retry_strategy = Retry(
            total=max_retries,
            backoff_factor=1,
            status_forcelist=[429, 500, 502, 503, 504],
            allowed_methods=["GET", "POST"],
        )
        adapter = HTTPAdapter(max_retries=retry_strategy)
        self.session.mount("http://", adapter)
        self.session.mount("https://", adapter)
        
        # Set default headers
        self.session.headers.update({
            'User-Agent': 'TNKBClient/1.0.0 (Python)',
            'Content-Type': 'application/json',
            'X-RapidAPI-Host': 'cek-nopol-kendaraan.p.rapidapi.com',
        })
        
        if self.rapidapi_key:
            self.session.headers.update({
                'X-RapidAPI-Key': self.rapidapi_key
            })
    
    def check_plate(self, plate_number: str) -> VehicleInfo:
        """
        Check and decode Indonesian vehicle plate number
        
        Args:
            plate_number: Vehicle plate number (e.g., 'B 1234 ABC')
            
        Returns:
            VehicleInfo: Decoded vehicle information
            
        Raises:
            InvalidPlateError: If plate format is invalid
            APIError: If API request fails
            
        Example:
            >>> client = TNKBClient()
            >>> vehicle = client.check_plate('B 1234 ABC')
            >>> print(f"Region: {vehicle.region_name}")
        """
        # Validate and normalize plate
        normalized_plate = self._normalize_plate(plate_number)
        
        # Parse plate components
        region_code, digits, letters = self._parse_plate(normalized_plate)
        
        try:
            # Try API call first
            response = self._call_api(
                self.CHECK_KENDARAAN_ENDPOINT,
                {'nopol': normalized_plate}
            )
            return self._build_vehicle_info(response, normalized_plate)
        except APIError as e:
            logger.warning(f"API call failed: {e}, falling back to local parsing")
            # Fallback to local parsing if API fails
            return self._parse_locally(normalized_plate, region_code, digits)
    
    def validate_plate(self, plate_number: str) -> bool:
        """
        Validate Indonesian vehicle plate format
        
        Args:
            plate_number: Vehicle plate number to validate
            
        Returns:
            bool: True if valid, False otherwise
        """
        try:
            self._validate_plate_format(plate_number)
            return True
        except InvalidPlateError:
            return False
    
    def get_region_info(self, region_code: str) -> Optional[Dict[str, Any]]:
        """
        Get information about a registration region
        
        Args:
            region_code: Region code (e.g., 'B', 'AA')
            
        Returns:
            dict: Region information or None if not found
        """
        region_code = region_code.upper()
        if region_code in REGION_CODES:
            return REGION_CODES[region_code]
        return None
    
    def list_regions(self) -> List[Dict[str, str]]:
        """
        List all Indonesian vehicle registration regions
        
        Returns:
            list: List of all regions with codes and names
        """
        return [
            {
                'code': code,
                'name': info['name'],
                'province': info.get('province', '')
            }
            for code, info in sorted(REGION_CODES.items())
        ]
    
    def bulk_check(self, plate_numbers: List[str]) -> List[VehicleInfo]:
        """
        Check multiple plate numbers
        
        Args:
            plate_numbers: List of plate numbers to check
            
        Returns:
            list: List of VehicleInfo objects
        """
        results = []
        for plate in plate_numbers:
            try:
                results.append(self.check_plate(plate))
            except TNKBError as e:
                logger.error(f"Failed to check plate {plate}: {e}")
                results.append(self._create_invalid_vehicle(plate, str(e)))
        return results
    
    # Private methods
    
    def _normalize_plate(self, plate_number: str) -> str:
        """Normalize plate number format"""
        if not plate_number:
            raise InvalidPlateError("Plate number cannot be empty")
        
        # Remove extra whitespace and convert to uppercase
        normalized = ' '.join(plate_number.upper().split())
        
        # Validate format
        self._validate_plate_format(normalized)
        
        return normalized
    
    def _validate_plate_format(self, plate_number: str) -> None:
        """Validate plate number format"""
        if not self.PLATE_PATTERN.match(plate_number):
            raise InvalidPlateError(
                f"Invalid plate format: {plate_number}. "
                f"Expected format: '[A-Z] [1-4 DIGITS] [A-Z]' (e.g., 'B 1234 ABC')"
            )
    
    def _parse_plate(self, plate_number: str) -> tuple:
        """Parse plate into components"""
        match = self.PLATE_PATTERN.match(plate_number)
        if not match:
            raise InvalidPlateError(f"Cannot parse plate: {plate_number}")
        
        region_code, digits, letters = match.groups()
        return region_code, digits, letters
    
    def _call_api(
        self,
        endpoint: str,
        params: Dict[str, Any],
        method: str = 'GET'
    ) -> Dict[str, Any]:
        """
        Make API request with error handling
        
        Args:
            endpoint: API endpoint URL
            params: Request parameters
            method: HTTP method (GET/POST)
            
        Returns:
            dict: API response
            
        Raises:
            APIError: If request fails
        """
        try:
            if method.upper() == 'GET':
                response = self.session.get(
                    endpoint,
                    params=params,
                    timeout=self.timeout,
                    verify=self.verify_ssl,
                )
            else:
                response = self.session.post(
                    endpoint,
                    json=params,
                    timeout=self.timeout,
                    verify=self.verify_ssl,
                )
            
            response.raise_for_status()
            
            data = response.json()
            
            if not data.get('success'):
                raise APIError(f"API error: {data.get('message', 'Unknown error')}")
            
            return data.get('data', data)
            
        except requests.exceptions.Timeout:
            raise TNKBTimeoutError(f"API request timeout after {self.timeout}s")
        except requests.exceptions.ConnectionError as e:
            raise NetworkError(f"Network connection error: {e}")
        except requests.exceptions.RequestException as e:
            raise APIError(f"API request failed: {e}")
        except ValueError as e:
            raise APIError(f"Invalid API response: {e}")
    
    def _build_vehicle_info(
        self,
        api_response: Dict[str, Any],
        plate_number: str
    ) -> VehicleInfo:
        """Build VehicleInfo from API response"""
        region_code = api_response.get('region_code', '').upper()
        region_info = REGION_CODES.get(region_code, {})
        
        return VehicleInfo(
            plate_number=plate_number,
            region_code=region_code,
            region_name=region_info.get('name', 'Unknown'),
            vehicle_type=api_response.get('vehicle_type', 'Unknown'),
            is_valid=True,
            details=api_response,
        )
    
    def _parse_locally(
        self,
        plate_number: str,
        region_code: str,
        digits: str
    ) -> VehicleInfo:
        """Parse plate locally when API is unavailable"""
        region_code = region_code.upper()
        region_info = REGION_CODES.get(region_code, {})
        
        return VehicleInfo(
            plate_number=plate_number,
            region_code=region_code,
            region_name=region_info.get('name', 'Unknown Region'),
            vehicle_type='Car',  # Default
            is_valid=True,
            details={
                'source': 'local_parsing',
                'region_code': region_code,
                'digits': digits,
                'note': 'Parsed locally without API'
            }
        )
    
    def _create_invalid_vehicle(self, plate: str, error: str) -> VehicleInfo:
        """Create invalid vehicle info object"""
        return VehicleInfo(
            plate_number=plate,
            region_code='',
            region_name='Unknown',
            vehicle_type='Unknown',
            is_valid=False,
            details={'error': error}
        )
    
    def close(self) -> None:
        """Close session and cleanup"""
        if self.session:
            self.session.close()
    
    def __enter__(self):
        """Context manager support"""
        return self
    
    def __exit__(self, exc_type, exc_val, exc_tb):
        """Context manager exit"""
        self.close()
