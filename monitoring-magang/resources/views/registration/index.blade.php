
@extends('layouts.pdlk')

@section('title', 'Daftar Pendaftaran PDLK | Monitoring PDLK')
@section('breadcrumb', 'Pengelolaan PDLK / Daftar Pendaftaran')
@section('page-title', 'Daftar Pendaftaran PDLK')

@section('content')

@php
    $daftarPendaftaran = collect($registrations ?? []);

    $totalPendaftaran = $daftarPendaftaran->count();

    $jumlahMenunggu = $daftarPendaftaran
        ->filter(fn ($item) => data_get($item, 'status') === 'menunggu')
        ->count();

    $jumlahDiterima = $daftarPendaftaran
        ->filter(fn ($item) => data_get($item, 'status') === 'diterima')
        ->count();

    $jumlahDitolak = $daftarPendaftaran
        ->filter(fn ($item) => data_get($item, 'status') === 'ditolak')
        ->count();
@endphp

<style>
    .registration-page {
        --registration-blue: #348db8;
        --registration-text: #26364d;
        --registration-muted: #718096;
        --registration-border: #e1e7ef;

        color: var(--registration-text);
    }

    .registration-page,
    .registration-page * {
        box-sizing: border-box;
    }

    .registration-page .registration-heading {
        margin-bottom: 22px;
    }

    .registration-page .registration-heading h2 {
        margin: 0 0 6px;
        font-size: 20px;
        font-weight: 600;
        line-height: 1.5;
    }

    .registration-page .registration-heading p {
        margin: 0;
        color: var(--registration-muted);
        font-size: 12px;
        line-height: 1.8;
    }

    /* Ringkasan jumlah pendaftaran */
    .registration-page .registration-summary {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 24px;
    }

    .registration-page .registration-summary-card {
        min-width: 0;
        padding: 18px;
        border: 1px solid var(--registration-border);
        border-radius: 5px;
        background: #fff;
    }

    .registration-page .registration-summary-label {
        margin-bottom: 12px;
        color: var(--registration-muted);
        font-size: 12px;
        line-height: 1.6;
    }

    .registration-page .registration-summary-value {
        margin-bottom: 8px;
        font-size: 27px;
        font-weight: 700;
        line-height: 1.2;
    }

    .registration-page .registration-summary-description {
        margin: 0;
        color: var(--registration-muted);
        font-size: 11px;
        line-height: 1.7;
    }

    .registration-page .registration-count-waiting {
        color: #b7791f;
    }

    .registration-page .registration-count-accepted {
        color: #198754;
    }

    .registration-page .registration-count-rejected {
        color: #dc3545;
    }

    /* Tabel */
    .registration-page .registration-table-panel {
        padding: 20px;
        border: 1px solid var(--registration-border);
        border-radius: 5px;
        background: #fff;
    }

    .registration-page .registration-table-heading {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 17px;
    }

    .registration-page .registration-table-heading h3 {
        margin: 0 0 5px;
        font-size: 16px;
        font-weight: 600;
        line-height: 1.5;
    }

    .registration-page .registration-table-heading p {
        margin: 0;
        color: var(--registration-muted);
        font-size: 12px;
        line-height: 1.7;
    }

    .registration-page .registration-table-wrapper {
        width: 100%;
        overflow-x: auto;
        border: 1px solid #edf0f4;
    }

    .registration-page .registration-table {
        width: 100%;
        min-width: 850px;
        border-collapse: collapse;
        background: #fff;
        font-size: 12px;
    }

    .registration-page .registration-table th {
        padding: 13px 12px;
        background: #f5f8fb;
        color: #506477;
        font-weight: 700;
        text-align: left;
        white-space: nowrap;
        border-bottom: 1px solid var(--registration-border);
    }

    .registration-page .registration-table td {
        padding: 13px 12px;
        color: #354052;
        line-height: 1.7;
        vertical-align: middle;
        border-bottom: 1px solid #edf0f4;
    }

    .registration-page .registration-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .registration-page .registration-table tbody tr:hover {
        background: #fafcfe;
    }

    .registration-page .registration-student-name {
        min-width: 150px;
        font-weight: 600;
    }

    .registration-page .registration-status {
        display: inline-flex;
        align-items: center;
        padding: 5px 9px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.5;
        white-space: nowrap;
    }

    .registration-page .registration-status-waiting {
        background: #fff2cf;
        color: #946200;
    }

    .registration-page .registration-status-accepted {
        background: #e2f7ec;
        color: #087443;
    }

    .registration-page .registration-status-rejected {
        background: #ffeaec;
        color: #b4232d;
    }

    .registration-page .registration-actions {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 7px;
    }

    .registration-page .registration-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 32px;
        padding: 7px 10px;
        border: 1px solid transparent;
        border-radius: 4px;
        color: #fff;
        font-family: inherit;
        font-size: 11px;
        font-weight: 600;
        line-height: 1.4;
        cursor: pointer;
        transition: background .15s ease;
    }

    .registration-page .registration-button-accept {
        background: #198754;
        border-color: #198754;
    }

    .registration-page .registration-button-accept:hover {
        background: #146c43;
    }

    .registration-page .registration-button-reject {
        background: #dc3545;
        border-color: #dc3545;
    }

    .registration-page .registration-button-reject:hover {
        background: #bb2d3b;
    }

    .registration-page .registration-processed {
        color: #718096;
        font-size: 11px;
    }

    .registration-page .registration-empty {
        padding: 32px 18px;
        color: var(--registration-muted);
        text-align: center;
        line-height: 1.8;
    }

    /* Notifikasi */
    .registration-page .registration-alert {
        padding: 13px 15px;
        margin-bottom: 20px;
        border-left: 4px solid var(--registration-blue);
        border-radius: 3px;
        background: #e8f3f9;
        color: #40586b;
        font-size: 12px;
        line-height: 1.8;
    }

    .registration-page .registration-alert-error {
        border-left-color: #dc3545;
        background: #fff0f0;
        color: #a12626;
    }


.registration-page .registration-toolbar {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 220px;
    gap: 14px;
    margin-bottom: 16px;
}

.registration-page .registration-filter-group {
    display: flex;
    flex-direction: column;
    gap: 7px;
    min-width: 0;
}

.registration-page .registration-filter-group label {
    color: #506477;
    font-size: 12px;
    font-weight: 600;
}

.registration-page .registration-filter-control {
    width: 100%;
    height: 39px;
    box-sizing: border-box;
    padding: 9px 11px;
    border: 1px solid #ccd6e3;
    border-radius: 4px;
    background: #fff;
    color: #354052;
    font-family: inherit;
    font-size: 12px;
}

.registration-page .registration-filter-control:focus {
    outline: none;
    border-color: #348db8;
    box-shadow: 0 0 0 3px rgba(52, 141, 184, .10);
}

.registration-page .registration-filter-control::placeholder {
    color: #9aa5b4;
}

.registration-page .registration-table tr[hidden] {
    display: none;
}

@media (max-width: 600px) {
    .registration-page .registration-toolbar {
        grid-template-columns: minmax(0, 1fr);
    }
}


    @media (max-width: 850px) {
        .registration-page .registration-summary {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 550px) {
        .registration-page .registration-summary {
            grid-template-columns: minmax(0, 1fr);
        }

        .registration-page .registration-table-panel {
            padding: 14px;
        }
    }
</style>

<div class="registration-page">

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="registration-alert" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="registration-alert registration-alert-error" role="alert">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="registration-alert registration-alert-error" role="alert">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- Judul dan jumlah data --}}
    <div class="registration-heading">
        <h2>Data Pendaftaran Mahasiswa</h2>
        <p>
            Total pendaftaran:
            <strong>{{ $totalPendaftaran }}</strong> data
        </p>
    </div>

    {{-- Ringkasan status --}}
    <div class="registration-summary">

        <article class="registration-summary-card">
            <div class="registration-summary-label">Menunggu</div>

            <div class="registration-summary-value registration-count-waiting">
                {{ $jumlahMenunggu }}
            </div>

            <p class="registration-summary-description">
                Menunggu pemeriksaan dan keputusan.
            </p>
        </article>

        <article class="registration-summary-card">
            <div class="registration-summary-label">Diterima</div>

            <div class="registration-summary-value registration-count-accepted">
                {{ $jumlahDiterima }}
            </div>

            <p class="registration-summary-description">
                Pendaftaran yang telah diterima.
            </p>
        </article>

        <article class="registration-summary-card">
            <div class="registration-summary-label">Ditolak</div>

            <div class="registration-summary-value registration-count-rejected">
                {{ $jumlahDitolak }}
            </div>

            <p class="registration-summary-description">
                Pendaftaran yang telah ditolak.
            </p>
        </article>

    </div>

    {{-- Tabel pendaftaran --}}
    <section class="registration-table-panel">

        <div class="registration-table-heading">
            <div>
                <h3>Daftar Pengajuan PDLK</h3>
                <p>Periksa informasi mahasiswa dan status pengajuannya.</p>
            </div>
        </div>


<div class="registration-toolbar">

    <div class="registration-filter-group">
        <label for="registrationSearch">
            Cari Mahasiswa
        </label>

        <input
            type="search"
            id="registrationSearch"
            class="registration-filter-control"
            placeholder="Masukkan NIM, nama, atau tempat PDLK..."
        >
    </div>

    <div class="registration-filter-group">
        <label for="registrationStatusFilter">
            Filter Status
        </label>

        <select
            id="registrationStatusFilter"
            class="registration-filter-control"
        >
            <option value="semua">Semua Status</option>
            <option value="menunggu">Menunggu</option>
            <option value="diterima">Diterima</option>
            <option value="ditolak">Ditolak</option>
        </select>
    </div>

</div>

        <div class="registration-table-wrapper">

            <table class="registration-table">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>NIM</th>
                        <th>Nama Mahasiswa</th>
                        <th>Tempat PDLK</th>
                        <th>Status</th>
                        <th>Tanggal Daftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($daftarPendaftaran as $pendaftaran)

                        @php
                            $id = data_get($pendaftaran, 'id');
                            $nim = data_get($pendaftaran, 'mahasiswa_id')
                                ?? data_get($pendaftaran, 'nim', '-');
                            $nama = data_get($pendaftaran, 'nama', '-');
                            $tempat = data_get($pendaftaran, 'tempat_magang', '-');
                            $status = strtolower(
                                (string) data_get($pendaftaran, 'status', '')
                            );

                            $tanggal = data_get($pendaftaran, 'created_at')
                                ?? data_get($pendaftaran, 'tanggal_daftar');

                            $statusClass = match ($status) {
                                'menunggu' => 'registration-status-waiting',
                                'diterima' => 'registration-status-accepted',
                                'ditolak' => 'registration-status-rejected',
                                default => '',
                            };

                            $tanggalFormat = '-';

                            if ($tanggal) {
                                try {
                                    $tanggalFormat = \Illuminate\Support\Carbon::parse(
                                        $tanggal
                                    )->format('d/m/Y H:i');
                                } catch (\Throwable $e) {
                                    $tanggalFormat = (string) $tanggal;
                                }
                            }
                        @endphp

                        <tr data-status="{{ $status }}">
                            <td>{{ $id ?? '-' }}</td>

                            <td>{{ $nim }}</td>

                            <td class="registration-student-name">
                                {{ $nama }}
                            </td>

                            <td>{{ $tempat }}</td>

                            <td>
                                <span class="registration-status {{ $statusClass }}">
                                    {{ ucfirst($status ?: 'Tidak diketahui') }}
                                </span>
                            </td>

                            <td>{{ $tanggalFormat }}</td>

                            <td>
                                @if ($status === 'menunggu' && $id !== null)

                                    <div class="registration-actions">

                                        <form
                                            method="POST"
                                            action="{{ route('registration.updateStatus', ['id' => $id]) }}"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <input
                                                type="hidden"
                                                name="status"
                                                value="diterima"
                                            >

                                            <button
                                                type="submit"
                                                class="registration-button registration-button-accept"
                                            >
                                                Terima
                                            </button>
                                        </form>

                                        <form
                                            method="POST"
                                            action="{{ route('registration.updateStatus', ['id' => $id]) }}"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <input
                                                type="hidden"
                                                name="status"
                                                value="ditolak"
                                            >

                                            <button
                                                type="submit"
                                                class="registration-button registration-button-reject"
                                            >
                                                Tolak
                                            </button>
                                        </form>

                                    </div>

                                @else
                                    <span class="registration-processed">
                                        Sudah diproses
                                    </span>
                                @endif
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="7">
                                <div class="registration-empty">
                                    Belum ada data pendaftaran PDLK.
                                </div>
                            </td>
                        </tr>

                    @endforelse
                </tbody>

            </table>

        </div>

    </section>


<script>
    (function () {
        const searchInput = document.getElementById('registrationSearch');
        const statusFilter = document.getElementById('registrationStatusFilter');
        const tableBody = document.querySelector(
            '.registration-page .registration-table tbody'
        );

        if (!searchInput || !statusFilter || !tableBody) {
            return;
        }

        const rows = Array.from(
            tableBody.querySelectorAll('tr[data-status]')
        );

        if (rows.length === 0) {
            return;
        }

        const emptyRow = document.createElement('tr');

        emptyRow.innerHTML = `
            <td colspan="7">
                <div class="registration-empty">
                    Tidak ada pendaftaran yang sesuai dengan pencarian.
                </div>
            </td>
        `;

        function filterRegistrations() {
            const keyword = searchInput.value
                .trim()
                .toLocaleLowerCase();

            const selectedStatus = statusFilter.value;
            let visibleCount = 0;

            rows.forEach(function (row) {
                const rowText = row.textContent.toLocaleLowerCase();

                const matchesKeyword =
                    rowText.includes(keyword);

                const matchesStatus =
                    selectedStatus === 'semua' ||
                    row.dataset.status === selectedStatus;

                const visible = matchesKeyword && matchesStatus;

                row.hidden = !visible;

                if (visible) {
                    visibleCount++;
                }
            });

            if (visibleCount === 0) {
                if (!emptyRow.isConnected) {
                    tableBody.appendChild(emptyRow);
                }
            } else {
                emptyRow.remove();
            }
        }

        searchInput.addEventListener(
            'input',
            filterRegistrations
        );

        statusFilter.addEventListener(
            'change',
            filterRegistrations
        );
    })();
</script>

</div>

@endsection
