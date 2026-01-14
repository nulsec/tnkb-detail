"""
TNKB (Tanda Nomor Kendaraan Bermotor) - Indonesian Vehicle Registration Decoder
Python Package Setup
"""

from setuptools import setup, find_packages

with open("README.md", "r", encoding="utf-8") as fh:
    long_description = fh.read()

setup(
    name="tnkb-client",
    version="1.0.0",
    author="nulsec",
    author_email="security@nulsec.com",
    description="Indonesian vehicle registration number (TNKB) decoder and validator",
    long_description=long_description,
    long_description_content_type="text/markdown",
    url="https://github.com/nulsec/tnkb-detail",
    packages=find_packages(),
    classifiers=[
        "Programming Language :: Python :: 3",
        "Programming Language :: Python :: 3.8",
        "Programming Language :: Python :: 3.9",
        "Programming Language :: Python :: 3.10",
        "Programming Language :: Python :: 3.11",
        "Programming Language :: Python :: 3.12",
        "License :: OSI Approved :: MIT License",
        "Operating System :: OS Independent",
        "Development Status :: 4 - Beta",
        "Intended Audience :: Developers",
        "Topic :: Software Development :: Libraries :: Python Modules",
    ],
    python_requires=">=3.8",
    install_requires=[
        "requests>=2.25.0",
        "python-dotenv>=0.19.0",
    ],
    extras_require={
        "dev": [
            "pytest>=6.0",
            "pytest-cov>=2.12.0",
            "black>=21.0",
            "flake8>=3.9",
        ],
    },
)
