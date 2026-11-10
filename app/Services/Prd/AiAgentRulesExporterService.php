<?php

namespace App\Services\Prd;

use App\Models\VisionBlueprint;
use App\Services\PrdGeneratorService;
use App\Services\ProjectOsBlueprintCompilerService;
use App\Services\Security\DocumentThreatSanitizerService;
use Illuminate\Support\Str;
use ZipArchive;

class AiAgentRulesExporterService
{
    /**
     * Supported AI Agent rule targets.
     */
    public const AGENTS = [
        'antigravity' => [
            'id' => 'antigravity',
            'name' => 'Google Antigravity & Gemini CLI',
            'filename' => 'AGENTS.md',
            'badge' => 'GEMINI 2.5 / DEEPMIND',
            'icon' => 'globe',
            'desc_en' => 'Rules for Google Antigravity IDE & Gemini CLI with multi-agent subagent orchestration, MCP tools, and Ponytail decision ladder.',
            'desc_id' => 'Instruksi sistem untuk Google Antigravity & Gemini CLI dengan orkestrasi sub-agent mandiri, alat MCP, dan tangga keputusan Ponytail.',
            'workspace_path' => 'AGENTS.md',
        ],
        'claude' => [
            'id' => 'claude',
            'name' => 'Anthropic Claude Code',
            'filename' => 'CLAUDE.md',
            'badge' => 'CLAUDE 3.7 SONNET',
            'icon' => 'brain',
            'desc_en' => 'Rules for Claude Code CLI with 6-stage ECC Harness, sandbox jail, and read-before-write invariants.',
            'desc_id' => 'Instruksi sistem untuk Claude Code CLI dengan 6-tahap ECC Harness, sandbox jail .claude/settings.json, dan protokol baca sebelum tulis.',
            'workspace_path' => 'CLAUDE.md',
        ],
        'codex' => [
            'id' => 'codex',
            'name' => 'OpenAI Codex & ChatGPT',
            'filename' => 'CODEX.md',
            'badge' => 'GPT-4o / CODEX',
            'icon' => 'bolt',
            'desc_en' => 'Rules for OpenAI Codex CLI, Custom GPTs, and ChatGPT workspace with zero-assumption verification.',
            'desc_id' => 'Instruksi sistem untuk OpenAI Codex CLI, Custom GPT, dan workspace ChatGPT dengan verifikasi kontrak zero-assumption.',
            'workspace_path' => 'CODEX.md',
        ],
        'cursor' => [
            'id' => 'cursor',
            'name' => 'Cursor AI IDE',
            'filename' => '.cursorrules',
            'badge' => 'CURSOR COMPOSER',
            'icon' => 'code',
            'desc_en' => 'Rules for Cursor AI IDE with @file context anchors, Laravel 13, Filament v5, and keyset cursor pagination O(1).',
            'desc_id' => 'Instruksi sistem untuk Cursor AI IDE dengan jangkar konteks file, Laravel 13, Filament v5, dan pagination cursor O(1).',
            'workspace_path' => '.cursorrules',
        ],
        'windsurf' => [
            'id' => 'windsurf',
            'name' => 'Codeium Windsurf',
            'filename' => '.windsurfrules',
            'badge' => 'CASCADE AGENT',
            'icon' => 'wave',
            'desc_en' => 'Rules for Codeium Windsurf Cascade Agent with persistent memories, terminal contracts, and anti-hallucination guardrails.',
            'desc_id' => 'Instruksi sistem untuk Codeium Windsurf Cascade Agent dengan memori proyek persisten dan pagar pengaman anti-halusinasi.',
            'workspace_path' => '.windsurfrules',
        ],
        'copilot' => [
            'id' => 'copilot',
            'name' => 'GitHub Copilot',
            'filename' => 'copilot-instructions.md',
            'badge' => 'COPILOT WORKSPACE',
            'icon' => 'github',
            'desc_en' => 'Rules for GitHub Copilot Workspace and VS Code Copilot Chat with Pest test conventions and strict types.',
            'desc_id' => 'Instruksi sistem untuk GitHub Copilot Workspace dan Chat VS Code dengan konvensi uji Pest dan pengetikan ketat PHP.',
            'workspace_path' => '.github/copilot-instructions.md',
        ],
    ];

    /**
     * Generate content for a specific AI agent format.
     */
    public static function renderAgentRules(
        string $agentId,
        VisionBlueprint $blueprint,
        array $prd = [],
        array $univSchema = [],
        bool $isEn = false
    ): array {
        if (empty($prd)) {
            $prd = $blueprint->prd_content ?? PrdGeneratorService::generate($blueprint, null, $isEn ? 'en' : 'id');
        }
        if (empty($univSchema)) {
            $univSchema = $prd['universal_blueprint_schema'] ?? ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint);
        }

        return match ($agentId) {
            'antigravity', 'gemini', 'agents' => [
                self::renderAntigravityRules($blueprint, $prd, $univSchema, $isEn),
                'AGENTS.md',
            ],
            'claude' => [
                self::renderClaudeCodeRules($blueprint, $prd, $univSchema, $isEn),
                'CLAUDE.md',
            ],
            'codex', 'openai', 'chatgpt' => [
                self::renderCodexRules($blueprint, $prd, $univSchema, $isEn),
                'CODEX.md',
            ],
            'windsurf' => [
                self::renderWindsurfRules($blueprint, $prd, $univSchema, $isEn),
                '.windsurfrules',
            ],
            'copilot', 'github-copilot' => [
                self::renderCopilotRules($blueprint, $prd, $univSchema, $isEn),
                'copilot-instructions.md',
            ],
            default => [
                self::renderCursorRules($blueprint, $prd, $univSchema, $isEn),
                '.cursorrules',
            ],
        };
    }

    /**
     * 1. Google Antigravity & Gemini CLI (AGENTS.md)
     */
    public static function renderAntigravityRules(
        VisionBlueprint $blueprint,
        array $prd = [],
        array $univSchema = [],
        bool $isEn = false
    ): string {
        $projectName = $blueprint->nama_bisnis ?: ($blueprint->client_name ?: 'Enterprise Project');
        $specId = strtoupper(substr((string) $blueprint->id, 0, 10));
        $meta = $univSchema['project_metadata'] ?? [];
        $stackC = $univSchema['tech_stack_contract'] ?? [];

        $out = "<identity>\n";
        $out .= "You are Antigravity, a powerful agentic AI coding assistant designed by the Google DeepMind team working on Advanced Agentic Coding.\n";
        $out .= "You are pair programming on \"{$projectName}\" (Spec ID: {$specId}), a mission-critical digital enterprise system.\n";
        $out .= "</identity>\n\n";

        $out .= "# AGENTS.md — GOOGLE ANTIGRAVITY & GEMINI CLI SYSTEM INSTRUCTIONS\n";
        $out .= "# Project: {$projectName}\n";
        $out .= "# Spec ID: {$specId} | Engine: Project OS v9.0 (ECC Multi-Agent Harness)\n\n";

        $out .= "## 1. MANDATORY PROTOCOLS (STRICT & UNCOMPROMISING)\n";
        $out .= "- **Strict Ponytail Decision Ladder**:\n";
        $out .= "  1. *Rung 1 (Framework Native First)*: Leverage built-in Laravel 13, Filament v5, and Livewire 4 before custom code.\n";
        $out .= "  2. *Rung 2 (Reuse Existing Codebase)*: Check existing services, traits, and components before writing new ones.\n";
        $out .= "  3. *Rung 3 (Configuration Over Code)*: Use Laravel configs, environment variables, and database settings.\n";
        $out .= "  4. *Rung 4 (Surgical & Minimalist Diff)*: Apply precise, targeted diffs to conserve tokens and preserve stability.\n";
        $out .= "- **Architectural Knowledge Graph (`.agents/graphify/GRAPH_REPORT.md`)**:\n";
        $out .= "  Always consult `GRAPH_REPORT.md` before querying files. Locate models, tables, resources, and services without blind file searching.\n";
        $out .= "- **Database Primary Keys (Strict ULID Standard)**:\n";
        $out .= "  ALWAYS use ULID (`->ulid('id')->primary()`) with `HasUlids` trait for all business domain tables. NEVER use auto-increment integer IDs or `uuid` in PostgreSQL (which throws `SQLSTATE[22P02]`).\n";
        $out .= "- **Keyset Cursor Pagination O(1)**:\n";
        $out .= "  NEVER use standard `paginate()` (OFFSET-based). ALWAYS use `cursorPaginate()` with stable keyset fallbacks (`->orderBy('id', 'asc')`).\n";
        $out .= "- **Zero Tolerance for Magic Strings & Demo Aliases**:\n";
        $out .= "  NEVER match business state or entities via fuzzy string queries (`WHERE LIKE '%APEX%'` or hardcoded demo aliases). ALWAYS use concrete relational foreign keys or exact primary key lookups.\n";
        $out .= "- **Multi-Language 2-Tier Architecture (ID & EN)**:\n";
        $out .= "  Tier 1 fields must be JSON array (`{\"id\": \"...\", \"en\": \"...\"}`). Input placeholders MUST be bilingual (`locale === 'en' ? 'e.g. ...' : 'misal: ...'`).\n";
        $out .= "- **Solid Brutalist UI Standard**:\n";
        $out .= "  Subtle thin corners (`rounded-none` or `rounded-xs`). Strict ban on capsule/pill buttons (`rounded-full`). Solid sleek monochrome borders (`border-zinc-200 dark:border-zinc-800`). WCAG AAA contrast ratio.\n";
        $out .= "- **Pixel-Perfect Sticky Navigation & Zero-Gap DOM Alignment**:\n";
        $out .= "  Scroll margin and JS jumpTo MUST land exactly flush on the toolbar's bottom border (`top: globalNavH + toolbarH`) with zero arbitrary gaps. Wrap DOM expansions in nextTick before measuring coordinates. All section IDs must strictly match DOM elements (zero ghost IDs).\n\n";

        $out .= "---\n\n";
        $out .= PrdMarkdownExporterService::toCursorrules($blueprint, $prd, $isEn);

        return $out;
    }

    /**
     * 2. Anthropic Claude Code (CLAUDE.md)
     */
    public static function renderClaudeCodeRules(
        VisionBlueprint $blueprint,
        array $prd = [],
        array $univSchema = [],
        bool $isEn = false
    ): string {
        $projectName = $blueprint->nama_bisnis ?: ($blueprint->client_name ?: 'Enterprise Project');
        $specId = strtoupper(substr((string) $blueprint->id, 0, 10));

        $out = "# CLAUDE.md — ANTHROPIC CLAUDE CODE SYSTEM INSTRUCTIONS\n";
        $out .= "# Project: {$projectName} (Spec ID: {$specId})\n";
        $out .= "# Harness: 6-Stage ECC (Plan -> Test -> Implement -> Review -> Verify -> Deploy)\n\n";

        $out .= "## 1. CLAUDE CODE EXECUTION GUARDRAILS\n";
        $out .= "- **Read Before Write**: Always inspect existing models, migrations, and components before creating new files.\n";
        $out .= "- **Bash Command Safety**: Run non-interactive commands. Never use interactive prompts or long-running blocking commands without backgrounding.\n";
        $out .= "- **AgentShield Jail**: Obey `.claude/settings.json`. Never modify `.env*`, `.git/hooks/*`, or execute unauthorized shell scripts.\n";
        $out .= "- **Testing Protocol**: When writing features, verify behavior via Pest tests: `php artisan test --filter=...`.\n";
        $out .= "- **Strict ULID & Keyset O(1)**: PostgreSQL 16 strict ULID primary keys (`VARCHAR(26)`), keyset cursor pagination (`cursorPaginate()`).\n";
        $out .= "- **Pixel-Perfect Sticky Navigation**: Scroll margins and jumpTo must align flush against sticky toolbars with zero gap and full DOM expansion via nextTick.\n";
        $out .= "- **Zero Assumption Directives**: Read `PROJECT_OS_EXECUTION_BLUEPRINT.md` and `.agents/SYNTAX_LOCK_2026.md` for framework conventions.\n\n";

        $out .= "---\n\n";
        $out .= PrdMarkdownExporterService::toCursorrules($blueprint, $prd, $isEn);

        return $out;
    }

    /**
     * 3. OpenAI Codex & ChatGPT (CODEX.md)
     */
    public static function renderCodexRules(
        VisionBlueprint $blueprint,
        array $prd = [],
        array $univSchema = [],
        bool $isEn = false
    ): string {
        $projectName = $blueprint->nama_bisnis ?: ($blueprint->client_name ?: 'Enterprise Project');
        $specId = strtoupper(substr((string) $blueprint->id, 0, 10));

        $out = "# CODEX.md — OPENAI CODEX & CHATGPT SYSTEM INSTRUCTIONS\n";
        $out .= "# Project: {$projectName} (Spec ID: {$specId})\n";
        $out .= "# Target: OpenAI Codex CLI / ChatGPT Advanced Data Analysis / Custom GPT\n\n";

        $out .= "## 1. SYSTEM ROLE & REASONING CONSTRAINTS\n";
        $out .= "You are an expert enterprise software architect. When generating code for this repository:\n";
        $out .= "1. Follow step-by-step reasoning verification before writing code.\n";
        $out .= "2. Adhere strictly to the database schemas and API contracts defined in this specification.\n";
        $out .= "3. All API error responses MUST follow RFC 9457 (`application/problem+json`).\n";
        $out .= "4. Financial math must use `DECIMAL(19,4)` with `HALF_UP` penny allocation; never use IEEE-754 floats.\n";
        $out .= "5. Enforce ULID primary keys (`VARCHAR(26)`) and Keyset Cursor Pagination O(1) across all database queries.\n";
        $out .= "6. Never use hardcoded magic strings or fuzzy string queries for business states.\n";
        $out .= "7. Enforce pixel-perfect sticky navigation: scroll offsets must land flush with sticky toolbars without arbitrary gaps, and accordion states must expand before coordinate measurement.\n\n";

        $out .= "---\n\n";
        $out .= PrdMarkdownExporterService::toCursorrules($blueprint, $prd, $isEn);

        return $out;
    }

    /**
     * 4. Cursor AI IDE (.cursorrules)
     */
    public static function renderCursorRules(
        VisionBlueprint $blueprint,
        array $prd = [],
        array $univSchema = [],
        bool $isEn = false
    ): string {
        return PrdMarkdownExporterService::toCursorrules($blueprint, $prd, $isEn);
    }

    /**
     * 5. Codeium Windsurf (.windsurfrules)
     */
    public static function renderWindsurfRules(
        VisionBlueprint $blueprint,
        array $prd = [],
        array $univSchema = [],
        bool $isEn = false
    ): string {
        $projectName = $blueprint->nama_bisnis ?: ($blueprint->client_name ?: 'Enterprise Project');
        $specId = strtoupper(substr((string) $blueprint->id, 0, 10));

        $out = "# .windsurfrules — CODEIUM WINDSURF (CASCADE) SYSTEM INSTRUCTIONS\n";
        $out .= "# Project: {$projectName} (Spec ID: {$specId})\n";
        $out .= "# Target: Windsurf Cascade Agent & Collaborative Scratchpad\n\n";

        $out .= "## 1. CASCADE MEMORY & EXECUTION DIRECTIVES\n";
        $out .= "- Maintain project memory in `.agents/BLUEPRINT_SCHEMA.json` and `.agents/graphify/GRAPH_REPORT.md`.\n";
        $out .= "- Enforce Laravel 13, Filament v5, Livewire 4, and Flux UI conventions.\n";
        $out .= "- Strict ULID primary keys (`VARCHAR(26)`) and Keyset Cursor Pagination O(1).\n";
        $out .= "- Zero tolerance for magic strings or demo entity sniffing in backend queries.\n";
        $out .= "- Comply with 2-tier bilingual support (ID & EN) and solid brutalist UI rules.\n\n";

        $out .= "---\n\n";
        $out .= PrdMarkdownExporterService::toCursorrules($blueprint, $prd, $isEn);

        return $out;
    }

    /**
     * 6. GitHub Copilot (copilot-instructions.md)
     */
    public static function renderCopilotRules(
        VisionBlueprint $blueprint,
        array $prd = [],
        array $univSchema = [],
        bool $isEn = false
    ): string {
        $projectName = $blueprint->nama_bisnis ?: ($blueprint->client_name ?: 'Enterprise Project');
        $specId = strtoupper(substr((string) $blueprint->id, 0, 10));

        $out = "# copilot-instructions.md — GITHUB COPILOT SYSTEM INSTRUCTIONS\n";
        $out .= "# Project: {$projectName} (Spec ID: {$specId})\n";
        $out .= "# Target: GitHub Copilot Workspace & VS Code Copilot Chat\n\n";

        $out .= "## 1. REPOSITORY ARCHITECTURE & STYLE CONVENTIONS\n";
        $out .= "- Language & Framework: PHP 8.5+, Laravel 13, Filament v5, Livewire 4, Tailwind CSS.\n";
        $out .= "- Primary Keys: ULID (`VARCHAR(26)`) with `HasUlids` trait. Foreign keys: `foreignUlid()`.\n";
        $out .= "- Pagination: `cursorPaginate()` with `->orderBy('id', 'asc')`. Never offset-based `paginate()`.\n";
        $out .= "- Testing: Write automated unit and feature tests using Pest PHP framework.\n";
        $out .= "- Invariants: Zero hardcoded magic strings, Tier 1 bilingual support, solid brutalist UI.\n\n";

        $out .= "---\n\n";
        $out .= PrdMarkdownExporterService::toCursorrules($blueprint, $prd, $isEn);

        return $out;
    }

    /**
     * 7. Bilingual Setup README (README_AI_RULES.md)
     */
    public static function renderAiRulesReadme(VisionBlueprint $blueprint, bool $isEn = false): string
    {
        $projectName = $blueprint->nama_bisnis ?: ($blueprint->client_name ?: 'Enterprise Project');
        $slug = $blueprint->slug ?: 'project';

        return <<<MARKDOWN
# 🤖 PROJECT OS // AI AGENT RULES PACK
## Project: {$projectName} (Slug: `{$slug}`)
### Generated by Neriah Pro Architecture Engine

Welcome to your Enterprise AI Agent Rules Pack. This package contains production-grade system instruction contracts tailored for all 6 major AI coding engines.

---

## 📁 Included Files & Placement Guide

| File | Target AI Agent | Where to Place in Workspace Root |
| :--- | :--- | :--- |
| **`AGENTS.md`** | **Google Antigravity & Gemini CLI** | `/AGENTS.md` (Workspace root) |
| **`CLAUDE.md`** | **Anthropic Claude Code CLI** | `/CLAUDE.md` (Workspace root) |
| **`.cursorrules`** | **Cursor AI IDE** | `/.cursorrules` (Workspace root) |
| **`CODEX.md`** | **OpenAI Codex & ChatGPT** | `/CODEX.md` (Workspace root) |
| **`.windsurfrules`** | **Codeium Windsurf (Cascade)** | `/.windsurfrules` (Workspace root) |
| **`copilot-instructions.md`** | **GitHub Copilot** | `/.github/copilot-instructions.md` |
| **`settings.json`** | **Claude Code Jail Config** | `/.claude/settings.json` |
| **`PROJECT_OS_EXECUTION_BLUEPRINT.md`** | **All AI Agents** | `/PROJECT_OS_EXECUTION_BLUEPRINT.md` |

---

## 🚀 How to Use with Each Agent

### 1. Google Antigravity & Gemini CLI
Place `AGENTS.md` in your project root. Antigravity will automatically detect `AGENTS.md` and enforce the Ponytail decision ladder, subagent orchestration, and ULID architecture constraints.

### 2. Anthropic Claude Code CLI
Place `CLAUDE.md` in the project root and `settings.json` inside `.claude/settings.json`.
Start your session:
```bash
claude
```
Claude Code will automatically load the 6-stage ECC Harness and obey file-path jails.

### 3. Cursor AI IDE
Place `.cursorrules` in your project root. Cursor Composer and Agent will immediately read all invariants before generating code.

### 4. OpenAI Codex / ChatGPT
Place `CODEX.md` in your project root, or paste its contents into your Custom GPT / ChatGPT system instructions.

### 5. Codeium Windsurf
Place `.windsurfrules` in your project root. Windsurf's Cascade agent will persist memory and obey framework constraints.

### 6. GitHub Copilot
Move `copilot-instructions.md` into `.github/copilot-instructions.md`. GitHub Copilot Chat and Workspace will automatically inherit all repository conventions.

---

## 🔒 Architectural Directives Locked in These Rules
1. **Strict PostgreSQL ULID (`VARCHAR(26)`)**: No auto-increment IDs. No `uuid` type casting.
2. **Keyset Cursor Pagination O(1)**: Zero offset pagination.
3. **Zero Magic Strings ("Anti-Dosa Hardcode")**: Strict concrete relational foreign keys.
4. **Tier 1 Dual-Locale Compliance**: Bilingual JSON fields and neutral placeholders.
5. **Solid Brutalist UI Standard**: Clean sharp edges (`rounded-none`), WCAG AAA contrast ratio.

*© 2026 Neriah Pro Enterprise System Architecture. All rights reserved.*
MARKDOWN;
    }

    /**
     * Compile all agent rule files into an associative array [path => content].
     */
    public static function generateAllFiles(
        VisionBlueprint $blueprint,
        array $prd = [],
        array $univSchema = [],
        bool $isEn = false
    ): array {
        if (empty($prd)) {
            $prd = $blueprint->prd_content ?? PrdGeneratorService::generate($blueprint, null, $isEn ? 'en' : 'id');
        }
        if (empty($univSchema)) {
            $univSchema = $prd['universal_blueprint_schema'] ?? ProjectOsBlueprintCompilerService::compileUniversalSchema($blueprint);
        }

        $files = [
            'AGENTS.md' => self::renderAntigravityRules($blueprint, $prd, $univSchema, $isEn),
            'CLAUDE.md' => self::renderClaudeCodeRules($blueprint, $prd, $univSchema, $isEn),
            '.cursorrules' => self::renderCursorRules($blueprint, $prd, $univSchema, $isEn),
            'CODEX.md' => self::renderCodexRules($blueprint, $prd, $univSchema, $isEn),
            '.windsurfrules' => self::renderWindsurfRules($blueprint, $prd, $univSchema, $isEn),
            'copilot-instructions.md' => self::renderCopilotRules($blueprint, $prd, $univSchema, $isEn),
            '.github/copilot-instructions.md' => self::renderCopilotRules($blueprint, $prd, $univSchema, $isEn),
            '.claude/settings.json' => DocumentThreatSanitizerService::renderClaudeSettingsJson($univSchema),
            'PROJECT_OS_EXECUTION_BLUEPRINT.md' => ProjectOsBlueprintCompilerService::renderExecutionBlueprintMarkdown($univSchema),
            'README_AI_RULES.md' => self::renderAiRulesReadme($blueprint, $isEn),
        ];

        return $files;
    }

    /**
     * Create an in-memory or temporary ZIP bundle containing all AI agent rules.
     */
    public static function createRulesZipBundle(
        VisionBlueprint $blueprint,
        array $prd = [],
        array $univSchema = [],
        bool $isEn = false
    ): string {
        $files = self::generateAllFiles($blueprint, $prd, $univSchema, $isEn);

        $tempDir = storage_path('app/temp/ai-rules');
        if (! file_exists($tempDir)) {
            @mkdir($tempDir, 0755, true);
        }

        $slug = $blueprint->slug ?: Str::slug($blueprint->nama_bisnis ?: 'project');
        $zipPath = $tempDir . '/' . $slug . '-AI-RULES-BUNDLE.zip';

        if (class_exists(ZipArchive::class)) {
            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                foreach ($files as $relativePath => $content) {
                    $zip->addFromString($relativePath, (string) $content);
                }
                $zip->close();
            }
        }

        return $zipPath;
    }
}

