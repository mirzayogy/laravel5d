<style>
    .data {
        width: 100%;
        border-collapse: collapse;
    }

    .data th {
        background-color: #0b6e69;
        color: #ffffff;
        padding: 2mm;
        border: 0.2mm solid #0b6e69;
    }

    .data td {
        padding: 2mm;
        border: 0.2mm solid #999999;
    }

    .tengah {
        text-align: center;
    }
</style>

<page backtop="10mm" backbottom="14mm">
    <page_footer>
        <table style="width: 100%; font-size: 9pt; color: #666666;">
            <tr>
                <td style="width: 50%;">Dicetak: {{ now()->format('d-m-Y H:i') }}</td>
                <td style="width: 50%; text-align: right;">Halaman [[page_cu]] dari [[page_nb]]</td>
            </tr>
        </table>
    </page_footer>

    <h3 class="tengah">LAPORAN DATA PROGRAM STUDI</h3>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 8%;">No</th>
                <th style="width: 15%;">Kode</th>
                <th style="width: 40%;">Nama Prodi</th>
                <th style="width: 37%;">Nama Kaprodi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($semua_prodi as $prodi)
                <tr>
                    <td class="tengah">{{ $loop->iteration }}</td>
                    <td class="tengah">{{ $prodi->kode_prodi }}</td>
                    <td>{{ $prodi->nama_prodi }}</td>
                    <td>{{ $prodi->nama_kaprodi }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</page>
