# Skill: Dokumen ke Markdown menggunakan MarkItDown

## Deskripsi
Gunakan skill ini ketika user meminta Anda untuk membaca, memahami, atau mengonversi file non-markdown (seperti `.docx`, `.pptx`, `.xlsx`, `.pdf`, `.csv`, gambar, atau audio) menjadi format Markdown (`.md`).

## Instruksi Eksekusi
1. Pastikan executable `markitdown` tersedia. Pada sistem ini, markitdown sudah terinstal via `uv` di `C:\Users\Iriandi\.local\bin\markitdown.exe`.
2. Jalankan perintah CLI untuk mengekstrak teks ke file Markdown:
   ```powershell
   markitdown <path_ke_file> -o <nama_output>.md
   ```
   Atau untuk membaca output langsung via terminal:
   ```powershell
   markitdown <path_ke_file>
   ```
3. Baca konten dari hasil file `.md` yang dikonversi untuk menjawab pertanyaan user atau melakukan modifikasi kode lebih lanjut.
4. Dalam ekosistem Neriah Pro, gunakan `App\Services\MarkItDown\MarkItDownService` untuk memproses unggahan file secara terprogram di sisi backend Laravel.
