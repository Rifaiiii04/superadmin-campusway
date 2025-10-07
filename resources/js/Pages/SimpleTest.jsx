export default function SimpleTest() {
    return (
        <div style={{ padding: '20px', background: '#e0f2fe', border: '2px solid #0369a1' }}>
            <h1>🎉 React Component Works!</h1>
            <p>If you see this, React + Inertia are working perfectly!</p>
            <p>Time: {new Date().toLocaleTimeString()}</p>
        </div>
    );
}
