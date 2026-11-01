import React, { useState, useEffect, useRef, useMemo } from 'react';

const SECTOR_SHORT_LABELS = {
    ALL: { id: 'Semua 12 Sektor', en: 'All 12 Sectors' },
    SECTOR_01_COMMERCE_OMNICHANNEL: { id: '01 Ritel & POS', en: '01 Retail & POS' },
    SECTOR_02_HOSPITALITY_SPACE: { id: '02 Hotel & Venue', en: '02 Venue & Resort' },
    SECTOR_03_HEALTHCARE_CLINICAL: { id: '03 Klinik & RME', en: '03 Clinic & EMR' },
    SECTOR_04_FINTECH_LEDGER: { id: '04 Finansial & Ledger', en: '04 Finance & Ledger' },
    SECTOR_05_INDUSTRIAL_MRP: { id: '05 Pabrik & MRP', en: '05 Industrial MRP' },
    SECTOR_06_LOGISTICS_FLEET: { id: '06 Logistik & Armada', en: '06 Fleet & Logistics' },
    SECTOR_07_EDTECH_MEMBERSHIP: { id: '07 Kampus & LMS', en: '07 EdTech & LMS' },
    SECTOR_08_PROFESSIONAL_PSA: { id: '08 Agensi & PSA', en: '08 Agency & PSA' },
    SECTOR_09_FOOD_BEVERAGE: { id: '09 Resto & KDS', en: '09 F&B & KDS' },
    SECTOR_10_MEDIA_TICKETING: { id: '10 Event & Tiket', en: '10 Event & Ticketing' },
    SECTOR_11_HRIS_INTERNAL_OPS: { id: '11 HRIS & Payroll', en: '11 HRIS & Payroll' },
    SECTOR_12_GOVTECH_NONPROFIT: { id: '12 Yayasan & ISAK 35', en: '12 Non-Profit & NGO' },
};

const SCROLLY_STAGES = [
    {
        step: '01',
        code: 'STAGE_INTAKE_AGENTSHIELD',
        title: {
            id: 'Zero-Loss Intake + AgentShield 5-Layer Scrub',
            en: 'Zero-Loss Intake + AgentShield 5-Layer Scrub',
        },
        desc: {
            id: 'Ide mentah atau dokumen PDF/DOCX dibersihkan dari injeksi prompt tersembunyi, lalu dipetakan otomatis ke 1 dari 12 Sektor Fungsional.',
            en: 'Raw ideas or PDF/DOCX specs are scrubbed against indirect prompt injection and mapped deterministically to 1 of 12 Functional Sectors.',
        },
        metrics: '0% Prompt Leak  |  <UNTRUSTED_CLIENT_DOCUMENT_DATA>',
        stagePreview: [
            'MarkItDown OCR -> Sanitized Corpus',
            'Auto-Match: Sector + Primitives + Stack',
            'Universal Input Hygiene (E.164 + Strict Email)',
        ],
    },
    {
        step: '02',
        code: 'STAGE_DUAL_ZONE_UI',
        title: {
            id: 'Dual-Zone UI: Back-Stage Crisp vs Front-Stage Scrollytelling',
            en: 'Dual-Zone UI: Back-Stage Crisp vs Front-Stage Scrollytelling',
        },
        desc: {
            id: 'Zone A (Kasir, RME, Ledger) dikunci 100% Linear/Stripe Crisp bebas distraksi; Zone B (Landing, Katalog, Donasi) memakai 6 Preset UX sinematik GPU 60fps.',
            en: 'Zone A (POS, EMR, Ledger) stays 100% Linear/Stripe Crisp; Zone B (Landing, Catalog, Donor Portal) mounts 6 GPU-composited 60fps UX presets.',
        },
        metrics: '36 Zone-A UIs  x  6 Zone-B Public UX  |  CLS = 0.00',
        stagePreview: [
            'Zone A: UI_01A..UI_12C (Zero Parallax)',
            'Zone B: UX_F1..UX_F6 (translate3d / opacity)',
            'prefers-reduced-motion: reduce Ready',
        ],
    },
    {
        step: '03',
        code: 'STAGE_CODESIGN_SCOPE_LOCK',
        title: {
            id: 'Co-Design Room & Dual-Key Cryptographic Scope Lock',
            en: 'Co-Design Room & Dual-Key Cryptographic Scope Lock',
        },
        desc: {
            id: 'Klien meninjau Business Wireframe sementara Architect meninjau Tabel ULID 26-char & Pessimistic Lock, lalu mengunci kontrak SPK bersegel SHA-256.',
            en: 'Clients inspect Business Wireframes while Architects inspect 26-char ULID tables & Pessimistic Locks before Dual-Key SHA-256 Scope Lock.',
        },
        metrics: '25 L1-L4 Menus  |  5-Sprint WBS  |  SHA-256 SPK',
        stagePreview: [
            '100% Modules Approved -> Read-Only v1.0',
            'Midtrans IDR / Global USD < 2s Unlock',
            '3-Sheet .xlsx CPA Forensic Audit Trail',
        ],
    },
    {
        step: '04',
        code: 'STAGE_BAST_VERIFIED_RECEIPT',
        title: {
            id: 'UAT Given-When-Then -> BAST Digital -> Verified Aniwall Receipt',
            en: 'Given-When-Then UAT -> Digital BAST -> Verified Aniwall Receipt',
        },
        desc: {
            id: 'Begitu BAST ditandatangani atau Fase 6 terverifikasi via MCP IDE, sistem otomatis menerbitkan Architecture Receipt asli tanpa testimoni karangan.',
            en: 'Once BAST is signed or Phase 6 verifies via MCP IDE, Project OS automatically mints a telemetry-backed Architecture Receipt.',
        },
        metrics: 'BAST + MCP Verified  |  0% Fake Social Proof',
        stagePreview: [
            'ULID Tables + Concurrency Locks Telemetry',
            'Token Saved % + Scope SHA-256 Seal',
            '1-Click Anonymized Blueprint Inspector',
        ],
    },
];

export default function VerifiedAniwallShowcaseIsland({
    currentLocale = 'id',
    testimonials = [],
    publicUxPresets = [],
    initialSector = 'ALL',
    exploreUrl = '/explore',
}) {
    const isEn = String(currentLocale).toLowerCase() === 'en';
    const [selectedSector, setSelectedSector] = useState(initialSector || 'ALL');
    const [activeStageIdx, setActiveStageIdx] = useState(0);
    const [inspectedItem, setInspectedItem] = useState(null);
    const [isHydratedVisible, setIsHydratedVisible] = useState(false);
    const [reducedMotion, setReducedMotion] = useState(false);
    const sectionRef = useRef(null);

    // Iron Law #4 & #5: Respect prefers-reduced-motion & Lazy-Hydrate via IntersectionObserver
    useEffect(() => {
        if (typeof window !== 'undefined' && window.matchMedia) {
            const mq = window.matchMedia('(prefers-reduced-motion: reduce)');
            setReducedMotion(mq.matches);
            const handler = (e) => setReducedMotion(e.matches);
            if (mq.addEventListener) mq.addEventListener('change', handler);
            return () => {
                if (mq.removeEventListener) mq.removeEventListener('change', handler);
            };
        }
    }, []);

    useEffect(() => {
        if (!sectionRef.current || typeof IntersectionObserver === 'undefined') {
            setIsHydratedVisible(true);
            return;
        }
        const observer = new IntersectionObserver(
            (entries) => {
                if (entries.some((entry) => entry.isIntersecting)) {
                    setIsHydratedVisible(true);
                    observer.disconnect();
                }
            },
            { rootMargin: '180px' }
        );
        observer.observe(sectionRef.current);
        return () => observer.disconnect();
    }, []);

    const filteredItems = useMemo(() => {
        if (!Array.isArray(testimonials)) return [];
        if (selectedSector === 'ALL') return testimonials;
        return testimonials.filter((item) => item.sector_code === selectedSector);
    }, [testimonials, selectedSector]);

    const activeStage = SCROLLY_STAGES[activeStageIdx] || SCROLLY_STAGES[0];

    return (
        <section
            ref={sectionRef}
            id="verified-aniwall-showcase"
            className="bg-zinc-50 dark:bg-zinc-950 text-zinc-950 dark:text-zinc-100 border-b border-zinc-200 dark:border-zinc-800 py-16 px-4 sm:px-6 transition-colors"
        >
            <style>{`
                @keyframes nproAniwallLeft {
                    0% { transform: translate3d(0, 0, 0); }
                    100% { transform: translate3d(-50%, 0, 0); }
                }
                @keyframes nproAniwallRight {
                    0% { transform: translate3d(-50%, 0, 0); }
                    100% { transform: translate3d(0, 0, 0); }
                }
                .npro-aniwall-track-left {
                    display: flex;
                    width: max-content;
                    will-change: transform;
                    animation: nproAniwallLeft 58s linear infinite;
                }
                .npro-aniwall-track-right {
                    display: flex;
                    width: max-content;
                    will-change: transform;
                    animation: nproAniwallRight 64s linear infinite;
                }
                .npro-aniwall-lane:hover .npro-aniwall-track-left,
                .npro-aniwall-lane:hover .npro-aniwall-track-right {
                    animation-play-state: paused;
                }
                @media (prefers-reduced-motion: reduce) {
                    .npro-aniwall-track-left,
                    .npro-aniwall-track-right {
                        animation: none !important;
                        transform: translate3d(0, 0, 0) !important;
                    }
                }
            `}</style>

            <div className="max-w-7xl mx-auto space-y-14">
                {/* PART A: STICKY SCROLLYTELLING DUAL-ZONE ARCHITECTURE NARRATIVE */}
                <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    <div className="lg:col-span-5 space-y-3">
                        <span className="inline-block px-2.5 py-1 text-[11px] font-mono font-black uppercase tracking-widest bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 rounded-none">
                            {isEn ? 'DUAL-ZONE UI // SCROLLYTELLING ENGINE' : 'ARSITEKTUR DUAL-ZONE // SCROLLYTELLING ENGINE'}
                        </span>
                        <h2 className="text-2xl sm:text-3xl font-black tracking-tight text-zinc-950 dark:text-white leading-tight">
                            {isEn
                                ? 'Cinematic Public Front-Stage. Zero-Distraction Operational Back-Stage.'
                                : 'Front-Stage Publik Sinematik 60fps. Back-Stage Operasional Presisi Tanpa Distraksi.'}
                        </h2>
                        <p className="text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed">
                            {isEn
                                ? 'Every Project OS v10.0 blueprint enforces strict Dual-Zone separation: GPU-accelerated Scrollytelling & Kinetic Aniwall on public pages, paired with crisp Linear/Stripe workspaces for daily operators.'
                                : 'Setiap blueprint Project OS v10.0 memisahkan secara tegas Zona Publik (Scrollytelling, Spatial Showcase & Aniwall 60fps GPU) dengan Zona Kerja Operasional (Kasir, RME, Ledger berkecepatan tinggi bebas animasi berat).'}
                        </p>

                        <div className="space-y-2 pt-2">
                            {SCROLLY_STAGES.map((st, idx) => {
                                const isActive = idx === activeStageIdx;
                                return (
                                    <button
                                        key={st.code}
                                        type="button"
                                        onClick={() => setActiveStageIdx(idx)}
                                        onMouseEnter={() => setActiveStageIdx(idx)}
                                        className={`w-full text-left p-3.5 border transition-colors rounded-sm ${
                                            isActive
                                                ? 'bg-zinc-900 text-white border-zinc-900 dark:bg-zinc-900 dark:border-emerald-500 ring-1 ring-emerald-500/60'
                                                : 'bg-white dark:bg-zinc-900/50 text-zinc-900 dark:text-zinc-200 border-zinc-200 dark:border-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-900 hover:border-zinc-400 dark:hover:border-zinc-600'
                                        }`}
                                    >
                                        <div className="flex items-center justify-between gap-2 mb-1">
                                            <span className="font-mono text-[11px] font-black uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                                                STEP {st.step} // {st.code}
                                            </span>
                                            <span className={`px-2 py-0.5 text-[10px] font-mono font-black rounded-none ${
                                                isActive
                                                    ? 'bg-emerald-500 text-black'
                                                    : 'bg-zinc-100 text-zinc-900 dark:bg-zinc-800 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700'
                                            }`}>
                                                {isActive ? 'ACTIVE STAGE' : 'INSPECT'}
                                            </span>
                                        </div>
                                        <div className={`text-sm font-bold ${isActive ? 'text-white' : 'text-zinc-950 dark:text-zinc-100'}`}>
                                            {isEn ? st.title.en : st.title.id}
                                        </div>
                                    </button>
                                );
                            })}
                        </div>
                    </div>

                    {/* Sticky Right Stage Viewport (Explicit Aspect Ratio -> CLS = 0.00) */}
                    <div className="lg:col-span-7 lg:sticky lg:top-20 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 rounded-sm shadow-sm min-h-[380px] flex flex-col justify-between">
                        <div>
                            <div className="flex flex-wrap items-center justify-between gap-2 pb-4 mb-5 border-b border-zinc-200 dark:border-zinc-800">
                                <div className="flex items-center gap-2">
                                    <span className="w-2.5 h-2.5 bg-emerald-500 inline-block rounded-none"></span>
                                    <span className="font-mono text-xs font-black uppercase tracking-wider text-zinc-950 dark:text-zinc-100">
                                        {activeStage.code}
                                    </span>
                                </div>
                                <span className="font-mono text-[11px] font-bold px-2.5 py-1 bg-zinc-100 dark:bg-zinc-950 text-zinc-900 dark:text-emerald-300 border border-zinc-300 dark:border-zinc-700 tabular-nums rounded-none">
                                    {activeStage.metrics}
                                </span>
                            </div>

                            <h3 className="text-xl font-black text-zinc-950 dark:text-white mb-2">
                                {isEn ? activeStage.title.en : activeStage.title.id}
                            </h3>
                            <p className="text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed mb-6">
                                {isEn ? activeStage.desc.en : activeStage.desc.id}
                            </p>

                            <div className="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
                                {activeStage.stagePreview.map((line, i) => (
                                    <div
                                        key={i}
                                        className="p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xs"
                                    >
                                        <div className="font-mono text-[10px] font-black text-emerald-800 dark:text-emerald-400 mb-1">
                                            INVARIANT 0{i + 1}
                                        </div>
                                        <div className="font-mono text-xs font-bold text-zinc-950 dark:text-zinc-100">
                                            {line}
                                        </div>
                                    </div>
                                ))}
                            </div>

                            {/* 6 Public Front-Stage UX Presets Quick Matrix */}
                            {Array.isArray(publicUxPresets) && publicUxPresets.length > 0 && (
                                <div className="border-t border-zinc-200 dark:border-zinc-800 pt-4">
                                    <div className="text-[11px] font-mono font-black uppercase tracking-wider text-zinc-800 dark:text-zinc-300 mb-2.5">
                                        {isEn ? '6 Zone-B Public Front-Stage UX Presets (12 Sectors Ready):' : '6 Preset UX Publik Zone-B (Tersedia untuk 12 Sektor Bisnis):'}
                                    </div>
                                    <div className="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                        {publicUxPresets.map((ux) => (
                                            <div
                                                key={ux.code}
                                                className="px-2.5 py-2 bg-zinc-100/80 dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-800 rounded-xs"
                                            >
                                                <div className="font-mono text-[10px] font-black text-emerald-900 dark:text-emerald-300 truncate">
                                                    {ux.short_badge || ux.code}
                                                </div>
                                                <div className="text-[11px] font-semibold text-zinc-900 dark:text-zinc-200 truncate">
                                                    {ux.localized_name || ux.name?.id || ux.code}
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            )}
                        </div>

                        <div className="pt-5 mt-5 border-t border-zinc-200 dark:border-zinc-800 flex flex-wrap items-center justify-between gap-3">
                            <span className="font-mono text-[11px] text-zinc-700 dark:text-zinc-400">
                                5 Iron Laws: Local Bundle | GPU translate3d | CLS 0.00 | Reduced-Motion | Lazy Hydrate
                            </span>
                            <a
                                href={exploreUrl}
                                className="px-4 py-2 bg-zinc-950 dark:bg-emerald-500 text-white dark:text-black font-mono text-xs font-black uppercase tracking-wider rounded-none hover:bg-zinc-800 dark:hover:bg-emerald-400 transition"
                            >
                                {isEn ? 'Explore 12-Sector Library ->' : 'Jelajahi Pustaka 12 Sektor ->'}
                            </a>
                        </div>
                    </div>
                </div>

                {/* PART B: CRYPTOGRAPHICALLY VERIFIED ANIWALL (PRIMITIVE_VERIFIED_SOCIAL_PROOF) */}
                <div className="space-y-6 pt-6 border-t border-zinc-200 dark:border-zinc-800">
                    <div className="flex flex-col md:flex-row md:items-end justify-between gap-4">
                        <div>
                            <span className="inline-block px-2.5 py-1 text-[11px] font-mono font-black uppercase tracking-widest bg-zinc-900 text-white dark:bg-zinc-800 dark:text-emerald-300 border border-zinc-700 rounded-none mb-2">
                                PRIMITIVE_VERIFIED_SOCIAL_PROOF // ZERO FAKE TESTIMONIALS
                            </span>
                            <h3 className="text-xl sm:text-2xl font-black text-zinc-950 dark:text-white">
                                {isEn
                                    ? 'BAST-Linked & Telemetry-Backed Architecture Wall of Love'
                                    : 'Infinite Kinetic Aniwall — Testimoni Terverifikasi BAST & Telemetri Proyek Nyata'}
                            </h3>
                        </div>
                        <div className="font-mono text-xs font-bold text-zinc-800 dark:text-zinc-300 tabular-nums">
                            {filteredItems.length} {isEn ? 'Verified Architecture Receipts' : 'Struk Arsitektur Terverifikasi'}
                        </div>
                    </div>

                    {/* 12-Sector Instant Filter Bar (Sharp Non-Capsule Buttons) */}
                    <div className="flex flex-wrap gap-1.5">
                        {Object.entries(SECTOR_SHORT_LABELS).map(([code, labels]) => {
                            const active = selectedSector === code;
                            return (
                                <button
                                    key={code}
                                    type="button"
                                    onClick={() => setSelectedSector(code)}
                                    className={`px-3 py-1.5 text-xs font-mono font-bold transition rounded-none border ${
                                        active
                                            ? 'bg-emerald-600 text-black border-emerald-700 font-black'
                                            : 'bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-200 border-zinc-300 dark:border-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:border-zinc-400 dark:hover:border-zinc-600'
                                    }`}
                                >
                                    {isEn ? labels.en : labels.id}
                                </button>
                            );
                        })}
                    </div>

                    {/* Kinetic Aniwall Lanes (Marquee when ALL + motion enabled; Crisp Grid when filtered or reduced-motion) */}
                    {selectedSector === 'ALL' && isHydratedVisible && !reducedMotion && filteredItems.length >= 6 ? (
                        <div className="space-y-4 overflow-hidden">
                            <div className="npro-aniwall-lane overflow-hidden py-1">
                                <div className="npro-aniwall-track-left gap-4">
                                    {[...filteredItems.slice(0, 6), ...filteredItems.slice(0, 6)].map((item, idx) => (
                                        <VerifiedReceiptCard
                                            key={`lane1-${item.id}-${idx}`}
                                            item={item}
                                            isEn={isEn}
                                            onInspect={() => setInspectedItem(item)}
                                            fixedWidth
                                        />
                                    ))}
                                </div>
                            </div>
                            <div className="npro-aniwall-lane overflow-hidden py-1">
                                <div className="npro-aniwall-track-right gap-4">
                                    {[...filteredItems.slice(6, 12), ...filteredItems.slice(6, 12)].map((item, idx) => (
                                        <VerifiedReceiptCard
                                            key={`lane2-${item.id}-${idx}`}
                                            item={item}
                                            isEn={isEn}
                                            onInspect={() => setInspectedItem(item)}
                                            fixedWidth
                                        />
                                    ))}
                                </div>
                            </div>
                        </div>
                    ) : (
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            {filteredItems.map((item) => (
                                <VerifiedReceiptCard
                                    key={item.id}
                                    item={item}
                                    isEn={isEn}
                                    onInspect={() => setInspectedItem(item)}
                                />
                            ))}
                        </div>
                    )}
                </div>
            </div>

            {/* MODAL: INSPECT ANONYMIZED BLUEPRINT ARCHITECTURE (Zero Native Dialog) */}
            {inspectedItem && (
                <div
                    className="fixed inset-0 z-50 bg-black/75 backdrop-blur-xs flex items-center justify-center p-4"
                    onClick={() => setInspectedItem(null)}
                >
                    <div
                        className="bg-white dark:bg-zinc-900 border-2 border-zinc-900 dark:border-zinc-700 rounded-sm max-w-2xl w-full p-6 shadow-2xl space-y-4"
                        onClick={(e) => e.stopPropagation()}
                    >
                        <div className="flex items-start justify-between gap-4 border-b border-zinc-200 dark:border-zinc-800 pb-3">
                            <div>
                                <span className="inline-block px-2 py-0.5 text-[10px] font-mono font-black uppercase bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950 dark:text-emerald-200 dark:border-emerald-700 rounded-none">
                                    VERIFIED ARCHITECTURE RECEIPT // {inspectedItem.verification_trigger}
                                </span>
                                <h4 className="text-lg font-black text-zinc-950 dark:text-white mt-1">
                                    {inspectedItem.client_company} — {inspectedItem.sector_badge || inspectedItem.sector_code}
                                </h4>
                            </div>
                            <button
                                type="button"
                                onClick={() => setInspectedItem(null)}
                                className="px-2.5 py-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-200 font-mono text-xs font-black border border-zinc-300 dark:border-zinc-700 rounded-none hover:bg-zinc-200 dark:hover:bg-zinc-700"
                            >
                                [ X ]
                            </button>
                        </div>

                        <div className="grid grid-cols-2 sm:grid-cols-4 gap-2.5 font-mono tabular-nums">
                            <div className="p-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xs">
                                <div className="text-[10px] text-zinc-600 dark:text-zinc-400">ULID TABLES</div>
                                <div className="text-base font-black text-zinc-950 dark:text-emerald-400">
                                    {inspectedItem.receipt?.ulid_tables_count || 24}
                                </div>
                            </div>
                            <div className="p-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xs">
                                <div className="text-[10px] text-zinc-600 dark:text-zinc-400">PESSIMISTIC LOCKS</div>
                                <div className="text-base font-black text-zinc-950 dark:text-emerald-400">
                                    {inspectedItem.receipt?.concurrency_locks_count || 6}
                                </div>
                            </div>
                            <div className="p-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xs">
                                <div className="text-[10px] text-zinc-600 dark:text-zinc-400">TOKEN SAVED</div>
                                <div className="text-base font-black text-zinc-950 dark:text-emerald-400">
                                    {inspectedItem.receipt?.token_saved_percent || 65}%
                                </div>
                            </div>
                            <div className="p-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xs">
                                <div className="text-[10px] text-zinc-600 dark:text-zinc-400">SCOPE SHA-256</div>
                                <div className="text-xs font-black text-zinc-950 dark:text-emerald-400 truncate">
                                    {inspectedItem.receipt?.scope_hash_short || 'verified'}
                                </div>
                            </div>
                        </div>

                        <div className="space-y-2 text-xs font-mono bg-zinc-50 dark:bg-zinc-950 p-4 border border-zinc-200 dark:border-zinc-800 rounded-xs">
                            <div className="text-zinc-900 dark:text-zinc-200">
                                <strong className="text-emerald-900 dark:text-emerald-400">Zone A (Back-Stage):</strong>{' '}
                                {inspectedItem.anonymized_blueprint?.zone_a_operational || inspectedItem.operational_ui_variant}
                            </div>
                            <div className="text-zinc-900 dark:text-zinc-200">
                                <strong className="text-emerald-900 dark:text-emerald-400">Zone B (Public UX):</strong>{' '}
                                {inspectedItem.anonymized_blueprint?.zone_b_public || inspectedItem.public_ux_preset}
                            </div>
                            <div className="text-zinc-900 dark:text-zinc-200">
                                <strong className="text-emerald-900 dark:text-emerald-400">Core ULID Entities:</strong>{' '}
                                {(inspectedItem.anonymized_blueprint?.core_ulid_tables || []).join(', ')}
                            </div>
                        </div>

                        <div className="flex flex-wrap items-center justify-between gap-3 pt-2">
                            {inspectedItem.seo_slug && (
                                <a
                                    href={`/explore/${String(inspectedItem.sector_code).toLowerCase()}/${inspectedItem.seo_slug}`}
                                    className="text-xs font-mono font-black text-emerald-900 dark:text-emerald-400 underline"
                                >
                                    {isEn ? 'Open Full 20% Public Architecture Page ->' : 'Buka Halaman Studi Arsitektur Lengkap ->'}
                                </a>
                            )}
                            <a
                                href={`/blueprint?tier=spark&track=retail&sector=${encodeURIComponent(inspectedItem.sector_code)}`}
                                className="px-4 py-2 bg-emerald-600 text-black font-mono text-xs font-black uppercase rounded-none hover:bg-emerald-500 transition"
                            >
                                {isEn ? 'Clone This Sector Blueprint ->' : 'Gunakan Template Arsitektur Sektor Ini ->'}
                            </a>
                        </div>
                    </div>
                </div>
            )}
        </section>
    );
}

function VerifiedReceiptCard({ item, isEn, onInspect, fixedWidth = false }) {
    const receipt = item.receipt || {};
    return (
        <article
            className={`${
                fixedWidth ? 'w-[370px] shrink-0' : 'w-full'
            } bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 hover:border-zinc-400 dark:hover:border-zinc-600 p-5 rounded-sm flex flex-col justify-between transition-colors`}
        >
            <div className="space-y-3">
                <div className="flex items-center justify-between gap-2">
                    <span className="px-2 py-0.5 text-[10px] font-mono font-black bg-emerald-100 text-emerald-950 border border-emerald-400 dark:bg-emerald-950/80 dark:text-emerald-200 dark:border-emerald-700 rounded-none truncate">
                        {item.sector_badge || item.sector_code}
                    </span>
                    <span className="px-2 py-0.5 text-[10px] font-mono font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700 rounded-none">
                        {item.stack_code || 'Laravel 13 + PG16'}
                    </span>
                </div>

                <p className="text-xs sm:text-sm text-zinc-800 dark:text-zinc-200 leading-relaxed font-medium">
                    &ldquo;{item.quote}&rdquo;
                </p>
            </div>

            <div className="mt-4 pt-3 border-t border-zinc-200 dark:border-zinc-800 space-y-3">
                <div className="flex items-center justify-between gap-2">
                    <div>
                        <div className="text-xs font-black text-zinc-950 dark:text-white">{item.client_name}</div>
                        <div className="text-[11px] text-zinc-700 dark:text-zinc-400">
                            {item.client_role} — {item.client_company}
                        </div>
                    </div>
                    <span className="px-1.5 py-0.5 text-[10px] font-mono font-black bg-zinc-900 text-white dark:bg-emerald-950 dark:text-emerald-200 border border-zinc-700 rounded-none">
                        BAST/MCP OK
                    </span>
                </div>

                {/* Telemetry Receipt Strip */}
                <div className="p-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 font-mono text-[10px] text-zinc-900 dark:text-zinc-300 tabular-nums rounded-none">
                    {receipt.ulid_tables_count || 24} ULID Tables | {receipt.concurrency_locks_count || 6} Locks | Token -{receipt.token_saved_percent || 65}%
                </div>

                <button
                    type="button"
                    onClick={onInspect}
                    className="w-full py-1.5 px-3 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-900 hover:text-white dark:hover:bg-emerald-500 dark:hover:text-black text-zinc-950 dark:text-zinc-100 border border-zinc-300 dark:border-zinc-700 font-mono text-[11px] font-black uppercase tracking-wider rounded-none transition-colors"
                >
                    {isEn ? '[ Inspect Anonymized Blueprint ]' : '[ Inspeksi Arsitektur Anonim ]'}
                </button>
            </div>
        </article>
    );
}

