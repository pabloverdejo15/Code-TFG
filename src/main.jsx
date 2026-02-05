import React from 'react';
import ReactDOM from 'react-dom/client';
import App from './components/App';

console.log('VITE: main.jsx loaded');
const root = document.getElementById('react-root');
if (!root) {
    console.error('VITE: CRITICAL - #react-root NOT FOUND');
} else {
    console.log('VITE: Mounting React app...');
    ReactDOM.createRoot(root).render(
        <React.StrictMode>
            <App />
        </React.StrictMode>
    );
}
