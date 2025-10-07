const fs = require('fs');
const babel = require('@babel/core');

console.log('🔨 Building React app manually...');

// Baca file app.jsx
const appCode = fs.readFileSync('./resources/js/app.jsx', 'utf8');

// Transpile JSX ke JS regular
const result = babel.transformSync(appCode, {
  presets: ['@babel/preset-react'],
  filename: 'app.jsx'
});

// Buat output yang browser-compatible
const output = `
// Super Admin Campusway - Built Manually
(function() {
  'use strict';
  
  ${result.code}
  
  // Initialize app
  if (typeof App !== 'undefined') {
    console.log('✅ React app initialized');
  }
})();
`;

// Tulis file output
fs.writeFileSync('./public/build/app.js', output);
console.log('✅ Manual build completed: public/build/app.js');

// Copy CSS (jika ada)
try {
  fs.copyFileSync('./resources/css/app.css', './public/build/app.css');
  console.log('✅ CSS copied: public/build/app.css');
} catch (e) {
  console.log('ℹ️ No CSS file found');
}
