import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';

export default function GlobalNavigationIsland({ settings }) {
  const [scrolled, setScrolled] = useState(false);

  useEffect(() => {
    const handleScroll = () => {
      setScrolled(window.scrollY > 50);
    };
    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  const navType = settings?.type || 'default'; // 'default', 'centered', 'minimal'

  return (
    <motion.nav 
      className={`fixed top-0 w-full z-50 transition-all duration-300 border-b border-gray-900 ${scrolled ? 'bg-black/95 backdrop-blur-lg py-3' : 'bg-transparent py-5'}`}
    >
      <div className={`mx-auto px-6 flex items-center ${navType === 'centered' ? 'max-w-4xl justify-between' : 'max-w-7xl justify-between'}`}>
        
        {/* Logo */}
        <a href="/" className="text-xl font-black uppercase tracking-tighter text-white hover:text-[#00f0ff] transition-colors">
          NERIAH<span className="text-[#00f0ff]">PRO</span>
        </a>
        
        {/* Links */}
        {navType !== 'minimal' && (
            <div className={`hidden md:flex gap-10 items-center text-xs font-mono uppercase tracking-widest text-gray-400`}>
              <a href="#products" className="hover:text-white transition-colors">Products</a>
              <a href="#services" className="hover:text-white transition-colors">Services</a>
            </div>
        )}

        {/* Action */}
        <div className="flex items-center">
            <a href="/admin" className="text-xs font-bold uppercase tracking-widest text-[#00f0ff] hover:text-white transition-colors border-b border-transparent hover:border-white pb-1">
              Client OS
            </a>
        </div>
        
      </div>
    </motion.nav>
  );
}
