import React, { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';

const steps = [
  { id: 'intro', title: 'Start Your Journey', desc: 'Tell us who you are.' },
  { id: 'vision', title: 'The Vision', desc: 'What are we building?' },
  { id: 'budget', title: 'Investment', desc: 'Scope & Budget' },
  { id: 'consent', title: 'Privacy First', desc: 'Finalize & Submit' }
];

export default function ClientOnboardingIsland({ csrfToken, submitUrl }) {
  const [currentStep, setCurrentStep] = useState(0);
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    company_name: '',
    project_needs: {},
    budget_range: '',
    privacy_consent_agreed: false
  });

  const [visionText, setVisionText] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [isSuccess, setIsSuccess] = useState(false);

  const nextStep = () => setCurrentStep((prev) => Math.min(prev + 1, steps.length - 1));
  const prevStep = () => setCurrentStep((prev) => Math.max(prev - 1, 0));

  const handleChange = (e) => {
    const { name, value, type, checked } = e.target;
    if (name === 'vision') {
        setVisionText(value);
        setFormData(prev => ({ ...prev, project_needs: { ...prev.project_needs, vision: value } }));
    } else {
        setFormData(prev => ({
        ...prev,
        [name]: type === 'checkbox' ? checked : value
        }));
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!formData.privacy_consent_agreed) {
        alert("You must agree to the privacy policy.");
        return;
    }
    
    setIsSubmitting(true);
    
    try {
        const response = await fetch(submitUrl || '/api/onboarding', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            },
            body: JSON.stringify(formData)
        });
        
        if (response.ok) {
            setIsSuccess(true);
        } else {
            console.error("Submission failed.");
        }
    } catch (err) {
        console.error(err);
    } finally {
        setIsSubmitting(false);
    }
  };

  if (isSuccess) {
      return (
          <motion.div 
            initial={{ opacity: 0, scale: 0.9 }} 
            animate={{ opacity: 1, scale: 1 }} 
            className="p-10 border border-[#00f0ff] bg-black text-white rounded-xl shadow-[0_0_20px_rgba(0,240,255,0.3)] text-center max-w-2xl mx-auto font-sans"
          >
              <h2 className="text-4xl font-black uppercase tracking-tighter mb-4 text-[#00f0ff]">Idea Logged.</h2>
              <p className="text-lg opacity-80">Your vision is secure with us. We will reach out shortly to begin mapping out your Project OS.</p>
          </motion.div>
      );
  }

  return (
    <div className="max-w-3xl mx-auto p-8 font-sans text-white bg-[#0a0a0a] border-l-4 border-[#00f0ff] relative overflow-hidden shadow-2xl">
      {/* Decorative neon blur */}
      <div className="absolute top-0 right-0 w-64 h-64 bg-[#00f0ff] opacity-10 blur-[100px] pointer-events-none rounded-full" />
      
      <div className="mb-10">
        <h2 className="text-3xl font-black uppercase tracking-tighter">{steps[currentStep].title}</h2>
        <p className="text-sm opacity-60 font-mono mt-1">{steps[currentStep].desc}</p>
        
        <div className="flex gap-2 mt-6">
            {steps.map((step, idx) => (
                <div key={step.id} className={`h-2 flex-1 rounded-full transition-all duration-500 ${idx <= currentStep ? 'bg-[#00f0ff] shadow-[0_0_10px_rgba(0,240,255,0.5)]' : 'bg-gray-800'}`} />
            ))}
        </div>
      </div>

      <AnimatePresence mode="wait">
        <motion.div
          key={currentStep}
          initial={{ opacity: 0, x: 20 }}
          animate={{ opacity: 1, x: 0 }}
          exit={{ opacity: 0, x: -20 }}
          transition={{ duration: 0.3 }}
          className="min-h-[250px]"
        >
          {currentStep === 0 && (
            <div className="space-y-6">
              <div>
                  <label className="block text-xs uppercase font-bold tracking-widest opacity-50 mb-2">Full Name</label>
                  <input type="text" name="name" value={formData.name} onChange={handleChange} className="w-full bg-black border-b-2 border-gray-800 focus:border-[#00f0ff] outline-none py-3 text-xl transition-colors" placeholder="John Doe" />
              </div>
              <div>
                  <label className="block text-xs uppercase font-bold tracking-widest opacity-50 mb-2">Email Address</label>
                  <input type="email" name="email" value={formData.email} onChange={handleChange} className="w-full bg-black border-b-2 border-gray-800 focus:border-[#00f0ff] outline-none py-3 text-xl transition-colors" placeholder="john@example.com" />
              </div>
              <div>
                  <label className="block text-xs uppercase font-bold tracking-widest opacity-50 mb-2">Company (Optional)</label>
                  <input type="text" name="company_name" value={formData.company_name} onChange={handleChange} className="w-full bg-black border-b-2 border-gray-800 focus:border-[#00f0ff] outline-none py-3 text-xl transition-colors" placeholder="Acme Corp" />
              </div>
            </div>
          )}

          {currentStep === 1 && (
            <div className="space-y-6">
              <div>
                  <label className="block text-xs uppercase font-bold tracking-widest opacity-50 mb-2">Describe Your Vision</label>
                  <textarea name="vision" value={visionText} onChange={handleChange} rows="5" className="w-full bg-[#111] border border-gray-800 focus:border-[#00f0ff] p-4 text-lg outline-none resize-none transition-colors" placeholder="We are trying to solve..." />
                  <p className="text-xs text-gray-500 mt-2">Do not include sensitive financial data or passwords.</p>
              </div>
            </div>
          )}

          {currentStep === 2 && (
            <div className="space-y-6">
              <label className="block text-xs uppercase font-bold tracking-widest opacity-50 mb-4">Select Budget Range</label>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  {['<$5k', '$5k - $15k', '$15k - $50k', '>$50k (Enterprise)'].map(range => (
                      <button 
                        key={range} 
                        onClick={() => handleChange({ target: { name: 'budget_range', value: range } })}
                        className={`py-4 px-6 border ${formData.budget_range === range ? 'border-[#00f0ff] bg-[rgba(0,240,255,0.05)] text-[#00f0ff]' : 'border-gray-800 hover:border-gray-600'} text-left transition-all duration-300 font-mono`}
                      >
                          {range}
                      </button>
                  ))}
              </div>
            </div>
          )}

          {currentStep === 3 && (
            <div className="space-y-6">
              <div className="bg-[#111] p-6 border border-gray-800">
                  <h3 className="text-lg font-bold mb-2">Data Privacy Commitment</h3>
                  <p className="text-sm opacity-70 mb-4">Your responses are securely logged using ULID architecture. We map your ideas to provide a tailored Project OS blueprint without compromising your privacy.</p>
                  
                  <label className="flex items-center gap-3 cursor-pointer">
                      <input type="checkbox" name="privacy_consent_agreed" checked={formData.privacy_consent_agreed} onChange={handleChange} className="w-5 h-5 accent-[#00f0ff]" />
                      <span className="text-sm">I agree to the secure processing of my project ideas.</span>
                  </label>
              </div>
            </div>
          )}
        </motion.div>
      </AnimatePresence>

      <div className="mt-10 flex justify-between items-center">
          <button 
            onClick={prevStep} 
            disabled={currentStep === 0}
            className={`text-sm uppercase tracking-widest font-bold ${currentStep === 0 ? 'opacity-20 cursor-not-allowed' : 'opacity-100 hover:text-[#00f0ff]'} transition-colors`}
          >
              Back
          </button>
          
          {currentStep < steps.length - 1 ? (
              <button 
                onClick={nextStep}
                className="bg-white text-black px-8 py-3 font-black uppercase tracking-widest hover:bg-[#00f0ff] transition-colors"
              >
                  Next
              </button>
          ) : (
              <button 
                onClick={handleSubmit}
                disabled={isSubmitting || !formData.privacy_consent_agreed}
                className={`bg-[#00f0ff] text-black px-8 py-3 font-black uppercase tracking-widest transition-all ${isSubmitting || !formData.privacy_consent_agreed ? 'opacity-50 cursor-not-allowed' : 'hover:bg-white shadow-[0_0_15px_rgba(0,240,255,0.4)]'}`}
              >
                  {isSubmitting ? 'Securing...' : 'Initialize OS'}
              </button>
          )}
      </div>
    </div>
  );
}
