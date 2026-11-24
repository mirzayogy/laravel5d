<style>
    .kop td {
        vertical-align: middle;
    }

    .judul-instansi {
        font-size: 16pt;
        font-weight: bold;
    }

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

    .aktif {
        color: #1a7f37;
        font-weight: bold;
    }
</style>

<page backtop="32mm" backbottom="14mm" backleft="0mm" backright="0mm">
    <page_header>
        <table class="kop" style="width: 100%; border-bottom: 0.6mm solid #000000;">
            <tr>
                <td style="width: 15%;">
                    <img src="{{ public_path('dist/assets/img/AdminLTELogo.png') }}" style="width: 20mm;">
                </td>
                <td style="width: 85%;">
                    <span class="judul-instansi">LABORATORIUM KOMPUTER</span><br>
                    Sistem Informasi Praktikum (SIMTIKUM)<br>
                    Jl. Contoh No. 1, Banjarbaru
                </td>
            </tr>
        </table>
    </page_header>

    <page_footer>
        <table style="width: 100%; font-size: 9pt; color: #666666;">
            <tr>
                <td style="width: 50%;">Dicetak: {{ now()->format('d-m-Y H:i') }}</td>
                <td style="width: 50%; text-align: right;">Halaman [[page_cu]] dari [[page_nb]]</td>
            </tr>
        </table>
    </page_footer>

    <h3 class="tengah">LAPORAN DATA PERIODE</h3>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 8%;">No</th>
                <th style="width: 47%;">Periode</th>
                <th style="width: 20%;">Singkatan</th>
                <th style="width: 25%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($semua_periode as $periode)
                <tr>
                    <td class="tengah">{{ $loop->iteration }}</td>
                    <td>{{ $periode->periode }}</td>
                    <td class="tengah">{{ $periode->singkatan }}</td>
                    <td class="tengah">
                        @if ($periode->status)
                            <span class="aktif">Aktif</span>
                        @else
                            Tidak aktif
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <br>
    <table style="width: 100%;">
        <tr>
            <td style="width: 60%;">
                <qrcode value="{{ route('periode.index') }}" ec="M" style="width: 25mm; border: none;"></qrcode>
            </td>
            <td style="width: 40%;">
                Banjarbaru, {{ now()->translatedFormat('d F Y') }}<br>
                Koordinator Laboratorium,<br><br><br><br>
                <b>Nama Koordinator</b>
            </td>
        </tr>
    </table>
</page>
