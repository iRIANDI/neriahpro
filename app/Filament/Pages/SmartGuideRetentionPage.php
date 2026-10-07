<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\CmsGlobalSetting;
use Illuminate\Contracts\Support\Htmlable;

class SmartGuideRetentionPage extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-sparkles';
    protected static string | \UnitEnum | null $navigationGroup = 'Project OS // Core';
    protected static ?string $navigationLabel = 'Smart Guide & Unit Economics';
    protected static ?string $title = 'Executive Smart Guide // Unit Economics & Retention';
    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.smart-guide-retention';

    /**
     * Strictly restricted ONLY to Super Admin Yoseph Iriandi Tambunan.
     * Midtrans QA reviewers, test staff, and other accounts are strictly blocked.
     */
    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        // Midtrans reviewer or any secondary account is strictly DENIED
        if ($user->hasRole('midtrans_reviewer') || $user->email === 'reviewer.midtrans@neriahpro.com') {
            return false;
        }

        // HANYA super admin yoseph.iriandi.tambunan@gmail.com
        return $user->email === 'yoseph.iriandi.tambunan@gmail.com';
    }

    public function getHeading(): string | Htmlable
    {
        return 'Executive Smart Guide // Unit Economics, Margin & High-Retention Playbook';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return 'Dokumentasi Rahasia Bisnis & Operasional Neriah Pro: Validasi Unit Economics 98%+ Gross Margin, Batasan Tier, Alur Passwordless OTP, dan Strategi Upsell Studio Rp 50 Juta.';
    }

    public function getViewData(): array
    {
        return [
            'pricingSettings' => [
                'spark_price' => CmsGlobalSetting::getVal('pricing_retail_spark_price', 'Rp 0'),
                'lite_price' => CmsGlobalSetting::getVal('pricing_retail_lite_price', 'Rp 99.000'),
                'pro_price' => CmsGlobalSetting::getVal('pricing_retail_pro_price', 'Rp 399.000'),
                'ultimate_price' => CmsGlobalSetting::getVal('pricing_retail_ultimate_price', 'Rp 1.490.000'),
                'spark_limit' => CmsGlobalSetting::getVal('pricing_retail_spark_limit', '2x Audit/Bulan (Auto-Reset tgl 1)'),
                'lite_limit' => CmsGlobalSetting::getVal('pricing_retail_lite_limit', '1 Proyek // 30 Hari Jendela Revisi Form'),
                'pro_limit' => CmsGlobalSetting::getVal('pricing_retail_pro_limit', '1 Proyek // 6 Bulan Unlimited AI Re-prompt'),
                'ultimate_limit' => CmsGlobalSetting::getVal('pricing_retail_ultimate_limit', '1 Proyek Enterprise // 1 Tahun Prioritas Update'),
                'login_policy' => CmsGlobalSetting::getVal('pricing_retail_login_policy', 'Spark Bebas Login // Paket Berbayar Wajib Login Akun'),
                'disclaimer' => CmsGlobalSetting::getVal('pricing_retail_disclaimer', '100% Self-Service Blueprint // Zero Neriah Pro Coding'),
            ]
        ];
    }
}
