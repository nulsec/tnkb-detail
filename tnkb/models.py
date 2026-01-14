"""
TNKB Models - Data Classes
"""

from dataclasses import dataclass
from typing import Optional, Dict, Any
from datetime import datetime


@dataclass
class VehicleInfo:
    """Vehicle information from TNKB"""
    
    plate_number: str
    region_code: str
    region_name: str
    vehicle_type: str
    is_valid: bool
    details: Optional[Dict[str, Any]] = None
    created_at: datetime = None
    
    def __post_init__(self):
        if self.created_at is None:
            self.created_at = datetime.now()
    
    def to_dict(self) -> Dict[str, Any]:
        """Convert to dictionary"""
        return {
            'plate_number': self.plate_number,
            'region_code': self.region_code,
            'region_name': self.region_name,
            'vehicle_type': self.vehicle_type,
            'is_valid': self.is_valid,
            'details': self.details,
            'created_at': self.created_at.isoformat() if self.created_at else None,
        }
    
    def __str__(self) -> str:
        return f"Vehicle({self.plate_number} - {self.region_name})"
    
    def __repr__(self) -> str:
        return f"VehicleInfo(plate='{self.plate_number}', region='{self.region_name}', valid={self.is_valid})"


@dataclass
class RegionInfo:
    """Indonesian vehicle registration region"""
    
    code: str
    name: str
    province: Optional[str] = None
    description: Optional[str] = None
    
    def to_dict(self) -> Dict[str, Any]:
        return {
            'code': self.code,
            'name': self.name,
            'province': self.province,
            'description': self.description,
        }


# Indonesian region codes mapping
REGION_CODES = {
    'A': {'name': 'DKI Jakarta', 'province': 'Jakarta'},
    'B': {'name': 'Jawa Barat', 'province': 'West Java'},
    'C': {'name': 'Jawa Tengah', 'province': 'Central Java'},
    'D': {'name': 'Bandung', 'province': 'West Java'},
    'E': {'name': 'Pekalongan', 'province': 'Central Java'},
    'F': {'name': 'Purwokerto', 'province': 'Central Java'},
    'G': {'name': 'Yogyakarta', 'province': 'Yogyakarta'},
    'H': {'name': 'Surabaya', 'province': 'East Java'},
    'K': {'name': 'Semarang', 'province': 'Central Java'},
    'L': {'name': 'Surabaya', 'province': 'East Java'},
    'M': {'name': 'Madura', 'province': 'East Java'},
    'N': {'name': 'Malang', 'province': 'East Java'},
    'P': {'name': 'Banyumas', 'province': 'Central Java'},
    'R': {'name': 'Pati', 'province': 'Central Java'},
    'S': {'name': 'Karanganyar', 'province': 'Central Java'},
    'T': {'name': 'Sidoarjo', 'province': 'East Java'},
    'U': {'name': 'Tegal', 'province': 'Central Java'},
    'W': {'name': 'Mataram', 'province': 'West Nusa Tenggara'},
    'AA': {'name': 'Medan', 'province': 'North Sumatra'},
    'AB': {'name': 'Padang', 'province': 'West Sumatra'},
    'AD': {'name': 'Aceh', 'province': 'Aceh'},
    'AE': {'name': 'Pekanbaru', 'province': 'Riau'},
    'AG': {'name': 'Jambi', 'province': 'Jambi'},
    'BA': {'name': 'Bengkulu', 'province': 'Bengkulu'},
    'BB': {'name': 'Lampung', 'province': 'Lampung'},
    'BC': {'name': 'Palembang', 'province': 'South Sumatra'},
    'BD': {'name': 'Pangkal Pinang', 'province': 'Bangka Belitung'},
    'BE': {'name': 'Bandar Lampung', 'province': 'Lampung'},
    'BK': {'name': 'Batam', 'province': 'Riau Islands'},
    'BM': {'name': 'Banjarmasin', 'province': 'South Kalimantan'},
    'BN': {'name': 'Balikpapan', 'province': 'East Kalimantan'},
    'BP': {'name': 'Pontianak', 'province': 'West Kalimantan'},
    'BR': {'name': 'Samarinda', 'province': 'East Kalimantan'},
    'BS': {'name': 'Palangka Raya', 'province': 'Central Kalimantan'},
    'BT': {'name': 'Banjarmasin', 'province': 'South Kalimantan'},
    'CC': {'name': 'Manado', 'province': 'North Sulawesi'},
    'CD': {'name': 'Gorontalo', 'province': 'Gorontalo'},
    'CT': {'name': 'Palu', 'province': 'Central Sulawesi'},
    'DA': {'name': 'Makassar', 'province': 'South Sulawesi'},
    'DB': {'name': 'Pare-Pare', 'province': 'South Sulawesi'},
    'DC': {'name': 'Kendari', 'province': 'Southeast Sulawesi'},
    'DD': {'name': 'Baubau', 'province': 'Southeast Sulawesi'},
    'DE': {'name': 'Ambon', 'province': 'Maluku'},
    'EB': {'name': 'Manado', 'province': 'North Sulawesi'},
    'ED': {'name': 'Denpasar', 'province': 'Bali'},
    'EE': {'name': 'Mataram', 'province': 'West Nusa Tenggara'},
    'EF': {'name': 'Kupang', 'province': 'East Nusa Tenggara'},
    'KB': {'name': 'Jayapura', 'province': 'Papua'},
    'PA': {'name': 'Pontianak', 'province': 'West Kalimantan'},
}
