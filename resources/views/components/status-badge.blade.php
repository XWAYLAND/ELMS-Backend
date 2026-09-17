@props(['status'])

@php
    $labels = [
        'menunggu'         => 'Menunggu',
        'disetujui'       => 'Disetujui',
        'ditolak'         => 'Ditolak',
        'aktif'           => 'Aktif',
        'menunggu_kembali'=> 'Menunggu Kembali',
        'dikembalikan'    => 'Dikembalikan',
        'terlambat'       => 'Terlambat',
    ];
    $colors = [
        'menunggu'         => '#F59E0B',
        'disetujui'       => '#10B981',
        'ditolak'         => '#EF4444',
        'aktif'           => '#3B82F6',
        'menunggu_kembali'=> '#8B5CF6',
        'dikembalikan'    => '#6B7280',
        'terlambat'       => '#DC2626',
    ];
    $label = $labels[$status] ?? ucfirst($status);
    $color = $colors[$status] ?? '#6B7280';
@endphp

<span class="status-badge" style="background: {{ $color }}20; color: {{ $color }};">
    {{ $label }}
</span>