<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Laporan SIBEL')</title>
    <style>
        :root {
            --ink: #0f172a;
            --muted: #64748b;
            --line: #cbd5e1;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: #f1f5f9;
            color: var(--ink);
            font-family: "Segoe UI", "Helvetica Neue", Arial, sans-serif;
            font-size: 12.5px;
            line-height: 1.55;
        }

        .toolbar {
            position: sticky;
            top: 0;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 20px;
            background: #064e3b;
            color: #fff;
        }

        .toolbar a, .toolbar button {
            border: 0;
            border-radius: 8px;
            padding: 9px 16px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
        }

        .toolbar .btn-print { background: #fff; color: #065f46; }
        .toolbar .btn-print:hover { background: #ecfdf5; }
        .toolbar .btn-back { background: rgba(255, 255, 255, .15); color: #fff; }
        .toolbar .btn-back:hover { background: rgba(255, 255, 255, .28); }

        .sheet {
            width: 210mm;
            min-height: 297mm;
            margin: 24px auto;
            padding: 16mm 15mm;
            background: #fff;
            box-shadow: 0 10px 30px rgba(15, 23, 42, .12);
        }

        .letterhead {
            display: flex;
            align-items: center;
            gap: 16px;
            border-bottom: 3px double var(--ink);
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .letterhead img { max-width: 74px; max-height: 74px; object-fit: contain; }
        .letterhead .logo-fallback {
            width: 74px; height: 74px; border: 2px solid var(--line); border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 26px; font-weight: 800; color: var(--muted);
        }
        .letterhead h1 { margin: 0; font-size: 17px; letter-spacing: .02em; text-transform: uppercase; }
        .letterhead p { margin: 2px 0 0; color: var(--muted); font-size: 12px; }

        .doc-title { text-align: center; margin: 0 0 18px; }
        .doc-title h2 { margin: 0; font-size: 15px; text-transform: uppercase; letter-spacing: .06em; text-decoration: underline; }
        .doc-title p { margin: 4px 0 0; color: var(--muted); font-size: 12px; }

        table { width: 100%; border-collapse: collapse; }
        .meta td { padding: 4px 6px; vertical-align: top; }
        .meta td:first-child { width: 170px; color: var(--muted); }
        .meta td:nth-child(2) { width: 12px; }
        .grid th, .grid td { border: 1px solid var(--line); padding: 7px 9px; }
        .grid th { background: #f8fafc; text-align: left; font-size: 12px; }
        .grid td.num { text-align: center; width: 60px; }

        .bars { margin: 10px 0 0; }
        .bar-row { display: flex; align-items: center; gap: 10px; margin-bottom: 7px; }
        .bar-label { width: 110px; font-size: 12px; font-weight: 600; }
        .bar-track { flex: 1; height: 16px; border-radius: 999px; background: #e2e8f0; overflow: hidden; }
        .bar-fill { height: 100%; border-radius: 999px; }
        .bar-value { width: 90px; font-size: 11.5px; color: var(--muted); }

        .box { border: 1px solid var(--line); border-radius: 8px; padding: 12px 14px; margin-top: 14px; }
        .box h3 { margin: 0 0 6px; font-size: 12.5px; text-transform: uppercase; letter-spacing: .04em; color: var(--muted); }
        .box p { margin: 0; white-space: pre-line; }
        .badge {
            display: inline-block; border: 1px solid var(--ink); border-radius: 999px;
            padding: 2px 12px; font-size: 12px; font-weight: 700;
        }

        .ethics { margin-top: 16px; border-top: 1px dashed var(--line); padding-top: 10px; font-size: 10.5px; color: var(--muted); }

        .signature { margin-top: 34px; display: flex; justify-content: flex-end; }
        .signature .block { text-align: center; width: 230px; }
        .signature p { margin: 0; }
        .signature .space { height: 64px; }

        @media print {
            @page { size: A4; margin: 12mm; }
            body { background: #fff; font-size: 11.5px; }
            .toolbar { display: none !important; }
            .sheet { width: auto; min-height: auto; margin: 0; padding: 0; box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button type="button" class="btn-print" onclick="window.print()">🖨️ Cetak / Simpan PDF</button>
        <a class="btn-back" href="{{ $backUrl ?? url()->previous() }}">← Kembali</a>
    </div>

    <div class="sheet">
        <header class="letterhead">
            @if (!empty($settings['logo']))
                <img src="{{ img_url($settings['logo']) }}" alt="Logo Sekolah">
            @else
                <div class="logo-fallback">{{ strtoupper(substr($settings['site_name'] ?? 'S', 0, 1)) }}</div>
            @endif
            <div>
                <h1>{{ $settings['site_name'] ?? config('app.name') }}</h1>
                <p>{{ $settings['address'] ?? '' }}</p>
                <p>{{ collect([$settings['phone'] ?? null, $settings['email'] ?? null])->filter()->implode(' • ') }}</p>
            </div>
        </header>

        @yield('content')
    </div>

    <script>
        document.addEventListener('keydown', function (event) {
            if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'p') {
                window.print();
            }
        });
    </script>
</body>
</html>
