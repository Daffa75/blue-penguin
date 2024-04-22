<table>
  <thead>
    <tr>
      <th colspan="5" rowspan="4"></th> <!-- Image header-->
    </tr>

    <tr></tr>
    <tr></tr>
    <tr></tr>

    <tr>
      <th colspan="5">UNIVERSITAS HASANUDDIN</th>
    </tr>

    <tr>
      <th colspan="5">DEPARTEMEN TEKNIK INFORMATIKA</th>
    </tr>

    <tr>
      <th colspan="5">LAMPIRAN ASET TETAP ALAT DAN MESIN</th>
    </tr>

    <tr>
      <th colspan="5">TANGGAL XXXX</th>
    </tr>

    <tr>
      <th rowspan="2">No.</th>
      <th rowspan="2">NAMA BARANG</th>
      <th rowspan="2">PEROLEHAN</th>
      <th rowspan="2">NILAI PEROLEHAN</th>
      <th rowspan="2">KONDISI</th>
      <th rowspan="2">DITEMUKAN</th>
      <th rowspan="2">DIGUNAKAN</th>
      <th rowspan="2">KETERANGAN / LOKASI ASET</th>
      <th colspan="3">GAMBAR</th>
    </tr>

    <tr>
      <th>Foto Fisik + User / Penyaluran</th>
      <th>Fisik</th>
      <th>Nomor</th>
    </tr>
  </thead>

  <tbody>
    @foreach($list_inventaris as $inventaris)
      <tr>
        <td>{{ $inventaris->registration_number }}</td>
        <td>{{ $inventaris->name }}</td>
        <td>{{ $inventaris->date }}</td>
        <td>{{ $inventaris->price }}</td>
        <td>{{ $inventaris->condition }}</td>
        <td>{{ $inventaris->is_found == true ? 'Ya' : 'Tidak' }}</td>
        <td>{{ $inventaris->is_used == true ? 'Ya' : 'Tidak' }}</td>
        <td>{{ $inventaris->lecturer->name }}</td>
        <td height="120"></td> <!-- For image -->
      </tr>
    @endforeach
  </tbody>
</table>
