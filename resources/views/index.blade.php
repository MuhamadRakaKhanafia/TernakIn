@extends('layouts.app')

@section('title', 'TernakIN - Dashboard')
@section('page-title', 'Dashboard TernakIN')
@section('content')

            <!-- Dashboard Cards -->
            <div class="dashboard-cards">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Total Penyakit</div>
                        <div class="card-icon" style="background-color: var(--primary-color);">
                            <i class="fas fa-viruses"></i>
                        </div>
                    </div>
                    <div class="card-content">142</div>
                    <a href="{{ route('diseases.index') }}" class="card-footer">
                        Lihat detail <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Obat Tersedia</div>
                        <div class="card-icon" style="background-color: var(--accent-color);">
                            <i class="fas fa-pills"></i>
                        </div>
                    </div>
                    <div class="card-content">87</div>
                    <a href="{{ route('medicines.index') }}" class="card-footer">
                        Lihat detail <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Artikel</div>
                        <div class="card-icon" style="background-color: var(--primary-light);">
                            <i class="fas fa-newspaper"></i>
                        </div>
                    </div>
                    <div class="card-content">56</div>
                    <a href="{{ route('articles.index') }}" class="card-footer">
                        Lihat detail <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Sesi AI Chat</div>
                        <div class="card-icon" style="background-color: #e76f51;">
                            <i class="fas fa-robot"></i>
                        </div>
                    </div>
                    <div class="card-content">1,248</div>
                    <a href="{{ route('chat.index') }}" class="card-footer">
                        Lihat detail <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Recent Diseases Table -->
            <div class="table-container">
                <div class="table-header">
                    <div class="table-title">Penyakit Terbaru</div>
                    <a href="{{ route('diseases.index') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Baru
        </a>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Nama Penyakit</th>
                            <th>Jenis Hewan</th>
                            <th>Gejala Utama</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Antraks</td>
                            <td>Sapi</td>
                            <td>Demam tinggi, pembengkakan</td>
                            <td><span class="status active">Aktif</span></td>
                            <td>
                                <i class="fas fa-edit action-icon edit" title="Edit"></i>
                                <i class="fas fa-trash action-icon delete" title="Hapus"></i>
                            </td>
                        </tr>
                        <tr>
                            <td>Flu Burung</td>
                            <td>Unggas</td>
                            <td>Lesu, nafsu makan turun</td>
                            <td><span class="status active">Aktif</span></td>
                            <td>
                                <i class="fas fa-edit action-icon edit" title="Edit"></i>
                                <i class="fas fa-trash action-icon delete" title="Hapus"></i>
                            </td>
                        </tr>
                        <tr>
                            <td>Scabies</td>
                            <td>Kambing</td>
                            <td>Gatal, kerontokan bulu</td>
                            <td><span class="status pending">Perlu Update</span></td>
                            <td>
                                <i class="fas fa-edit action-icon edit" title="Edit"></i>
                                <i class="fas fa-trash action-icon delete" title="Hapus"></i>
                            </td>
                        </tr>
                        <tr>
                            <td>PMK</td>
                            <td>Sapi & Kambing</td>
                            <td>Lepuh di mulut, pincang</td>
                            <td><span class="status active">Aktif</span></td>
                            <td>
                                <i class="fas fa-edit action-icon edit" title="Edit"></i>
                                <i class="fas fa-trash action-icon delete" title="Hapus"></i>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Recent Activity -->
            <div class="table-container">
                <div class="table-header">
                    <div class="table-title">Aktivitas Terbaru</div>
                </div>
                <ul class="activity-list">
                    <li class="activity-item">
                        <div class="activity-icon" style="background-color: var(--primary-color);">
                            <i class="fas fa-plus"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Penyakit baru ditambahkan</div>
                            <div class="activity-time">2 jam yang lalu</div>
                        </div>
                    </li>
                    <li class="activity-item">
                        <div class="activity-icon" style="background-color: var(--accent-color);">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Data obat diperbarui</div>
                            <div class="activity-time">5 jam yang lalu</div>
                        </div>
                    </li>
                    <li class="activity-item">
                        <div class="activity-icon" style="background-color: var(--primary-light);">
                            <i class="fas fa-file-medical"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Artikel baru dipublikasi</div>
                            <div class="activity-time">Kemarin, 14:30</div>
                        </div>
                    </li>
                    <li class="activity-item">
                        <div class="activity-icon" style="background-color: #e76f51;">
                            <i class="fas fa-robot"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Sesi AI Chat meningkat 15%</div>
                            <div class="activity-time">2 hari yang lalu</div>
                        </div>
                    </li>
                </ul>
            </div>
@endsection
