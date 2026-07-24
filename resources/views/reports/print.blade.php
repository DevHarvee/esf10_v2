<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Print SF10</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; color: #000; background: #fff; }
        .page { padding: 12px; }
        table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        td, th { border: 1px solid #000; font-size: 10px; padding: 2px; }
        .center { text-align: center; }
        .band { background: #1f2f86; color: #fff; }
        .fail { color: #d61f26; font-weight: 700; }
        .toolbar {
            position: fixed;
            left: 12px;
            right: 12px;
            bottom: 12px;
            display: flex;
            justify-content: space-between;
            gap: 8px;
        }
        .btn {
            border: 0;
            border-radius: 8px;
            text-decoration: none;
            color: #fff;
            padding: 10px 12px;
            font-size: 13px;
            cursor: pointer;
        }
        .btn.back { background: #0d8a3a; }
        .btn.export { background: #1f2f86; }
        .btn.print { background: #d61f26; }
        @media print {
            .toolbar { display: none; }
            @page { margin: 8mm; }
        }
    </style>
</head>
<body>
<div class="page">
    <table>
        <thead>
            <tr>
                <td class="center" colspan="{{ $tableColumnCount }}">
                    <p>Republic of the Philippines</p>
                    <p>Department of Education</p>
                    <h3 style="margin: 0;">Learner Permanent Record for Junior High School (SF10-JHS)</h3>
                    <p><i>(Formerly Form 137)</i></p>
                </td>
            </tr>
        </thead>
        <tbody>
            <tr><td colspan="{{ $tableColumnCount }}" class="center band">LEARNER INFORMATION</td></tr>
            <tr>
                <td colspan="2"><b>LAST NAME</b> {{ $student->lname }}</td>
                <td colspan="2"><b>FIRST NAME</b> {{ $student->fname }}</td>
                <td colspan="2"><b>NAME EXTN (JR, I, II)</b> {{ $student->ext }}</td>
                <td colspan="{{ $tableColumnCount - 6 }}"><b>MIDDLE NAME</b> {{ $student->mname }}</td>
            </tr>
            <tr>
                <td colspan="4"><b>Learner Reference Number (LRN):</b> {{ $student->lrn }}</td>
                <td colspan="2"><b>Birthdate</b> {{ $student->bday }}</td>
                <td colspan="{{ $tableColumnCount - 6 }}"><b>Sex</b> {{ $student->sex }}</td>
            </tr>

            @foreach($records as $sy => $rows)
                @php
                    $finalRating = 0;
                    $totalCounter = 0;
                @endphp

                <tr><td colspan="{{ $tableColumnCount }}" class="center band">SCHOLASTIC RECORD S.Y. {{ $sy }}</td></tr>
                <tr>
                    <td colspan="{{ $tableColumnCount - 4 }}">School: APARRI EAST NATIONAL HIGH SCHOOL</td>
                    <td>School ID: 300471</td>
                    <td>District: FIRST</td>
                    <td>Division: CAGAYAN</td>
                    <td>Region: 02</td>
                </tr>
                <tr>
                    <td>Classified as: {{ optional($rows->first())->grade }}</td>
                    <td>Section: {{ optional($rows->first())->section }}</td>
                    <td>School Year: {{ $sy }}</td>
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
                        $vals = [];
                    @endphp
                    <tr>
                        <td colspan="3">{{ $subject->subject }}</td>
                        @foreach($terms as $term)
                            @php
                                $row = $rows->get($term);
                                $v = data_get($row, $key);
                                if ($key === 's8' && $row) {
                                    $v = round((((float) data_get($row, 's9')) + ((float) data_get($row, 's10')) + ((float) data_get($row, 's11')) + ((float) data_get($row, 's12'))) / 4, 2);
                                }
                                if ($v !== null && $v !== '') {
                                    $vals[] = (float) $v;
                                }
                            @endphp
                            <td class="center {{ ($v !== null && $v !== '' && (float)$v < 75) ? 'fail' : '' }}">{{ $v }}</td>
                        @endforeach
                        @php
                            $avg = count($vals) ? round(array_sum($vals) / count($vals)) : '';
                            if ($avg !== '') {
                                $finalRating += $avg;
                                $totalCounter++;
                            }
                        @endphp
                        <td class="center">{{ $avg }}</td>
                        <td class="center" colspan="2">{{ $avg !== '' ? ($avg > 74 ? 'PASSED' : 'FAILED') : '' }}</td>
                    </tr>
                @endforeach

                @php
                    $generalAverage = $totalCounter > 0 ? round($finalRating / $totalCounter) : '';
                    $promotionStatus = $generalAverage !== '' && $generalAverage > 74 ? 'PROMOTED' : '';
                @endphp

                <tr>
                    <td colspan="3"></td>
                    <td colspan="{{ $quarterColumnCount }}" class="center"><i>General Average</i></td>
                    <td class="center">{{ $generalAverage }}</td>
                    <td colspan="2" class="center">{{ $promotionStatus }}</td>
                </tr>
                <tr><td colspan="{{ $tableColumnCount }}" class="band"></td></tr>
                <tr>
                    <td colspan="2">Remedial Classes</td>
                    <td colspan="{{ $tableColumnCount - 2 }}">Conducted</td>
                </tr>
                <tr>
                    <td class="center" colspan="3">Learning Areas</td>
                    <td class="center" colspan="2">Final Rating</td>
                    <td class="center" colspan="2">Remedial Class Mark</td>
                    <td class="center" colspan="2">Recomputed Final Grade</td>
                    <td class="center" colspan="{{ max(1, $tableColumnCount - 9) }}">Remarks</td>
                </tr>
                <tr>
                    <td colspan="3">&nbsp;</td>
                    <td colspan="2">&nbsp;</td>
                    <td colspan="2">&nbsp;</td>
                    <td colspan="2">&nbsp;</td>
                    <td colspan="{{ max(1, $tableColumnCount - 9) }}">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="3">&nbsp;</td>
                    <td colspan="2">&nbsp;</td>
                    <td colspan="2">&nbsp;</td>
                    <td colspan="2">&nbsp;</td>
                    <td colspan="{{ max(1, $tableColumnCount - 9) }}">&nbsp;</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="toolbar">
    <a href="{{ route('students.index') }}" class="btn back">Back</a>
    <a href="{{ route('reports.export', $student->id) }}" class="btn export">Export to Excel</a>
    <button type="button" class="btn print" id="print-btn">Print</button>
</div>

<script>
    (() => {
        const btn = document.getElementById('print-btn');
        if (btn) {
            btn.addEventListener('click', () => window.print());
        }

        if (@json($autoprint)) {
            window.addEventListener('load', () => {
                setTimeout(() => window.print(), 180);
            });
        }
    })();
</script>
</body>
</html>
