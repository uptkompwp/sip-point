@inject('report', 'App\Http\Controllers\Api\ReportController')
<table style="width: 100%;border: 1px solid black">
    <tr>
        <td style="text-align: center;background:yellow;padding:20px;border: 1px solid black" colspan="{{ count($sesi) + 5 }}">&quot;REKAP POINT
            &quot;{{ $makul->makul }} ({{$makul->sks}}) SKS&quot; {{ $kelas->kelas }}&quot;</td>
    </tr>
    <tr>
        <td style="background: #CCFFFF;width:200px;text-align: center;border: 1px solid black" rowspan="2">NIM</td>
        @foreach ($sesi as $s)
            <td style="background: #CCFFFF;text-align: center;padding:10px;border: 1px solid black" colspan="2">
                Pertemuan
                {{ $s->pertemuan }}
                    @if ($s->tambahan)
                        (&quot;Tambahan&quot;)
                    @endif
                </td>
        @endforeach
    </tr>
    <tr>
        @foreach ($sesi as $s)
            <td style="background: #FF99CC;width:100px;padding:5px;border: 1px solid black">Praktek</td>
            <td style="background: #FFCC99;width:100px;padding:5px;border: 1px solid black">Teori</td>
        @endforeach
    </tr>
    <tbody>
        @foreach ($mahasiswa as $mhs)
            <tr>
                <td style="border: 1px solid black">{{ $mhs->nim }}</td>
                @foreach ($sesi as $s)
                    <td style="border: 1px solid black">
                        {{ $report->getDetail($mhs->id, $s->id, 'PRAKTEK') }}
                    </td>
                    <td style="border: 1px solid black">
                        {{ $report->getDetail($mhs->id, $s->id, 'TEORI') }}
                    </td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>

{{-- rekap total --}}
<table style="width: 100%;border: 1px solid black">
    <tr>
        <td style="text-align: center;background:yellow;padding:20px;border: 1px solid black" colspan="3">&quot;Total Point
            &quot;{{ $makul->makul }}&quot; {{ $kelas->kelas }}&quot;</td>
    </tr>
    <tr>
        <td style="background: #CCFFFF;width:200px;text-align: center;border: 1px solid black" >NIM</td>
        <td style="background: #CCFFFF;width:200px;text-align: center;border: 1px solid black" >Praktek
        </td>
        <td style="background: #CCFFFF;width:200px;text-align: center;border: 1px solid black">Teori</td>
    </tr>
    <tbody>
        @foreach ($mahasiswa as $mhs)
            <tr>
                <td style="border: 1px solid black">{{ $mhs->nim }}</td>
                <td style="border: 1px solid black">{{ $report->getTotal($mhs->id, 'PRAKTEK') }}</td>
                <td style="border: 1px solid black">{{ $report->getTotal($mhs->id, 'TEORI') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
