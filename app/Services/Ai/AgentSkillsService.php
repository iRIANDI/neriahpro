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

    public function __construct()
    {
        $this->skillsPath = base_path('.agents/skills');
        $this->lockFilePath = base_path('skills-lock.json');
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
