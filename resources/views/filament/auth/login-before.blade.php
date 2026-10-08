@php
    $isEn = app()->getLocale() === 'en';
@endphp
<div style="margin-bottom: 1.25rem; padding: 0.875rem 1rem; border-radius: 0.75rem; background: rgba(244, 244, 245, 0.8); border: 1px solid rgba(228, 228, 231, 1); text-align: left;" class="dark:bg-zinc-900/80 dark:border-zinc-800">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.625rem; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(228, 228, 231, 0.8);" class="dark:border-zinc-800">
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span style="display: inline-block; padding: 0.125rem 0.375rem; border-radius: 0.25rem; font-family: monospace; font-size: 9px; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase;" class="bg-zinc-900 text-zinc-100 dark:bg-white dark:text-zinc-900">
                {{ $isEn ? 'CORE PILLARS' : 'PILAR UTAMA' }}
            </span>
            <span style="font-size: 11px; font-weight: 600; color: #3f3f46;" class="dark:text-zinc-300">
                {{ $isEn ? 'Enterprise Mission Architecture' : 'Arsitektur Misi Enterprise' }}
            </span>
        </div>
        <span style="font-family: monospace; font-size: 9px; color: #71717a;" class="dark:text-zinc-400">
            v2.5 // PROD
        </span>
    </div>

    <!-- Responsive auto-fit grid: 1 column on mobile portrait, 3 columns on tablet/desktop landscape -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 0.5rem;">
        <!-- Pillar 1: Vision Blueprint -->
        <div style="padding: 0.5rem 0.625rem; border-radius: 0.5rem; background: #ffffff; border: 1px solid rgba(228, 228, 231, 0.9);" class="dark:bg-zinc-950 dark:border-zinc-800">
            <div style="display: flex; align-items: center; gap: 0.375rem; margin-bottom: 0.25rem; color: #d97706; font-size: 11px; font-weight: 700; font-family: monospace;" class="dark:text-amber-400">
                <svg width="14" height="14" style="width: 14px; height: 14px; min-width: 14px; min-height: 14px; max-width: 14px; max-height: 14px; display: inline-block; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Vision Blueprint</span>
            </div>
            <p style="font-size: 10px; color: #52525b; line-height: 1.35; margin: 0;" class="dark:text-zinc-400">
                {{ $isEn 
                    ? 'Automated PRD synthesis, ERD architecture, and PostgreSQL ULID schemas.' 
                    : 'Sintesis PRD otomatis, arsitektur ERD, dan skema PostgreSQL ULID.' }}
            </p>
        </div>

        <!-- Pillar 2: Scope Lock OS -->
        <div style="padding: 0.5rem 0.625rem; border-radius: 0.5rem; background: #ffffff; border: 1px solid rgba(228, 228, 231, 0.9);" class="dark:bg-zinc-950 dark:border-zinc-800">
            <div style="display: flex; align-items: center; gap: 0.375rem; margin-bottom: 0.25rem; color: #d97706; font-size: 11px; font-weight: 700; font-family: monospace;" class="dark:text-amber-400">
                <svg width="14" height="14" style="width: 14px; height: 14px; min-width: 14px; min-height: 14px; max-width: 14px; max-height: 14px; display: inline-block; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                <span>Scope Lock OS</span>
            </div>
            <p style="font-size: 10px; color: #52525b; line-height: 1.35; margin: 0;" class="dark:text-zinc-400">
                {{ $isEn 
                    ? 'Anti-scope-creep contracts, milestone escrow, and deliverable certainty.' 
                    : 'Kontrak anti-scope-creep, milestone escrow, dan kepastian deliverable.' }}
            </p>
        </div>

        <!-- Pillar 3: CV Pro Studio -->
        <div style="padding: 0.5rem 0.625rem; border-radius: 0.5rem; background: #ffffff; border: 1px solid rgba(228, 228, 231, 0.9);" class="dark:bg-zinc-950 dark:border-zinc-800">
            <div style="display: flex; align-items: center; gap: 0.375rem; margin-bottom: 0.25rem; color: #d97706; font-size: 11px; font-weight: 700; font-family: monospace;" class="dark:text-amber-400">
                <svg width="14" height="14" style="width: 14px; height: 14px; min-width: 14px; min-height: 14px; max-width: 14px; max-height: 14px; display: inline-block; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 100-6 3 3 0 000 6z"></path>
                </svg>
                <span>CV Pro Studio</span>
            </div>
            <p style="font-size: 10px; color: #52525b; line-height: 1.35; margin: 0;" class="dark:text-zinc-400">
                {{ $isEn 
                    ? 'MarkItDown document scanning, ATS CV audit, and AI mock voice interview.' 
                    : 'MarkItDown scan dokumen, audit ATS CV, dan mock interview suara.' }}
            </p>
        </div>
    </div>
</div>
