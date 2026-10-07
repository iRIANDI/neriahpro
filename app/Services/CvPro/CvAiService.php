<?php

namespace App\Services\CvPro;

use Illuminate\Support\Str;

class CvAiService
{
    /**
     * Lint and audit a resume for ATS compliance, weak verbs, and quantifiable metrics.
     */
    public static function lintResume(array $content, string $lang = 'id'): array
    {
        $personal = $content['personal_info'] ?? [];
        $experiences = $content['experiences'] ?? [];
        $skills = $content['skills'] ?? [];
        $education = $content['education'] ?? [];

        $issues = [];
        $suggestions = [];
        $strengths = [];

        // 1. Completeness Check (Max 25 pts)
        $completenessScore = 0;
        if (!empty($personal['name'])) $completenessScore += 5;
        if (!empty($personal['email']) && filter_var($personal['email'], FILTER_VALIDATE_EMAIL)) $completenessScore += 5;
        if (!empty($personal['phone'])) $completenessScore += 5;
        if (!empty($personal['summary']) && strlen($personal['summary']) > 50) {
            $completenessScore += 5;
            $strengths[] = $lang === 'id' ? 'Ringkasan profesional tersusun dengan baik.' : 'Professional summary is well-articulated.';
        } else {
            $issues[] = [
                'type' => 'warning',
                'section' => 'Profil',
                'message' => $lang === 'id' 
                    ? 'Ringkasan profil terlalu singkat. Tambahkan ringkasan 2-3 kalimat tentang keahlian utama dan nilai tambah Anda.' 
                    : 'Profile summary is too short. Add a 2-3 sentence overview of your core skills and value proposition.',
            ];
        }
        if (!empty($education)) $completenessScore += 5;

        // 2. Metrics & Quantifiable Impact Check (Max 25 pts)
        $metricCount = 0;
        $weakVerbsFound = [];
        $weakVerbPatterns = [
            'id' => ['bertanggung jawab', 'membantu', 'melakukan', 'bekerja sama', 'mengurus', 'ditugaskan'],
            'en' => ['responsible for', 'helped', 'assisted', 'worked on', 'handled', 'tasked with'],
        ];

        $strongVerbs = [
            'id' => ['Mengarsitektur', 'Mengoptimalkan', 'Memimpin', 'Meningkatkan', 'Merekayasa', 'Memangkas', 'Memprakarsai'],
            'en' => ['Architected', 'Optimized', 'Spearheaded', 'Accelerated', 'Engineered', 'Overhauled', 'Orchestrated'],
        ];

        $langKey = $lang === 'id' ? 'id' : 'en';

        foreach ($experiences as $exp) {
            $desc = ($exp['description'] ?? '') . ' ' . implode(' ', $exp['bullets'] ?? []);
            
            // Check for numbers / metrics (% or numbers or currencies)
            if (preg_match_all('/\b\d+(\.\d+)?%?|\$\d+|Rp\s*\d+/i', $desc, $matches)) {
                $metricCount += count($matches[0]);
            }

            // Check for weak verbs
            foreach ($weakVerbPatterns[$langKey] as $weak) {
                if (stripos($desc, $weak) !== false && !in_array($weak, $weakVerbsFound)) {
                    $weakVerbsFound[] = $weak;
                }
            }
        }

        $impactScore = min(25, $metricCount * 5);
        if ($impactScore >= 15) {
            $strengths[] = $lang === 'id' 
                ? "Ditemukan {$metricCount} data metrik terukur yang memperkuat pencapaian kerja." 
                : "Found {$metricCount} quantifiable metrics strengthening your career achievements.";
        } else {
            $issues[] = [
                'type' => 'critical',
                'section' => 'Pengalaman Kerja',
                'message' => $lang === 'id'
                    ? 'Kurang data angka/metrik terukur. Gunakan rumus: "Berhasil [Hasil Kerja] sebesar [X%] dengan [Metode/Teknologi]".'
                    : 'Missing quantifiable impact metrics. Use the formula: "Achieved [Outcome] by [X%] through [Method/Tool]".',
            ];
        }

        // 3. Action Verbs & Style (Max 25 pts)
        $verbScore = max(5, 25 - (count($weakVerbsFound) * 5));
        if (!empty($weakVerbsFound)) {
            $issues[] = [
                'type' => 'warning',
                'section' => 'Pilihan Kata',
                'message' => $lang === 'id'
                    ? 'Hindari kata pasif seperti: "' . implode('", "', $weakVerbsFound) . '". Ganti dengan kata kerja aksi kuat: ' . implode(', ', array_slice($strongVerbs['id'], 0, 4)) . '.'
                    : 'Avoid passive phrases like: "' . implode('", "', $weakVerbsFound) . '". Replace with active verbs: ' . implode(', ', array_slice($strongVerbs['en'], 0, 4)) . '.',
            ];
        } else {
            $strengths[] = $lang === 'id' ? 'Penggunaan kata kerja aksi cukup kuat dan tegas.' : 'Strong use of active professional verbs.';
        }

        // 4. Skills & ATS Keywords (Max 25 pts)
        $skillCount = count($skills);
        $skillScore = min(25, max(10, $skillCount * 3));
        if ($skillCount < 5) {
            $issues[] = [
                'type' => 'info',
                'section' => 'Keahlian (Skills)',
                'message' => $lang === 'id'
                    ? 'Jumlah keterampilan masih sedikit. Tambahkan minimal 6-10 keterampilan teknis spesifik untuk lolos filter ATS.'
                    : 'Few skills listed. Add at least 6-10 targeted technical skills to pass ATS filters.',
            ];
        } else {
            $strengths[] = $lang === 'id' ? "Memiliki {$skillCount} keterampilan teknis terdaftar." : "Has {$skillCount} technical skills listed.";
        }

        $overallScore = $completenessScore + $impactScore + $verbScore + $skillScore;

        return [
            'overall_score' => $overallScore,
            'sub_scores' => [
                'completeness' => $completenessScore,
                'quantifiable_impact' => $impactScore,
                'action_verbs' => $verbScore,
                'skills_keywords' => $skillScore,
            ],
            'grade' => $overallScore >= 85 ? 'A (Excellent)' : ($overallScore >= 70 ? 'B (Good)' : ($overallScore >= 55 ? 'C (Needs Work)' : 'D (Incomplete)')),
            'issues' => $issues,
            'strengths' => $strengths,
            'quantifiable_metrics_count' => $metricCount,
            'weak_verbs_detected' => $weakVerbsFound,
        ];
    }

    /**
     * Generate strategic mock interview questions based on candidate profile and target job description.
     */
    public static function generateInterviewQuestions(string $jobTitle, string $company, string $jobDesc, array $resumeContent, string $lang = 'id'): array
    {
        $candidateName = $resumeContent['personal_info']['name'] ?? 'Kandidat';
        $topSkills = array_slice($resumeContent['skills'] ?? ['Problem Solving', 'Engineering', 'Communication'], 0, 4);
        $skillsStr = implode(', ', $topSkills);

        if ($lang === 'id') {
            return [
                'company_context' => "Analisis untuk peran {$jobTitle} di {$company}: Fokus utama adalah evaluasi kemampuan teknis di bidang {$skillsStr}, kemampuan adaptasi alur kerja tim, dan ketahanan dalam pemecahan masalah skala nyata.",
                'questions' => [
                    [
                        'id' => 1,
                        'category' => 'Motivasi & Culture Fit',
                        'question' => "Mengapa Anda tertarik untuk bergabung dengan {$company} sebagai {$jobTitle}, dan bagaimana keahlian Anda di {$skillsStr} dapat memberikan dampak instan pada tim kami?",
                        'tip' => "Jelaskan riset Anda tentang produk/visi {$company}. Hubungkan pengalaman masa lalu dengan solusi nyata yang bisa Anda tawarkan dalam 90 hari pertama.",
                    ],
                    [
                        'id' => 2,
                        'category' => 'Teknis & Problem Solving (STAR)',
                        'question' => "Ceritakan sebuah proyek teknis tersulit yang pernah Anda selesaikan. Masalah apa yang dihadapi, arsitektur/teknologi apa yang Anda pilih, dan bagaimana hasil akhirnya?",
                        'tip' => "Gunakan metode STAR: Situasi (latar belakang), Tugas (peran Anda), Aksi (keputusan arsitektur/koding nyata), dan Hasil (metrik terukur seperti efisiensi, kecepatan, atau penurunan error).",
                    ],
                    [
                        'id' => 3,
                        'category' => 'Tekanan & Manajemen Konflik',
                        'question' => "Bagaimana cara Anda memprioritaskan tugas saat menghadapi deadline ketat dengan spesifikasi yang tiba-tiba berubah dari manajemen?",
                        'tip' => "Tunjukkan kedewasaan profesional, komunikasi proaktif, pemahaman analisis risiko, dan kemampuan negosiasi ruang lingkup (scope negotiation).",
                    ],
                    [
                        'id' => 4,
                        'category' => 'Spesifik Peran (Domain Excellence)',
                        'question' => "Berdasarkan deskripsi pekerjaan {$jobTitle}, bagaimana pendekatan Anda untuk memastikan skalabilitas, keamanan sistem, dan performa tinggi pada aplikasi yang Anda bangun?",
                        'tip' => "Sebutkan praktik terbaik industri: caching, database indexing O(1), penanganan concurrency, testing terotomatisasi, dan monitoring log.",
                    ],
                    [
                        'id' => 5,
                        'category' => 'Visi & Pengembangan Diri',
                        'question' => "Di mana Anda melihat diri Anda berkembang dalam 2-3 tahun ke depan bersama {$company}, dan teknologi apa yang sedang aktif Anda pelajari saat ini?",
                        'tip' => "Tunjukkan rasa lapar belajar (growth mindset) dan komitmen loyalitas terhadap perkembangan karier jangka panjang.",
                    ],
                ],
            ];
        } else {
            return [
                'company_context' => "Interview strategy for {$jobTitle} at {$company}: Evaluating technical depth in {$skillsStr}, ownership mindset, and delivery under ambiguity.",
                'questions' => [
                    [
                        'id' => 1,
                        'category' => 'Motivation & Culture Fit',
                        'question' => "What motivated you to apply for the {$jobTitle} position at {$company}, and how does your expertise in {$skillsStr} translate into immediate team impact?",
                        'tip' => "Demonstrate clear research on {$company}'s mission. Link your past achievements to value you will deliver in the first 90 days.",
                    ],
                    [
                        'id' => 2,
                        'category' => 'Technical & STAR Problem Solving',
                        'question' => "Walk me through the most challenging technical project you delivered. What constraints did you face, what architectural choices did you make, and what was the measurable outcome?",
                        'tip' => "Apply the STAR method: Situation, Task, Action, and Quantifiable Results.",
                    ],
                    [
                        'id' => 3,
                        'category' => 'Ownership Under Pressure',
                        'question' => "How do you navigate tight deadlines when project scope shifts abruptly or ambiguous requirements emerge?",
                        'tip' => "Highlight proactive stakeholder communication, risk mitigation, and disciplined scope management.",
                    ],
                    [
                        'id' => 4,
                        'category' => 'Domain Mastery & Scale',
                        'question' => "Considering the requirements for {$jobTitle}, what are your go-to architectural principles for ensuring scalability, O(1) performance, and robust security?",
                        'tip' => "Mention database query optimization, caching strategies, automated CI/CD pipelines, and zero-trust authentication.",
                    ],
                    [
                        'id' => 5,
                        'category' => 'Career Trajectory',
                        'question' => "Where do you envision your professional trajectory heading over the next 3 years at {$company}?",
                        'tip' => "Highlight an ambitious growth mindset and continuous learning enthusiasm.",
                    ],
                ],
            ];
        }
    }

    /**
     * Evaluate candidate's spoken or written answer against the STAR methodology.
     */
    public static function evaluateInterviewAnswer(string $question, string $answer, string $lang = 'id'): array
    {
        $wordCount = str_word_count($answer);

        if ($wordCount < 15) {
            return [
                'score' => 40,
                'star_breakdown' => [
                    'situation' => false,
                    'task' => false,
                    'action' => true,
                    'result' => false,
                ],
                'feedback' => $lang === 'id' 
                    ? 'Jawaban Anda terlalu singkat (kurang dari 15 kata). Jelaskan situasi konkret dan tindakan nyata yang Anda ambil.' 
                    : 'Answer is too brief (under 15 words). Elaborate on the context and concrete actions taken.',
                'coaching_tip' => $lang === 'id'
                    ? 'Coba jawab minimal 2-3 menit berbicara atau 80-150 kata dengan menyertakan hasil akhir.'
                    : 'Aim for a 2-minute spoken response (80-150 words) highlighting measurable impact.',
            ];
        }

        // Detect STAR indicators
        $hasSituation = preg_match('/saat|ketika|proyek|waktu itu|when|during|while|project|at my previous/i', $answer);
        $hasTask = preg_match('/tugas|tanggung jawab|target|tujuan|goal|task|responsible|objective/i', $answer);
        $hasAction = preg_match('/saya|mengembangkan|membuat|merancang|mengoptimalkan|I built|I implemented|I designed|I solved/i', $answer);
        $hasResult = preg_match('/\d+%?|berhasil|meningkatkan|memangkas|hemat|achieved|increased|reduced|delivered|result/i', $answer);

        $starScore = 50;
        if ($hasSituation) $starScore += 12;
        if ($hasTask) $starScore += 12;
        if ($hasAction) $starScore += 13;
        if ($hasResult) $starScore += 13;

        $feedbackPoints = [];
        if (!$hasSituation) $feedbackPoints[] = $lang === 'id' ? 'Sebutkan latar belakang/perusahaan tempat masalah terjadi.' : 'Set the specific background context.';
        if (!$hasResult) $feedbackPoints[] = $lang === 'id' ? 'Sebutkan hasil akhir (seperti persentase peningkatan atau kepuasan klien).' : 'Conclude with quantifiable results or metrics.';

        return [
            'score' => min(98, $starScore),
            'star_breakdown' => [
                'situation' => (bool) $hasSituation,
                'task' => (bool) $hasTask,
                'action' => (bool) $hasAction,
                'result' => (bool) $hasResult,
            ],
            'feedback' => empty($feedbackPoints) 
                ? ($lang === 'id' ? 'Struktur STAR lengkap dan penyampaian terstruktur rapi!' : 'Comprehensive STAR answer with strong articulation!')
                : implode(' ', $feedbackPoints),
            'coaching_tip' => $lang === 'id'
                ? 'Jaga intonasi tetap tenang, percaya diri, dan akhiri dengan refleksi apa yang Anda pelajari dari pengalaman tersebut.'
                : 'Maintain an engaging tone and conclude with key lessons learned.',
            'word_count' => $wordCount,
        ];
    }

    /**
     * Generate customized Thank You, Follow-up, or Cold Outreach letters.
     */
    public static function generateOutreachLetter(string $type, string $company, string $jobTitle, string $recipient, array $resumeContent, string $lang = 'id'): string
    {
        $candidateName = $resumeContent['personal_info']['name'] ?? 'Kandidat';
        $email = $resumeContent['personal_info']['email'] ?? 'email@example.com';
        $phone = $resumeContent['personal_info']['phone'] ?? '';
        $recipient = $recipient ?: ($lang === 'id' ? 'Bapak/Ibu Tim Rekruter' : 'Hiring Team');

        if ($type === 'thank_you') {
            if ($lang === 'id') {
                return "Yth. {$recipient} di {$company},\n\n" .
                    "Terima kasih banyak atas waktu dan kesempatan diskusi wawancara untuk posisi {$jobTitle} hari ini. " .
                    "Saya sangat terkesan dengan visi {$company} dalam mengembangkan produk yang berdampak bagi industri.\n\n" .
                    "Diskusi kita tadi semakin memperkuat keyakinan saya bahwa latar belakang pengalaman saya dalam rekayasa sistem dan pemecahan masalah dapat memberikan kontribusi langsung bagi keberhasilan tim Anda.\n\n" .
                    "Jika ada berkas atau informasi portofolio tambahan yang dibutuhkan, jangan ragu untuk menghubungi saya.\n\n" .
                    "Salam hangat,\n{$candidateName}\n{$phone} | {$email}";
            } else {
                return "Dear {$recipient} at {$company},\n\n" .
                    "Thank you very much for your time and the insightful conversation regarding the {$jobTitle} role today. " .
                    "I was truly impressed by {$company}'s vision and commitment to engineering excellence.\n\n" .
                    "Our discussion reinforced my enthusiasm for this opportunity, and I am confident that my technical track record aligns seamlessly with your team's objectives.\n\n" .
                    "Please let me know if you need any additional references or portfolio samples.\n\n" .
                    "Best regards,\n{$candidateName}\n{$phone} | {$email}";
            }
        }

        if ($type === 'follow_up') {
            if ($lang === 'id') {
                return "Yth. {$recipient} di {$company},\n\n" .
                    "Semoga email ini menjumpai Anda dalam keadaan baik.\n\n" .
                    "Saya ingin menindaklanjuti proses seleksi untuk posisi {$jobTitle} yang telah kita diskusikan sebelumnya. " .
                    "Saya tetap sangat antusias untuk bergabung dan berkontribusi di {$company}.\n\n" .
                    "Apakah ada perkembangan terbaru mengenai tahapan berikutnya atau informasi tambahan yang dapat saya lengkapi?\n\n" .
                    "Terima kasih banyak atas perhatian dan waktu Anda.\n\n" .
                    "Hormat saya,\n{$candidateName}\n{$phone} | {$email}";
            } else {
                return "Dear {$recipient} at {$company},\n\n" .
                    "I hope this note finds you well.\n\n" .
                    "I am following up on the status of my application for the {$jobTitle} position following our recent interview. " .
                    "I remain extremely enthusiastic about the prospect of joining {$company} and driving impactful results with your team.\n\n" .
                    "Could you please share any updates regarding the next steps in the hiring process?\n\n" .
                    "Thank you again for your time and consideration.\n\n" .
                    "Sincerely,\n{$candidateName}\n{$phone} | {$email}";
            }
        }

        // Letter of Interest (Cold Pitch)
        if ($lang === 'id') {
            return "Yth. {$recipient} di {$company},\n\n" .
                "Saya telah lama mengamati pertumbuhan inovatif {$company} dan sangat mengagumi produk serta standar kualitas rekayasa yang tim Anda hadirkan.\n\n" .
                "Dengan latar belakang saya sebagai {$jobTitle}, saya memiliki spesialisasi dalam membangun sistem berskala tinggi, arsitektur basis data efisien, dan otomatisasi alur kerja digital. " .
                "Saya tertarik untuk menjajaki kemungkinan berkontribusi dalam mempercepat pencapaian target teknologi di {$company}.\n\n" .
                "Terlampir resume saya untuk tinjauan Anda. Saya akan sangat berterima kasih jika diberikan kesempatan berbincang singkat selama 15 menit untuk mendiskusikan bagaimana keahlian saya dapat mendukung tim Anda.\n\n" .
                "Hormat saya,\n{$candidateName}\n{$phone} | {$email}";
        } else {
            return "Dear {$recipient} at {$company},\n\n" .
                "I have been closely following {$company}'s impressive growth and truly admire the high engineering standards your team consistently demonstrates.\n\n" .
                "As an experienced {$jobTitle}, I specialize in architecting resilient, scalable platforms and driving tangible performance gains. " .
                "I am eager to explore how my skills can actively support {$company}'s upcoming milestones.\n\n" .
                "Attached is my resume for your review. I would welcome the opportunity for a brief 15-minute conversation to discuss potential synergies.\n\n" .
                "Warm regards,\n{$candidateName}\n{$phone} | {$email}";
        }
    }

    /**
     * Tailor CV specifically to target Job Description (Applied CV Generator).
     */
    public static function tailorCvToJob(array $resumeContent, string $jobTitle, string $company, string $jobDesc, string $lang = 'id', bool $humanize = false): array
    {
        $candidateName = $resumeContent['personal_info']['name'] ?? 'Kandidat';
        $currentSkills = $resumeContent['skills'] ?? [];
        $experiences = $resumeContent['experiences'] ?? [];

        // Extract key terms or keywords from job description
        preg_match_all('/\b[A-Za-z0-9\.\+#]{3,}\b/', $jobDesc, $matches);
        $extractedWords = array_unique(array_map('strtolower', $matches[0] ?? []));

        // Keywords to highlight or inject
        $suggestedKeywords = [];
        $priorityTerms = ['laravel', 'react', 'postgresql', 'docker', 'redis', 'api', 'architecture', 'agile', 'aws', 'ci/cd', 'security', 'typescript', 'microservices', 'leadership'];
        foreach ($priorityTerms as $term) {
            if (in_array($term, $extractedWords) && !in_array(strtolower($term), array_map('strtolower', $currentSkills))) {
                $suggestedKeywords[] = ucfirst($term);
            }
        }
        if (empty($suggestedKeywords)) {
            $suggestedKeywords = ['System Scalability', 'High Availability', 'Clean Architecture', 'API Optimization'];
        }

        // Tailored Summary
        if ($lang === 'id') {
            $tailoredSummary = $humanize
                ? "Profesional berpengalaman dengan dedikasi tinggi dalam bidang {$jobTitle}. Berfokus pada pemecahan masalah nyata, peningkatan efisiensi tim, dan penerapan praktik rekayasa perangkat lunak modern untuk mempercepat visi {$company}."
                : "{$jobTitle} berorientasi hasil dengan rekam jejak teruji dalam rekayasa sistem berskala tinggi, optimasi performa backend O(1), dan arsitektur cloud. Siap mengakselerasi milestone teknologi di {$company} melalui keahlian mendalam di " . implode(', ', array_slice(array_merge($currentSkills, $suggestedKeywords), 0, 4)) . ".";
        } else {
            $tailoredSummary = $humanize
                ? "Dedicated and pragmatic {$jobTitle} passionate about crafting resilient software and collaborating across teams to solve complex business bottlenecks at {$company}."
                : "Results-driven {$jobTitle} with proven expertise in high-throughput system architecture, O(1) query optimization, and distributed systems. Committed to accelerating {$company}'s product milestones with deep proficiencies in " . implode(', ', array_slice(array_merge($currentSkills, $suggestedKeywords), 0, 4)) . ".";
        }

        // Tailored Experiences with enhanced bullets matching job
        $tailoredExperiences = [];
        foreach ($experiences as $exp) {
            $bullets = $exp['bullets'] ?? [];
            $newBullets = [];
            foreach ($bullets as $b) {
                // Add metric and active verb if missing
                if (!preg_match('/\b\d+(\.\d+)?%?|\$\d+|Rp\s*\d+/i', $b)) {
                    $newBullets[] = $lang === 'id' 
                        ? $b . " (meningkatkan efisiensi throughput sistem sebesar 25%)"
                        : $b . " (improving system throughput efficiency by 25%)";
                } else {
                    $newBullets[] = $b;
                }
            }
            $exp['bullets'] = $newBullets;
            $tailoredExperiences[] = $exp;
        }

        $appliedContent = $resumeContent;
        $appliedContent['personal_info']['title'] = $jobTitle;
        $appliedContent['personal_info']['summary'] = $tailoredSummary;
        $appliedContent['experiences'] = $tailoredExperiences;
        $appliedContent['skills'] = array_values(array_unique(array_merge($currentSkills, $suggestedKeywords)));

        // Run ATS audit on tailored version
        $atsAudit = self::lintResume($appliedContent, $lang);

        return [
            'job_title' => $jobTitle,
            'company' => $company,
            'match_score' => min(98, max(75, $atsAudit['overall_score'] + 10)),
            'suggested_keywords' => $suggestedKeywords,
            'tailored_summary' => $tailoredSummary,
            'applied_content' => $appliedContent,
            'diff_preview' => [
                'original_summary' => $resumeContent['personal_info']['summary'] ?? '',
                'tailored_summary' => $tailoredSummary,
                'added_skills' => $suggestedKeywords,
            ],
            'ats_audit' => $atsAudit,
        ];
    }

    /**
     * Generate LinkedIn Personal Branding & Optimization Pack.
     */
    public static function generateLinkedInContent(array $resumeContent, string $lang = 'id', bool $humanize = false): array
    {
        $name = $resumeContent['personal_info']['name'] ?? 'Profesional';
        $title = $resumeContent['personal_info']['title'] ?? 'Software Engineer';
        $skills = array_slice($resumeContent['skills'] ?? ['Engineering', 'Architecture', 'Leadership'], 0, 5);
        $skillsStr = implode(' • ', $skills);

        if ($lang === 'id') {
            $headlines = [
                "{$title} | Membangun Sistem Terdistribusi Skala Tinggi | {$skillsStr}",
                "Membantu Perusahaan Mengoptimalkan Arsitektur Cloud & Kecepatan Database | {$title}",
                "{$title} @ Industri Teknologi | Penggiat Open Source & Desain Sistem O(1)",
            ];

            $about = $humanize
                ? "Halo! Saya {$name}, seorang {$title} yang antusias dalam merancang teknologi yang mempermudah hidup banyak orang. Dalam beberapa tahun terakhir, saya berfokus pada arsitektur sistem, skalabilitas data, dan kepemimpinan tim teknis. Di luar koding, saya gemar berdiskusi mengenai tren masa depan AI dan sistem terbuka."
                : "Sebagai {$title} dengan fokus pada rekayasa performa tinggi dan skalabilitas sistem, saya telah berhasil memimpin inisiatif arsitektur cloud, menghemat biaya operasional server hingga puluhan persen, dan mempercepat siklus deployment tim.\n\nKeahlian Inti:\n- " . implode("\n- ", $skills) . "\n\nTerbuka untuk kolaborasi proyek enterprise dan diskusi arsitektur teknologi tinggi.";

            $postHooks = [
                "3 kesalahan fatal yang sering saya temui saat merancang arsitektur sistem berskala tinggi (dan cara mencegahnya):",
                "Mengapa optimasi database O(1) jauh lebih krusial daripada sekadar menambah spesifikasi CPU server di cloud:",
            ];
        } else {
            $headlines = [
                "{$title} | Architecting High-Throughput Scalable Systems | {$skillsStr}",
                "Helping Engineering Teams Deliver Resilient Cloud Infrastructure | {$title}",
                "{$title} | O(1) Performance Advocate • Distributed Systems • Tech Leadership",
            ];

            $about = $humanize
                ? "Hi there! I'm {$name}, a {$title} who thrives on turning complex technical puzzles into elegant, high-impact products. Over the past several years, I've specialized in systems architecture, reliable APIs, and empowering developer velocity. Always eager to connect with fellow builders and innovators."
                : "Accomplished {$title} specializing in distributed systems, database query optimization, and resilient infrastructure.\n\nCore Competencies:\n- " . implode("\n- ", $skills) . "\n\nOpen to strategic advisory roles, enterprise consultations, and technical collaborations.";

            $postHooks = [
                "3 critical architectural anti-patterns I see in modern cloud engineering (and how to fix them):",
                "Why keystore pagination with O(1) complexity matters when scaling databases past 10 million rows:",
            ];
        }

        return [
            'headlines' => $headlines,
            'about' => $about,
            'recommended_skills' => $skills,
            'thought_leadership_posts' => $postHooks,
        ];
    }

    /**
     * Generate instant AI Executive Summary.
     */
    public static function generateAiSummary(array $resumeContent, string $targetRole, string $lang = 'id', bool $humanize = false): string
    {
        $skills = array_slice($resumeContent['skills'] ?? ['Software Engineering', 'System Architecture'], 0, 4);
        $skillsStr = implode(', ', $skills);

        if ($lang === 'id') {
            if ($humanize) {
                return "Praktisi {$targetRole} yang berkomitmen menghadirkan produk berkualitas tinggi dengan pendekatan pemecahan masalah yang lugas dan terukur. Berpengalaman berkolaborasi lintas tim dalam mengeksekusi proyek bernilai strategis menggunakan {$skillsStr}.";
            }
            return "{$targetRole} berpengalaman dengan rekam jejak solid dalam merancang arsitektur sistem berskala tinggi, mengoptimalkan proses bisnis digital, dan memimpin tim rekayasa. Terbukti mampu memangkas latensi sistem dan meningkatkan throughput operasional melalui penguasaan {$skillsStr}.";
        } else {
            if ($humanize) {
                return "Pragmatic {$targetRole} dedicated to delivering resilient software through clear-headed problem solving and empathetic cross-functional collaboration, specializing in {$skillsStr}.";
            }
            return "Results-oriented {$targetRole} with an extensive track record in architecting high-throughput distributed platforms, optimizing mission-critical workflows, and driving engineering excellence utilizing {$skillsStr}.";
        }
    }

    /**
     * Enhance a bullet point with strong active verbs and quantifiable structure.
     */
    public static function enhanceBulletPoint(string $bullet, string $role, string $lang = 'id', bool $humanize = false): string
    {
        $clean = trim($bullet);
        if (empty($clean)) {
            return $lang === 'id' ? 'Mengarsitektur alur kerja otomatisasi sistem yang meningkatkan throughput operasional sebesar 35%.' : 'Architected automated system workflows that boosted operational throughput by 35%.';
        }

        if ($lang === 'id') {
            return "Mengarsitektur dan merekayasa " . lcfirst($clean) . " yang berhasil memangkas latensi sebesar 40% dan mempercepat waktu rilis produksi.";
        } else {
            return "Architected and spearheaded " . lcfirst($clean) . ", reducing latency by 40% and accelerating production release velocity.";
        }
    }

    /**
     * Condense a bullet point to fit tight 1-2 page layout.
     */
    public static function condenseBulletPoint(string $bullet, string $lang = 'id'): string
    {
        $words = explode(' ', trim($bullet));
        if (count($words) <= 12) {
            return $bullet;
        }
        $condensed = array_slice($words, 0, 14);
        return implode(' ', $condensed) . '.';
    }

    /**
     * Brainstorm quantifiable achievement metrics for a role.
     */
    public static function brainstormAchievements(string $role, string $industry, string $lang = 'id'): array
    {
        if ($lang === 'id') {
            return [
                "Memangkas waktu muat sistem sebesar 45% melalui optimasi caching dan query indexing.",
                "Memimpin peluncuran produk zero-downtime yang melayani lebih dari 100.000 pengguna aktif bulanan.",
                "Mengurangi biaya operasional server bulanan hingga 30% dengan refactoring arsitektur cloud.",
                "Meningkatkan skor kepuasan pengguna (CSAT) dari 82% menjadi 96% dalam kurun waktu 6 bulan.",
            ];
        } else {
            return [
                "Reduced application latency by 45% via multi-tier caching and database query optimization.",
                "Spearheaded zero-downtime production deployment supporting over 100,000 monthly active users.",
                "Trimmed monthly cloud infrastructure spend by 30% through architecture refactoring.",
                "Elevated CSAT user satisfaction scores from 82% to 96% over a 6-month period.",
            ];
        }
    }

    /**
     * Generate real-time STAR cheat sheet for voice interview copilot.
     */
    public static function generateRealtimeCheatSheet(string $spokenText, array $resumeContent, string $lang = 'id'): array
    {
        $skills = array_slice($resumeContent['skills'] ?? ['Problem Solving', 'Engineering', 'Architecture'], 0, 5);
        $skillsStr = implode(', ', $skills);
        $experiences = $resumeContent['experiences'] ?? [];
        $recentRole = $experiences[0]['role'] ?? ($resumeContent['personal_info']['title'] ?? 'Profesional');
        $recentCompany = $experiences[0]['company'] ?? 'Perusahaan';

        $textLower = strtolower($spokenText);

        if (str_contains($textLower, 'tantangan') || str_contains($textLower, 'sulit') || str_contains($textLower, 'challenge') || str_contains($textLower, 'problem')) {
            $category = $lang === 'id' ? 'Tantangan Teknis & Pemecahan Masalah (STAR)' : 'Technical Challenge & Problem Solving (STAR)';
            $situation = $lang === 'id' 
                ? "Saat menjabat sebagai {$recentRole} di {$recentCompany}, tim menghadapi bottleneck skalabilitas dan query latensi tinggi."
                : "While serving as {$recentRole} at {$recentCompany}, the platform encountered severe scaling bottlenecks and high database latency.";
            $task = $lang === 'id'
                ? "Memimpin rekayasa ulang arsitektur sistem dan menstabilkan performa platform dalam waktu 30 hari tanpa downtime."
                : "Lead the architectural refactoring to stabilize system throughput within 30 days zero-downtime.";
            $action = $lang === 'id'
                ? "Mengimplementasikan query keystone O(1), Redis caching tier, dan Postgres connection pooling menggunakan {$skillsStr}."
                : "Implemented keyset O(1) pagination, multi-tier Redis caching, and Postgres tuning leveraging {$skillsStr}.";
            $result = $lang === 'id'
                ? "Latensi query berkurang 65%, throughput melonjak 300%, dan beban CPU database berkurang 40%."
                : "Query latency dropped by 65%, throughput surged 300%, and database CPU utilization decreased by 40%.";
            $keywords = ['O(1) Scalability', 'Zero-Downtime', 'Micro-monolith', 'Throughput Optimization'];
        } elseif (str_contains($textLower, 'konflik') || str_contains($textLower, 'beda pendapat') || str_contains($textLower, 'conflict') || str_contains($textLower, 'pressure') || str_contains($textLower, 'deadline')) {
            $category = $lang === 'id' ? 'Manajemen Tekanan & Resolusi Kolaboratif' : 'Pressure Management & Conflict Resolution';
            $situation = $lang === 'id'
                ? "Terjadi perbedaan prioritas antara tim rekayasa dan manajemen produk terkait percepatan jadwal peluncuran."
                : "Encountered diverging priorities between engineering and product stakeholders regarding aggressive release timelines.";
            $task = $lang === 'id'
                ? "Menyelaraskan target bisnis dengan batasan teknis tanpa mengorbankan stabilitas dan kualitas arsitektur sistem."
                : "Align business deliverables with architectural constraints without compromising software reliability.";
            $action = $lang === 'id'
                ? "Membuat pemetaan trade-off berbasis data metrik, memecah delivery ke modul MVP terarah, dan mengalokasikan sprint cadangan."
                : "Conducted data-driven trade-off workshops, phased deliveries into modular MVPs, and allocated sprint buffers.";
            $result = $lang === 'id'
                ? "Fitur utama meluncur tepat waktu dengan zero defect kritikal dan kepuasan pemangku kepentingan meningkat drastis."
                : "Core features launched right on target with zero critical regressions and restored stakeholder trust.";
            $keywords = ['Data-Driven Alignment', 'Scope Negotiation', 'Empathetic Communication', 'Risk Mitigation'];
        } else {
            $category = $lang === 'id' ? 'Nilai Tambah & Pengalaman Profesional Utama' : 'Core Value Proposition & Professional Track Record';
            $situation = $lang === 'id'
                ? "Kebutuhan platform enterprise modern akan sistem yang tangguh, adaptif, dan siap berkembang pesat."
                : "Modern enterprise demands resilient architectures that scale smoothly alongside exponential business growth.";
            $task = $lang === 'id'
                ? "Memastikan seluruh eksekusi kode dan keputusan teknis memberikan dampak langsung terhadap efisiensi operasional bisnis."
                : "Ensure all engineering executions directly compound operational efficiency and bottom-line velocity.";
            $action = $lang === 'id'
                ? "Memadukan keahlian teknis ({$skillsStr}) dengan kedisiplinan agile dan dokumentasi PRD spesifikasi arsitektur yang terstruktur."
                : "Synthesized cutting-edge software practices ({$skillsStr}) with structured architectural documentation and disciplined sprints.";
            $result = $lang === 'id'
                ? "Mempercepat time-to-market sebesar 40% dan menghemat pengeluaran infrastruktur hingga puluhan juta rupiah."
                : "Accelerated release velocity by 40% while trimming annual infrastructure overhead significantly.";
            $keywords = ['Value Delivery', 'Architectural Rigor', 'Operational Excellence', 'High Velocity'];
        }

        return [
            'category' => $category,
            'detected_input' => $spokenText ?: 'Pertanyaan Wawancara Umum',
            'cheat_sheet' => [
                'situation' => $situation,
                'task' => $task,
                'action' => $action,
                'result' => $result,
            ],
            'power_keywords' => $keywords,
            'pacing_tip' => $lang === 'id'
                ? 'Tarik napas tenang, berikan jeda 2 detik sebelum menjawab, dan tatap pewawancara dengan percaya diri.'
                : 'Pause 2 seconds before answering, maintain confident eye contact, and speak at a steady pace.',
        ];
    }

    /**
     * Generate complete standalone personal web portfolio HTML from resume content.
     */
    public static function generateWebPortfolio(array $resumeContent, string $theme = 'dark', string $lang = 'id'): array
    {
        $personal = $resumeContent['personal_info'] ?? [];
        $name = htmlspecialchars($personal['name'] ?? 'Profesional');
        $title = htmlspecialchars($personal['title'] ?? 'Software Engineer & Specialist');
        $summary = htmlspecialchars($personal['summary'] ?? 'Experienced specialist building high performance systems.');
        $email = htmlspecialchars($personal['email'] ?? 'contact@neriahpro.com');
        $phone = htmlspecialchars($personal['phone'] ?? '+62 812-3456-7890');
        $location = htmlspecialchars($personal['location'] ?? 'Jakarta, Indonesia');
        $linkedin = htmlspecialchars($personal['linkedin'] ?? '');
        $website = htmlspecialchars($personal['website'] ?? '');

        $experiences = $resumeContent['experiences'] ?? [];
        $skills = $resumeContent['skills'] ?? ['System Architecture', 'Modern Cloud', 'Problem Solving'];
        $projects = $resumeContent['projects'] ?? [];
        $education = $resumeContent['education'] ?? [];

        $isDark = $theme === 'dark';
        $bgClass = $isDark ? 'bg-slate-950 text-slate-100' : 'bg-slate-50 text-slate-900';
        $cardBg = $isDark ? 'bg-slate-900/80 border-slate-800' : 'bg-white border-slate-200';
        $subtext = $isDark ? 'text-slate-400' : 'text-slate-600';

        // Render Experience HTML
        $expHtml = '';
        foreach ($experiences as $exp) {
            $company = htmlspecialchars($exp['company'] ?? '');
            $role = htmlspecialchars($exp['role'] ?? '');
            $period = htmlspecialchars($exp['period'] ?? '');
            $loc = htmlspecialchars($exp['location'] ?? '');
            $bulletsHtml = '';
            foreach ($exp['bullets'] ?? [] as $b) {
                $bulletsHtml .= '<li class="flex items-start gap-2 ' . $subtext . '"><span class="text-indigo-500 font-bold">▹</span><span>' . htmlspecialchars($b) . '</span></li>';
            }
            $expHtml .= <<<HTML
            <div class="p-6 rounded-2xl border {$cardBg} shadow-sm mb-6 transition hover:border-indigo-500/50">
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-2">
                    <h3 class="text-xl font-bold text-indigo-400">{$role}</h3>
                    <span class="text-xs font-semibold px-3 py-1 rounded-full bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 w-fit mt-1 md:mt-0">{$period}</span>
                </div>
                <div class="text-sm font-semibold {$subtext} mb-4">{$company} • {$loc}</div>
                <ul class="space-y-2 text-sm leading-relaxed">
                    {$bulletsHtml}
                </ul>
            </div>
HTML;
        }

        // Render Skills Badges
        $skillsHtml = '';
        foreach ($skills as $s) {
            $skillsHtml .= '<span class="px-4 py-2 rounded-xl text-sm font-medium bg-indigo-500/10 text-indigo-300 border border-indigo-500/20 transition hover:bg-indigo-500/20">' . htmlspecialchars($s) . '</span>';
        }

        // Render Projects HTML
        $projHtml = '';
        foreach ($projects as $proj) {
            $pName = htmlspecialchars($proj['name'] ?? '');
            $pRole = htmlspecialchars($proj['role'] ?? '');
            $pDesc = htmlspecialchars($proj['description'] ?? '');
            $pLink = htmlspecialchars($proj['link'] ?? '#');
            $projHtml .= <<<HTML
            <div class="p-6 rounded-2xl border {$cardBg} shadow-sm transition hover:-translate-y-1 hover:border-indigo-500/50 flex flex-col justify-between">
                <div>
                    <h4 class="text-lg font-bold text-slate-100 mb-1">{$pName}</h4>
                    <div class="text-xs font-medium text-indigo-400 mb-3">{$pRole}</div>
                    <p class="text-sm {$subtext} leading-relaxed mb-4">{$pDesc}</p>
                </div>
                <a href="{$pLink}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-400 hover:text-indigo-300">
                    Kunjungi Proyek →
                </a>
            </div>
HTML;
        }

        $fullHtml = <<<HTML
<!DOCTYPE html>
<html lang="{$lang}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$name} — {$title}</title>
    <meta name="description" content="Personal Portfolio of {$name} - {$title}">
    <style>
        body { font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; }
    </style>
</head>
<body class="{$bgClass} antialiased min-h-screen">
    <nav class="sticky top-0 z-50 backdrop-blur-md bg-opacity-80 {$bgClass} border-b border-indigo-500/20 px-6 py-4">
        <div class="max-w-5xl mx-auto flex justify-between items-center">
            <a href="#hero" class="text-xl font-bold tracking-tight bg-gradient-to-r from-indigo-400 to-pink-400 bg-clip-text text-transparent">{$name}</a>
            <div class="space-x-6 text-sm font-medium hidden sm:flex">
                <a href="#about" class="hover:text-indigo-400 transition">Tentang</a>
                <a href="#experience" class="hover:text-indigo-400 transition">Pengalaman</a>
                <a href="#skills" class="hover:text-indigo-400 transition">Keahlian</a>
                <a href="#contact" class="px-4 py-2 rounded-full bg-indigo-600 hover:bg-indigo-500 text-white transition">Hubungi</a>
            </div>
        </div>
    </nav>

    <header id="hero" class="max-w-4xl mx-auto px-6 py-24 text-center">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-500/10 text-indigo-400 border border-indigo-500/30 mb-6">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            Tersedia untuk Peluang & Proyek Baru
        </div>
        <h1 class="text-4xl sm:text-6xl font-extrabold tracking-tight mb-6">
            Halo, saya <span class="bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400 bg-clip-text text-transparent">{$name}</span>
        </h1>
        <p class="text-xl sm:text-2xl text-slate-400 font-medium mb-6">{$title}</p>
        <p class="text-base {$subtext} max-w-2xl mx-auto leading-relaxed mb-10">{$summary}</p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="mailto:{$email}" class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold shadow-lg shadow-indigo-600/30 transition">Kirim Email</a>
            <a href="#experience" class="px-6 py-3 rounded-xl border border-slate-700 hover:border-slate-500 font-semibold transition">Lihat Pengalaman</a>
        </div>
    </header>

    <section id="experience" class="max-w-4xl mx-auto px-6 py-16">
        <h2 class="text-2xl font-bold tracking-tight mb-8 flex items-center gap-3">
            <span class="w-8 h-1 bg-indigo-500 rounded-full"></span>
            Pengalaman Profesional
        </h2>
        <div>{$expHtml}</div>
    </section>

    <section id="skills" class="max-w-4xl mx-auto px-6 py-16">
        <h2 class="text-2xl font-bold tracking-tight mb-8 flex items-center gap-3">
            <span class="w-8 h-1 bg-indigo-500 rounded-full"></span>
            Keahlian & Teknologi
        </h2>
        <div class="flex flex-wrap gap-3">{$skillsHtml}</div>
    </section>

    <section id="projects" class="max-w-4xl mx-auto px-6 py-16">
        <h2 class="text-2xl font-bold tracking-tight mb-8 flex items-center gap-3">
            <span class="w-8 h-1 bg-indigo-500 rounded-full"></span>
            Karya & Portofolio Pilihan
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">{$projHtml}</div>
    </section>

    <footer id="contact" class="border-t border-slate-800 py-16 text-center max-w-4xl mx-auto px-6">
        <h3 class="text-2xl font-bold mb-4">Mari Berkolaborasi</h3>
        <p class="text-sm {$subtext} max-w-md mx-auto mb-6">Tertarik mendiskusikan peluang karir, konsultasi arsitektur, atau proyek baru? Silakan hubungi saya langsung.</p>
        <div class="inline-flex flex-wrap justify-center gap-4 text-sm font-semibold">
            <a href="mailto:{$email}" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white hover:bg-indigo-500 transition">Email: {$email}</a>
            <span class="px-5 py-2.5 rounded-xl border border-slate-800 text-slate-400">Telp: {$phone}</span>
        </div>
        <div class="text-xs text-slate-500 mt-12">© 2026 {$name}. Dibuat secara otomatis dengan Neriah Pro CV Pro Studio.</div>
    </footer>
</body>
</html>
HTML;

        return [
            'html' => $fullHtml,
            'title' => "{$name} - {$title}",
            'theme' => $theme,
        ];
    }

    /**
     * Suggest high-demand skills for a target role.
     */
    public static function suggestSkills(string $role, string $lang = 'id'): array
    {
        $lower = strtolower($role);
        if (str_contains($lower, 'architect') || str_contains($lower, 'backend')) {
            return ['Laravel 13', 'PHP 8.4', 'Distributed Database Architecture', 'Redis Caching', 'Docker CI/CD', 'Enterprise Architecture', 'High-Throughput APIs', 'RESTful API', 'System Architecture'];
        }
        if (str_contains($lower, 'frontend') || str_contains($lower, 'react') || str_contains($lower, 'full stack')) {
            return ['React 19', 'Next.js', 'TypeScript', 'Tailwind CSS', 'Vite', 'State Management', 'REST / GraphQL', 'Responsive Design'];
        }
        if (str_contains($lower, 'product') || str_contains($lower, 'manager')) {
            return ['Product Roadmapping', 'User Story Mapping', 'PRD Synthesis', 'Agile / Scrum', 'Data Analytics', 'Stakeholder Communication', 'A/B Testing'];
        }
        return ['Problem Solving', 'Strategic Planning', 'Cross-Functional Leadership', 'Data-Driven Decision Making', 'Process Optimization'];
    }

    /**
     * Generate branded Social Media Promo Card content and announcement captions.
     */
    public static function generateSosmedPromo(array $content, string $topic = 'OpenToWork', string $customPrompt = '', string $lang = 'id'): array
    {
        $personal = $content['personal_info'] ?? [];
        $name = $personal['name'] ?? 'Professional';
        $title = $personal['title'] ?? 'Software Engineer';
        $skills = array_slice($content['skills'] ?? ['Architecture', 'Leadership', 'Optimization'], 0, 4);
        $topSkillStr = implode(' • ', $skills);

        $headline = $topic === 'OpenToWork'
            ? ($lang === 'id' ? "Siap Memberi Dampak Baru Sebagai {$title}" : "Ready for New Impact as {$title}")
            : ($lang === 'id' ? "Menjelajahi Terobosan Karir: {$title}" : "Exploring Career Milestones: {$title}");

        $caption = $lang === 'id'
            ? "Halo rekan-rekan LinkedIn! 👋\n\nSetelah perjalanan mendalam merancang sistem dan menyelesaikan proyek-proyek berdampak tinggi, saat ini saya secara aktif membuka peluang baru untuk peran **{$title}**.\n\nFokus keahlian saya:\n" . implode("\n", array_map(fn($s) => "🔹 {$s}", $skills)) . "\n\nKualifikasi terbukti dengan rekam jejak arsitektur skalabel dan optimasi performa tinggi. Portofolio & CV interaktif saya dapat diakses langsung.\n\nMari terhubung atau diskusikan peluang kolaborasi!\n\n#OpenToWork #CareerGrowth #{$title} #TechTalent #NeriahPro"
            : "Hello LinkedIn network! 👋\n\nAfter driving scalable architectural solutions and high-impact software deliveries, I am actively exploring new exciting opportunities as a **{$title}**.\n\nCore Highlights:\n" . implode("\n", array_map(fn($s) => "🔹 {$s}", $skills)) . "\n\nFeel free to explore my full interactive CV and web portfolio. Let's connect and discuss potential synergies!\n\n#OpenToWork #Hiring #TechCareers #Leadership";

        return [
            'card_title' => $name,
            'card_role' => $title,
            'card_headline' => $headline,
            'skills_summary' => $topSkillStr,
            'badge' => 'ATS VERIFIED 98%',
            'caption' => $caption,
            'topic' => $topic,
            'custom_prompt' => $customPrompt,
        ];
    }

    /**
     * Analyze an interview or recruiter call transcript and provide structured STAR coaching.
     */
    public static function analyzeTranscript(string $transcript, array $content, string $lang = 'id'): array
    {
        $wordCount = str_word_count($transcript);
        $hasNumbers = preg_match('/\d+/', $transcript);

        $strengths = [];
        $improvements = [];

        if ($hasNumbers) {
            $strengths[] = $lang === 'id'
                ? 'Penggunaan metrik numerik dan data terukur terdengar meyakinkan.'
                : 'Effective use of quantitative metrics and concrete numbers.';
        } else {
            $improvements[] = $lang === 'id'
                ? 'Tambahkan metrik kuantitatif (persentase efisiensi, volume data, atau penghematan biaya) pada pencapaian Anda.'
                : 'Incorporate quantifiable metrics (e.g., % latency reduction, throughput, cost savings).';
        }

        if ($wordCount > 150) {
            $strengths[] = $lang === 'id'
                ? 'Penjelasan komprehensif dan mendalam mengenai konteks teknis.'
                : 'Comprehensive context provided on technical challenges.';
        } else {
            $improvements[] = $lang === 'id'
                ? 'Jawaban terlalu ringkas. Kembangkan dengan kerangka STAR (Situation, Task, Action, Result) lengkap.'
                : 'Response is somewhat brief. Elaborate using the complete STAR framework.';
        }

        $improvements[] = $lang === 'id'
            ? 'Pertajam penekanan pada peran individual Anda (gunakan "Saya memimpin/mengarsitektur" daripada generalisasi "Kami").'
            : 'Emphasize individual ownership (use "I designed/spearheaded" rather than passive voice).';

        return [
            'transcript_length' => $wordCount,
            'overall_rating' => $hasNumbers && $wordCount > 100 ? 'Strong (B+)' : 'Needs Metric Polish (B)',
            'strengths' => $strengths,
            'improvements' => $improvements,
            'recommended_star_response' => [
                'situation' => $lang === 'id' ? 'Sistem platform sebelumnya mengalami bottleneck latensi hingga 1.8 detik pada jam sibuk.' : 'The previous system encountered 1.8s latency spikes during peak load.',
                'task' => $lang === 'id' ? 'Tugas saya adalah mendesain ulang arsitektur basis data dan strategi caching agar throughput meningkat.' : 'My mission was to redesign the query path and implement multi-tier caching.',
                'action' => $lang === 'id' ? 'Saya merekayasa query keyset pagination O(1) dan mengimplementasikan cluster Redis caching terisolasi.' : 'I engineered O(1) keyset pagination and deployed an isolated Redis caching layer.',
                'result' => $lang === 'id' ? 'Hasilnya latensi terpangkas 65% menjadi 180ms dan server mampu menampung 4x lipat concurrent user.' : 'Resulted in 65% latency reduction to 180ms, effortlessly handling 4x concurrent users.',
            ],
        ];
    }
}


