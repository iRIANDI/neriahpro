<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $fullName = $content['personal_info']['full_name'] ?? $content['personal_info']['name'] ?? 'Kandidat Profesional';
        $headline = $content['personal_info']['headline'] ?? $resume->target_role ?? 'Professional Talent';
        $summary = $content['personal_info']['summary'] ?? 'Curriculum Vitae Profesional Standar ATS';
    @endphp

    <title>{{ $fullName }} - {{ $headline }} // CV Pro Neriah Pro</title>
    <meta name="description" content="{{ Str::limit($summary, 160) }}">
    <link rel="canonical" href="{{ $resume->public_url }}">

    <!-- Open Graph / Candidate Profile -->
    <meta property="og:type" content="profile">
    <meta property="og:url" content="{{ $resume->public_url }}">
    <meta property="og:title" content="{{ $fullName }} - {{ $headline }}">
    <meta property="og:description" content="{{ Str::limit($summary, 160) }}">
    <meta property="og:site_name" content="CV Pro by Neriah Pro">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ $fullName }} - {{ $headline }}">
    <meta name="twitter:description" content="{{ Str::limit($summary, 160) }}">

    <!-- Schema.org JSON-LD Structured Data (ProfilePage & Person) -->
    {!! \App\Services\Seo\SchemaOrgService::render([
        \App\Services\Seo\SchemaOrgService::organization(),
        \App\Services\Seo\SchemaOrgService::resumeProfile($resume),
        \App\Services\Seo\SchemaOrgService::breadcrumbs([
            'Home' => url('/'),
            'CV Pro' => route('cv-pro.index'),
            $fullName => $resume->public_url,
        ])
    ]) !!}

    <!-- Local Fonts (Zero External Latency) -->
    <link rel="stylesheet" href="{{ asset('fonts/instrument-sans/instrument-sans.css') }}">

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css'])

    <style>
        body {
            font-family: '{{ $resume->font_family ?: 'Inter' }}', sans-serif;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background-color: #ffffff !important;
                color: #000000 !important;
                padding: 0 !important;
            }
            .a4-page {
                box-shadow: none !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                padding: 15mm 20mm !important;
            }
        }
    </style>
</head>
<body class="bg-zinc-100 text-zinc-900 min-h-screen py-8 antialiased">

    <!-- Sticky Floating Action Bar (Hidden on print) -->
    <header class="no-print fixed top-4 inset-x-0 z-50 flex justify-center px-4">
        <div class="bg-zinc-900/90 backdrop-blur-md text-white border border-zinc-700/80 px-4 py-2.5 shadow-2xl flex items-center gap-3 sm:gap-6 text-xs font-mono">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="font-bold tracking-wider">VERIFIED ATS CV</span>
                <span class="px-2 py-0.5 bg-emerald-500/20 text-emerald-400 font-bold border border-emerald-500/30">
                    SKOR: {{ $resume->ats_score }}/100
                </span>
            </div>

            <div class="h-4 w-px bg-zinc-700"></div>

            <div class="flex items-center gap-2">
                <button onclick="window.print()" class="px-3 py-1.5 bg-emerald-500 hover:bg-emerald-400 text-black font-bold transition flex items-center gap-1.5 shadow">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    <span>Download PDF / Cetak</span>
                </button>
                <button onclick="navigator.clipboard.writeText(window.location.href); (window.showToast ? window.showToast({ type: 'success', title: 'TAUTAN DISALIN', message: 'Tautan portofolio telah disalin ke clipboard.' }) : null)" class="px-3 py-1.5 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 font-bold transition border border-zinc-700">
                    Salin Link
                </button>
                <a href="/cv-pro" class="hidden sm:inline-block px-3 py-1.5 bg-zinc-800 hover:bg-zinc-700 text-emerald-400 font-bold transition border border-zinc-700">
                    Buka CV Pro Studio &rarr;
                </a>
            </div>
        </div>
    </header>

    @php
        $p = $content['personal_info'] ?? [];
        $experiences = $content['experiences'] ?? [];
        $education = $content['education'] ?? [];
        $skills = $content['skills'] ?? [];
        $certifications = $content['certifications'] ?? [];
        $projects = $content['projects'] ?? [];
        $accentColor = $resume->primary_color ?: '#4f46e5';
    @endphp

    <!-- A4 Document Canvas Container -->
    <div class="a4-page max-w-[210mm] mx-auto bg-white shadow-xl p-10 sm:p-14 my-12 border border-zinc-200">
        
        <!-- Header Section -->
        <header class="border-b pb-6 mb-6" style="border-color: {{ $accentColor }}20;">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold tracking-tight text-zinc-950">{{ $p['name'] ?? 'Nama Lengkap' }}</h1>
                    <p class="text-base font-semibold mt-1" style="color: {{ $accentColor }}">{{ $p['title'] ?? 'Posisi / Gelar Profesi' }}</p>
                </div>
                @if(!empty($resume->photo_url))
                    <img src="{{ $resume->photo_url }}" alt="Profile" class="w-20 h-20 rounded-full object-cover border-2 shadow-sm" style="border-color: {{ $accentColor }}">
                @endif
            </div>

            <!-- Contact & Meta Links -->
            <div class="flex flex-wrap items-center gap-y-1.5 gap-x-4 text-xs text-zinc-600 mt-4 font-mono">
                @if(!empty($p['email']))
                    <div class="flex items-center gap-1">
                        <span>✉</span>
                        <a href="mailto:{{ $p['email'] }}" class="hover:underline">{{ $p['email'] }}</a>
                    </div>
                @endif
                @if(!empty($p['phone']))
                    <div class="flex items-center gap-1">
                        <span>📞</span>
                        <span>{{ $p['phone'] }}</span>
                    </div>
                @endif
                @if(!empty($p['location']))
                    <div class="flex items-center gap-1">
                        <span>📍</span>
                        <span>{{ $p['location'] }}</span>
                    </div>
                @endif
                @if(!empty($p['linkedin']))
                    <div class="flex items-center gap-1">
                        <span>💼</span>
                        <a href="https://{{ $p['linkedin'] }}" target="_blank" class="hover:underline text-indigo-600">{{ $p['linkedin'] }}</a>
                    </div>
                @endif
                @if(!empty($p['website']))
                    <div class="flex items-center gap-1">
                        <span>🌐</span>
                        <a href="{{ $p['website'] }}" target="_blank" class="hover:underline text-indigo-600">{{ $p['website'] }}</a>
                    </div>
                @endif
            </div>
        </header>

        <!-- Summary -->
        @if(!empty($p['summary']))
            <section class="mb-6">
                <h2 class="text-xs font-bold uppercase tracking-wider mb-2 font-mono" style="color: {{ $accentColor }}">
                    // Profil Profesional
                </h2>
                <p class="text-sm leading-relaxed text-zinc-700 text-justify">
                    {{ $p['summary'] }}
                </p>
            </section>
        @endif

        <!-- Work Experience -->
        @if(!empty($experiences))
            <section class="mb-6">
                <h2 class="text-xs font-bold uppercase tracking-wider mb-3 font-mono border-b pb-1" style="color: {{ $accentColor }}; border-color: {{ $accentColor }}20;">
                    // Pengalaman Kerja
                </h2>
                <div class="space-y-4">
                    @foreach($experiences as $exp)
                        <div>
                            <div class="flex justify-between items-baseline">
                                <h3 class="text-sm font-bold text-zinc-900">{{ $exp['role'] ?? '' }}</h3>
                                <span class="text-xs font-mono text-zinc-500">{{ $exp['period'] ?? '' }}</span>
                            </div>
                            <div class="flex justify-between items-baseline text-xs text-zinc-600 mb-1.5">
                                <span class="font-semibold">{{ $exp['company'] ?? '' }}</span>
                                <span class="italic">{{ $exp['location'] ?? '' }}</span>
                            </div>
                            @if(!empty($exp['description']))
                                <p class="text-xs text-zinc-700 mb-1.5">{{ $exp['description'] }}</p>
                            @endif
                            @if(!empty($exp['bullets']))
                                <ul class="list-disc list-outside ml-4 space-y-1 text-xs text-zinc-700">
                                    @foreach($exp['bullets'] as $bullet)
                                        @if(trim($bullet))
                                            <li>{{ $bullet }}</li>
                                        @endif
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Education -->
        @if(!empty($education))
            <section class="mb-6">
                <h2 class="text-xs font-bold uppercase tracking-wider mb-3 font-mono border-b pb-1" style="color: {{ $accentColor }}; border-color: {{ $accentColor }}20;">
                    // Pendidikan
                </h2>
                <div class="space-y-3">
                    @foreach($education as $edu)
                        <div class="flex justify-between items-baseline">
                            <div>
                                <h3 class="text-sm font-bold text-zinc-900">{{ $edu['institution'] ?? '' }}</h3>
                                <p class="text-xs text-zinc-700">{{ $edu['degree'] ?? '' }} {{ !empty($edu['field']) ? '- ' . $edu['field'] : '' }}</p>
                                @if(!empty($edu['gpa']))
                                    <p class="text-[11px] text-zinc-500 font-mono mt-0.5">IPK/GPA: {{ $edu['gpa'] }}</p>
                                @endif
                            </div>
                            <span class="text-xs font-mono text-zinc-500">{{ $edu['year'] ?? '' }}</span>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Technical Skills -->
        @if(!empty($skills))
            <section class="mb-6">
                <h2 class="text-xs font-bold uppercase tracking-wider mb-2.5 font-mono border-b pb-1" style="color: {{ $accentColor }}; border-color: {{ $accentColor }}20;">
                    // Keahlian Teknis
                </h2>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($skills as $skill)
                        @if(trim($skill))
                            <span class="px-2 py-0.5 bg-zinc-100 text-zinc-800 text-xs font-medium rounded border border-zinc-200">
                                {{ $skill }}
                            </span>
                        @endif
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Projects & Portfolio -->
        @if(!empty($projects))
            <section class="mb-6">
                <h2 class="text-xs font-bold uppercase tracking-wider mb-3 font-mono border-b pb-1" style="color: {{ $accentColor }}; border-color: {{ $accentColor }}20;">
                    // Proyek Unggulan
                </h2>
                <div class="space-y-3">
                    @foreach($projects as $prj)
                        <div>
                            <div class="flex justify-between items-baseline">
                                <h3 class="text-sm font-bold text-zinc-900">{{ $prj['name'] ?? '' }}</h3>
                                @if(!empty($prj['link']))
                                    <a href="{{ $prj['link'] }}" target="_blank" class="text-xs text-indigo-600 hover:underline font-mono">Tautan Proyek &rarr;</a>
                                @endif
                            </div>
                            <p class="text-xs text-zinc-700 mt-0.5">{{ $prj['description'] ?? '' }}</p>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Certifications -->
        @if(!empty($certifications))
            <section>
                <h2 class="text-xs font-bold uppercase tracking-wider mb-2.5 font-mono border-b pb-1" style="color: {{ $accentColor }}; border-color: {{ $accentColor }}20;">
                    // Sertifikasi & Lisensi
                </h2>
                <div class="space-y-1.5">
                    @foreach($certifications as $cert)
                        <div class="flex justify-between items-baseline text-xs">
                            <span class="font-bold text-zinc-800">{{ $cert['name'] ?? '' }} • <span class="font-normal text-zinc-600">{{ $cert['issuer'] ?? '' }}</span></span>
                            <span class="font-mono text-zinc-500">{{ $cert['year'] ?? '' }}</span>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

    </div>

</body>
</html>
