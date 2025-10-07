export default function Test() {
    return (
        <div style={{ padding: '20px', background: '#f0f0f0' }}>
            <h1>Test Page Works!</h1>
            <p>If you see this, React is working.</p>
            <p>Time: {new Date().toLocaleTimeString()}</p>
        </div>
    );
}
