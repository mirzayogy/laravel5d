<div class="mb-3">
    <label for="kode_prodi" class="form-label">Kode Prodi</label>
    <input type="text" class="form-control @error('kode_prodi') is-invalid @enderror" id="kode_prodi" name="kode_prodi"
        value="{{ old('kode_prodi', $prodi->kode_prodi ?? '') }}" placeholder="contoh: TI" />
    @error('kode_prodi')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="nama_prodi" class="form-label">Nama Prodi</label>
    <input type="text" class="form-control @error('nama_prodi') is-invalid @enderror" id="nama_prodi"
        name="nama_prodi" value="{{ old('nama_prodi', $prodi->nama_prodi ?? '') }}"
        placeholder="contoh: Teknik Informatika" />
    @error('nama_prodi')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="nama_kaprodi" class="form-label">Nama Kaprodi</label>
    <input type="text" class="form-control @error('nama_kaprodi') is-invalid @enderror" id="nama_kaprodi"
        name="nama_kaprodi" value="{{ old('nama_kaprodi', $prodi->nama_kaprodi ?? '') }}"
        placeholder="nama lengkap beserta gelar" />
    @error('nama_kaprodi')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
