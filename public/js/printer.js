// Listener untuk Cetak Satuan (Thermal 50x30mm)
window.addEventListener('trigger-print', event => {
    const divId = event.detail.id;
    const content = document.getElementById(divId);
    if (!content) return;

    const win = window.open('', '_blank', 'width=300,height=400');
    
    let html = '<html><head><style>';
    // Setup ukuran label 50mm x 30mm
    html += '@page { size: 50mm 30mm; margin: 0; }';
    html += 'body { margin: 0; padding: 2mm; font-family: "Arial", sans-serif; width: 50mm; height: 30mm; box-sizing: border-box; overflow: hidden; }';
    html += '.print-wrapper { display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; width: 100%; height: 100%; }';
    // QR Code disesuaikan agar muat di area kecil
    html += 'svg { width: 18mm !important; height: 18mm !important; margin-bottom: 1mm; }';
    html += 'p { margin: 0; padding: 0; line-height: 1; font-size: 7pt; font-weight: bold; width: 100%; overflow: hidden; white-space: nowrap; }';
    html += '.header-school { font-size: 5pt; border-bottom: 0.5pt solid black; margin-bottom: 1mm; padding-bottom: 0.5mm; width: 100%; }';
    html += '.sn-text { font-size: 8pt; margin-top: 1mm; }';
    html += '</style></head><body>';
    html += '<div class="print-wrapper">' + content.innerHTML + '</div>';
    html += '</body></html>';

    win.document.write(html);
    win.document.close();

    win.onload = function() {
        setTimeout(() => {
            win.print();
            win.close();
        }, 300);
    };
});

// Listener untuk Cetak Masal (Sekarang mendukung Thermal berturut-turut)
window.addEventListener('trigger-print-all', event => {
    const allLabels = document.querySelectorAll('[id^="print-area-"]');
    if (allLabels.length === 0) return alert('Tidak ada label untuk dicetak');

    const win = window.open('', '_blank');
    
    let html = '<html><head><title>Cetak Masal Thermal</title><style>';
    // Menggunakan ukuran thermal, setiap label akan memaksa 'page break' (potong kertas)
    html += '@page { size: 50mm 30mm; margin: 0; }';
    html += 'body { margin: 0; padding: 0; font-family: "Arial", sans-serif; }';
    html += '.label-page { width: 50mm; height: 30mm; padding: 2mm; box-sizing: border-box; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; page-break-after: always; overflow: hidden; }';
    html += 'svg { width: 18mm !important; height: 18mm !important; margin-bottom: 1mm; }';
    html += 'p { margin: 0; padding: 0; line-height: 1.1; font-size: 7pt; font-weight: bold; }';
    html += '.header-school { font-size: 5pt; border-bottom: 0.5pt solid black; margin-bottom: 1mm; width: 100%; }';
    html += '</style></head><body>';

    allLabels.forEach(label => {
        html += '<div class="label-page">' + label.innerHTML + '</div>';
    });

    html += '</body></html>';

    win.document.write(html);
    win.document.close();

    win.onload = function() {
        setTimeout(() => {
            win.print();
            win.close();
        }, 500);
    };
});

// Fungsi DIR tetap A4 karena berupa dokumen tabel
window.addEventListener('trigger-print-dir', event => {
    // ... (tetap gunakan kode Anda yang lama karena DIR memang harus A4) ...
});

window.addEventListener('trigger-print-dir', event => {
    const data = Array.isArray(event.detail) ? event.detail[0] : event.detail;
    const assets = data.assets;
    
    const win = window.open('', '_blank');
    
    let html = '<html><head><title>DIR - ' + data.roomName + '</title><style>';
    html += 'body { font-family: Arial, sans-serif; padding: 20px; color: #333; }';
    html += '.header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }';
    html += '.header h2 { margin: 0; text-transform: uppercase; }';
    html += '.info { margin-bottom: 20px; width: 100%; }';
    html += 'table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 12px; }';
    html += 'th, td { border: 1px solid #000; padding: 8px; text-align: left; }';
    html += 'th { background-color: #f2f2f2; }';
    html += '.footer { margin-top: 30px; display: flex; justify-content: space-between; }';
    html += '.signature { text-align: center; width: 200px; }';
    html += '.spacer { height: 60px; }';
    html += '@media print { .no-print { display: none; } }';
    html += '</style></head><body>';

    // Header Dokumen
    html += '<div class="header"><h2>Daftar Inventaris Ruangan (DIR)</h2><h3>SMKN 7 BALEENDAH</h3></div>';
    
    // Info Ruangan
    html += '<table class="info" style="border:none;">';
    html += '<tr><td style="border:none; width:120px;">NAMA RUANGAN</td><td style="border:none;">: ' + data.roomName + '</td></tr>';
    html += '<tr><td style="border:none;">TOTAL ASET</td><td style="border:none;">: ' + assets.length + ' Unit</td></tr>';
    html += '<tr><td style="border:none;">TOTAL NILAI</td><td style="border:none;">: ' + data.totalValue + '</td></tr>';
    html += '</table>';

    // Tabel Aset
    html += '<table><thead><tr>';
    html += '<th style="width:30px;">NO</th><th>NAMA BARANG</th><th>NOMOR SERI (SN)</th><th>KONDISI</th><th>SUMBER DANA</th>';
    html += '</tr></thead><tbody>';

    assets.forEach((asset, index) => {
        html += '<tr>';
        html += '<td>' + (index + 1) + '</td>';
        html += '<td>' + asset.name + '</td>';
        html += '<td>' + (asset.sn || '-') + '</td>';
        html += '<td>' + asset.condition.toUpperCase() + '</td>';
        html += '<td>' + asset.source + '</td>';
        html += '</tr>';
    });

    html += '</tbody></table>';

    // Tanda Tangan
    const today = new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
    html += '<div class="footer">';
    html += '<div class="signature"><p>Mengetahui,</p><p>Kepala Sarpras</p><div class="spacer"></div><p>( Gungun Gunawan, S.Pd )</p></div>';
    html += '<div class="signature"><p>Baleendah, ' + today + '</p><p>Penanggung Jawab</p><div class="spacer"></div><p>( ' + data.picName + ' )</p></div>';
    html += '</div>';

    html += '</body></html>';

    win.document.write(html);
    win.document.close();
    win.onload = () => { win.print(); win.close(); };
});