import React from 'react';
import { createRoot } from 'react-dom/client';

import ClientOnboardingIsland from './Islands/ClientOnboardingIsland.jsx';
import HeroIsland from './Islands/HeroIsland.jsx';
import ProductGridIsland from './Islands/ProductGridIsland.jsx';
import GlobalNavigationIsland from './Islands/GlobalNavigationIsland.jsx';
import FooterIsland from './Islands/FooterIsland.jsx';

const islands = {
    ClientOnboardingIsland,
    HeroIsland,
    ProductGridIsland,
    GlobalNavigationIsland,
    FooterIsland,
};

document.addEventListener('DOMContentLoaded', () => {
    const islandNodes = document.querySelectorAll('[data-react-island]');
    
    islandNodes.forEach((node) => {
        const componentName = node.getAttribute('data-react-island');
        const Component = islands[componentName];
        
        if (Component) {
            const rawProps = node.getAttribute('data-react-props');
            const props = rawProps ? JSON.parse(rawProps) : {};
            
            const root = createRoot(node);
            root.render(<Component {...props} />);
        } else {
            console.warn(`Island component ${componentName} not found.`);
        }
    });
});
