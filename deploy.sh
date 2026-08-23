#!/bin/bash

# Menampilkan menu jika dijalankan tanpa parameter angka
if [ -z "$1" ]; then
    echo "================================================="
    echo "   MENU DEPLOYMENT NERIAH PRO"
    echo "================================================="
    echo "Cara pakai: ./deploy.sh [angka_skenario]"
    echo ""
    echo "Daftar Skenario:"
    echo "  1 - Skenario Ringan (Hanya update tampilan/logika)"
    echo "  2 - Migrasi Aman (Menambah tabel/kolom tanpa hapus data)"
    echo "  3 - Hancur & Bangun DB (Migrate Fresh + Seed) ⚠️ STAGING ONLY"
    echo "  4 - Install Plugin Baru (Composer + Filament Upgrade)"
    echo "  5 - Dump Autoload (Update nama Class/Folder)"
    echo "  6 - Build Assets Frontend (Vite/Tailwind/NPM)"
    echo "================================================="
    exit 1
fi

echo "[1/3] Sinkronisasi kode ke versi GitHub (main)..."
git fetch origin main
git reset --hard origin/main

echo "[2/3] Mengeksekusi Skenario $1..."
case $1 in
    1)
        echo "--- Menjalankan Skenario 1: UI/Blade Only ---"
        php artisan optimize:clear
        ;;
    2)
        echo "--- Menjalankan Skenario 2: Migrate Safe ---"
        composer install --no-dev --optimize-autoloader
        php artisan migrate --force
        php artisan optimize:clear
        ;;
    3)
        echo "--- Menjalankan Skenario 3: Migrate Fresh (HANCURKAN DB) ---"
        php artisan migrate:fresh --seed --force
        php artisan optimize:clear
        ;;
    4)
        echo "--- Menjalankan Skenario 4: Install Package Baru ---"
        composer install --no-dev --optimize-autoloader
        php artisan filament:upgrade
        php artisan optimize:clear
        ;;
    5)
        echo "--- Menjalankan Skenario 5: Dump Autoload ---"
        composer dump-autoload -o
        php artisan optimize:clear
        ;;
    6)
        echo "--- Menjalankan Skenario 6: Build Assets (Vite/Tailwind) ---"
        npm install
        npm run build
        php artisan optimize:clear
        ;;
    *)
        echo "❌ Error: Pilihan Skenario Tidak Valid! Ketik ./deploy.sh untuk melihat daftar."
        exit 1
        ;;
esac

echo "[3/3] === DEPLOYMENT SELESAI === 🎉"
