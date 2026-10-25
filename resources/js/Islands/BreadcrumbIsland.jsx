import React from 'react';

export default function BreadcrumbIsland({ paths }) {
  if (!paths || paths.length <= 1) return null;

  return (
    <nav 
      aria-label="Breadcrumb"
      className="w-full bg-zinc-100/95 dark:bg-zinc-900/95 backdrop-blur-md border-b border-zinc-200 dark:border-zinc-800 py-3 sm:py-3.5 min-h-[44px] sm:min-h-[48px] flex items-center transition-colors font-mono"
    >
      <div className="max-w-7xl mx-auto px-4 sm:px-6 w-full flex items-center gap-2 overflow-x-auto whitespace-nowrap text-xs leading-none">
        {paths.map((path, idx) => {
          const isLast = idx === paths.length - 1;
          return (
            <React.Fragment key={idx}>
              {isLast ? (
                <span className="text-emerald-700 dark:text-emerald-400 font-bold uppercase tracking-wider inline-flex items-center">
                  {path.label}
                </span>
              ) : (
                <a 
                  href={path.url} 
                  className="text-zinc-500 hover:text-zinc-950 dark:text-zinc-400 dark:hover:text-white uppercase tracking-wider transition-colors inline-flex items-center"
                >
                  {path.label}
                </a>
              )}
              {!isLast && (
                <span className="text-zinc-400 dark:text-zinc-600 text-xs select-none inline-flex items-center">/</span>
              )}
            </React.Fragment>
          );
        })}
      </div>
    </nav>
  );
}
