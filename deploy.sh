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

if [ -z "$DEPLOY_SYNCED" ]; then
    echo "[1/3] Sinkronisasi kode ke versi GitHub (main)..."
    if [ -d .git ]; then
        git fetch origin main
        git reset --hard origin/main
    fi
    DEPLOY_SYNCED=1 exec bash "$0" "$@"
fi

echo "[2/3] Mengeksekusi Skenario $1..."
# Selalu bersihkan cache config terlebih dahulu agar environment variables terbaru langsung terbaca
rm -f bootstrap/cache/*.php 2>/dev/null || true
php artisan optimize:clear 2>/dev/null || true

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
        # Bersihkan file fisik media lama agar tidak menjadi file yatim (orphan)
        rm -rf storage/app/public/*
        php artisan migrate:fresh --seed --force
        php artisan storage:link 2>/dev/null || true
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

# Otomatis pastikan izin folder storage selalu aman setelah artisan dijalankan
mkdir -p storage/app/public storage/app/.cache storage/framework/views storage/framework/cache/data storage/framework/sessions storage/logs bootstrap/cache
chmod -R 777 storage bootstrap/cache 2>/dev/null || true
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

echo "[3/3] === DEPLOYMENT SELESAI === 🎉"
