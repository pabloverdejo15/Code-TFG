import React from 'react';
import DarkVeil from './DarkVeil';

function App() {
    return (
        <div style={{ position: 'fixed', top: 0, left: 0, width: '100%', height: '100%', zIndex: 0, pointerEvents: 'none' }}>
            <DarkVeil
                hueShift={0}
                noiseIntensity={0.15}
                scanlineIntensity={0}
                speed={0.5}
                scanlineFrequency={0}
                warpAmount={0}
                resolutionScale={1}
            />
        </div>
    );
}

export default App;
