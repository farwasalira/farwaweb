$files = Get-ChildItem -Path 'c:\xampp\htdocs\webpupuk\views\admin' -Filter '*.php' -File
foreach ($file in $files) {
    $content = Get-Content -Path $file.FullName -Raw
    $newContent = $content -replace '<i class="bx bx-plus"></i> Tambah[^<]*', '<i class="bx bx-plus"></i> Tambah Data'
    $newContent = $newContent -replace '<i class="bx bx-save"></i> (Update|Perbarui Catatan|Perbarui|Simpan Catatan|Simpan Perubahan|Simpan)[^<]*', '<i class="bx bx-save"></i> Simpan'
    if ($content -ne $newContent) {
        Set-Content -Path $file.FullName -Value $newContent
        Write-Host "Modified $($file.Name)"
    }
}
