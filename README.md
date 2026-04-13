# EV-HUB Project (Kelompok D)

## RULES NGERJAIN (Standar Kolaborasi Git & Jira)

**ATURAN WAJIB:** Tidak boleh ada yang melakukan `git push origin main` secara langsung. Semua pengerjaan harus menggunakan **Branch Perorangan** masing-masing dan disatukan ke `main` melalui mekanisme **Pull Request (PR)**.

### Langkah Pengerjaan Harian:

**1. Sinkronisasi Kode Utama (Wajib Awal Pengerjaan)**
Pastikan kode selalu *up-to-date* dengan pekerjaan teman-teman yang lain.
`git checkout main`
`git pull origin main`

**2. Pindah/Buat Branch Perorangan**
Gunakan branch dengan nama masing-masing. Jika branch belum ada, gunakan perintah ini:
`git checkout -b NamaKamu_TubesPPL`
*(Contoh: Langgeng_TubesPPL, Wisnu_TubesPPL)*

Jika branch sudah pernah dibuat sebelumnya, cukup ketik:
`git checkout NamaKamu_TubesPPL`

**3. Kerjakan Kode (Laravel)**
Kerjakan fitur (Controller, Model, View, Migration) sesuai dengan pembagian PBI di Jira.

**4. Simpan dan Commit (WAJIB CANTUMKAN ID JIRA)**
Meskipun pengerjaan disatukan dalam satu branch, **pesan commit harus tetap dipisah per pekerjaan** agar terdeteksi oleh Jira.
`git add .`
`git commit -m "[TPD-X] feat: membuat antarmuka dashboard admin"`
*(Ganti TPD-X dengan ID tiket Jira yang sedang dikerjakan. Contoh: TPD-19)* Ini dicek di jira yah

**5. Push ke Branch Sendiri**
`git push origin NamaKamu_TubesPPL`

**6. Buat Pull Request (PR)**
Jika progresmu udh kelar atau fitur sudah selesai, buka GitHub dan buat PR dari branch perorangan menuju `main`. Infokan ke grup untuk di-review oleh PM sblm di-Merge.