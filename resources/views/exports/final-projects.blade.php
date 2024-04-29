<?php 
use Filament\Facades\Filament;

$status = [
  "ongoing" => "Seminar Proposal",
  "finalizing" => "Seminar Hasil",
  "publication" => "Publikasi",
  "thesis" => "Ujian Tesis",
  "done" => "Wisuda",
];


?>

<table>
  <thead>
    <tr>
      <th>Nama</th>
      <th>NIM</th>
      <th>Judul</th>
      <th>Pembimbing 1</th>
      <th>Pembimbing 2</th>
      <th>Penguji 1</th>
      <th>Penguji 2</th>
      @if($url != 'final-projects')
        <th>Penguji 3</th>
      @endif
      <th>Status</th>
    </tr>
  </thead>

  <tbody>
    @foreach($data_final_projects as $data)
      <tr>
        <td>{{ $data->student->name }}</td>
        <td>{{ $data->student->nim }}</td>
        <td>{{ $data->title }}</td>
        <td>{{ $data->lecturers[0]->name }}</td>
        @if($url == 'final-projects')
          <td>{{ count($data->lecturers) == 3 ? '-' : $data->lecturers[1]->name }}</td>
          <td>{{ count($data->lecturers) == 3 ? $data->lecturers[1]->name : $data->lecturers[2]->name }}</td>
          <td>{{ count($data->lecturers) == 3 ? $data->lecturers[2]->name : $data->lecturers[3]->name }}</td>
        @else
          <td>{{ $data->lecturers[1]->name ?? '' }}</td>
          <td>{{ $data->lecturers[2]->name ?? '' }}</td>
          <td>{{ $data->lecturers[3]->name ?? '' }}</td>
          <td>{{ $data->lecturers[4]->name ?? '' }}</td>
        @endif
        <td>{{ $status[strtolower($data->status)] }}</td>
      </tr>
    @endforeach
  </tbody>
</table>
