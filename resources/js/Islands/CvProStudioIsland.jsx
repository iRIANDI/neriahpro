import React, { useState, useEffect, useRef } from 'react';
import { 
  FileText, 
  Sparkles, 
  Mic, 
  MicOff, 
  Mail, 
  Download, 
  Eye, 
  CheckCircle, 
  AlertTriangle, 
  Plus, 
  Trash2, 
  Copy, 
  Save, 
  Share2, 
  RotateCcw, 
  Briefcase, 
  GraduationCap, 
  Award, 
  Code, 
  Layout, 
  Type, 
  Palette, 
  Check, 
  ChevronRight,
  TrendingUp,
  Volume2,
  UploadCloud,
  FileUp,
  Scan,
  X,
  DollarSign,
  FileSpreadsheet,
  ArrowUpRight,
  Wand2,
  Sliders,
  FileDown,
  Layers,
  HelpCircle,
  Clock,
  ArrowRight,
  Sparkle
} from 'lucide-react';

export default function CvProStudioIsland({ initialData }) {
  // Navigation Tabs: 'editor' | 'job_hub' | 'finance' | 'ats_audit' | 'mock_interview' | 'outreach'
  const [activeTab, setActiveTab] = useState('editor');
  const [lang, setLang] = useState('id');
  const [humanize, setHumanize] = useState(false);

  // Resume Data State
  const [resumeId, setResumeId] = useState(null);
  const [resumeSlug, setResumeSlug] = useState('');
  const [title, setTitle] = useState('Resume Profesional');
  const [template, setTemplate] = useState('modern_minimalist');
  const [fontFamily, setFontFamily] = useState('Inter');
  const [primaryColor, setPrimaryColor] = useState('#4f46e5');
  const [content, setContent] = useState(initialData || {});

  // UI States
  const [saving, setSaving] = useState(false);
  const [saveSuccessMessage, setSaveSuccessMessage] = useState('');
  const [atsScore, setAtsScore] = useState(78);
  const [atsAudit, setAtsAudit] = useState(null);
  const [auditing, setAuditing] = useState(false);

  // Quick AI Helper Loading States
  const [aiLoading, setAiLoading] = useState({});
  const [brainstormModalOpen, setBrainstormModalOpen] = useState(false);
  const [brainstormSuggestions, setBrainstormSuggestions] = useState([]);

  // Job Hub & Kanban State
  const [jobs, setJobs] = useState([
    {
      id: 'job-1',
      company: 'Neriah Pro Enterprise',
      role: 'Lead Systems Architect',
      salary: 'Rp 35.000.000 / bln',
      status: 'interviewing',
      date: '2026-09-28',
      desc: 'Mencari Lead Systems Architect untuk memimpin arsitektur cloud terdistribusi dengan Laravel 13, React 19, dan PostgreSQL Strict ULID.',
      notes: 'Wawancara user teknis dijadwalkan Jumat jam 14:00 WIB.'
    },
    {
      id: 'job-2',
      company: 'Fintech Nusantara Ltd',
      role: 'Senior Backend Engineer',
      salary: 'Rp 28.000.000 / bln',
      status: 'applied',
      date: '2026-09-25',
      desc: 'Pengembangan payment gateway Midtrans dan ledger keuangan terdesentralisasi dengan O(1) query pagination.',
      notes: 'Menunggu respon dari HRD talent acquisition.'
    },
    {
      id: 'job-3',
      company: 'Unicorn Tech Asia',
      role: 'Principal Cloud Engineer',
      salary: 'Rp 45.000.000 / bln',
      status: 'wishlist',
      date: '2026-09-29',
      desc: 'Memimpin tim platform SRE, infrastruktur Docker/Kubernetes berkapasitas jutaan pengguna.',
      notes: 'Perlu menyesuaikan CV dengan kata kunci Kubernetes & AWS.'
    }
  ]);
  const [showAddJobModal, setShowAddJobModal] = useState(false);
  const [newJob, setNewJob] = useState({
    company: '',
    role: '',
    salary: '',
    status: 'wishlist',
    desc: '',
    notes: ''
  });

  // Tailored Applied CV & Diff Modal
  const [diffModalOpen, setDiffModalOpen] = useState(false);
  const [diffData, setDiffData] = useState(null);
  const [tailoring, setTailoring] = useState(false);

  // Mock Interview State
  const [targetCompany, setTargetCompany] = useState('Neriah Pro Enterprise');
  const [jobTitle, setJobTitle] = useState('Lead Systems Architect');
  const [jobDesc, setJobDesc] = useState('');
  const [interviewQuestions, setInterviewQuestions] = useState([]);
  const [selectedQuestionIndex, setSelectedQuestionIndex] = useState(0);
  const [userAnswer, setUserAnswer] = useState('');
  const [isRecording, setIsRecording] = useState(false);
  const [generatingInterview, setGeneratingInterview] = useState(false);
  const [evaluatingAnswer, setEvaluatingAnswer] = useState(false);
  const [answerEvaluation, setAnswerEvaluation] = useState(null);

  // Outreach & LinkedIn State
  const [outreachSubTab, setOutreachSubTab] = useState('letter'); // 'letter' | 'linkedin'
  const [outreachType, setOutreachType] = useState('thank_you');
  const [outreachRecipient, setOutreachRecipient] = useState('');
  const [generatedLetter, setGeneratedLetter] = useState('');
  const [generatingOutreach, setGeneratingOutreach] = useState(false);
  const [copyNotification, setCopyNotification] = useState(false);
  const [linkedInContent, setLinkedInContent] = useState(null);
  const [generatingLinkedIn, setGeneratingLinkedIn] = useState(false);

  // Finance & Pricing Ledger State
  const [financeTransactions, setFinanceTransactions] = useState([
    {
      id: 'TRX-101',
      date: '2026-09-29',
      client_name: 'Budi Hartono (PT Solusi Cemerlang)',
      package: 'Executive VIP (Career AI + Coaching)',
      price: 300000,
      payment_method: 'Midtrans QRIS',
      status: 'paid'
    },
    {
      id: 'TRX-102',
      date: '2026-09-27',
      client_name: 'Siti Aminah, S.Kom',
      package: 'Full Stack (Siap Kerja + Cover Letter)',
      price: 150000,
      payment_method: 'Bank Transfer (BCA)',
      status: 'paid'
    },
    {
      id: 'TRX-103',
      date: '2026-09-26',
      client_name: 'Rian Pratama',
      package: 'Standard (ATS Optimized)',
      price: 75000,
      payment_method: 'GoPay / E-Wallet',
      status: 'paid'
    },
    {
      id: 'TRX-104',
      date: '2026-09-24',
      client_name: 'Farhan Maulana',
      package: 'Starter / Lite (Template)',
      price: 35000,
      payment_method: 'ShopeePay',
      status: 'pending'
    }
  ]);
  const [showAddTransactionModal, setShowAddTransactionModal] = useState(false);
  const [newTransaction, setNewTransaction] = useState({
    client_name: '',
    package: 'Standard (ATS Optimized)',
    price: 75000,
    payment_method: 'Midtrans QRIS',
    status: 'paid'
  });

  // Microsoft MarkItDown Upload & Scan State
  const [uploadModalOpen, setUploadModalOpen] = useState(false);
  const [uploading, setUploading] = useState(false);
  const [uploadError, setUploadError] = useState('');
  const [uploadSuccess, setUploadSuccess] = useState('');
  const [markitdownResult, setMarkitdownResult] = useState(null);
  const fileInputRef = useRef(null);

  // Speech Recognition Ref
  const recognitionRef = useRef(null);

  // Run initial ATS linting on mount
  useEffect(() => {
    runAtsAudit(content);
  }, []);

  // Sync speech recognition
  useEffect(() => {
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (SpeechRecognition) {
      const recognition = new SpeechRecognition();
      recognition.continuous = true;
      recognition.interimResults = true;
      recognition.lang = lang === 'id' ? 'id-ID' : 'en-US';

      recognition.onresult = (event) => {
        let transcript = '';
        for (let i = event.resultIndex; i < event.results.length; i++) {
          transcript += event.results[i][0].transcript;
        }
        setUserAnswer((prev) => prev ? prev + ' ' + transcript : transcript);
      };

      recognition.onerror = () => {
        setIsRecording(false);
      };

      recognition.onend = () => {
        setIsRecording(false);
      };

      recognitionRef.current = recognition;
    }
  }, [lang]);

  const toggleRecording = () => {
    if (!recognitionRef.current) {
      alert(lang === 'id' ? 'Fitur Speech Recognition tidak didukung di browser ini. Anda dapat mengetik jawaban langsung.' : 'Speech Recognition is not supported on this browser. You can type your answer directly.');
      return;
    }

    if (isRecording) {
      recognitionRef.current.stop();
      setIsRecording(false);
    } else {
      try {
        recognitionRef.current.start();
        setIsRecording(true);
      } catch (err) {
        setIsRecording(false);
      }
    }
  };

  // Helper to update personal info
  const handlePersonalChange = (key, val) => {
    setContent((prev) => ({
      ...prev,
      personal_info: {
        ...(prev.personal_info || {}),
        [key]: val,
      },
    }));
  };

  // Run AI ATS Audit
  const runAtsAudit = async (customContent = null) => {
    setAuditing(true);
    try {
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      const res = await fetch('/api/cv-pro/lint', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf || '',
        },
        body: JSON.stringify({
          content: customContent || content,
          lang,
        }),
      });
      const data = await res.json();
      if (data.success) {
        setAtsScore(data.data.overall_score);
        setAtsAudit(data.data);
      }
    } catch (e) {
      console.error(e);
    } finally {
      setAuditing(false);
    }
  };

  // Save to Cloud
  const handleSaveToCloud = async () => {
    setSaving(true);
    setSaveSuccessMessage('');
    try {
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      const res = await fetch('/api/cv-pro/save', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf || '',
        },
        body: JSON.stringify({
          id: resumeId,
          title,
          target_role: content.personal_info?.title || 'Profesional',
          template,
          font_family: fontFamily,
          primary_color: primaryColor,
          content,
          lang,
        }),
      });
      const data = await res.json();
      if (data.success) {
        setResumeId(data.data.id);
        setResumeSlug(data.data.slug);
        setAtsScore(data.data.ats_score);
        setSaveSuccessMessage(data.message);
        setTimeout(() => setSaveSuccessMessage(''), 4000);
      } else {
        alert('Gagal menyimpan: ' + JSON.stringify(data.errors || data.message));
      }
    } catch (err) {
      alert('Terjadi kesalahan jaringan: ' + err.message);
    } finally {
      setSaving(false);
    }
  };

  // Quick AI Helper API call
  const triggerAiHelper = async (action, extraParams = {}) => {
    const key = `${action}_${extraParams.index ?? ''}`;
    setAiLoading((prev) => ({ ...prev, [key]: true }));

    try {
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      const res = await fetch('/api/cv-pro/ai-helper', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf || '',
        },
        body: JSON.stringify({
          action,
          lang,
          humanize,
          content,
          ...extraParams,
        }),
      });
      const data = await res.json();
      if (data.success) {
        if (action === 'summary') {
          handlePersonalChange('summary', data.data.summary);
        } else if (action === 'enhance_bullet' || action === 'condense_bullet') {
          const { expIndex, bulletIndex } = extraParams;
          const exps = [...(content.experiences || [])];
          if (exps[expIndex] && exps[expIndex].bullets) {
            exps[expIndex].bullets[bulletIndex] = data.data.bullet;
            setContent((prev) => ({ ...prev, experiences: exps }));
          }
        } else if (action === 'brainstorm') {
          setBrainstormSuggestions(data.data.achievements || []);
          setBrainstormModalOpen(true);
        } else if (action === 'skills') {
          const newSkills = data.data.skills || [];
          const current = content.skills || [];
          const merged = Array.from(new Set([...current, ...newSkills]));
          setContent((prev) => ({ ...prev, skills: merged }));
        }
      }
    } catch (e) {
      console.error(e);
    } finally {
      setAiLoading((prev) => ({ ...prev, [key]: false }));
    }
  };

  // Tailor CV to target Job Posting
  const handleTailorCv = async (targetJob) => {
    setTailoring(true);
    try {
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      const res = await fetch('/api/cv-pro/tailor', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf || '',
        },
        body: JSON.stringify({
          job_title: targetJob.role,
          company: targetJob.company,
          job_description: targetJob.desc || '',
          resume_content: content,
          lang,
          humanize,
        }),
      });
      const data = await res.json();
      if (data.success) {
        setDiffData(data.data);
        setDiffModalOpen(true);
      }
    } catch (e) {
      console.error(e);
      alert('Gagal menyesuaikan CV: ' + e.message);
    } finally {
      setTailoring(false);
    }
  };

  // Apply Tailored CV to Master CV State
  const applyTailoredDiff = () => {
    if (!diffData || !diffData.applied_content) return;
    setContent(diffData.applied_content);
    setDiffModalOpen(false);
    setActiveTab('editor');
    runAtsAudit(diffData.applied_content);
    alert(lang === 'id' ? '✓ CV Anda telah disesuaikan dan diperbarui ke Master Editor!' : '✓ Your CV has been successfully updated to Master Editor!');
  };

  // Generate LinkedIn Pack
  const handleGenerateLinkedIn = async () => {
    setGeneratingLinkedIn(true);
    try {
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      const res = await fetch('/api/cv-pro/linkedin', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf || '',
        },
        body: JSON.stringify({
          resume_content: content,
          lang,
          humanize,
        }),
      });
      const data = await res.json();
      if (data.success) {
        setLinkedInContent(data.data);
      }
    } catch (e) {
      console.error(e);
    } finally {
      setGeneratingLinkedIn(false);
    }
  };

  // Generate Mock Interview Questions
  const handleGenerateInterview = async () => {
    setGeneratingInterview(true);
    setAnswerEvaluation(null);
    try {
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      const res = await fetch('/api/cv-pro/interview/generate', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf || '',
        },
        body: JSON.stringify({
          resume_id: resumeId,
          job_title: jobTitle,
          company: targetCompany,
          job_description: jobDesc,
          resume_content: content,
          lang,
        }),
      });
      const data = await res.json();
      if (data.success) {
        setInterviewQuestions(data.data.questions || []);
        setSelectedQuestionIndex(0);
        setUserAnswer('');
      }
    } catch (e) {
      console.error(e);
    } finally {
      setGeneratingInterview(false);
    }
  };

  // Evaluate Interview Answer
  const handleEvaluateAnswer = async () => {
    if (!interviewQuestions[selectedQuestionIndex] || !userAnswer.trim()) {
      alert(lang === 'id' ? 'Silakan isi atau rekam jawaban Anda terlebih dahulu!' : 'Please provide your answer first!');
      return;
    }
    setEvaluatingAnswer(true);
    try {
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      const res = await fetch('/api/cv-pro/interview/evaluate', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf || '',
        },
        body: JSON.stringify({
          question: interviewQuestions[selectedQuestionIndex].question,
          answer: userAnswer,
          lang,
        }),
      });
      const data = await res.json();
      if (data.success) {
        setAnswerEvaluation(data.data);
      }
    } catch (e) {
      console.error(e);
    } finally {
      setEvaluatingAnswer(false);
    }
  };

  // Generate Outreach Letter
  const handleGenerateOutreach = async () => {
    setGeneratingOutreach(true);
    try {
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      const res = await fetch('/api/cv-pro/outreach/generate', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf || '',
        },
        body: JSON.stringify({
          resume_id: resumeId,
          type: outreachType,
          company: targetCompany,
          job_title: jobTitle,
          recipient: outreachRecipient,
          resume_content: content,
          lang,
        }),
      });
      const data = await res.json();
      if (data.success) {
        setGeneratedLetter(data.data.content);
      }
    } catch (e) {
      console.error(e);
    } finally {
      setGeneratingOutreach(false);
    }
  };

  const copyToClipboard = (text) => {
    navigator.clipboard.writeText(text);
    setCopyNotification(true);
    setTimeout(() => setCopyNotification(false), 2000);
  };

  // Export CV as JSON
  const handleExportJson = () => {
    const dataStr = 'data:text/json;charset=utf-8,' + encodeURIComponent(JSON.stringify(content, null, 2));
    const downloadAnchor = document.createElement('a');
    downloadAnchor.setAttribute('href', dataStr);
    downloadAnchor.setAttribute('download', `cv-pro-backup-${Date.now()}.json`);
    document.body.appendChild(downloadAnchor);
    downloadAnchor.click();
    downloadAnchor.remove();
  };

  // Import CV from JSON
  const handleImportJson = (e) => {
    const file = e.target.files?.[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = (event) => {
      try {
        const parsed = JSON.parse(event.target.result);
        setContent(parsed);
        runAtsAudit(parsed);
        alert(lang === 'id' ? 'Data CV berhasil diimpor!' : 'CV data loaded successfully!');
      } catch (err) {
        alert('File JSON tidak valid: ' + err.message);
      }
    };
    reader.readAsText(file);
  };

  // Export Financial Ledger to CSV
  const handleExportFinanceCsv = () => {
    const headers = ['ID Transaksi', 'Tanggal', 'Nama Klien', 'Paket Layanan', 'Nominal IDR', 'Metode Pembayaran', 'Status'];
    const rows = financeTransactions.map(t => [
      t.id,
      t.date,
      `"${t.client_name.replace(/"/g, '""')}"`,
      `"${t.package.replace(/"/g, '""')}"`,
      t.price,
      t.payment_method,
      t.status
    ]);
    const csvContent = 'data:text/csv;charset=utf-8,' + [headers.join(','), ...rows.map(r => r.join(','))].join('\n');
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', `laporan-keuangan-cv-pro-${Date.now()}.csv`);
    document.body.appendChild(link);
    link.click();
    link.remove();
  };

  // Kanban Stage Helpers
  const stages = [
    { key: 'wishlist', label: 'Wishlist / Target', color: 'border-zinc-300 dark:border-zinc-700' },
    { key: 'applied', label: 'Dilamar (Applied)', color: 'border-blue-500' },
    { key: 'interviewing', label: 'Wawancara (Interview)', color: 'border-amber-500' },
    { key: 'offered', label: 'Penawaran (Offer)', color: 'border-emerald-500' },
    { key: 'rejected', label: 'Ditolak (Rejected)', color: 'border-rose-500' }
  ];

  const moveJobStage = (jobId, direction) => {
    const order = ['wishlist', 'applied', 'interviewing', 'offered', 'rejected'];
    setJobs(prev => prev.map(j => {
      if (j.id !== jobId) return j;
      const curIdx = order.indexOf(j.status);
      const nextIdx = direction === 'next' ? Math.min(order.length - 1, curIdx + 1) : Math.max(0, curIdx - 1);
      return { ...j, status: order[nextIdx] };
    }));
  };

  const deleteJob = (jobId) => {
    if (confirm(lang === 'id' ? 'Hapus lamaran ini dari pelacak?' : 'Delete this application?')) {
      setJobs(prev => prev.filter(j => j.id !== jobId));
    }
  };

  // Microsoft MarkItDown Document & Scan Processor
  const handleFileUpload = async (e) => {
    const file = e.target.files?.[0];
    if (!file) return;

    setUploading(true);
    setUploadError('');
    setUploadSuccess('');
    setMarkitdownResult(null);

    const formData = new FormData();
    formData.append('cv_file', file);
    formData.append('lang', lang);

    try {
      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      const res = await fetch('/api/cv-pro/upload-cv', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrf || '',
        },
        body: formData,
      });

      const data = await res.json();
      if (data.success) {
        setMarkitdownResult(data.data);
        setUploadSuccess(data.message || 'File berhasil diproses dengan Microsoft MarkItDown!');
        
        // Auto-populate CV Studio with parsed data
        if (data.data.parsed_content) {
          setContent(data.data.parsed_content);
          if (data.data.parsed_content.personal_info?.full_name) {
            setTitle(`CV - ${data.data.parsed_content.personal_info.full_name}`);
          }
        }
        if (data.data.ats_audit) {
          setAtsScore(data.data.ats_audit.overall_score || 80);
          setAtsAudit(data.data.ats_audit);
        }
      } else {
        setUploadError(data.message || 'Gagal memproses berkas CV.');
      }
    } catch (err) {
      console.error(err);
      setUploadError('Terjadi kesalahan koneksi saat mengunggah berkas.');
    } finally {
      setUploading(false);
      if (fileInputRef.current) {
        fileInputRef.current.value = '';
      }
    }
  };

  // Add work experience item
  const addExperience = () => {
    const newItem = {
      id: Date.now(),
      company: 'Nama Perusahaan',
      role: 'Posisi / Role',
      period: '2023 - Sekarang',
      location: 'Kota / Remote',
      description: 'Deskripsi tanggung jawab singkat',
      bullets: ['Berhasil menyelesaikan proyek X dengan metodologi Y', 'Mengoptimalkan alur kerja hingga meningkat 20%'],
    };
    setContent((prev) => ({
      ...prev,
      experiences: [...(prev.experiences || []), newItem],
    }));
  };

  // Remove experience item
  const removeExperience = (id) => {
    setContent((prev) => ({
      ...prev,
      experiences: (prev.experiences || []).filter((item) => item.id !== id),
    }));
  };

  // Add skill
  const addSkill = (newSkill) => {
    if (!newSkill.trim()) return;
    setContent((prev) => ({
      ...prev,
      skills: [...(prev.skills || []), newSkill.trim()],
    }));
  };

  // Remove skill
  const removeSkill = (index) => {
    setContent((prev) => ({
      ...prev,
      skills: (prev.skills || []).filter((_, i) => i !== index),
    }));
  };

  // Finance KPI calculations
  const totalOmset = financeTransactions.filter(t => t.status === 'paid').reduce((acc, t) => acc + t.price, 0);
  const totalPaidCount = financeTransactions.filter(t => t.status === 'paid').length;
  const aov = totalPaidCount > 0 ? Math.round(totalOmset / totalPaidCount) : 0;

  const p = content.personal_info || {};

  return (
    <div className="min-h-screen bg-zinc-100 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 font-sans pb-16 transition-colors">
      
      {/* 1. TOP STUDIO TOOLBAR */}
      <div className="bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 sticky top-14 z-40 px-4 py-2.5 shadow-sm transition-colors">
        <div className="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-3">
          
          {/* Left: Branding & Tabs */}
          <div className="flex items-center gap-2 sm:gap-4">
            <div className="flex items-center gap-2 font-mono text-xs font-black uppercase">
              <span className="w-5 h-5 bg-indigo-600 text-white flex items-center justify-center font-bold text-[11px]">CV</span>
              <span className="hidden sm:inline">CV PRO // STUDIO</span>
            </div>

            <div className="h-4 w-px bg-zinc-300 dark:bg-zinc-700 hidden sm:block"></div>

            {/* Navigation Tabs */}
            <div className="flex items-center gap-1 bg-zinc-100 dark:bg-zinc-800 p-0.5 border border-zinc-200 dark:border-zinc-700 text-xs font-medium">
              <button
                onClick={() => setActiveTab('editor')}
                className={`px-3 py-1 flex items-center gap-1.5 transition ${activeTab === 'editor' ? 'bg-white dark:bg-zinc-900 text-indigo-600 dark:text-indigo-400 font-bold shadow-sm' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white'}`}
              >
                <Layout className="w-3.5 h-3.5" />
                <span>Editor & Preview</span>
              </button>

              <button
                onClick={() => setActiveTab('job_hub')}
                className={`px-3 py-1 flex items-center gap-1.5 transition ${activeTab === 'job_hub' ? 'bg-white dark:bg-zinc-900 text-indigo-600 dark:text-indigo-400 font-bold shadow-sm' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white'}`}
              >
                <Briefcase className="w-3.5 h-3.5" />
                <span>Job Hub & Pelacak</span>
                <span className="px-1.5 py-0.2 text-[10px] font-mono bg-zinc-200 dark:bg-zinc-700 rounded-full">
                  {jobs.length}
                </span>
              </button>

              <button
                onClick={() => setActiveTab('finance')}
                className={`px-3 py-1 flex items-center gap-1.5 transition ${activeTab === 'finance' ? 'bg-white dark:bg-zinc-900 text-indigo-600 dark:text-indigo-400 font-bold shadow-sm' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white'}`}
              >
                <DollarSign className="w-3.5 h-3.5" />
                <span>Keuangan Pro</span>
              </button>

              <button
                onClick={() => { setActiveTab('ats_audit'); runAtsAudit(); }}
                className={`px-3 py-1 flex items-center gap-1.5 transition ${activeTab === 'ats_audit' ? 'bg-white dark:bg-zinc-900 text-indigo-600 dark:text-indigo-400 font-bold shadow-sm' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white'}`}
              >
                <Sparkles className="w-3.5 h-3.5" />
                <span>AI ATS Audit</span>
                <span className={`px-1.5 py-0.2 text-[10px] font-mono font-bold ${atsScore >= 80 ? 'bg-emerald-500 text-black' : (atsScore >= 60 ? 'bg-amber-500 text-black' : 'bg-rose-500 text-white')}`}>
                  {atsScore}%
                </span>
              </button>

              <button
                onClick={() => setActiveTab('mock_interview')}
                className={`px-3 py-1 flex items-center gap-1.5 transition ${activeTab === 'mock_interview' ? 'bg-white dark:bg-zinc-900 text-indigo-600 dark:text-indigo-400 font-bold shadow-sm' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white'}`}
              >
                <Mic className="w-3.5 h-3.5" />
                <span>Mock Interview</span>
              </button>

              <button
                onClick={() => setActiveTab('outreach')}
                className={`px-3 py-1 flex items-center gap-1.5 transition ${activeTab === 'outreach' ? 'bg-white dark:bg-zinc-900 text-indigo-600 dark:text-indigo-400 font-bold shadow-sm' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white'}`}
              >
                <Mail className="w-3.5 h-3.5" />
                <span>Outreach Suite</span>
              </button>
            </div>
          </div>

          {/* Right: Actions */}
          <div className="flex items-center gap-2">
            {saveSuccessMessage && (
              <span className="text-xs text-emerald-600 dark:text-emerald-400 font-medium animate-pulse flex items-center gap-1">
                <Check className="w-3.5 h-3.5 inline" /> {saveSuccessMessage}
              </span>
            )}

            <button
              onClick={() => setUploadModalOpen(true)}
              className="px-3 py-1.5 bg-zinc-900 hover:bg-zinc-800 text-zinc-100 border border-zinc-700 text-xs font-bold transition flex items-center gap-1.5 shadow-sm"
              title="Unggah berkas CV atau scan fisik via Microsoft MarkItDown"
            >
              <UploadCloud className="w-3.5 h-3.5 text-indigo-400" />
              <span className="hidden sm:inline">Scan / Upload CV</span>
              <span className="sm:hidden">Upload</span>
            </button>

            <button
              onClick={handleSaveToCloud}
              disabled={saving}
              className="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-sm disabled:opacity-50"
            >
              <Save className="w-3.5 h-3.5" />
              <span>{saving ? 'Menyimpan...' : 'Simpan Cloud'}</span>
            </button>

            {resumeSlug && (
              <a
                href={`/cv/${resumeSlug}`}
                target="_blank"
                rel="noreferrer"
                className="px-2.5 py-1.5 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 text-xs font-mono font-bold transition flex items-center gap-1"
                title="Buka Halaman Publik"
              >
                <Eye className="w-3.5 h-3.5" />
                <span className="hidden sm:inline">Preview Link</span>
              </a>
            )}

            <button
              onClick={() => window.print()}
              className="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-sm"
              title="Download PDF via print dialog"
            >
              <Download className="w-3.5 h-3.5" />
              <span>Cetak / PDF</span>
            </button>
          </div>
        </div>
      </div>

      {/* MARKITDOWN UPLOAD & SCAN MODAL */}
      {uploadModalOpen && (
        <div className="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
          <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl flex flex-col">
            <div className="p-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
              <div className="flex items-center gap-2">
                <div className="w-8 h-8 rounded bg-indigo-600/10 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                  <Scan className="w-4 h-4" />
                </div>
                <div>
                  <h3 className="text-sm font-bold text-zinc-900 dark:text-white">
                    Microsoft MarkItDown CV Scan & Upload Engine
                  </h3>
                  <p className="text-[11px] text-zinc-500">
                    Konversi dokumen PDF, DOCX, scan fisik (PNG/JPG), dan teks menjadi format Markdown terstruktur.
                  </p>
                </div>
              </div>
              <button 
                onClick={() => setUploadModalOpen(false)}
                className="text-zinc-400 hover:text-zinc-600 dark:hover:text-white"
              >
                <X className="w-4 h-4" />
              </button>
            </div>

            <div className="p-6 space-y-4">
              <div 
                onClick={() => fileInputRef.current?.click()}
                className="border-2 border-dashed border-zinc-300 dark:border-zinc-700 hover:border-indigo-500 dark:hover:border-indigo-400 rounded-lg p-8 text-center cursor-pointer transition bg-zinc-50 dark:bg-zinc-950/50 flex flex-col items-center justify-center gap-3"
              >
                <div className="w-12 h-12 rounded-full bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shadow-inner">
                  {uploading ? (
                    <div className="w-6 h-6 border-2 border-indigo-600 border-t-transparent rounded-full animate-spin" />
                  ) : (
                    <FileUp className="w-6 h-6" />
                  )}
                </div>

                <div>
                  <p className="text-xs font-bold text-zinc-800 dark:text-zinc-200">
                    {uploading ? 'Sedang Memindai dengan MarkItDown...' : 'Pilih atau Tarik Berkas CV ke Sini'}
                  </p>
                  <p className="text-[11px] text-zinc-500 mt-1">
                    Mendukung PDF, Word (DOCX), Scan Fisik (PNG, JPG, WEBP), TXT, Markdown, CSV (Maks 20MB)
                  </p>
                </div>

                <span className="inline-block px-3 py-1 bg-zinc-200 dark:bg-zinc-800 text-[10px] font-mono text-zinc-600 dark:text-zinc-400 rounded">
                  Pipeline: MarkItDown → Structured AST → Auto-Fill Studio
                </span>
              </div>

              <input 
                type="file" 
                ref={fileInputRef} 
                onChange={handleFileUpload} 
                accept=".pdf,.docx,.doc,.txt,.md,.png,.jpg,.jpeg,.webp,.csv" 
                className="hidden" 
              />

              {uploadError && (
                <div className="p-3 bg-rose-500/10 border border-rose-500/30 text-rose-600 dark:text-rose-400 text-xs flex items-center gap-2">
                  <AlertTriangle className="w-4 h-4 shrink-0" />
                  <span>{uploadError}</span>
                </div>
              )}

              {uploadSuccess && (
                <div className="p-3 bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 text-xs flex items-center gap-2">
                  <CheckCircle className="w-4 h-4 shrink-0" />
                  <span>{uploadSuccess}</span>
                </div>
              )}

              {markitdownResult && (
                <div className="mt-4 border border-zinc-200 dark:border-zinc-800 rounded bg-zinc-50 dark:bg-zinc-950 p-4">
                  <div className="flex items-center justify-between pb-2 mb-2 border-b border-zinc-200 dark:border-zinc-800">
                    <span className="text-xs font-mono font-bold text-indigo-600 dark:text-indigo-400 flex items-center gap-1.5">
                      <Code className="w-3.5 h-3.5" />
                      MarkItDown Engine Output ({markitdownResult.engine})
                    </span>
                    <span className="text-[10px] font-mono px-2 py-0.5 bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 rounded uppercase">
                      {markitdownResult.format}
                    </span>
                  </div>

                  <pre className="text-[11px] font-mono text-zinc-700 dark:text-zinc-300 max-h-48 overflow-y-auto whitespace-pre-wrap select-all bg-white dark:bg-zinc-900 p-2.5 rounded border border-zinc-200 dark:border-zinc-800">
                    {markitdownResult.markdown}
                  </pre>

                  <div className="mt-3 flex items-center justify-between">
                    <p className="text-[11px] text-emerald-600 dark:text-emerald-400">
                      ✓ Formulir CV Studio & Skor ATS telah otomatis diperbarui.
                    </p>
                    <button
                      onClick={() => setUploadModalOpen(false)}
                      className="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded"
                    >
                      Buka di Studio
                    </button>
                  </div>
                </div>
              )}
            </div>
          </div>
        </div>
      )}

      {/* DIFF MODAL (APPLIED CV vs MASTER CV) */}
      {diffModalOpen && diffData && (
        <div className="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
          <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 w-full max-w-3xl max-h-[90vh] overflow-y-auto shadow-2xl flex flex-col">
            <div className="p-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
              <div className="flex items-center gap-2">
                <div className="w-8 h-8 rounded bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
                  <Sparkles className="w-4 h-4" />
                </div>
                <div>
                  <h3 className="text-sm font-bold text-zinc-900 dark:text-white">
                    Pratinjau Penyesuaian CV: {diffData.job_title} @ {diffData.company}
                  </h3>
                  <p className="text-[11px] text-zinc-500">
                    Kesesuaian Skor: <strong className="text-emerald-600">{diffData.match_score}%</strong> • Kata Kunci Ditambahkan: {diffData.suggested_keywords?.join(', ')}
                  </p>
                </div>
              </div>
              <button 
                onClick={() => setDiffModalOpen(false)}
                className="text-zinc-400 hover:text-zinc-600 dark:hover:text-white"
              >
                <X className="w-4 h-4" />
              </button>
            </div>

            <div className="p-6 space-y-4 text-xs">
              <div className="p-3 bg-indigo-50 dark:bg-indigo-950/30 border border-indigo-200 dark:border-indigo-800 text-indigo-900 dark:text-indigo-200">
                <p>
                  <strong>Rekomendasi AI:</strong> Ringkasan dan kata kunci teknis telah dioptimalkan agar lolos filter ATS rekruter perusahaan target dengan bobot relevansi tinggi.
                </p>
              </div>

              {/* Diff Box */}
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div className="border border-zinc-200 dark:border-zinc-800 p-3 bg-zinc-50 dark:bg-zinc-950">
                  <span className="text-[10px] font-mono font-bold uppercase text-zinc-400 block mb-1">Sebelumnya (Master CV)</span>
                  <p className="text-zinc-600 dark:text-zinc-400 leading-relaxed italic">
                    "{diffData.diff_preview?.original_summary || 'Belum ada ringkasan'}"
                  </p>
                </div>
                <div className="border border-emerald-500/40 p-3 bg-emerald-50/50 dark:bg-emerald-950/20">
                  <span className="text-[10px] font-mono font-bold uppercase text-emerald-600 block mb-1">Disesuaikan untuk Lowongan (Applied CV)</span>
                  <p className="text-zinc-800 dark:text-zinc-200 leading-relaxed font-medium">
                    "{diffData.tailored_summary}"
                  </p>
                </div>
              </div>

              {/* Added Skills Tags */}
              <div>
                <span className="text-[11px] font-bold block mb-1.5">Kata Kunci & Keahlian Tambahan:</span>
                <div className="flex flex-wrap gap-1.5">
                  {(diffData.suggested_keywords || []).map((kw, i) => (
                    <span key={i} className="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-900 text-emerald-800 dark:text-emerald-200 text-[11px] font-mono font-bold rounded">
                      + {kw}
                    </span>
                  ))}
                </div>
              </div>

              <div className="pt-4 border-t border-zinc-200 dark:border-zinc-800 flex justify-end gap-2">
                <button
                  onClick={() => setDiffModalOpen(false)}
                  className="px-4 py-2 border border-zinc-300 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-bold hover:bg-zinc-100 dark:hover:bg-zinc-800 transition"
                >
                  Tutup
                </button>
                <button
                  onClick={applyTailoredDiff}
                  className="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition flex items-center gap-1.5 shadow"
                >
                  <Check className="w-3.5 h-3.5" />
                  <span>Terapkan Perubahan ke Master CV</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* BRAINSTORM ACHIEVEMENTS MODAL */}
      {brainstormModalOpen && (
        <div className="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
          <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 w-full max-w-xl max-h-[90vh] overflow-y-auto shadow-2xl flex flex-col">
            <div className="p-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
              <div className="flex items-center gap-2">
                <Sparkles className="w-4 h-4 text-indigo-500" />
                <h3 className="text-sm font-bold text-zinc-900 dark:text-white">
                  Inspirasi Pencapaian & Metrik Terukur (AI Brainstorm)
                </h3>
              </div>
              <button onClick={() => setBrainstormModalOpen(false)} className="text-zinc-400 hover:text-white">
                <X className="w-4 h-4" />
              </button>
            </div>
            <div className="p-5 space-y-3 text-xs">
              <p className="text-zinc-500">
                Pilih atau salin poin pencapaian berorientasi metrik di bawah ini untuk disisipkan ke pengalaman kerja Anda:
              </p>
              <div className="space-y-2">
                {brainstormSuggestions.map((sug, idx) => (
                  <div key={idx} className="p-3 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-start justify-between gap-3">
                    <p className="text-zinc-800 dark:text-zinc-200 leading-relaxed font-mono text-[11px]">{sug}</p>
                    <button
                      onClick={() => { copyToClipboard(sug); }}
                      className="px-2.5 py-1 bg-indigo-600 hover:bg-indigo-500 text-white text-[10px] font-bold shrink-0 transition rounded"
                    >
                      Salin
                    </button>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </div>
      )}

      {/* ADD JOB MODAL */}
      {showAddJobModal && (
        <div className="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
          <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 w-full max-w-md shadow-2xl flex flex-col">
            <div className="p-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
              <h3 className="text-sm font-bold text-zinc-900 dark:text-white">Tambah Pelacak Lamaran Baru</h3>
              <button onClick={() => setShowAddJobModal(false)} className="text-zinc-400 hover:text-white">
                <X className="w-4 h-4" />
              </button>
            </div>
            <div className="p-5 space-y-3 text-xs">
              <div>
                <label className="block text-zinc-600 dark:text-zinc-400 mb-1 font-medium">Nama Perusahaan</label>
                <input
                  type="text"
                  value={newJob.company}
                  onChange={(e) => setNewJob({ ...newJob, company: e.target.value })}
                  placeholder="e.g. Tokopedia / GoTo"
                  className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-1.5 text-xs"
                />
              </div>
              <div>
                <label className="block text-zinc-600 dark:text-zinc-400 mb-1 font-medium">Posisi yang Dilamar</label>
                <input
                  type="text"
                  value={newJob.role}
                  onChange={(e) => setNewJob({ ...newJob, role: e.target.value })}
                  placeholder="e.g. Lead Systems Architect"
                  className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-1.5 text-xs"
                />
              </div>
              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-zinc-600 dark:text-zinc-400 mb-1 font-medium">Ekspektasi Gaji</label>
                  <input
                    type="text"
                    value={newJob.salary}
                    onChange={(e) => setNewJob({ ...newJob, salary: e.target.value })}
                    placeholder="e.g. Rp 30.000.000"
                    className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-1.5 text-xs"
                  />
                </div>
                <div>
                  <label className="block text-zinc-600 dark:text-zinc-400 mb-1 font-medium">Status Awal</label>
                  <select
                    value={newJob.status}
                    onChange={(e) => setNewJob({ ...newJob, status: e.target.value })}
                    className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-2 py-1.5 text-xs"
                  >
                    <option value="wishlist">Wishlist / Target</option>
                    <option value="applied">Dilamar (Applied)</option>
                    <option value="interviewing">Wawancara (Interview)</option>
                    <option value="offered">Penawaran (Offer)</option>
                  </select>
                </div>
              </div>
              <div>
                <label className="block text-zinc-600 dark:text-zinc-400 mb-1 font-medium">Deskripsi Pekerjaan / Kriteria (Opsional)</label>
                <textarea
                  rows={3}
                  value={newJob.desc}
                  onChange={(e) => setNewJob({ ...newJob, desc: e.target.value })}
                  placeholder="Tempel syarat atau kriteria pekerjaan di sini..."
                  className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 p-2 text-xs"
                />
              </div>
              <div className="pt-2 flex justify-end gap-2">
                <button
                  onClick={() => setShowAddJobModal(false)}
                  className="px-3 py-1.5 border border-zinc-300 text-xs font-bold"
                >
                  Batal
                </button>
                <button
                  onClick={() => {
                    if (!newJob.company || !newJob.role) {
                      alert('Isi nama perusahaan dan posisi!');
                      return;
                    }
                    const created = {
                      id: `job-${Date.now()}`,
                      ...newJob,
                      date: new Date().toISOString().split('T')[0]
                    };
                    setJobs(prev => [created, ...prev]);
                    setShowAddJobModal(false);
                    setNewJob({ company: '', role: '', salary: '', status: 'wishlist', desc: '', notes: '' });
                  }}
                  className="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold"
                >
                  Simpan Lamaran
                </button>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* ADD TRANSACTION MODAL */}
      {showAddTransactionModal && (
        <div className="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
          <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 w-full max-w-md shadow-2xl flex flex-col">
            <div className="p-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
              <h3 className="text-sm font-bold text-zinc-900 dark:text-white">Tambah Catatan Transaksi Layanan</h3>
              <button onClick={() => setShowAddTransactionModal(false)} className="text-zinc-400 hover:text-white">
                <X className="w-4 h-4" />
              </button>
            </div>
            <div className="p-5 space-y-3 text-xs">
              <div>
                <label className="block text-zinc-600 dark:text-zinc-400 mb-1 font-medium">Nama Klien</label>
                <input
                  type="text"
                  value={newTransaction.client_name}
                  onChange={(e) => setNewTransaction({ ...newTransaction, client_name: e.target.value })}
                  placeholder="e.g. Ahmad Fauzi, S.T."
                  className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-1.5 text-xs"
                />
              </div>
              <div>
                <label className="block text-zinc-600 dark:text-zinc-400 mb-1 font-medium">Paket Layanan</label>
                <select
                  value={newTransaction.package}
                  onChange={(e) => {
                    const pkg = e.target.value;
                    let price = 75000;
                    if (pkg.includes('Lite')) price = 35000;
                    if (pkg.includes('Full Stack')) price = 150000;
                    if (pkg.includes('VIP')) price = 300000;
                    setNewTransaction({ ...newTransaction, package: pkg, price });
                  }}
                  className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-2 py-1.5 text-xs"
                >
                  <option value="Starter / Lite (Template)">1. Starter / Lite (Template) - Rp 35.000</option>
                  <option value="Standard (ATS Optimized)">2. Standard (ATS Optimized) - Rp 75.000</option>
                  <option value="Full Stack (Siap Kerja + Cover Letter)">3. Full Stack (Siap Kerja) - Rp 150.000</option>
                  <option value="Executive VIP (Career AI + Coaching)">4. Executive VIP (Career AI) - Rp 300.000</option>
                </select>
              </div>
              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-zinc-600 dark:text-zinc-400 mb-1 font-medium">Nominal (IDR)</label>
                  <input
                    type="number"
                    value={newTransaction.price}
                    onChange={(e) => setNewTransaction({ ...newTransaction, price: Number(e.target.value) })}
                    className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-1.5 text-xs"
                  />
                </div>
                <div>
                  <label className="block text-zinc-600 dark:text-zinc-400 mb-1 font-medium">Status</label>
                  <select
                    value={newTransaction.status}
                    onChange={(e) => setNewTransaction({ ...newTransaction, status: e.target.value })}
                    className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-2 py-1.5 text-xs"
                  >
                    <option value="paid">Lunas (Paid)</option>
                    <option value="pending">Menunggu (Pending)</option>
                  </select>
                </div>
              </div>
              <div className="pt-2 flex justify-end gap-2">
                <button
                  onClick={() => setShowAddTransactionModal(false)}
                  className="px-3 py-1.5 border border-zinc-300 text-xs font-bold"
                >
                  Batal
                </button>
                <button
                  onClick={() => {
                    if (!newTransaction.client_name) {
                      alert('Isi nama klien!');
                      return;
                    }
                    const created = {
                      id: `TRX-${Date.now().toString().slice(-4)}`,
                      date: new Date().toISOString().split('T')[0],
                      ...newTransaction
                    };
                    setFinanceTransactions(prev => [created, ...prev]);
                    setShowAddTransactionModal(false);
                    setNewTransaction({ client_name: '', package: 'Standard (ATS Optimized)', price: 75000, payment_method: 'Midtrans QRIS', status: 'paid' });
                  }}
                  className="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold"
                >
                  Simpan Transaksi
                </button>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* 2. MAIN CONTAINER WITH TAB PANELS */}
      <div className="max-w-7xl mx-auto px-4 mt-6">

        {/* ========================================================================= */}
        {/* TAB 1: EDITOR & LIVE PREVIEW CANVAS                                       */}
        {/* ========================================================================= */}
        {activeTab === 'editor' && (
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {/* Left Column: Form Editor (5 cols) */}
            <div className="lg:col-span-5 space-y-6">
              
              {/* Template & Styling Control Card */}
              <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-4 shadow-sm">
                <h3 className="text-xs font-mono font-bold uppercase text-zinc-500 dark:text-zinc-400 mb-3 flex items-center justify-between">
                  <span className="flex items-center gap-2">
                    <Palette className="w-3.5 h-3.5 text-indigo-500" />
                    <span>Gaya & Format Visual</span>
                  </span>
                  <div className="flex items-center gap-1.5">
                    <button
                      onClick={handleExportJson}
                      className="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-[10px] font-mono hover:bg-zinc-200 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 rounded"
                      title="Simpan backup CV format JSON"
                    >
                      Export JSON
                    </button>
                    <label
                      className="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-[10px] font-mono hover:bg-zinc-200 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 rounded cursor-pointer"
                      title="Pulihkan backup CV format JSON"
                    >
                      Import JSON
                      <input type="file" accept=".json" onChange={handleImportJson} className="hidden" />
                    </label>
                  </div>
                </h3>
                <div className="grid grid-cols-3 gap-3 text-xs">
                  <div>
                    <label className="block text-zinc-600 dark:text-zinc-400 mb-1 font-medium">Template</label>
                    <select
                      value={template}
                      onChange={(e) => setTemplate(e.target.value)}
                      className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-2 py-1.5 text-xs text-zinc-900 dark:text-zinc-100"
                    >
                      <option value="modern_minimalist">Modern Minimalist</option>
                      <option value="executive_clean">Executive Clean</option>
                      <option value="creative_ats">Creative ATS</option>
                      <option value="tech_dark">Tech Dark</option>
                    </select>
                  </div>
                  <div>
                    <label className="block text-zinc-600 dark:text-zinc-400 mb-1 font-medium">Font Family</label>
                    <select
                      value={fontFamily}
                      onChange={(e) => setFontFamily(e.target.value)}
                      className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-2 py-1.5 text-xs text-zinc-900 dark:text-zinc-100"
                    >
                      <option value="Inter">Inter (Sans)</option>
                      <option value="Roboto">Roboto (Clean)</option>
                      <option value="Lato">Lato (Warm)</option>
                      <option value="Merriweather">Merriweather (Serif)</option>
                    </select>
                  </div>
                  <div>
                    <label className="block text-zinc-600 dark:text-zinc-400 mb-1 font-medium">Aksen Warna</label>
                    <div className="flex items-center gap-1.5 pt-1">
                      {['#4f46e5', '#059669', '#e11d48', '#d97706', '#475569', '#7c3aed'].map((c) => (
                        <button
                          key={c}
                          type="button"
                          onClick={() => setPrimaryColor(c)}
                          className={`w-5 h-5 rounded-full border-2 transition ${primaryColor === c ? 'scale-125 border-zinc-950 dark:border-white shadow-sm' : 'border-transparent'}`}
                          style={{ backgroundColor: c }}
                        />
                      ))}
                    </div>
                  </div>
                </div>
              </div>

              {/* Personal Info Section */}
              <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-5 shadow-sm space-y-3.5">
                <h3 className="text-xs font-mono font-bold uppercase text-zinc-900 dark:text-zinc-100 border-b border-zinc-200 dark:border-zinc-800 pb-2 flex items-center justify-between">
                  <span>1. Identitas & Kontak</span>
                  <span className="text-[10px] text-indigo-500 font-normal">Wajib Lengkap</span>
                </h3>

                <div className="grid grid-cols-2 gap-3 text-xs">
                  <div className="col-span-2">
                    <label className="block text-zinc-600 dark:text-zinc-400 mb-1">Nama Lengkap & Gelar</label>
                    <input
                      type="text"
                      value={p.name || ''}
                      onChange={(e) => handlePersonalChange('name', e.target.value)}
                      placeholder="e.g. Alex Pratama, S.Kom"
                      className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-1.5 text-xs text-zinc-900 dark:text-zinc-100"
                    />
                  </div>

                  <div>
                    <label className="block text-zinc-600 dark:text-zinc-400 mb-1">Role / Posisi Profesi</label>
                    <input
                      type="text"
                      value={p.title || ''}
                      onChange={(e) => handlePersonalChange('title', e.target.value)}
                      placeholder="e.g. Senior Full Stack Engineer"
                      className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-1.5 text-xs text-zinc-900 dark:text-zinc-100"
                    />
                  </div>

                  <div>
                    <label className="block text-zinc-600 dark:text-zinc-400 mb-1">Email Kontak</label>
                    <input
                      type="email"
                      value={p.email || ''}
                      onChange={(e) => handlePersonalChange('email', e.target.value)}
                      placeholder="alex@example.com"
                      className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-1.5 text-xs text-zinc-900 dark:text-zinc-100"
                    />
                  </div>

                  <div>
                    <label className="block text-zinc-600 dark:text-zinc-400 mb-1">Nomor WhatsApp</label>
                    <input
                      type="text"
                      value={p.phone || ''}
                      onChange={(e) => handlePersonalChange('phone', e.target.value)}
                      placeholder="+62 812-3456-7890"
                      className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-1.5 text-xs text-zinc-900 dark:text-zinc-100"
                    />
                  </div>

                  <div>
                    <label className="block text-zinc-600 dark:text-zinc-400 mb-1">Lokasi Domisili</label>
                    <input
                      type="text"
                      value={p.location || ''}
                      onChange={(e) => handlePersonalChange('location', e.target.value)}
                      placeholder="Jakarta, Indonesia"
                      className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-1.5 text-xs text-zinc-900 dark:text-zinc-100"
                    />
                  </div>

                  <div>
                    <label className="block text-zinc-600 dark:text-zinc-400 mb-1">LinkedIn URL</label>
                    <input
                      type="text"
                      value={p.linkedin || ''}
                      onChange={(e) => handlePersonalChange('linkedin', e.target.value)}
                      placeholder="linkedin.com/in/alex"
                      className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-1.5 text-xs text-zinc-900 dark:text-zinc-100"
                    />
                  </div>

                  <div>
                    <label className="block text-zinc-600 dark:text-zinc-400 mb-1">Website / Portfolio</label>
                    <input
                      type="text"
                      value={p.website || ''}
                      onChange={(e) => handlePersonalChange('website', e.target.value)}
                      placeholder="https://neriahpro.com"
                      className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-1.5 text-xs text-zinc-900 dark:text-zinc-100"
                    />
                  </div>

                  <div className="col-span-2">
                    <div className="flex items-center justify-between mb-1">
                      <label className="text-zinc-600 dark:text-zinc-400 font-medium">Ringkasan Profil (Executive Summary)</label>
                      <button
                        type="button"
                        onClick={() => triggerAiHelper('summary', { role: p.title || 'Profesional' })}
                        disabled={aiLoading['summary_']}
                        className="px-2 py-0.5 bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 text-[10.5px] font-bold flex items-center gap-1 border border-indigo-200 dark:border-indigo-800 rounded transition"
                      >
                        <Wand2 className={`w-3 h-3 ${aiLoading['summary_'] ? 'animate-spin' : ''}`} />
                        <span>{aiLoading['summary_'] ? 'AI Berpikir...' : 'AI Generate Summary'}</span>
                      </button>
                    </div>
                    <textarea
                      rows={3}
                      value={p.summary || ''}
                      onChange={(e) => handlePersonalChange('summary', e.target.value)}
                      placeholder="Tuliskan 2-3 kalimat ringkasan profesional Anda..."
                      className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 p-2 text-xs text-zinc-900 dark:text-zinc-100"
                    />
                  </div>
                </div>
              </div>

              {/* Work Experience Section */}
              <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-5 shadow-sm space-y-4">
                <div className="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-2">
                  <h3 className="text-xs font-mono font-bold uppercase text-zinc-900 dark:text-zinc-100">
                    2. Pengalaman Kerja
                  </h3>
                  <div className="flex items-center gap-2">
                    <button
                      type="button"
                      onClick={() => triggerAiHelper('brainstorm', { role: p.title || 'Software Engineer' })}
                      className="text-xs text-amber-600 dark:text-amber-400 hover:underline flex items-center gap-1 font-bold"
                      title="Brainstorm metrik angka & pencapaian terukur"
                    >
                      <Sparkles className="w-3 h-3" />
                      <span>Brainstorm Metrik</span>
                    </button>
                    <button
                      onClick={addExperience}
                      className="text-xs text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1 font-bold"
                    >
                      <Plus className="w-3.5 h-3.5" />
                      <span>Tambah Karir</span>
                    </button>
                  </div>
                </div>

                <div className="space-y-4">
                  {(content.experiences || []).map((exp, eIdx) => (
                    <div key={exp.id || eIdx} className="p-3 bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200 dark:border-zinc-700/60 rounded space-y-2 text-xs">
                      <div className="flex justify-between items-start">
                        <div className="grid grid-cols-2 gap-2 flex-1 mr-2">
                          <input
                            type="text"
                            value={exp.role || ''}
                            onChange={(e) => {
                              const arr = [...content.experiences];
                              arr[eIdx].role = e.target.value;
                              setContent({ ...content, experiences: arr });
                            }}
                            placeholder="Posisi (e.g. Lead Engineer)"
                            className="bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-2 py-1 font-bold"
                          />
                          <input
                            type="text"
                            value={exp.company || ''}
                            onChange={(e) => {
                              const arr = [...content.experiences];
                              arr[eIdx].company = e.target.value;
                              setContent({ ...content, experiences: arr });
                            }}
                            placeholder="Perusahaan"
                            className="bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-2 py-1"
                          />
                        </div>
                        <button
                          onClick={() => removeExperience(exp.id)}
                          className="text-zinc-400 hover:text-rose-500 p-1"
                          title="Hapus"
                        >
                          <Trash2 className="w-3.5 h-3.5" />
                        </button>
                      </div>

                      <div className="grid grid-cols-2 gap-2">
                        <input
                          type="text"
                          value={exp.period || ''}
                          onChange={(e) => {
                            const arr = [...content.experiences];
                            arr[eIdx].period = e.target.value;
                            setContent({ ...content, experiences: arr });
                          }}
                          placeholder="Periode (e.g. 2022 - Sekarang)"
                          className="bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-2 py-1 text-[11px]"
                        />
                        <input
                          type="text"
                          value={exp.location || ''}
                          onChange={(e) => {
                            const arr = [...content.experiences];
                            arr[eIdx].location = e.target.value;
                            setContent({ ...content, experiences: arr });
                          }}
                          placeholder="Lokasi (e.g. Jakarta / Remote)"
                          className="bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-2 py-1 text-[11px]"
                        />
                      </div>

                      {/* Bullets with AI Enhance / Condense */}
                      <div className="space-y-1.5 pt-1">
                        <label className="text-[11px] font-bold text-zinc-500 block">Poin Pencapaian & Metrik:</label>
                        {(exp.bullets || []).map((bullet, bIdx) => (
                          <div key={bIdx} className="flex items-center gap-1.5">
                            <span className="text-zinc-400">•</span>
                            <input
                              type="text"
                              value={bullet}
                              onChange={(e) => {
                                const arr = [...content.experiences];
                                arr[eIdx].bullets[bIdx] = e.target.value;
                                setContent({ ...content, experiences: arr });
                              }}
                              className="flex-1 bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-2 py-1 text-[11px]"
                            />
                            {/* AI Enhance Button */}
                            <button
                              type="button"
                              onClick={() => triggerAiHelper('enhance_bullet', { bullet, role: exp.role || 'Engineer', expIndex: eIdx, bulletIndex: bIdx })}
                              disabled={aiLoading[`enhance_bullet_${eIdx}`]}
                              className="px-1.5 py-1 bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 text-[10px] font-mono rounded border border-emerald-300 dark:border-emerald-800"
                              title="Perkuat dengan kata kerja aksi & angka"
                            >
                              ⚡ Poles
                            </button>
                            {/* AI Condense Button */}
                            <button
                              type="button"
                              onClick={() => triggerAiHelper('condense_bullet', { bullet, expIndex: eIdx, bulletIndex: bIdx })}
                              className="px-1.5 py-1 bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-300 text-[10px] font-mono rounded"
                              title="Ringkas agar muat 1 halaman"
                            >
                              ✂ Ringkas
                            </button>
                            <button
                              onClick={() => {
                                const arr = [...content.experiences];
                                arr[eIdx].bullets = arr[eIdx].bullets.filter((_, i) => i !== bIdx);
                                setContent({ ...content, experiences: arr });
                              }}
                              className="text-zinc-400 hover:text-rose-500"
                            >
                              <X className="w-3.5 h-3.5" />
                            </button>
                          </div>
                        ))}
                        <button
                          onClick={() => {
                            const arr = [...content.experiences];
                            arr[eIdx].bullets = [...(arr[eIdx].bullets || []), 'Pencapaian baru'];
                            setContent({ ...content, experiences: arr });
                          }}
                          className="text-[10px] text-indigo-500 font-bold hover:underline flex items-center gap-1 pt-1"
                        >
                          <Plus className="w-3 h-3" /> Tambah Poin
                        </button>
                      </div>
                    </div>
                  ))}
                </div>
              </div>

              {/* Skills Section */}
              <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-5 shadow-sm space-y-3">
                <div className="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-2">
                  <h3 className="text-xs font-mono font-bold uppercase text-zinc-900 dark:text-zinc-100">
                    3. Keahlian Teknis (ATS Skills)
                  </h3>
                  <button
                    type="button"
                    onClick={() => triggerAiHelper('skills', { role: p.title || 'Engineer' })}
                    disabled={aiLoading['skills_']}
                    className="text-xs text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1 font-bold"
                  >
                    <Sparkles className="w-3 h-3" />
                    <span>AI Rekomendasi Skills</span>
                  </button>
                </div>

                <div className="flex flex-wrap gap-1.5">
                  {(content.skills || []).map((skill, sIdx) => (
                    <span
                      key={sIdx}
                      className="px-2.5 py-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 text-xs font-mono flex items-center gap-1.5 border border-zinc-200 dark:border-zinc-700 rounded"
                    >
                      <span>{skill}</span>
                      <button
                        onClick={() => removeSkill(sIdx)}
                        className="text-zinc-400 hover:text-rose-500"
                      >
                        <X className="w-3 h-3" />
                      </button>
                    </span>
                  ))}
                </div>

                <div className="flex gap-2 pt-2">
                  <input
                    type="text"
                    id="new-skill-input"
                    placeholder="Tambah keahlian manual (e.g. Docker, Redis)..."
                    className="flex-1 bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-1.5 text-xs text-zinc-900 dark:text-zinc-100"
                    onKeyDown={(e) => {
                      if (e.key === 'Enter') {
                        e.preventDefault();
                        addSkill(e.target.value);
                        e.target.value = '';
                      }
                    }}
                  />
                  <button
                    onClick={() => {
                      const input = document.getElementById('new-skill-input');
                      if (input && input.value) {
                        addSkill(input.value);
                        input.value = '';
                      }
                    }}
                    className="px-3 py-1.5 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 text-xs font-bold transition border border-zinc-300 dark:border-zinc-700"
                  >
                    Tambah
                  </button>
                </div>
              </div>

            </div>

            {/* Right Column: Live Interactive Preview (7 cols) */}
            <div className="lg:col-span-7 sticky top-28">
              <div className="bg-zinc-200 dark:bg-zinc-900 p-2 sm:p-6 border border-zinc-300 dark:border-zinc-800 shadow-inner flex flex-col items-center">
                <div className="w-full flex items-center justify-between pb-3 text-xs text-zinc-500 font-mono">
                  <span>LIVE PREVIEW // A4 REALTIME CANVAS</span>
                  <span>FONT: {fontFamily} • ATS SCORE: {atsScore}%</span>
                </div>

                {/* The Paper Canvas */}
                <div
                  className="w-full max-w-[210mm] bg-white text-zinc-900 shadow-2xl p-8 sm:p-12 min-h-[700px] border border-zinc-200 transition-all text-xs"
                  style={{ fontFamily: `'${fontFamily}', sans-serif` }}
                >
                  {/* Header Preview */}
                  <div className="border-b pb-5 mb-5" style={{ borderColor: `${primaryColor}25` }}>
                    <div className="flex justify-between items-start">
                      <div>
                        <h1 className="text-2xl font-black tracking-tight text-zinc-950">{p.name || 'Nama Anda'}</h1>
                        <p className="text-sm font-bold mt-0.5" style={{ color: primaryColor }}>{p.title || 'Posisi Impian'}</p>
                      </div>
                    </div>

                    <div className="flex flex-wrap gap-x-3 gap-y-1 text-[11px] text-zinc-600 mt-3 font-mono">
                      {p.email && <span>✉ {p.email}</span>}
                      {p.phone && <span>📞 {p.phone}</span>}
                      {p.location && <span>📍 {p.location}</span>}
                      {p.linkedin && <span>💼 {p.linkedin}</span>}
                    </div>
                  </div>

                  {/* Summary */}
                  {p.summary && (
                    <div className="mb-5">
                      <h4 className="text-[10px] font-bold uppercase font-mono tracking-wider mb-1.5" style={{ color: primaryColor }}>
                        // Ringkasan Profesional
                      </h4>
                      <p className="text-zinc-700 leading-relaxed text-justify text-[11.5px]">{p.summary}</p>
                    </div>
                  )}

                  {/* Experience */}
                  {(content.experiences || []).length > 0 && (
                    <div className="mb-5">
                      <h4 className="text-[10px] font-bold uppercase font-mono tracking-wider mb-2.5 border-b pb-0.5" style={{ color: primaryColor, borderColor: `${primaryColor}20` }}>
                        // Pengalaman Kerja
                      </h4>
                      <div className="space-y-3.5">
                        {content.experiences.map((exp, eIdx) => (
                          <div key={eIdx}>
                            <div className="flex justify-between font-bold text-zinc-900">
                              <span>{exp.role}</span>
                              <span className="font-mono text-zinc-500 font-normal">{exp.period}</span>
                            </div>
                            <div className="flex justify-between text-zinc-600 text-[11px] mb-1">
                              <span className="font-semibold">{exp.company}</span>
                              <span className="italic">{exp.location}</span>
                            </div>
                            {exp.description && <p className="text-zinc-700 mb-1">{exp.description}</p>}
                            {(exp.bullets || []).filter(Boolean).length > 0 && (
                              <ul className="list-disc list-outside ml-4 space-y-0.5 text-zinc-700">
                                {exp.bullets.filter(Boolean).map((b, bIdx) => (
                                  <li key={bIdx}>{b}</li>
                                ))}
                              </ul>
                            )}
                          </div>
                        ))}
                      </div>
                    </div>
                  )}

                  {/* Education */}
                  {(content.education || []).length > 0 && (
                    <div className="mb-5">
                      <h4 className="text-[10px] font-bold uppercase font-mono tracking-wider mb-2 border-b pb-0.5" style={{ color: primaryColor, borderColor: `${primaryColor}20` }}>
                        // Pendidikan Formal
                      </h4>
                      <div className="space-y-2">
                        {content.education.map((edu, edIdx) => (
                          <div key={edIdx} className="flex justify-between items-start">
                            <div>
                              <span className="font-bold text-zinc-900">{edu.institution}</span>
                              <p className="text-zinc-600 text-[11px]">{edu.degree} - {edu.field}</p>
                            </div>
                            <span className="font-mono text-zinc-500 text-[11px]">{edu.year}</span>
                          </div>
                        ))}
                      </div>
                    </div>
                  )}

                  {/* Skills */}
                  {(content.skills || []).length > 0 && (
                    <div>
                      <h4 className="text-[10px] font-bold uppercase font-mono tracking-wider mb-2 border-b pb-0.5" style={{ color: primaryColor, borderColor: `${primaryColor}20` }}>
                        // Keahlian Teknis
                      </h4>
                      <div className="flex flex-wrap gap-1">
                        {content.skills.map((skill, sIdx) => (
                          <span key={sIdx} className="px-1.5 py-0.5 bg-zinc-100 text-zinc-800 text-[10.5px] font-medium border border-zinc-200">
                            {skill}
                          </span>
                        ))}
                      </div>
                    </div>
                  )}
                </div>
              </div>
            </div>

          </div>
        )}

        {/* ========================================================================= */}
        {/* TAB 2: JOB HUB & KANBAN APPLICATION TRACKER                               */}
        {/* ========================================================================= */}
        {activeTab === 'job_hub' && (
          <div className="space-y-6">
            
            {/* Header Toolbar */}
            <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-4 shadow-sm flex flex-wrap items-center justify-between gap-4">
              <div>
                <h2 className="text-base font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                  <Briefcase className="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                  <span>Job Hub // Pelacak Lamaran & Penyesuaian CV Presisi</span>
                </h2>
                <p className="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                  Pantau alur rekrutmen lamaran kerja Anda dan sesuaikan CV secara otomatis untuk setiap lowongan dengan sekali klik.
                </p>
              </div>

              <div className="flex items-center gap-3">
                <button
                  onClick={() => setShowAddJobModal(true)}
                  className="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition flex items-center gap-1.5 shadow"
                >
                  <Plus className="w-4 h-4" />
                  <span>Tambah Lamaran Kerja</span>
                </button>
              </div>
            </div>

            {/* Kanban Columns */}
            <div className="grid grid-cols-1 md:grid-cols-5 gap-4 items-start">
              {stages.map((stage) => {
                const stageJobs = jobs.filter(j => j.status === stage.key);
                return (
                  <div key={stage.key} className="bg-zinc-50 dark:bg-zinc-900/60 border border-zinc-200 dark:border-zinc-800 rounded-lg p-3 min-h-[450px] flex flex-col">
                    <div className="flex items-center justify-between pb-2 mb-3 border-b border-zinc-200 dark:border-zinc-800">
                      <span className="text-xs font-mono font-bold uppercase text-zinc-700 dark:text-zinc-300">
                        {stage.label}
                      </span>
                      <span className="w-5 h-5 rounded-full bg-zinc-200 dark:bg-zinc-800 text-[10px] font-mono font-bold flex items-center justify-center">
                        {stageJobs.length}
                      </span>
                    </div>

                    <div className="space-y-3 flex-1">
                      {stageJobs.map((job) => (
                        <div key={job.id} className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-3.5 rounded shadow-sm hover:shadow transition space-y-2">
                          <div className="flex justify-between items-start">
                            <div>
                              <h4 className="text-xs font-bold text-zinc-900 dark:text-white leading-tight">{job.role}</h4>
                              <p className="text-[11px] font-semibold text-indigo-600 dark:text-indigo-400 mt-0.5">{job.company}</p>
                            </div>
                            <button
                              onClick={() => deleteJob(job.id)}
                              className="text-zinc-400 hover:text-rose-500 p-0.5"
                              title="Hapus"
                            >
                              <X className="w-3.5 h-3.5" />
                            </button>
                          </div>

                          {job.salary && (
                            <span className="inline-block px-1.5 py-0.5 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 text-[10.5px] font-mono font-semibold rounded">
                              💰 {job.salary}
                            </span>
                          )}

                          {job.desc && (
                            <p className="text-[11px] text-zinc-500 line-clamp-2 leading-relaxed">
                              {job.desc}
                            </p>
                          )}

                          {/* Action Buttons on Job Card */}
                          <div className="pt-2 border-t border-zinc-100 dark:border-zinc-800 flex flex-col gap-1.5">
                            <button
                              onClick={() => handleTailorCv(job)}
                              disabled={tailoring}
                              className="w-full py-1 px-2 bg-indigo-50 dark:bg-indigo-950/50 hover:bg-indigo-100 text-indigo-700 dark:text-indigo-300 text-[10.5px] font-bold rounded flex items-center justify-center gap-1 border border-indigo-200 dark:border-indigo-800/60"
                            >
                              <Wand2 className="w-3 h-3" />
                              <span>Sesuaikan CV untuk Lowongan Ini</span>
                            </button>

                            <div className="flex items-center justify-between text-[10px] text-zinc-400 pt-1">
                              <button
                                onClick={() => moveJobStage(job.id, 'prev')}
                                className="hover:text-indigo-500 font-bold px-1"
                                title="Pindah ke tahap sebelumnya"
                              >
                                ← Mundur
                              </button>
                              <button
                                onClick={() => {
                                  setTargetCompany(job.company);
                                  setJobTitle(job.role);
                                  setJobDesc(job.desc || '');
                                  setActiveTab('mock_interview');
                                }}
                                className="text-zinc-600 dark:text-zinc-400 hover:text-indigo-500 font-medium"
                              >
                                Latihan Mock
                              </button>
                              <button
                                onClick={() => moveJobStage(job.id, 'next')}
                                className="hover:text-indigo-500 font-bold px-1"
                                title="Pindah ke tahap berikutnya"
                              >
                                Maju →
                              </button>
                            </div>
                          </div>
                        </div>
                      ))}
                    </div>
                  </div>
                );
              })}
            </div>

          </div>
        )}

        {/* ========================================================================= */}
        {/* TAB 3: FINANCE & PRICING CALCULATOR                                       */}
        {/* ========================================================================= */}
        {activeTab === 'finance' && (
          <div className="space-y-6">
            
            {/* Top KPI Summary Cards */}
            <div className="grid grid-cols-1 sm:grid-cols-4 gap-4">
              <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-5 shadow-sm">
                <span className="text-xs font-mono font-bold uppercase text-zinc-500">Total Omset Layanan</span>
                <div className="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">
                  Rp {totalOmset.toLocaleString('id-ID')}
                </div>
                <span className="text-[11px] text-zinc-400">Dari {totalPaidCount} transaksi lunas</span>
              </div>

              <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-5 shadow-sm">
                <span className="text-xs font-mono font-bold uppercase text-zinc-500">Rata-rata Order (AOV)</span>
                <div className="text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-1">
                  Rp {aov.toLocaleString('id-ID')}
                </div>
                <span className="text-[11px] text-zinc-400">Nilai tiket per klien</span>
              </div>

              <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-5 shadow-sm">
                <span className="text-xs font-mono font-bold uppercase text-zinc-500">Total Order Dicatat</span>
                <div className="text-2xl font-black text-zinc-900 dark:text-white mt-1">
                  {financeTransactions.length}
                </div>
                <span className="text-[11px] text-zinc-400">B2C & B2B Consulting</span>
              </div>

              <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-5 shadow-sm">
                <span className="text-xs font-mono font-bold uppercase text-zinc-500">Tingkat Sukses Pelunasan</span>
                <div className="text-2xl font-black text-amber-500 mt-1">
                  {financeTransactions.length > 0 ? Math.round((totalPaidCount / financeTransactions.length) * 100) : 0}%
                </div>
                <span className="text-[11px] text-zinc-400">Metode QRIS & Transfer</span>
              </div>
            </div>

            {/* Rate Card & Service Packages Catalog */}
            <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 shadow-sm space-y-4">
              <h3 className="text-xs font-mono font-bold uppercase text-zinc-500 dark:text-zinc-400 flex items-center gap-2">
                <Sliders className="w-4 h-4 text-indigo-500" />
                <span>Katalog Paket Layanan & Kalkulator Biaya (Pricing Rate Card)</span>
              </h3>

              <div className="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
                <div className="p-4 border border-zinc-200 dark:border-zinc-800 rounded bg-zinc-50 dark:bg-zinc-950/40 space-y-2">
                  <span className="px-2 py-0.5 bg-zinc-200 dark:bg-zinc-800 font-mono font-bold text-[10px] rounded">1. STARTER / LITE</span>
                  <div className="text-lg font-black text-zinc-900 dark:text-white">Rp 35.000</div>
                  <ul className="text-zinc-600 dark:text-zinc-400 space-y-1 text-[11px]">
                    <li>✓ Akses template Modern</li>
                    <li>✓ Export PDF & Print View</li>
                    <li>✗ Tanpa konsultasi ahli</li>
                  </ul>
                </div>

                <div className="p-4 border-2 border-indigo-500/50 rounded bg-indigo-50/20 dark:bg-indigo-950/20 space-y-2 relative">
                  <span className="px-2 py-0.5 bg-indigo-600 text-white font-mono font-bold text-[10px] rounded">2. STANDARD (FAVORIT)</span>
                  <div className="text-lg font-black text-indigo-600 dark:text-indigo-400">Rp 75.000</div>
                  <ul className="text-zinc-700 dark:text-zinc-300 space-y-1 text-[11px]">
                    <li>✓ Audit ATS Skor 80+</li>
                    <li>✓ Poles kata kerja aksi aktif</li>
                    <li>✓ Sisipkan data metrik (%/Rp)</li>
                  </ul>
                </div>

                <div className="p-4 border border-zinc-200 dark:border-zinc-800 rounded bg-zinc-50 dark:bg-zinc-950/40 space-y-2">
                  <span className="px-2 py-0.5 bg-emerald-600 text-white font-mono font-bold text-[10px] rounded">3. FULL STACK</span>
                  <div className="text-lg font-black text-zinc-900 dark:text-white">Rp 150.000</div>
                  <ul className="text-zinc-600 dark:text-zinc-400 space-y-1 text-[11px]">
                    <li>✓ Semua fitur Standard</li>
                    <li>✓ Cover Letter tertarget</li>
                    <li>✓ Optimasi Profil LinkedIn</li>
                  </ul>
                </div>

                <div className="p-4 border border-zinc-200 dark:border-zinc-800 rounded bg-zinc-50 dark:bg-zinc-950/40 space-y-2">
                  <span className="px-2 py-0.5 bg-purple-600 text-white font-mono font-bold text-[10px] rounded">4. EXECUTIVE VIP</span>
                  <div className="text-lg font-black text-purple-600 dark:text-purple-400">Rp 300.000</div>
                  <ul className="text-zinc-600 dark:text-zinc-400 space-y-1 text-[11px]">
                    <li>✓ Semua fitur Full Stack</li>
                    <li>✓ Simulasi Mock Interview STAR</li>
                    <li>✓ Garansi lolos seleksi berkas</li>
                  </ul>
                </div>
              </div>
            </div>

            {/* Transactions Ledger Table */}
            <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 shadow-sm space-y-4">
              <div className="flex flex-wrap items-center justify-between gap-3 border-b border-zinc-200 dark:border-zinc-800 pb-3">
                <h3 className="text-sm font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                  <FileSpreadsheet className="w-4 h-4 text-emerald-500" />
                  <span>Buku Kas Transaksi Layanan Pembuatan CV</span>
                </h3>

                <div className="flex items-center gap-2">
                  <button
                    onClick={handleExportFinanceCsv}
                    className="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition flex items-center gap-1.5 shadow"
                  >
                    <Download className="w-3.5 h-3.5" />
                    <span>Export Excel (.csv)</span>
                  </button>
                  <button
                    onClick={() => setShowAddTransactionModal(true)}
                    className="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition flex items-center gap-1.5 shadow"
                  >
                    <Plus className="w-3.5 h-3.5" />
                    <span>Catat Transaksi Baru</span>
                  </button>
                </div>
              </div>

              <div className="overflow-x-auto">
                <table className="w-full text-left text-xs text-zinc-700 dark:text-zinc-300">
                  <thead className="bg-zinc-50 dark:bg-zinc-800 font-mono text-zinc-500 text-[11px] uppercase">
                    <tr>
                      <th className="p-2.5">ID TRX</th>
                      <th className="p-2.5">Tanggal</th>
                      <th className="p-2.5">Klien</th>
                      <th className="p-2.5">Paket Layanan</th>
                      <th className="p-2.5">Nominal (IDR)</th>
                      <th className="p-2.5">Metode Bayar</th>
                      <th className="p-2.5">Status</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-zinc-200 dark:divide-zinc-800">
                    {financeTransactions.map((t) => (
                      <tr key={t.id} className="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition">
                        <td className="p-2.5 font-mono font-bold text-indigo-600 dark:text-indigo-400">{t.id}</td>
                        <td className="p-2.5 font-mono">{t.date}</td>
                        <td className="p-2.5 font-semibold text-zinc-900 dark:text-white">{t.client_name}</td>
                        <td className="p-2.5">{t.package}</td>
                        <td className="p-2.5 font-mono font-bold">Rp {t.price.toLocaleString('id-ID')}</td>
                        <td className="p-2.5 text-zinc-500">{t.payment_method}</td>
                        <td className="p-2.5">
                          <span className={`px-2 py-0.5 text-[10px] font-mono font-bold rounded ${t.status === 'paid' ? 'bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400' : 'bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-400'}`}>
                            {t.status === 'paid' ? 'LUNAS' : 'MENUNGGU'}
                          </span>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>

          </div>
        )}

        {/* ========================================================================= */}
        {/* TAB 4: AI ATS AUDITOR & QUALITY LINTER                                    */}
        {/* ========================================================================= */}
        {activeTab === 'ats_audit' && (
          <div className="max-w-4xl mx-auto space-y-6">
            
            {/* Score Overview Card */}
            <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-6">
              <div className="flex items-center gap-5">
                <div className={`w-24 h-24 rounded-full flex flex-col items-center justify-center font-black text-2xl shadow-inner border-4 ${atsScore >= 80 ? 'border-emerald-500 text-emerald-500 bg-emerald-50 dark:bg-emerald-950/20' : (atsScore >= 60 ? 'border-amber-500 text-amber-500 bg-amber-50 dark:bg-amber-950/20' : 'border-rose-500 text-rose-500 bg-rose-50 dark:bg-rose-950/20')}`}>
                  <span>{atsScore}</span>
                  <span className="text-[10px] font-mono uppercase font-bold text-zinc-500">Skor ATS</span>
                </div>
                <div>
                  <h2 className="text-xl font-bold text-zinc-900 dark:text-white">
                    {atsScore >= 80 ? 'Resume Sangat Kompetitif' : (atsScore >= 60 ? 'Kualitas Baik, Perlu Sedikit Poles' : 'Perlu Optimasi Mendalam')}
                  </h2>
                  <p className="text-xs text-zinc-500 dark:text-zinc-400 mt-1 max-w-md">
                    Analisis heuristik terhadap kata kerja aksi, kelengkapan kontak, data metrik terukur (% / Rp), dan kerapatan kata kunci ATS.
                  </p>
                </div>
              </div>

              <button
                onClick={() => runAtsAudit()}
                disabled={auditing}
                className="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition flex items-center gap-2 shadow"
              >
                <RotateCcw className={`w-3.5 h-3.5 ${auditing ? 'animate-spin' : ''}`} />
                <span>{auditing ? 'Memindai...' : 'Pindai Ulang AI'}</span>
              </button>
            </div>

            {/* Sub-scores breakdown */}
            {atsAudit?.sub_scores && (
              <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
                {Object.entries(atsAudit.sub_scores).map(([k, v]) => (
                  <div key={k} className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-4 text-center">
                    <div className="text-xs text-zinc-500 uppercase font-mono">{k.replace('_', ' ')}</div>
                    <div className="text-xl font-bold text-indigo-600 dark:text-indigo-400 mt-1">{v} / 25</div>
                  </div>
                ))}
              </div>
            )}

            {/* Actionable Improvement Items */}
            <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 shadow-sm space-y-4">
              <h3 className="text-sm font-bold uppercase font-mono text-zinc-900 dark:text-white flex items-center gap-2">
                <AlertTriangle className="w-4 h-4 text-amber-500" />
                <span>Rekomendasi Perbaikan Prioritas Tinggi ({atsAudit?.issues?.length || 0})</span>
              </h3>

              <div className="space-y-3">
                {(atsAudit?.issues || []).map((issue, idx) => (
                  <div key={idx} className="p-3.5 bg-amber-50 dark:bg-amber-950/20 border-l-4 border-amber-500 text-xs text-amber-950 dark:text-amber-200 flex items-start gap-3">
                    <span className="font-bold uppercase font-mono px-1.5 py-0.5 bg-amber-200 dark:bg-amber-800 text-[10px] text-amber-900 dark:text-amber-100">
                      {issue.section}
                    </span>
                    <p className="flex-1">{issue.message}</p>
                  </div>
                ))}
              </div>

              {atsAudit?.strengths && atsAudit.strengths.length > 0 && (
                <div className="pt-4 border-t border-zinc-200 dark:border-zinc-800">
                  <h4 className="text-xs font-mono font-bold uppercase text-emerald-600 dark:text-emerald-400 mb-2 flex items-center gap-1.5">
                    <CheckCircle className="w-3.5 h-3.5" />
                    <span>Poin Kekuatan Terverifikasi</span>
                  </h4>
                  <ul className="list-disc list-inside space-y-1 text-xs text-zinc-600 dark:text-zinc-400">
                    {atsAudit.strengths.map((s, idx) => (
                      <li key={idx}>{s}</li>
                    ))}
                  </ul>
                </div>
              )}
            </div>

          </div>
        )}

        {/* ========================================================================= */}
        {/* TAB 5: VIRTUAL MOCK INTERVIEW STUDIO                                      */}
        {/* ========================================================================= */}
        {activeTab === 'mock_interview' && (
          <div className="max-w-4xl mx-auto space-y-6">
            
            {/* Target Job Setup Card */}
            <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 shadow-sm">
              <h3 className="text-xs font-mono font-bold uppercase text-indigo-600 dark:text-indigo-400 mb-3 flex items-center gap-2">
                <Briefcase className="w-4 h-4" />
                <span>Konfigurasi Target Wawancara Kerja</span>
              </h3>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs mb-4">
                <div>
                  <label className="block text-zinc-600 dark:text-zinc-400 mb-1 font-medium">Perusahaan Target</label>
                  <input
                    type="text"
                    value={targetCompany}
                    onChange={(e) => setTargetCompany(e.target.value)}
                    placeholder="e.g. Neriah Pro, Tokopedia, Google"
                    className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-2 text-xs"
                  />
                </div>

                <div>
                  <label className="block text-zinc-600 dark:text-zinc-400 mb-1 font-medium">Posisi yang Dilamar</label>
                  <input
                    type="text"
                    value={jobTitle}
                    onChange={(e) => setJobTitle(e.target.value)}
                    placeholder="e.g. Lead Systems Architect"
                    className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-2 text-xs"
                  />
                </div>
              </div>

              <button
                onClick={handleGenerateInterview}
                disabled={generatingInterview}
                className="w-full sm:w-auto px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition flex items-center justify-center gap-2 shadow"
              >
                <Sparkles className={`w-3.5 h-3.5 ${generatingInterview ? 'animate-spin' : ''}`} />
                <span>{generatingInterview ? 'AI Sedang Menyusun Pertanyaan...' : 'Generate 5 Pertanyaan Wawancara Strategis'}</span>
              </button>
            </div>

            {/* Questions Carousel & Voice Answer Recording */}
            {interviewQuestions.length > 0 && (
              <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 shadow-sm space-y-6">
                
                {/* Question Header & Category */}
                <div className="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-3">
                  <div className="flex items-center gap-2 text-xs font-mono">
                    <span className="px-2 py-0.5 bg-indigo-100 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 font-bold">
                      SOAL {selectedQuestionIndex + 1} DARI {interviewQuestions.length}
                    </span>
                    <span className="text-zinc-400">
                      • {interviewQuestions[selectedQuestionIndex].category}
                    </span>
                  </div>

                  <div className="flex gap-1">
                    {interviewQuestions.map((_, i) => (
                      <button
                        key={i}
                        onClick={() => { setSelectedQuestionIndex(i); setAnswerEvaluation(null); setUserAnswer(''); }}
                        className={`w-6 h-6 text-xs font-mono font-bold transition ${selectedQuestionIndex === i ? 'bg-indigo-600 text-white' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200'}`}
                      >
                        {i + 1}
                      </button>
                    ))}
                  </div>
                </div>

                {/* Question Statement */}
                <div>
                  <h4 className="text-base font-bold text-zinc-900 dark:text-white leading-snug">
                    "{interviewQuestions[selectedQuestionIndex].question}"
                  </h4>
                  <p className="text-xs text-indigo-600 dark:text-indigo-400 mt-2 font-mono flex items-start gap-1">
                    <span>💡 Kriteria Rekruter:</span>
                    <span>{interviewQuestions[selectedQuestionIndex].tip}</span>
                  </p>
                </div>

                {/* Candidate Answer Box with Voice Mic */}
                <div className="space-y-2">
                  <div className="flex items-center justify-between">
                    <label className="text-xs font-bold text-zinc-700 dark:text-zinc-300 flex items-center gap-1.5 font-mono">
                      <span>Jawaban Anda (Ketik atau Bicara via Mic)</span>
                    </label>

                    <button
                      onClick={toggleRecording}
                      className={`px-3 py-1 text-xs font-bold transition flex items-center gap-1.5 ${isRecording ? 'bg-rose-600 text-white animate-pulse' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200'}`}
                    >
                      {isRecording ? <MicOff className="w-3.5 h-3.5" /> : <Mic className="w-3.5 h-3.5" />}
                      <span>{isRecording ? 'Berhenti Bicara' : 'Mulai Rekam Suara'}</span>
                    </button>
                  </div>

                  <textarea
                    rows={4}
                    value={userAnswer}
                    onChange={(e) => setUserAnswer(e.target.value)}
                    placeholder="Bicaralah ke mikrofon Anda atau ketik jawaban Anda di sini. Gunakan metode STAR (Situation, Task, Action, Result)..."
                    className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 p-3 text-xs text-zinc-900 dark:text-zinc-100"
                  />
                </div>

                {/* Evaluate Answer Button */}
                <button
                  onClick={handleEvaluateAnswer}
                  disabled={evaluatingAnswer}
                  className="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition flex items-center gap-2 shadow"
                >
                  <CheckCircle className={`w-3.5 h-3.5 ${evaluatingAnswer ? 'animate-spin' : ''}`} />
                  <span>{evaluatingAnswer ? 'AI Sedang Mengevaluasi Kerangka STAR...' : 'Evaluasi Jawaban Saya (STAR Framework)'}</span>
                </button>

                {/* Evaluation Result Box */}
                {answerEvaluation && (
                  <div className="p-4 bg-zinc-50 dark:bg-zinc-800 border-l-4 border-indigo-500 text-xs space-y-3">
                    <div className="flex items-center justify-between">
                      <span className="font-bold text-zinc-900 dark:text-white uppercase font-mono">Hasil Analisis AI:</span>
                      <span className="px-2 py-0.5 bg-indigo-600 text-white font-bold font-mono">
                        Skor STAR: {answerEvaluation.score}/100
                      </span>
                    </div>

                    <div className="grid grid-cols-4 gap-2 text-center font-mono text-[11px]">
                      {Object.entries(answerEvaluation.star_breakdown || {}).map(([stage, pass]) => (
                        <div key={stage} className={`p-1.5 border ${pass ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400 font-bold' : 'border-zinc-300 dark:border-zinc-700 text-zinc-400'}`}>
                          {stage.toUpperCase()}: {pass ? '✓' : '✗'}
                        </div>
                      ))}
                    </div>

                    <p className="text-zinc-700 dark:text-zinc-300 leading-relaxed">
                      <strong>Ulasan:</strong> {answerEvaluation.feedback}
                    </p>

                    <p className="text-indigo-600 dark:text-indigo-400 font-mono">
                      <strong>Saran Pelatih:</strong> {answerEvaluation.coaching_tip}
                    </p>
                  </div>
                )}

              </div>
            )}

          </div>
        )}

        {/* ========================================================================= */}
        {/* TAB 6: OUTREACH & LINKEDIN PERSONAL BRANDING                              */}
        {/* ========================================================================= */}
        {activeTab === 'outreach' && (
          <div className="max-w-4xl mx-auto space-y-6">
            
            {/* Sub-tab Switcher: Letters vs LinkedIn */}
            <div className="flex border-b border-zinc-200 dark:border-zinc-800">
              <button
                onClick={() => setOutreachSubTab('letter')}
                className={`px-5 py-2.5 font-mono text-xs font-bold border-b-2 transition ${outreachSubTab === 'letter' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-zinc-500 hover:text-zinc-900'}`}
              >
                Surat Lamaran & Email Korespondensi
              </button>
              <button
                onClick={() => { setOutreachSubTab('linkedin'); if (!linkedInContent) handleGenerateLinkedIn(); }}
                className={`px-5 py-2.5 font-mono text-xs font-bold border-b-2 transition ${outreachSubTab === 'linkedin' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-zinc-500 hover:text-zinc-900'}`}
              >
                LinkedIn Personal Branding Suite
              </button>
            </div>

            {/* SUBTAB 1: LETTERS */}
            {outreachSubTab === 'letter' && (
              <div className="space-y-6">
                <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 shadow-sm space-y-4">
                  <h3 className="text-xs font-mono font-bold uppercase text-indigo-600 dark:text-indigo-400 flex items-center gap-2">
                    <Mail className="w-4 h-4" />
                    <span>Generator Surat & Email Korespondensi Karier</span>
                  </h3>

                  <div className="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                    <div>
                      <label className="block text-zinc-600 dark:text-zinc-400 mb-1 font-medium">Tipe Surat</label>
                      <select
                        value={outreachType}
                        onChange={(e) => setOutreachType(e.target.value)}
                        className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-2 text-xs"
                      >
                        <option value="thank_you">Thank You Email (Pasca Wawancara)</option>
                        <option value="follow_up">Follow-Up Email (Status Lamaran)</option>
                        <option value="letter_of_interest">Letter of Interest (Cold Pitch)</option>
                      </select>
                    </div>

                    <div>
                      <label className="block text-zinc-600 dark:text-zinc-400 mb-1 font-medium">Perusahaan Tujuan</label>
                      <input
                        type="text"
                        value={targetCompany}
                        onChange={(e) => setTargetCompany(e.target.value)}
                        placeholder="e.g. Neriah Pro"
                        className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-2 text-xs"
                      />
                    </div>

                    <div>
                      <label className="block text-zinc-600 dark:text-zinc-400 mb-1 font-medium">Nama Penerima / HRD (Opsional)</label>
                      <input
                        type="text"
                        value={outreachRecipient}
                        onChange={(e) => setOutreachRecipient(e.target.value)}
                        placeholder="e.g. Bapak Hendra (Lead HR)"
                        className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-2 text-xs"
                      />
                    </div>
                  </div>

                  <button
                    onClick={handleGenerateOutreach}
                    disabled={generatingOutreach}
                    className="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition flex items-center gap-2 shadow"
                  >
                    <Sparkles className={`w-3.5 h-3.5 ${generatingOutreach ? 'animate-spin' : ''}`} />
                    <span>{generatingOutreach ? 'Menyusun Surat...' : 'Generate Surat Profesional'}</span>
                  </button>
                </div>

                {generatedLetter && (
                  <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 shadow-sm space-y-4">
                    <div className="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-3">
                      <span className="text-xs font-mono font-bold uppercase text-zinc-500">DRAF SURAT SIAP KIRIM</span>
                      <button
                        onClick={() => copyToClipboard(generatedLetter)}
                        className="px-3 py-1 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 text-xs font-bold transition flex items-center gap-1.5 border border-zinc-300 dark:border-zinc-700"
                      >
                        <Copy className="w-3.5 h-3.5" />
                        <span>{copyNotification ? 'Tersalin ke Clipboard!' : 'Salin Teks'}</span>
                      </button>
                    </div>

                    <textarea
                      rows={10}
                      readOnly
                      value={generatedLetter}
                      className="w-full bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700 p-4 text-xs font-mono leading-relaxed text-zinc-800 dark:text-zinc-200"
                    />
                  </div>
                )}
              </div>
            )}

            {/* SUBTAB 2: LINKEDIN OPTIMIZER */}
            {outreachSubTab === 'linkedin' && (
              <div className="space-y-6">
                <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 shadow-sm space-y-4">
                  <div className="flex items-center justify-between">
                    <div>
                      <h3 className="text-sm font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                        <Linkedin className="w-4 h-4 text-blue-600" />
                        <span>LinkedIn Headline & Profile Optimization</span>
                      </h3>
                      <p className="text-xs text-zinc-500 mt-0.5">
                        Tingkatkan keterlihatan profil Anda kepada recruiter di LinkedIn dengan formula headline dan ringkasan yang menarik.
                      </p>
                    </div>

                    <button
                      onClick={handleGenerateLinkedIn}
                      disabled={generatingLinkedIn}
                      className="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold transition flex items-center gap-1.5 shadow"
                    >
                      <RotateCcw className={`w-3.5 h-3.5 ${generatingLinkedIn ? 'animate-spin' : ''}`} />
                      <span>{generatingLinkedIn ? 'Menghasilkan...' : 'Generate LinkedIn Pack'}</span>
                    </button>
                  </div>

                  {linkedInContent && (
                    <div className="space-y-6 pt-2">
                      {/* Headlines */}
                      <div>
                        <h4 className="text-xs font-mono font-bold uppercase text-zinc-600 dark:text-zinc-400 mb-2">
                          1. Pilihan Headline Profil (Copy & Paste ke LinkedIn):
                        </h4>
                        <div className="space-y-2">
                          {(linkedInContent.headlines || []).map((h, idx) => (
                            <div key={idx} className="p-3 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-between gap-3 text-xs">
                              <span className="font-semibold text-zinc-800 dark:text-zinc-200">{h}</span>
                              <button
                                onClick={() => copyToClipboard(h)}
                                className="px-2.5 py-1 bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-300 text-[10px] font-bold rounded shrink-0"
                              >
                                Salin
                              </button>
                            </div>
                          ))}
                        </div>
                      </div>

                      {/* About section */}
                      <div>
                        <div className="flex items-center justify-between mb-1.5">
                          <h4 className="text-xs font-mono font-bold uppercase text-zinc-600 dark:text-zinc-400">
                            2. Bagian "About / Tentang Saya" Optimal:
                          </h4>
                          <button
                            onClick={() => copyToClipboard(linkedInContent.about)}
                            className="text-xs text-blue-600 hover:underline font-bold"
                          >
                            Salin Tentang Saya
                          </button>
                        </div>
                        <textarea
                          rows={6}
                          readOnly
                          value={linkedInContent.about || ''}
                          className="w-full bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700 p-3 text-xs font-sans leading-relaxed text-zinc-800 dark:text-zinc-200"
                        />
                      </div>

                      {/* Thought leadership hooks */}
                      <div>
                        <h4 className="text-xs font-mono font-bold uppercase text-zinc-600 dark:text-zinc-400 mb-2">
                          3. Ide Topik Postingan LinkedIn untuk Personal Branding:
                        </h4>
                        <div className="space-y-2">
                          {(linkedInContent.thought_leadership_posts || []).map((post, idx) => (
                            <div key={idx} className="p-3 bg-blue-50/50 dark:bg-blue-950/20 border border-blue-200 dark:border-blue-900/40 text-xs flex items-center justify-between gap-3">
                              <span className="text-zinc-800 dark:text-zinc-200">"{post}"</span>
                              <button
                                onClick={() => copyToClipboard(post)}
                                className="px-2 py-0.5 bg-blue-600 text-white text-[10px] font-bold rounded shrink-0"
                              >
                                Salin
                              </button>
                            </div>
                          ))}
                        </div>
                      </div>
                    </div>
                  )}
                </div>
              </div>
            )}

          </div>
        )}

      </div>
    </div>
  );
}
