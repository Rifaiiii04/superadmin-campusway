#!/bin/bash

# Simple wrapper script to run the main script from filesh folder
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
bash "$SCRIPT_DIR/filesh/start-superadmin-backend-port3000.sh"

