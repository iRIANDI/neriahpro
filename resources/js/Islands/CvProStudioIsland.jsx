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
  Volume2
} from 'lucide-react';

export default function CvProStudioIsland({ initialData }) {
  // Navigation Tabs: 'editor' | 'ats_audit' | 'mock_interview' | 'outreach'
  const [activeTab, setActiveTab] = useState('editor');
  const [lang, setLang] = useState('id');

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

  // Outreach State
  const [outreachType, setOutreachType] = useState('thank_you');
  const [outreachRecipient, setOutreachRecipient] = useState('');
  const [generatedLetter, setGeneratedLetter] = useState('');
  const [generatingOutreach, setGeneratingOutreach] = useState(false);
  const [copyNotification, setCopyNotification] = useState(false);

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
                <h3 className="text-xs font-mono font-bold uppercase text-zinc-500 dark:text-zinc-400 mb-3 flex items-center gap-2">
                  <Palette className="w-3.5 h-3.5 text-indigo-500" />
                  <span>Gaya & Format Visual</span>
                </h3>
                <div className="grid grid-cols-2 gap-3 text-xs">
                  <div>
                    <label className="block text-zinc-600 dark:text-zinc-400 mb-1 font-medium">Template</label>
                    <select
                      value={template}
                      onChange={(e) => setTemplate(e.target.value)}
                      className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-2.5 py-1.5 text-xs text-zinc-900 dark:text-zinc-100"
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
                      className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-2.5 py-1.5 text-xs text-zinc-900 dark:text-zinc-100"
                    >
                      <option value="Inter">Inter (Sans)</option>
                      <option value="Roboto">Roboto (Clean)</option>
                      <option value="Lato">Lato (Warm)</option>
                      <option value="Merriweather">Merriweather (Serif)</option>
                    </select>
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

                  <div className="col-span-2">
                    <label className="block text-zinc-600 dark:text-zinc-400 mb-1">Ringkasan Profil (Executive Summary)</label>
                    <textarea
                      rows={3}
                      value={p.summary || ''}
                      onChange={(e) => handlePersonalChange('summary', e.target.value)}
                      placeholder="Jelaskan spesialisasi teknis dan pencapaian terukur Anda dalam 2-3 kalimat..."
                      className="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 p-2.5 text-xs text-zinc-900 dark:text-zinc-100"
                    />
                  </div>
                </div>
              </div>

              {/* Work Experience Section */}
              <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-5 shadow-sm space-y-4">
                <div className="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-2">
                  <h3 className="text-xs font-mono font-bold uppercase text-zinc-900 dark:text-zinc-100 flex items-center gap-1.5">
                    <Briefcase className="w-3.5 h-3.5 text-indigo-500" />
                    <span>2. Pengalaman Kerja ({content.experiences?.length || 0})</span>
                  </h3>
                  <button
                    onClick={addExperience}
                    className="px-2 py-1 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 text-xs font-bold transition flex items-center gap-1 border border-zinc-300 dark:border-zinc-700"
                  >
                    <Plus className="w-3 h-3" /> Tambah Posisi
                  </button>
                </div>

                <div className="space-y-4">
                  {(content.experiences || []).map((exp, idx) => (
                    <div key={exp.id || idx} className="p-3.5 bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700 relative text-xs space-y-2">
                      <button
                        onClick={() => removeExperience(exp.id)}
                        className="absolute top-2.5 right-2.5 text-zinc-400 hover:text-rose-500 transition"
                        title="Hapus Pengalaman"
                      >
                        <Trash2 className="w-3.5 h-3.5" />
                      </button>

                      <div className="grid grid-cols-2 gap-2 pr-6">
                        <div>
                          <label className="text-[10px] text-zinc-500 uppercase font-mono">Posisi / Role</label>
                          <input
                            type="text"
                            value={exp.role || ''}
                            onChange={(e) => {
                              const updated = [...content.experiences];
                              updated[idx].role = e.target.value;
                              setContent({ ...content, experiences: updated });
                            }}
                            className="w-full bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-2 py-1 text-xs"
                          />
                        </div>
                        <div>
                          <label className="text-[10px] text-zinc-500 uppercase font-mono">Perusahaan</label>
                          <input
                            type="text"
                            value={exp.company || ''}
                            onChange={(e) => {
                              const updated = [...content.experiences];
                              updated[idx].company = e.target.value;
                              setContent({ ...content, experiences: updated });
                            }}
                            className="w-full bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-2 py-1 text-xs"
                          />
                        </div>
                        <div>
                          <label className="text-[10px] text-zinc-500 uppercase font-mono">Periode Kerja</label>
                          <input
                            type="text"
                            value={exp.period || ''}
                            onChange={(e) => {
                              const updated = [...content.experiences];
                              updated[idx].period = e.target.value;
                              setContent({ ...content, experiences: updated });
                            }}
                            className="w-full bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-2 py-1 text-xs"
                          />
                        </div>
                        <div>
                          <label className="text-[10px] text-zinc-500 uppercase font-mono">Lokasi</label>
                          <input
                            type="text"
                            value={exp.location || ''}
                            onChange={(e) => {
                              const updated = [...content.experiences];
                              updated[idx].location = e.target.value;
                              setContent({ ...content, experiences: updated });
                            }}
                            className="w-full bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 px-2 py-1 text-xs"
                          />
                        </div>
                      </div>

                      <div>
                        <label className="text-[10px] text-zinc-500 uppercase font-mono">Poin Pencapaian Terukur (Bullet Points - pisahkan enter)</label>
                        <textarea
                          rows={3}
                          value={(exp.bullets || []).join('\n')}
                          onChange={(e) => {
                            const updated = [...content.experiences];
                            updated[idx].bullets = e.target.value.split('\n');
                            setContent({ ...content, experiences: updated });
                          }}
                          placeholder="Mengarsitektur sistem X yang meningkatkan kecepatan 40%..."
                          className="w-full bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 p-2 text-xs font-mono"
                        />
                      </div>
                    </div>
                  ))}
                </div>
              </div>

              {/* Skills Section */}
              <div className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-5 shadow-sm space-y-3">
                <h3 className="text-xs font-mono font-bold uppercase text-zinc-900 dark:text-zinc-100 flex items-center gap-1.5 border-b border-zinc-200 dark:border-zinc-800 pb-2">
                  <Code className="w-3.5 h-3.5 text-indigo-500" />
                  <span>3. Keahlian Teknis / Skills ({content.skills?.length || 0})</span>
                </h3>

                <div className="flex flex-wrap gap-1.5">
                  {(content.skills || []).map((skill, sIdx) => (
                    <span key={sIdx} className="inline-flex items-center gap-1 px-2.5 py-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 text-xs border border-zinc-200 dark:border-zinc-700 font-mono">
                      {skill}
                      <button onClick={() => removeSkill(sIdx)} className="text-zinc-400 hover:text-rose-500">
                        &times;
                      </button>
                    </span>
                  ))}
                </div>

                <div className="flex gap-2 pt-1">
                  <input
                    id="new-skill-input"
                    type="text"
                    placeholder="Tambah keahlian (e.g. PostgreSQL, Redis, Docker)..."
                    onKeyDown={(e) => {
                      if (e.key === 'Enter') {
                        e.preventDefault();
                        addSkill(e.target.value);
                        e.target.value = '';
                      }
                    }}
                    className="flex-1 bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 px-3 py-1.5 text-xs text-zinc-900 dark:text-zinc-100"
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
        {/* TAB 2: AI ATS AUDITOR & QUALITY LINTER                                    */}
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
        {/* TAB 3: VIRTUAL MOCK INTERVIEW STUDIO                                      */}
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
        {/* TAB 4: OUTREACH & NETWORKING SUITE                                        */}
        {/* ========================================================================= */}
        {activeTab === 'outreach' && (
          <div className="max-w-4xl mx-auto space-y-6">
            
            {/* Outreach Config Card */}
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

            {/* Generated Letter Display */}
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

      </div>
    </div>
  );
}
