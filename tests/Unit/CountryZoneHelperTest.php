<?php

namespace Tests\Unit;

use App\Support\CountryZoneHelper;
use Tests\TestCase;

class CountryZoneHelperTest extends TestCase
{
    public function test_get_all_countries(): void
    {
        $countries = CountryZoneHelper::getAll();
        $this->assertIsArray($countries);
        $this->assertNotEmpty($countries);
    }

    public function test_get_select_options(): void
    {
        $options = CountryZoneHelper::getSelectOptions();
        $this->assertIsArray($options);
        $this->assertArrayHasKey('ID', $options);
        $this->assertStringContainsString('+62', $options['ID']);
        $this->assertStringContainsString('Indonesia', $options['ID']);
    }

    public function test_find_by_iso(): void
    {
        $id = CountryZoneHelper::findByIso('ID');
        $this->assertNotNull($id);
        $this->assertEquals('+62', $id['code']);
        $this->assertEquals('Indonesia', $id['name']);

        $sg = CountryZoneHelper::findByIso('sg');
        $this->assertNotNull($sg);
        $this->assertEquals('+65', $sg['code']);

        $unknown = CountryZoneHelper::findByIso('XYZ');
        $this->assertNull($unknown);
    }

    public function test_get_dial_code(): void
    {
        $this->assertEquals('+62', CountryZoneHelper::getDialCode('ID'));
        $this->assertEquals('+65', CountryZoneHelper::getDialCode('SG'));
        $this->assertEquals('+1', CountryZoneHelper::getDialCode('US'));
        $this->assertEquals('+62', CountryZoneHelper::getDialCode('UNKNOWN', '+62'));
    }

    public function test_clean_local_number(): void
    {
        $this->assertEquals('81260840428', CountryZoneHelper::cleanLocalNumber('0812-6084-0428'));
        $this->assertEquals('81260840428', CountryZoneHelper::cleanLocalNumber('81260840428'));
        $this->assertEquals('', CountryZoneHelper::cleanLocalNumber('0000'));
    }

    public function test_split_phone_with_indonesian_prefix(): void
    {
        $parsed = CountryZoneHelper::splitPhone('6281260840428');
        $this->assertEquals('ID', $parsed['iso']);
        $this->assertEquals('+62', $parsed['dial_code']);
        $this->assertEquals('81260840428', $parsed['local_number']);
        $this->assertEquals('6281260840428', $parsed['full_e164']);
    }

    public function test_split_phone_with_leading_zero(): void
    {
        $parsed = CountryZoneHelper::splitPhone('081260840428');
        $this->assertEquals('ID', $parsed['iso']);
        $this->assertEquals('+62', $parsed['dial_code']);
        $this->assertEquals('81260840428', $parsed['local_number']);
        $this->assertEquals('6281260840428', $parsed['full_e164']);
    }

    public function test_split_phone_with_plus_and_foreign_country(): void
    {
        $parsed = CountryZoneHelper::splitPhone('+6591234567');
        $this->assertEquals('SG', $parsed['iso']);
        $this->assertEquals('+65', $parsed['dial_code']);
        $this->assertEquals('91234567', $parsed['local_number']);
        $this->assertEquals('6591234567', $parsed['full_e164']);
    }

    public function test_format_e164(): void
    {
        $e164 = CountryZoneHelper::formatE164('ID', '081260840428');
        $this->assertEquals('6281260840428', $e164);

        $e164Sg = CountryZoneHelper::formatE164('SG', '91234567');
        $this->assertEquals('6591234567', $e164Sg);
    }
}
