import React from 'react';

export default function BreadcrumbIsland({ paths }) {
  if (!paths || paths.length <= 1) return null;

  return (
    <nav className="w-full bg-zinc-100/95 dark:bg-zinc-900/95 backdrop-blur-xs border-b border-zinc-200 dark:border-zinc-800 py-2.5 mt-[100px] sm:mt-[108px] md:mt-[112px] transition-colors font-mono">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 flex items-center gap-2 overflow-x-auto whitespace-nowrap text-xs">
        {paths.map((path, idx) => {
          const isLast = idx === paths.length - 1;
          return (
            <React.Fragment key={idx}>
              {isLast ? (
                <span className="text-emerald-600 dark:text-emerald-400 font-bold uppercase tracking-wider">
                  {path.label}
                </span>
              ) : (
                <a 
                  href={path.url} 
                  className="text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white uppercase tracking-wider transition-colors"
                >
                  {path.label}
                </a>
              )}
              {!isLast && (
                <span className="text-zinc-400 dark:text-zinc-600 text-xs">/</span>
              )}
            </React.Fragment>
          );
        })}
      </div>
    </nav>
  );
}
