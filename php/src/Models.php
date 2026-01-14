<?php

declare(strict_types=1);

namespace TNKB;

/**
 * Vehicle information from TNKB
 */
class VehicleInfo
{
    public string $plateNumber;
    public string $regionCode;
    public string $regionName;
    public string $vehicleType;
    public bool $isValid;
    public array $details;
    public ?\DateTimeImmutable $createdAt;

    public function __construct(
        string $plateNumber,
        string $regionCode,
        string $regionName,
        string $vehicleType,
        bool $isValid,
        array $details = [],
        ?\DateTimeImmutable $createdAt = null
    ) {
        $this->plateNumber = $plateNumber;
        $this->regionCode = $regionCode;
        $this->regionName = $regionName;
        $this->vehicleType = $vehicleType;
        $this->isValid = $isValid;
        $this->details = $details;
        $this->createdAt = $createdAt ?? new \DateTimeImmutable();
    }

    public function toArray(): array
    {
        return [
            'plateNumber' => $this->plateNumber,
            'regionCode' => $this->regionCode,
            'regionName' => $this->regionName,
            'vehicleType' => $this->vehicleType,
            'isValid' => $this->isValid,
            'details' => $this->details,
            'createdAt' => $this->createdAt?->format('c'),
        ];
    }

    public function toJson(): string
    {
        return \json_encode($this->toArray(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}

/**
 * Region information
 */
class RegionInfo
{
    public string $code;
    public string $name;
    public ?string $province;

    public function __construct(
        string $code,
        string $name,
        ?string $province = null
    ) {
        $this->code = $code;
        $this->name = $name;
        $this->province = $province;
    }

    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'province' => $this->province,
        ];
    }
}

/**
 * Regional code mappings for Indonesian vehicle registration
 */
class RegionCodes
{
    public static array $REGIONS = [
        'A' => ['name' => 'DKI Jakarta', 'province' => 'Jakarta'],
        'B' => ['name' => 'Jawa Barat', 'province' => 'West Java'],
        'C' => ['name' => 'Jawa Tengah', 'province' => 'Central Java'],
        'D' => ['name' => 'Bandung', 'province' => 'West Java'],
        'E' => ['name' => 'Pekalongan', 'province' => 'Central Java'],
        'F' => ['name' => 'Purwokerto', 'province' => 'Central Java'],
        'G' => ['name' => 'Yogyakarta', 'province' => 'Yogyakarta'],
        'H' => ['name' => 'Surabaya', 'province' => 'East Java'],
        'K' => ['name' => 'Semarang', 'province' => 'Central Java'],
        'L' => ['name' => 'Surabaya', 'province' => 'East Java'],
        'M' => ['name' => 'Madura', 'province' => 'East Java'],
        'N' => ['name' => 'Malang', 'province' => 'East Java'],
        'P' => ['name' => 'Banyumas', 'province' => 'Central Java'],
        'R' => ['name' => 'Pati', 'province' => 'Central Java'],
        'S' => ['name' => 'Karanganyar', 'province' => 'Central Java'],
        'T' => ['name' => 'Sidoarjo', 'province' => 'East Java'],
        'U' => ['name' => 'Tegal', 'province' => 'Central Java'],
        'W' => ['name' => 'Mataram', 'province' => 'West Nusa Tenggara'],
        'AA' => ['name' => 'Medan', 'province' => 'North Sumatra'],
        'AB' => ['name' => 'Padang', 'province' => 'West Sumatra'],
        'AD' => ['name' => 'Aceh', 'province' => 'Aceh'],
        'AE' => ['name' => 'Pekanbaru', 'province' => 'Riau'],
        'AG' => ['name' => 'Jambi', 'province' => 'Jambi'],
        'BA' => ['name' => 'Bengkulu', 'province' => 'Bengkulu'],
        'BB' => ['name' => 'Lampung', 'province' => 'Lampung'],
        'BC' => ['name' => 'Palembang', 'province' => 'South Sumatra'],
        'BD' => ['name' => 'Pangkal Pinang', 'province' => 'Bangka Belitung'],
        'BE' => ['name' => 'Bandar Lampung', 'province' => 'Lampung'],
        'BK' => ['name' => 'Batam', 'province' => 'Riau Islands'],
        'BM' => ['name' => 'Banjarmasin', 'province' => 'South Kalimantan'],
        'BN' => ['name' => 'Balikpapan', 'province' => 'East Kalimantan'],
        'BP' => ['name' => 'Pontianak', 'province' => 'West Kalimantan'],
        'BR' => ['name' => 'Samarinda', 'province' => 'East Kalimantan'],
        'BS' => ['name' => 'Palangka Raya', 'province' => 'Central Kalimantan'],
        'BT' => ['name' => 'Banjarmasin', 'province' => 'South Kalimantan'],
        'CC' => ['name' => 'Manado', 'province' => 'North Sulawesi'],
        'CD' => ['name' => 'Gorontalo', 'province' => 'Gorontalo'],
        'CT' => ['name' => 'Palu', 'province' => 'Central Sulawesi'],
        'DA' => ['name' => 'Makassar', 'province' => 'South Sulawesi'],
        'DB' => ['name' => 'Pare-Pare', 'province' => 'South Sulawesi'],
        'DC' => ['name' => 'Kendari', 'province' => 'Southeast Sulawesi'],
        'DD' => ['name' => 'Baubau', 'province' => 'Southeast Sulawesi'],
        'DE' => ['name' => 'Ambon', 'province' => 'Maluku'],
        'EB' => ['name' => 'Manado', 'province' => 'North Sulawesi'],
        'ED' => ['name' => 'Denpasar', 'province' => 'Bali'],
        'EE' => ['name' => 'Mataram', 'province' => 'West Nusa Tenggara'],
        'EF' => ['name' => 'Kupang', 'province' => 'East Nusa Tenggara'],
        'KB' => ['name' => 'Jayapura', 'province' => 'Papua'],
        'PA' => ['name' => 'Pontianak', 'province' => 'West Kalimantan'],
    ];

    public static function getRegion(string $code): ?RegionInfo
    {
        $code = \strtoupper($code);
        if (!\array_key_exists($code, self::$REGIONS)) {
            return null;
        }

        $info = self::$REGIONS[$code];

        return new RegionInfo(
            $code,
            $info['name'],
            $info['province'] ?? null
        );
    }

    public static function getAllRegions(): array
    {
        $regions = [];
        \ksort(self::$REGIONS);

        foreach (self::$REGIONS as $code => $info) {
            $regions[] = new RegionInfo(
                $code,
                $info['name'],
                $info['province'] ?? null
            );
        }

        return $regions;
    }
}
