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

  return (
    <motion.nav 
      className={`fixed top-0 w-full z-50 transition-all duration-300 border-b ${scrolled ? 'bg-black/80 backdrop-blur-md border-gray-800 py-4' : 'bg-transparent border-transparent py-6'}`}
    >
      <div className="max-w-7xl mx-auto px-4 flex justify-between items-center">
        <a href="/" className="text-2xl font-black uppercase tracking-tighter text-white hover:text-[#00f0ff] transition-colors">
          NERIAH<span className="text-[#00f0ff]">PRO</span>
        </a>
        
        <div className="hidden md:flex gap-8 items-center text-sm font-mono uppercase tracking-widest text-gray-300">
          <a href="#products" className="hover:text-white transition-colors">Products</a>
          <a href="#services" className="hover:text-white transition-colors">Services</a>
          <a href="/admin" className="hover:text-[#00f0ff] transition-colors">Client Login</a>
        </div>
      </div>
    </motion.nav>
  );
}
