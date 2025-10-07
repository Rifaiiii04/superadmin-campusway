module.exports = {
  apps: [{
    name: 'superadmin-vite',
    script: 'node_modules/vite/bin/vite.js',
    args: '--host 0.0.0.0 --port 5173',
    cwd: '/var/www/superadmin/superadmin-campusway',
    env: {
      NODE_ENV: 'development'
    },
    autorestart: true,
    watch: false,
    max_memory_restart: '1G'
  }]
}
