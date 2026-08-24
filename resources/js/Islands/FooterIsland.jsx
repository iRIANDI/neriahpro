import React from 'react';

export default function FooterIsland({ settings }) {
  return (
    <footer className="bg-[#050505] border-t border-gray-900 pt-20 pb-10 text-white mt-20 font-sans">
      <div className="max-w-7xl mx-auto px-4 grid grid-cols-1 md:grid-cols-4 gap-12">
        <div className="col-span-2">
          <h2 className="text-4xl font-black uppercase tracking-tighter mb-4">
            NERIAH<span className="text-[#00f0ff]">PRO</span>
          </h2>
          <p className="opacity-50 max-w-sm font-mono text-sm leading-relaxed">
            Enterprise-grade OS and modular tools built for modern teams. We scale as you scale.
          </p>
        </div>
        
        <div>
          <h4 className="font-bold uppercase tracking-widest text-xs opacity-50 mb-6">Sitemap</h4>
          <ul className="space-y-4 font-mono text-sm">
            <li><a href="#" className="hover:text-[#00f0ff] transition-colors">Home</a></li>
            <li><a href="#" className="hover:text-[#00f0ff] transition-colors">Project OS</a></li>
            <li><a href="#" className="hover:text-[#00f0ff] transition-colors">CV Generator</a></li>
            <li><a href="#" className="hover:text-[#00f0ff] transition-colors">Pricing</a></li>
          </ul>
        </div>

        <div>
          <h4 className="font-bold uppercase tracking-widest text-xs opacity-50 mb-6">Legal</h4>
          <ul className="space-y-4 font-mono text-sm">
            <li><a href="#" className="hover:text-[#00f0ff] transition-colors">Privacy Policy</a></li>
            <li><a href="#" className="hover:text-[#00f0ff] transition-colors">Terms of Service</a></li>
            <li><a href="#" className="hover:text-[#00f0ff] transition-colors">Cookie Settings</a></li>
          </ul>
        </div>
      </div>
      
      <div className="max-w-7xl mx-auto px-4 mt-20 pt-8 border-t border-gray-900 flex justify-between items-center text-xs opacity-30 font-mono">
        <p>&copy; {new Date().getFullYear()} Neriah Pro. All rights reserved.</p>
        <p>SYSTEM ARMED.</p>
      </div>
    </footer>
  );
}
