<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Print Grade</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #fff; color: #000; }
        .page { padding: 14px; }
        .title { text-align: center; margin: 0 0 6px; font-size: 20px; }
        .sy { text-align: center; margin: 0 0 12px; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        td, th { border: 1px solid #000; padding: 4px; font-size: 12px; font-weight: 700; }
        .center { text-align: center; }
        .fail { color: #d61f26; }
        .toolbar {
            position: fixed;
            bottom: 14px;
            left: 14px;
            right: 14px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }
        .btn {
            display: inline-block;
            text-decoration: none;
            color: #fff;
            padding: 10px 14px;
            border-radius: 8px;
            cursor: pointer;
            border: 0;
            font-size: 13px;
        }
        .btn.back { background: #1f2f86; }
        .btn.print { background: #d61f26; }
        @media print {
            .toolbar { display: none; }
            @page { margin: 10mm; }
        }
    </style>
</head>
<body>
<div class="page">
    <h3 class="title">Grade of <span style="color:#1f2f86;">{{ $student->sname }}</span></h3>
    <p class="sy">S.Y. {{ $schoolYear }}</p>

    @php
        $finalRatingSum = 0;
        $totalCounter = 0;
    @endphp

    <table>
        <tr>
            <td colspan="{{ $tableColumnCount }}" class="center">SCHOLASTIC RECORD</td>
        </tr>
        <tr>
            <td colspan="{{ $tableColumnCount - 4 }}">School: APARRI EAST NATIONAL HIGH SCHOOL</td>
            <td>School ID: 300471</td>
            <td>District: FIRST</td>
            <td>Division: CAGAYAN</td>
            <td>Region: 02</td>
        </tr>
        <tr>
            <td>Classified as: {{ $student->grade }}</td>
            <td>Section: {{ $student->section }}</td>
            <td>School Year: {{ $schoolYear }}</td>
            <td colspan="{{ $tableColumnCount - 5 }}">Name of Adviser/Teacher: {{ auth()->user()->fullname }}</td>
            <td colspan="2">Signature:</td>
        </tr>
        <tr>
            <td class="center" colspan="3" rowspan="2">LEARNING AREAS</td>
            <td class="center" colspan="{{ $quarterColumnCount }}">Term Rating</td>
            <td class="center" rowspan="2">FINAL<br>RATING</td>
            <td class="center" colspan="2" rowspan="2">REMARKS</td>
        </tr>
        <tr>
            @foreach($terms as $term)
                <td class="center">{{ $term }}</td>
            @endforeach
        </tr>

        @foreach($subjects as $idx => $subject)
            @php
                $key = 's' . ($idx + 1);
                $subjectTotal = 0;
                $subjectCount = 0;
            @endphp
            <tr>
                <td colspan="3">{{ $subject->subject }}</td>
                @foreach($terms as $term)
                    @php
                        $row = $rows->get($term);
                        $value = data_get($row, $key);
                        if ($key === 's8' && $row) {
                            $value = round((((float) data_get($row, 's9')) + ((float) data_get($row, 's10')) + ((float) data_get($row, 's11')) + ((float) data_get($row, 's12'))) / 4, 2);
                        }
                        if ($value !== null && $value !== '') {
                            $subjectTotal += (float) $value;
                            $subjectCount++;
                        }
                    @endphp
                    <td class="center {{ ($value !== null && $value !== '' && (float)$value < 75) ? 'fail' : '' }}">{{ $value }}</td>
                @endforeach
                @php
                    $final = $subjectCount > 0 ? round($subjectTotal / $subjectCount) : '';
                    if ($final !== '') {
                        $finalRatingSum += $final;
                        $totalCounter++;
                    }
                    $remarks = $final !== '' && $final >= 75 ? 'PASSED' : ($final === '' ? '' : 'FAILED');
                @endphp
                <td class="center">{{ $final }}</td>
                <td class="center" colspan="2">{{ $remarks }}</td>
            </tr>
        @endforeach

        @php
            $generalAverage = $totalCounter > 0 ? round($finalRatingSum / $totalCounter) : '';
            $promotionStatus = ($generalAverage !== '' && $generalAverage > 74) ? 'PROMOTED' : '';
        @endphp

        <tr>
            <td colspan="3"></td>
            <td colspan="{{ $quarterColumnCount }}" class="center"><i>General Average</i></td>
            <td class="center">{{ $generalAverage }}</td>
            <td class="center" colspan="2">{{ $promotionStatus }}</td>
        </tr>
        <tr>
            <td colspan="{{ $tableColumnCount }}">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="2">Remedial Classes</td>
            <td colspan="{{ $tableColumnCount - 2 }}">Conducted</td>
        </tr>
        <tr>
            <td class="center" colspan="{{ $remedialSpans[0] }}">Learning Areas</td>
            <td class="center" colspan="{{ $remedialSpans[1] }}">Final Rating</td>
            <td class="center" colspan="{{ $remedialSpans[2] }}">Remedial Class Mark</td>
            <td class="center" colspan="{{ $remedialSpans[3] }}">Recomputed Final Grade</td>
            <td class="center" colspan="{{ $remedialSpans[4] }}">Remarks</td>
        </tr>
        <tr>
            <td colspan="{{ $remedialSpans[0] }}">&nbsp;</td>
            <td colspan="{{ $remedialSpans[1] }}">&nbsp;</td>
            <td colspan="{{ $remedialSpans[2] }}">&nbsp;</td>
            <td colspan="{{ $remedialSpans[3] }}">&nbsp;</td>
            <td colspan="{{ $remedialSpans[4] }}">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="{{ $remedialSpans[0] }}">&nbsp;</td>
            <td colspan="{{ $remedialSpans[1] }}">&nbsp;</td>
            <td colspan="{{ $remedialSpans[2] }}">&nbsp;</td>
            <td colspan="{{ $remedialSpans[3] }}">&nbsp;</td>
            <td colspan="{{ $remedialSpans[4] }}">&nbsp;</td>
        </tr>
    </table>
</div>

<div class="toolbar">
    <a href="{{ route('grades.index') }}" class="btn back">Return</a>
    <button type="button" class="btn print" id="print-btn">Print</button>
</div>

<script>
    (() => {
        const printBtn = document.getElementById('print-btn');
        if (printBtn) {
            printBtn.addEventListener('click', () => window.print());
        }

        const params = new URLSearchParams(window.location.search);
        if (params.get('autoprint') === '1') {
            window.addEventListener('load', () => {
                setTimeout(() => window.print(), 180);
            });
        }
    })();
</script>
</body>
</html>
