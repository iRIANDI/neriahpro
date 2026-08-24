import React from 'react';
import { motion } from 'framer-motion';

export default function BreadcrumbIsland({ paths }) {
  // paths is an array of objects: { label: 'Home', url: '/' }
  if (!paths || paths.length === 0) return null;

  return (
    <nav className="w-full bg-[#050505] border-b border-gray-900 py-3 mt-[73px]">
      <div className="max-w-7xl mx-auto px-4 flex items-center gap-2 overflow-x-auto whitespace-nowrap">
        {paths.map((path, idx) => {
          const isLast = idx === paths.length - 1;
          return (
            <React.Fragment key={idx}>
              <motion.a 
                whileHover={{ color: '#00f0ff' }}
                href={path.url} 
                className={`font-mono text-xs uppercase tracking-widest transition-colors ${isLast ? 'text-[#00f0ff] font-bold' : 'text-gray-500 hover:text-white'}`}
              >
                {path.label}
              </motion.a>
              {!isLast && (
                <span className="text-gray-700 mx-2 text-xs">/</span>
              )}
            </React.Fragment>
          );
        })}
      </div>
    </nav>
  );
}
