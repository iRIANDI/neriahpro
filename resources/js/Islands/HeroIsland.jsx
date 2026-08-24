import React from 'react';
import { motion } from 'framer-motion';

export default function HeroIsland({ headline, subheadline, cta_text, cta_link }) {
  return (
    <div className="relative w-full min-h-[90vh] flex flex-col items-center justify-center text-center overflow-hidden bg-black text-white px-4">
      {/* Decorative Blur Backgrounds */}
      <div className="absolute top-1/4 left-1/4 w-[40vw] h-[40vw] bg-[#00f0ff] opacity-10 rounded-full blur-[150px] pointer-events-none" />
      <div className="absolute bottom-1/4 right-1/4 w-[30vw] h-[30vw] bg-purple-600 opacity-10 rounded-full blur-[150px] pointer-events-none" />

      <motion.div 
        initial={{ y: 50, opacity: 0 }}
        animate={{ y: 0, opacity: 1 }}
        transition={{ duration: 0.8, ease: "easeOut" }}
        className="z-10 max-w-5xl"
      >
        <h1 className="text-6xl md:text-8xl font-black uppercase tracking-tighter leading-none mb-6">
          {headline || "Build The Future."}
        </h1>
        <p className="text-xl md:text-3xl font-mono opacity-70 mb-10 max-w-2xl mx-auto">
          {subheadline || "World-class enterprise OS and CMS built for scale."}
        </p>
        
        <motion.a 
          href={cta_link || "#onboarding"}
          whileHover={{ scale: 1.05 }}
          whileTap={{ scale: 0.95 }}
          className="inline-block bg-[#00f0ff] text-black font-black uppercase tracking-widest px-10 py-5 text-lg shadow-[0_0_20px_rgba(0,240,255,0.4)] hover:bg-white transition-colors"
        >
          {cta_text || "Initialize Now"}
        </motion.a>
      </motion.div>
    </div>
  );
}
