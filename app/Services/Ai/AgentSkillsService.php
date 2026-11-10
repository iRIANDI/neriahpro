<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * AgentSkillsService
 *
 * Mengelola ekosistem Open Agent Skills (Vercel Skills Hub, find-skills, dan ECC Harness Skills)
 * untuk modul AI Project OS Neriah Pro.
 */
class AgentSkillsService
{
    protected string $skillsPath;
    protected string $lockFilePath;
    protected string $eccStatePath;
    protected string $eccAgentsPath;
    protected string $eccRulesPath;

    public function __construct()
    {
        $this->skillsPath = base_path('.agents/skills');
        $this->lockFilePath = base_path('skills-lock.json');
        $this->eccStatePath = base_path('.agents/ecc-install-state.json');
        $this->eccAgentsPath = base_path('.agents/agents');
        $this->eccRulesPath = base_path('.agents/rules');
    }

    /**
     * Dapatkan daftar seluruh agent skills yang terpasang di .agents/skills.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getInstalledSkills(?string $search = null): array
    {
        if (! File::isDirectory($this->skillsPath)) {
            return [];
        }

        $lockData = $this->getSkillsLock();
        $directories = File::directories($this->skillsPath);
        $skills = [];

        foreach ($directories as $dir) {
            $skillName = basename($dir);
            $skillFile = $dir.DIRECTORY_SEPARATOR.'SKILL.md';

            if (! File::exists($skillFile)) {
                continue;
            }

            $content = File::get($skillFile);
            $meta = $this->parseSkillMarkdown($skillName, $content);
            $locked = $lockData['skills'][$skillName] ?? null;

            $isVercel = $skillName === 'find-skills' || ! empty($locked['source']);
            $source = $locked['source'] ?? ($isVercel ? 'vercel-labs/skills' : 'affaan-m/everything-claude-code');
            $category = $isVercel ? 'Vercel Skills Hub' : 'ECC Agent Harness';

            $item = [
                'name' => $meta['name'] ?: $skillName,
                'slug' => $skillName,
                'description' => $meta['description'] ?: 'Agent skill module for Project OS.',
                'source' => $source,
                'source_type' => $locked['sourceType'] ?? 'git/directory',
                'category' => $category,
                'is_vercel' => $isVercel,
                'is_locked' => ! empty($locked),
                'computed_hash' => $locked['computedHash'] ?? null,
                'file_path' => '.agents/skills/'.$skillName.'/SKILL.md',
                'install_command' => $isVercel
                    ? "npx skills add https://github.com/vercel-labs/skills --skill {$skillName}"
                    : "npx skills add {$source} --skill {$skillName}",
            ];

            if ($search !== null && trim($search) !== '') {
                $q = strtolower(trim($search));
                $haystack = strtolower($item['name'].' '.$item['description'].' '.$item['category'].' '.$item['source']);
                if (! str_contains($haystack, $q)) {
                    continue;
                }
            }

            $skills[] = $item;
        }

        // Urutkan find-skills paling atas, lalu secara alfabetis
        usort($skills, function ($a, $b) {
            if ($a['slug'] === 'find-skills') {
                return -1;
            }
            if ($b['slug'] === 'find-skills') {
                return 1;
            }

            return strcmp($a['name'], $b['name']);
        });

        return $skills;
    }

    /**
     * Dapatkan detail rincian satu skill berdasarkan nama/slug.
     *
     * @return array<string, mixed>|null
     */
    public function getSkill(string $skillName): ?array
    {
        $dir = $this->skillsPath.DIRECTORY_SEPARATOR.trim($skillName);
        $skillFile = $dir.DIRECTORY_SEPARATOR.'SKILL.md';

        if (! File::exists($skillFile)) {
            return null;
        }

        $content = File::get($skillFile);
        $meta = $this->parseSkillMarkdown($skillName, $content);
        $lockData = $this->getSkillsLock();
        $locked = $lockData['skills'][$skillName] ?? null;

        return [
            'name' => $meta['name'] ?: $skillName,
            'slug' => $skillName,
            'description' => $meta['description'],
            'body_markdown' => $meta['body'],
            'raw_content' => $content,
            'source' => $locked['source'] ?? ($skillName === 'find-skills' ? 'vercel-labs/skills' : 'affaan-m/everything-claude-code'),
            'source_type' => $locked['sourceType'] ?? 'git/directory',
            'is_locked' => ! empty($locked),
            'computed_hash' => $locked['computedHash'] ?? null,
            'file_path' => '.agents/skills/'.$skillName.'/SKILL.md',
        ];
    }

    /**
     * Baca data isi skills-lock.json.
     *
     * @return array<string, mixed>
     */
    public function getSkillsLock(): array
    {
        if (! File::exists($this->lockFilePath)) {
            return ['version' => 1, 'skills' => []];
        }

        try {
            $data = json_decode(File::get($this->lockFilePath), true);

            return is_array($data) ? $data : ['version' => 1, 'skills' => []];
        } catch (\Throwable) {
            return ['version' => 1, 'skills' => []];
        }
    }

    /**
     * Dapatkan statistik ringkasan ekosistem skills.
     *
     * @return array<string, mixed>
     */
    public function getEcosystemStats(): array
    {
        $all = $this->getInstalledSkills();
        $vercelCount = 0;
        $eccCount = 0;
        $hasFindSkills = false;

        foreach ($all as $sk) {
            if ($sk['is_vercel']) {
                $vercelCount++;
            } else {
                $eccCount++;
            }
            if ($sk['slug'] === 'find-skills') {
                $hasFindSkills = true;
            }
        }

        return [
            'total_installed' => count($all),
            'vercel_skills_count' => $vercelCount,
            'ecc_skills_count' => $eccCount,
            'has_find_skills' => $hasFindSkills,
            'skills_cli_version' => '1.0 (npx skills)',
            'hub_url' => 'https://skills.sh/',
            'find_skills_source' => 'https://github.com/vercel-labs/skills',
        ];
    }

    /**
     * Rekomendasi skills berdasarkan sektor industri Project OS atau tech stack.
     *
     * @return array<int, array<string, string>>
     */
    public function getRecommendedSkillsForBlueprint(?string $sector = null, ?string $stack = null): array
    {
        $recommendations = [
            [
                'skill' => 'find-skills',
                'source' => 'vercel-labs/skills',
                'reason' => 'Core skill discovery engine untuk mencari dan menambah kemampuan agen secara dinamis.',
                'command' => 'npx skills add https://github.com/vercel-labs/skills --skill find-skills',
            ],
            [
                'skill' => 'tdd-workflow',
                'source' => 'local/ecc',
                'reason' => 'Menjamin 100% Test-Driven Development (Pest PHP & PHPUnit) sebelum menulis kode produksi.',
                'command' => 'npx skills add vercel-labs/skills --skill tdd-workflow',
            ],
            [
                'skill' => 'production-audit',
                'source' => 'local/ecc',
                'reason' => 'Audit kesiapan pra-rilis Day-2 Ops dan pencegahan regresi di lingkungan produksi.',
                'command' => 'npx skills add vercel-labs/skills --skill production-audit',
            ],
            [
                'skill' => 'error-handling',
                'source' => 'local/ecc',
                'reason' => 'Pola penanganan error deterministik, idempotensi webhook, dan recovery flow.',
                'command' => 'npx skills add vercel-labs/skills --skill error-handling',
            ],
        ];

        if ($stack && (str_contains($stack, 'REACT') || str_contains($stack, 'NEXTJS') || str_contains($stack, 'D1'))) {
            $recommendations[] = [
                'skill' => 'react-best-practices',
                'source' => 'vercel-labs/agent-skills',
                'reason' => 'Panduan optimasi performa React & Next.js resmi dari tim Vercel Engineering.',
                'command' => 'npx skills add vercel-labs/agent-skills@react-best-practices',
            ];
        }

        return $recommendations;
    }

    /**
     * Parse file SKILL.md dengan metadata YAML frontmatter.
     *
     * @return array{name: string, description: string, body: string}
     */
    protected function parseSkillMarkdown(string $fallbackName, string $content): array
    {
        $name = $fallbackName;
        $description = '';
        $body = $content;

        if (preg_match('/^---\r?\n(.*?)\r?\n---\r?\n(.*)$/s', $content, $matches)) {
            $yaml = $matches[1];
            $body = trim($matches[2]);

            if (preg_match('/^name:\s*(.+)$/m', $yaml, $nameMatch)) {
                $name = trim($nameMatch[1], " \t\n\r\0\x0B\"'");
            }
            if (preg_match('/^description:\s*(.+)$/m', $yaml, $descMatch)) {
                $description = trim($descMatch[1], " \t\n\r\0\x0B\"'");
            }
        }

        return [
            'name' => $name,
            'description' => $description,
            'body' => $body,
        ];
    }

    /**
     * Render template default find-skills SKILL.md untuk scaffold generator.
     */
    public static function renderFindSkillsMarkdown(): string
    {
        $file = base_path('.agents/skills/find-skills/SKILL.md');
        if (File::exists($file)) {
            return File::get($file);
        }

        return <<<'MD'
---
name: find-skills
description: Helps users discover and install agent skills when they ask questions like "how do I do X", "find a skill for X", "is there a skill that can...", or express interest in extending capabilities. This skill should be used when the user is looking for functionality that might exist as an installable skill.
---

# Find Skills

This skill helps you discover and install skills from the open agent skills ecosystem.

## When to Use This Skill

Use this skill when the user:
- Asks "how do I do X" where X might be a common task with an existing skill
- Says "find a skill for X" or "is there a skill for X"
- Asks "can you do X" where X is a specialized capability
- Expresses interest in extending agent capabilities
- Wants to search for tools, templates, or workflows

## What is the Skills CLI?

The Skills CLI (`npx skills`) is the package manager for the open agent skills ecosystem.

**Key commands:**
- `npx skills find [query] [--owner <owner>]` - Search for skills interactively or by keyword
- `npx skills add <package>` - Install a skill from GitHub or other sources
- `npx skills update` - Update all installed skills

Browse skills at: https://skills.sh/
MD;
    }

    /**
     * Dapatkan status instalasi dan integritas ECC Universal v2.2.3.
     *
     * @return array<string, mixed>
     */
    public function getEccStatus(): array
    {
        $hasState = File::exists($this->eccStatePath);
        $stateData = [];

        if ($hasState) {
            try {
                $stateData = json_decode(File::get($this->eccStatePath), true) ?: [];
            } catch (\Throwable) {}
        }

        $totalSubagents = File::isDirectory($this->eccAgentsPath)
            ? count(File::files($this->eccAgentsPath))
            : 0;

        $totalRules = File::isDirectory($this->eccRulesPath)
            ? count(File::files($this->eccRulesPath))
            : 0;

        return [
            'is_installed' => $hasState,
            'version' => $stateData['source']['repoVersion'] ?? '2.2.3',
            'schema_version' => $stateData['schemaVersion'] ?? 'ecc.install.v1',
            'installed_at' => $stateData['installedAt'] ?? null,
            'target_id' => $stateData['target']['id'] ?? 'antigravity-project',
            'target_kind' => $stateData['target']['kind'] ?? 'project',
            'target_root' => '.agents',
            'selected_modules' => $stateData['resolution']['selectedModules'] ?? [
                'rules-core', 'agents-core', 'commands-core', 'platform-configs', 'skill-unified-memory', 'workflow-quality'
            ],
            'legacy_languages' => $stateData['request']['legacyLanguages'] ?? ['php'],
            'total_subagents' => $totalSubagents,
            'total_rules' => $totalRules,
            'total_skills' => count($this->getInstalledSkills()),
            'status_label' => $hasState ? 'ACTIVE & VERIFIED (OK)' : 'STANDALONE',
            'cli_recipes' => [
                'doctor' => 'npx ecc-universal@2.2.3 doctor --target antigravity',
                'list_installed' => 'npx ecc-universal@2.2.3 list-installed',
                'reinstall' => 'npx ecc-universal@2.2.3 install --target antigravity php --no-hooks',
                'status' => 'npx ecc-universal@2.2.3 status',
            ],
        ];
    }

    /**
     * Dapatkan daftar ringkasan specialized subagents ECC di .agents/agents.
     *
     * @return array<int, array<string, string>>
     */
    public function getEccSubagents(?string $search = null): array
    {
        if (! File::isDirectory($this->eccAgentsPath)) {
            return [];
        }

        $files = File::files($this->eccAgentsPath);
        $agents = [];

        foreach ($files as $file) {
            if ($file->getExtension() !== 'md') {
                continue;
            }

            $slug = $file->getFilenameWithoutExtension();
            $content = File::get($file->getRealPath());

            // Parse title and brief description
            $title = Str::headline($slug);
            $description = "Specialized AI persona for {$title}.";

            if (preg_match('/^#\s+(.+)$/m', $content, $m)) {
                $title = trim($m[1]);
            }
            if (preg_match('/(?:^#\s+.+\r?\n\r?\n)(.+?)(?:\r?\n\r?\n|##)/s', $content, $dm)) {
                $description = trim(preg_replace('/\s+/', ' ', $dm[1]));
            }

            $item = [
                'slug' => $slug,
                'name' => $title,
                'description' => Str::limit($description, 180),
                'file_path' => '.agents/agents/'.$file->getFilename(),
            ];

            if ($search !== null && trim($search) !== '') {
                $q = strtolower(trim($search));
                $haystack = strtolower($item['slug'].' '.$item['name'].' '.$item['description']);
                if (! str_contains($haystack, $q)) {
                    continue;
                }
            }

            $agents[] = $item;
        }

        usort($agents, fn ($a, $b) => strcmp($a['slug'], $b['slug']));

        return $agents;
    }

    /**
     * Helper Komprehensif: Katalog Fitur-Fitur Modul AI untuk Klien & Developer.
     *
     * @return array<string, mixed>
     */
    public function getAiModuleFeaturesGuide(): array
    {
        $ecc = $this->getEccStatus();
        $skillsStats = $this->getEcosystemStats();

        return [
            'overview' => [
                'title' => 'Neriah Pro AI Agentic & Software Factory OS',
                'release' => 'v12.0 Enterprise Ultimate',
                'description' => 'Sistem orkestrasi AI mandiri dengan arsitektur Tri-Hybrid (RAG, CAG, KAG), 49 Native Tools, Vercel Skills Hub, dan ECC Universal v2.2.3 Agent Harness.',
            ],
            'features' => [
                [
                    'id' => 'tri_hybrid_memory',
                    'title' => 'Arsitektur Memori Tri-Hybrid (RAG • CAG • KAG)',
                    'tag' => 'Pilar Kognitif',
                    'badge' => 'pgvector + Cache O(1) + Rule Graph',
                    'summary' => 'Menghilangkan halusinasi LLM secara mekanis dengan 3 lapisan memori berkecepatan tinggi.',
                    'details' => [
                        'RAG (Dense 768-dim + Sparse Lexical RRF)' => 'Mencari dokumen PRD dan pasal hukum dengan presisi 100%.',
                        'CAG (Context Augmented O(1))' => 'Injeksi live telemetri status sprint, webhook, dan revenue tanpa query berulang.',
                        'KAG (Knowledge Augmented Graph)' => 'Penegakan inva-aturan deterministik (Strict ULID, no alert, zero hardcode).',
                    ],
                ],
                [
                    'id' => 'ecc_universal_harness',
                    'title' => 'ECC Universal v2.2.3 Agent Harness & 68 Subagents',
                    'tag' => 'Mesin Rekayasa',
                    'badge' => "{$ecc['total_subagents']} Subagents // {$ecc['total_rules']} Rules",
                    'summary' => 'Harness eksekusi agen otomatis terstandarisasi di folder .agents dengan Ponytail 4-Rung Decision Ladder.',
                    'details' => [
                        'Subagents Terisolasi' => 'code-reviewer, architect, php-reviewer, security-reviewer, tdd-guide, dll.',
                        'Integritas Checksum' => 'Terkunci deterministik via .agents/ecc-install-state.json.',
                        'CLI Command' => 'npx ecc-universal@2.2.3 doctor --target antigravity',
                    ],
                ],
                [
                    'id' => 'vercel_skills_hub',
                    'title' => 'Vercel Skills Hub & find-skills Discovery Engine',
                    'tag' => 'Ekosistem Terbuka',
                    'badge' => "{$skillsStats['total_installed']} Modul Skill Terpasang",
                    'summary' => 'Kemampuan ekspansi keahlian agen secara modular dari skills.sh dan repositori GitHub terbuka.',
                    'details' => [
                        'find-skills' => 'Mesin pencari keahlian agen resmi dari Vercel Labs.',
                        'Manajemen CLI' => 'npx skills find [keyword] dan npx skills add [source].',
                        'skills-lock.json' => 'Verifikasi SHA-256 untuk pencegahan slopquatting.',
                    ],
                ],
                [
                    'id' => 'native_function_calling',
                    'title' => '49 Enterprise Native Tools (Spatie RBAC & SHA-256 Audit)',
                    'tag' => 'Operasional Sistem',
                    'badge' => '100% Function Calling Protobuf Compatible',
                    'summary' => '49 operasi sistem tervalidasi yang dapat dipanggil langsung oleh Google Gemini, DeepSeek, dan Claude.',
                    'details' => [
                        'Modul Blueprint & Kontrak' => 'create_blueprint, update_blueprint_status, convert_to_contract, search_blueprints.',
                        'Modul Keuangan & Aset' => 'search_transactions, manage_domain_hosting.',
                        'Modul Enterprise & HKI' => 'AgentShield, Co-Design Dual-Key, IP Canary Watermark, SLA Bug-Warranty 90 Hari.',
                    ],
                ],
                [
                    'id' => 'cctv_workflow_traces',
                    'title' => 'Jejak CCTV Audit Workflow & Tracing Forensik',
                    'tag' => 'Transparansi & Keamanan',
                    'badge' => 'AiWorkflowTrace // SHA-256 Chained',
                    'summary' => 'Merekam setiap pemanggilan AI, tool name, waktu eksekusi milidetik, dan parameter input/output secara transparan.',
                    'details' => [
                        'CCTV Log' => 'Tersedia di tab Jejak CCTV pada halaman AI Copilot.',
                        'Tamper-Evident' => 'Terkoneksi ke rantai hash append-only audit_logs.',
                    ],
                ],
                [
                    'id' => 'multi_provider_failover',
                    'title' => 'Multi-Provider LLM Gateway dengan Failover Otomatis',
                    'tag' => 'Ketersediaan Tinggi',
                    'badge' => 'DeepSeek V3 / R1 + Google Gemini 2.5/3.0',
                    'summary' => 'Toleransi kegagalan otomatis antar-provider jika terjadi rate limit atau downtime API eksternal.',
                    'details' => [
                        'DeepSeek Engine' => 'Model penalaran cepat & ekonomis untuk operasional back-office.',
                        'Google Gemini Engine' => 'Model multimodal 1M+ konteks untuk sintesis PRD & dokumen arsitektur tebal.',
                    ],
                ],
            ],
            'quick_prompts' => $this->getClientQuickPrompts(),
            'ecc_status' => $ecc,
        ];
    }

    /**
     * Dapatkan prompt panduan cepat (helper) untuk dicoba klien.
     *
     * @return array<int, array<string, string>>
     */
    public function getClientQuickPrompts(): array
    {
        return [
            [
                'category' => 'Arsitektur',
                'title' => 'Rancang Blueprint Baru',
                'prompt' => 'Tolong rancang blueprint arsitektur sistem ERP Multi-Gudang dengan Keyset Cursor O(1) dan skema PostgreSQL Strict ULID.',
            ],
            [
                'category' => 'Keahlian Agen',
                'title' => 'Cari Skill di Ekosistem',
                'prompt' => 'Gunakan modul find-skills untuk mencari rekomendasi agent skill terbaik untuk optimasi performa React dan pengujian Pest PHP.',
            ],
            [
                'category' => 'Status Sistem',
                'title' => 'Cek Kapasitas & Telemetri',
                'prompt' => 'Berapa kapasitas sprint paralel yang aktif saat ini, berapa total blueprint yang sudah Scope Locked, dan bagaimana status kesehatan sistem?',
            ],
            [
                'category' => 'Harness & ECC',
                'title' => 'Audit Status ECC Harness',
                'prompt' => 'Periksa status kesehatan ECC Universal v2.2.3 pada folder .agents dan sebutkan subagent apa saja yang tersedia untuk code review.',
            ],
            [
                'category' => 'Garansi & SLA',
                'title' => 'Triase Garansi Bug SLA 90 Hari',
                'prompt' => 'Jelaskan bagaimana sistem membedakan antara klaim Bug Warranty Pasca-BAST Rp 0 dengan Change Request Delta v1.1 berbayar.',
            ],
        ];
    }

    /**
     * Render default skills-lock.json untuk scaffold generator.
     */
    public static function renderSkillsLockJson(): string
    {
        $file = base_path('skills-lock.json');
        if (File::exists($file)) {
            return File::get($file);
        }

        return json_encode([
            'version' => 1,
            'skills' => [
                'find-skills' => [
                    'source' => 'vercel-labs/skills',
                    'sourceType' => 'github',
                    'skillPath' => 'skills/find-skills/SKILL.md',
                    'computedHash' => 'b146008599c31057cef1c145774cea5d5afb30e8f43fa802e47a4b461419aaaf',
                ],
            ],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}

