"""
TNKB Client - Exception Classes
"""


class TNKBError(Exception):
    """Base exception for TNKB errors"""
    pass


class InvalidPlateError(TNKBError):
    """Raised when an invalid plate number is provided"""
    pass


class APIError(TNKBError):
    """Raised when API request fails"""
    pass


class NetworkError(APIError):
    """Raised when network connection fails"""
    pass


class TimeoutError(APIError):
    """Raised when API request times out"""
    pass


class ValidationError(TNKBError):
    """Raised when validation fails"""
    pass
