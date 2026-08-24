import React from 'react';
import { motion } from 'framer-motion';

export default function ProductGridIsland({ title, products }) {
  const defaultProducts = [
    { title: "Project OS", desc: "End-to-end client management.", price: "$5k" },
    { title: "CV Generator", desc: "Automated resumes for professionals.", price: "$50/mo" },
    { title: "CMS Plugin Core", desc: "Headless CMS architecture.", price: "Custom" }
  ];

  const items = products || defaultProducts;

  return (
    <div className="py-20 px-4 max-w-7xl mx-auto bg-black text-white">
      <h2 className="text-4xl md:text-5xl font-black uppercase tracking-tighter mb-12 border-b-2 border-gray-800 pb-4">
        {title || "Our Arsenal."}
      </h2>
      
      <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
        {items.map((item, idx) => (
          <motion.div 
            key={idx}
            whileHover={{ y: -10, borderColor: '#00f0ff' }}
            className="border border-gray-800 p-8 transition-colors bg-[#111] group relative overflow-hidden"
          >
            {/* Hover Glare */}
            <div className="absolute top-0 left-[-100%] w-1/2 h-full bg-gradient-to-r from-transparent via-[rgba(0,240,255,0.1)] to-transparent skew-x-12 group-hover:left-[200%] transition-all duration-1000 ease-in-out" />
            
            <h3 className="text-2xl font-bold mb-4 group-hover:text-[#00f0ff] transition-colors">{item.title}</h3>
            <p className="text-gray-400 font-mono text-sm mb-8">{item.desc}</p>
            <div className="text-xl font-black uppercase tracking-widest">{item.price}</div>
          </motion.div>
        ))}
      </div>
    </div>
  );
}
