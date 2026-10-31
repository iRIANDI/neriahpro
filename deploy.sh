#!/bin/bash

# ==============================================================================
# DEPLOYMENT AUTOMATION NERIAH PRO (RAILPACK & CLOUD CONTAINER ENGINE)
# ==============================================================================
# Menampilkan menu jika dijalankan tanpa parameter angka
if [ -z "$1" ]; then
    echo "================================================="
    echo "   MENU DEPLOYMENT NERIAH PRO (RAILPACK ENGINE)"
    echo "================================================="
    echo "Cara pakai: ./deploy.sh [angka_skenario]"
    echo ""
    echo "Daftar Skenario:"
    echo "  1 - Skenario Ringan (Hanya update tampilan/logika Blade & PHP)"
    echo "  2 - Migrasi Aman (Menambah tabel/kolom tanpa hapus data)"
    echo "  3 - Hancur & Bangun DB (Migrate Fresh + Seed) ⚠️ STAGING ONLY"
    echo "  4 - Install Plugin Baru (Composer + Filament Upgrade)"
    echo "  5 - Dump Autoload (Update nama Class/Folder)"
    echo "  6 - Build Assets Frontend (Vite/Tailwind/NPM)"
    echo "================================================="
    exit 1
fi

GIT_REMOTE_URL="${GIT_REMOTE_URL:-https://github.com/iRIANDI/neriahpro.git}"

# Pastikan environment PATH mencakup direktori binary standar Linux / Railpack
export PATH="/usr/local/bin:/usr/bin:/bin:$HOME/.local/bin:$PATH"

if [ -z "$DEPLOY_SYNCED" ]; then
    if [ -d .git ]; then
        echo "[1/3] Sinkronisasi kode ke versi GitHub (main)..."
        git fetch origin main 2>/dev/null || git fetch "$GIT_REMOTE_URL" main 2>/dev/null || true
        git reset --hard origin/main 2>/dev/null || git reset --hard FETCH_HEAD 2>/dev/null || true
        echo "Commit saat ini: $(git rev-parse --short HEAD 2>/dev/null || echo 'main')"
        DEPLOY_SYNCED=1 exec bash "$0" "$@"
    else
        echo "[1/3] Container Railpack environment terdeteksi (tanpa .git). Menghubungkan ke GitHub ($GIT_REMOTE_URL)..."
        if command -v git >/dev/null 2>&1; then
            git init -b main 2>/dev/null || git init 2>/dev/null || true
            git remote add origin "$GIT_REMOTE_URL" 2>/dev/null || git remote set-url origin "$GIT_REMOTE_URL"
            git fetch origin main
            git reset --hard origin/main
            echo "Commit saat ini di container Railpack: $(git rev-parse --short HEAD 2>/dev/null || echo 'main')"
            DEPLOY_SYNCED=1 exec bash "$0" "$@"
        elif command -v curl >/dev/null 2>&1 && command -v tar >/dev/null 2>&1; then
            echo "Mengunduh kode terbaru dari GitHub main branch via tarball..."
            curl -sL "https://github.com/iRIANDI/neriahpro/archive/refs/heads/main.tar.gz" | tar -xz --strip-components=1
            echo "Kode terbaru berhasil diunduh dan diekstrak ke container Railpack."
            DEPLOY_SYNCED=1 exec bash "$0" "$@"
        else
            echo "[1/3] Container environment tanpa Git/Curl. Melanjutkan dengan file container..."
        fi
    fi
fi

echo "[2/3] Mengeksekusi Skenario $1 (Railpack Engine)..."

# Selalu bersihkan cache config dan view terlebih dahulu
rm -f bootstrap/cache/*.php 2>/dev/null || true
php artisan optimize:clear 2>/dev/null || {
    php artisan config:clear 2>/dev/null || true
    php artisan route:clear 2>/dev/null || true
    php artisan view:clear 2>/dev/null || true
    php artisan cache:clear 2>/dev/null || true
}
php -r "if (function_exists('opcache_reset')) { opcache_reset(); }" 2>/dev/null || true
php artisan tinker --execute="try { if (class_exists('App\Models\CmsPage')) { \$p = App\Models\CmsPage::where('slug', 'home')->first(); if (\$p && !empty(\$p->plugins)) { \$p->plugins = array_values(array_filter(\$p->plugins, fn(\$i) => !in_array(\$i['type'] ?? '', ['feature_grid', 'product_grid']))); \$p->save(); } } \Illuminate\Support\Facades\Cache::flush(); } catch (\Throwable \$e) {}" 2>/dev/null || true

case $1 in
    1)
        echo "--- Menjalankan Skenario 1: UI/Blade Only (Railpack) ---"
        php artisan optimize:clear
        php -r "if (function_exists('opcache_reset')) { opcache_reset(); }" 2>/dev/null || true
        ;;
    2)
        echo "--- Menjalankan Skenario 2: Migrate Safe (Railpack) ---"
        composer install --no-interaction --no-dev --prefer-dist --optimize-autoloader
        php artisan migrate --force
        php artisan db:seed --class=SuperAdminSeeder --force
        php artisan db:seed --class=ProjectOsMatrixSeeder --force
        php artisan db:seed --class=PosStackPresetSeeder --force
        php artisan db:seed --class=CmsSeeder --force
        php artisan db:seed --class=LandingPageSeeder --force
        php artisan db:seed --class=WorkflowEndToEndSeeder --force
        php artisan optimize:clear
        ;;
    3)
        echo "--- Menjalankan Skenario 3: Migrate Fresh (HANCURKAN DB) ⚠️ STAGING ONLY ---"
        # Bersihkan file fisik media lama agar tidak menjadi file yatim (orphan)
        rm -rf storage/app/public/*
        php artisan migrate:fresh --seed --force
        php artisan storage:link 2>/dev/null || true
        php artisan optimize:clear
        ;;
    4)
        echo "--- Menjalankan Skenario 4: Install Package Baru (Railpack) ---"
        composer install --no-interaction --no-dev --prefer-dist --optimize-autoloader
        php artisan filament:upgrade
        php artisan optimize:clear
        ;;
    5)
        echo "--- Menjalankan Skenario 5: Dump Autoload (Railpack) ---"
        composer dump-autoload -o --no-interaction
        php artisan optimize:clear
        ;;
    6)
        echo "--- Menjalankan Skenario 6: Build Assets Frontend (Railpack Vite/Tailwind) ---"
        if command -v npm >/dev/null 2>&1; then
            npm install --no-audit --no-fund
            npm run build
        else
            echo "⚠️ Warning: NPM tidak ditemukan di container. Menggunakan build assets yang sudah di-commit di git."
        fi
        php artisan db:seed --class=LandingPageSeeder --force 2>/dev/null || true
        php artisan optimize:clear
        ;;
    *)
        echo "❌ Error: Pilihan Skenario Tidak Valid! Ketik ./deploy.sh untuk melihat daftar."
        exit 1
        ;;
esac

# Otomatis pastikan izin dan direktori storage selalu aman di container Railpack
mkdir -p storage/app/public storage/app/.cache storage/framework/views storage/framework/cache/data storage/framework/sessions storage/logs bootstrap/cache
chmod -R 775 storage bootstrap/cache 2>/dev/null || chmod -R 777 storage bootstrap/cache 2>/dev/null || true

# Hanya ubah kepemilikan user www-data jika dijalankan sebagai user root
if [ "$(id -u 2>/dev/null || echo 1)" -eq 0 ]; then
    chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
fi

echo "[3/3] === DEPLOYMENT RAILPACK SELESAI === 🎉"
