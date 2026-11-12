import React from 'react';
import { createRoot } from 'react-dom/client';

class IslandErrorBoundary extends React.Component {
    constructor(props) {
        super(props);
        this.state = { hasError: false, errorMessage: '' };
    }

    static getDerivedStateFromError(error) {
        return {
            hasError: true,
            errorMessage: error?.message || 'Interactive module failed to initialize.',
        };
    }

    componentDidCatch(error, info) {
        console.error(`[IslandErrorBoundary:${this.props.name}]`, error, info);
    }

    render() {
        if (this.state.hasError) {
            return (
                <div className="max-w-3xl mx-auto my-8 p-6 bg-white dark:bg-zinc-950 border-2 border-rose-500 text-zinc-950 dark:text-zinc-100 font-mono text-xs rounded-none shadow-sm">
                    <div className="font-black uppercase text-rose-700 dark:text-rose-400 mb-2">
                        MODULE RECOVERY GUARD // {this.props.name}
                    </div>
                    <p className="mb-4 text-zinc-700 dark:text-zinc-300">
                        {this.state.errorMessage}
                    </p>
                    <button
                        type="button"
                        onClick={() => window.location.reload()}
                        className="px-4 py-2 bg-zinc-950 text-white dark:bg-emerald-500 dark:text-zinc-950 font-black uppercase rounded-none cursor-pointer"
                    >
                        Muat Ulang Modul
                    </button>
                </div>
            );
        }

        return this.props.children;
    }
}

import ClientOnboardingIsland from './Islands/ClientOnboardingIsland.jsx';
import HeroIsland from './Islands/HeroIsland.jsx';
import ProductGridIsland from './Islands/ProductGridIsland.jsx';
import GlobalNavigationIsland from './Islands/GlobalNavigationIsland.jsx';
import FooterIsland from './Islands/FooterIsland.jsx';
import BreadcrumbIsland from './Islands/BreadcrumbIsland.jsx';
import ProjectBlueprintIsland from './Islands/ProjectBlueprintIsland.jsx';
import CvProStudioIsland from './Islands/CvProStudioIsland.jsx';
import CvPricingIsland from './Islands/CvPricingIsland.jsx';
import ArchitecturePricingIsland from './Islands/ArchitecturePricingIsland.jsx';
import VerifiedAniwallShowcaseIsland from './Islands/VerifiedAniwallShowcaseIsland.jsx';

const islands = {
    ClientOnboardingIsland,
    HeroIsland,
    ProductGridIsland,
    GlobalNavigationIsland,
    FooterIsland,
    BreadcrumbIsland,
    ProjectBlueprintIsland,
    CvProStudioIsland,
    CvPricingIsland,
    ArchitecturePricingIsland,
    VerifiedAniwallShowcaseIsland,
};

document.addEventListener('DOMContentLoaded', () => {
    const islandNodes = document.querySelectorAll('[data-react-island]');
    
    islandNodes.forEach((node) => {
        try {
            const componentName = node.getAttribute('data-react-island');
            const Component = islands[componentName];
            
            if (Component) {
                const rawProps = node.getAttribute('data-react-props');
                const props = rawProps ? JSON.parse(rawProps) : {};
                
                const root = createRoot(node);
                root.render(
                    <IslandErrorBoundary name={componentName}>
                        <Component {...props} />
                    </IslandErrorBoundary>
                );
            } else {
                console.warn(`Island component ${componentName} not found.`);
            }
        } catch (err) {
            console.error('Failed to mount island:', err);
        }
    });

    // Notify full-screen loader and page observers that React Islands are mounted
    try {
        window.dispatchEvent(new CustomEvent('neriah-islands:ready'));
    } catch (e) {}
});
