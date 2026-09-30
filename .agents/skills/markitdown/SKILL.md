---
name: markitdown
description: Converts various document and media formats (PDF, DOCX, PPTX, XLSX, CSV, HTML, images, and audio) into clean, structured Markdown (.md) using the MarkItDown CLI or MCP tool.
---

# MarkItDown Document Converter Skill

Gunakan skill ini ketika diminta untuk membaca, mengekstrak, memahami, atau mengonversi berkas dokumen non-markdown (seperti `.pdf`, `.docx`, `.pptx`, `.xlsx`, `.csv`, `.html`, `.png`, `.jpg`, `.mp3`, `.wav`) menjadi format Markdown (`.md`).

---

## 1. Lokasi Eksekutabel & Lingkungan

Pada workstation ini, perkakas MarkItDown dikelola menggunakan `uv` dan tersedia di:
- **CLI Binary**: `C:\Users\Iriandi\.local\bin\markitdown.exe` (atau cukup jalankan `markitdown` jika path aktif).
- **MCP Server Binary**: `C:\Users\Iriandi\.local\bin\markitdown-mcp.exe`
- **Fallback runner**: `uv tool run markitdown`

> **Catatan Windows**: Jangan gunakan `pip install markitdown`. Jika perlu memperbarui, gunakan `uv tool install markitdown --force`.

---

## 2. Instruksi Eksekusi untuk Agen

### Skenario A: Konversi Berkas ke Output `.md`
Jalankan perintah PowerShell:
```powershell
markitdown <path_ke_file_input> -o <path_ke_file_output.md>
```
*Contoh:*
```powershell
markitdown "c:\xampp\htdocs\neriahpro\storage\app\public\dokumen.pdf" -o "c:\xampp\htdocs\neriahpro\storage\app\public\dokumen.md"
```

### Skenario B: Ekstraksi Teks Cepat Langsung ke Terminal (Piping / STDOUT)
Jika agen hanya perlu membaca teksnya tanpa membuat file baru:
```powershell
markitdown <path_ke_file_input>
```

### Skenario C: Format Tertentu (Spreadsheet & Slides)
- **Excel (`.xlsx`)**: Otomatis dikonversi menjadi tabel Markdown GitHub-flavored.
- **PowerPoint (`.pptx`)**: Otomatis dipisahkan per slide dengan heading Markdown (`# Slide 1`, dsb).
- **Word (`.docx`)**: Struktur heading, paragraf, dan daftar bullet tetap dipertahankan.

---

## 3. Integrasi Internal Neriah Pro (PHP / Laravel)

Di dalam aplikasi Neriah Pro, integrasi backend telah disiapkan melalui service:
- [MarkItDownService.php](file:///c:/xampp/htdocs/neriahpro/app/Services/MarkItDown/MarkItDownService.php): Wrapper eksekusi CLI / parser file upload.
- [CvMarkdownParser.php](file:///c:/xampp/htdocs/neriahpro/app/Services/MarkItDown/CvMarkdownParser.php): Parser struktur teks Markdown hasil ekstraksi menjadi entity CV & profil.
