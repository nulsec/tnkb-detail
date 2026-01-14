"""
TNKB Client - Main Module
Indonesian Vehicle Registration Number Decoder
"""

__version__ = "1.0.0"
__author__ = "nulsec"
__license__ = "MIT"

from .client import TNKBClient
from .exceptions import TNKBError, InvalidPlateError, APIError
from .models import VehicleInfo

__all__ = [
    "TNKBClient",
    "TNKBError",
    "InvalidPlateError",
    "APIError",
    "VehicleInfo",
]
