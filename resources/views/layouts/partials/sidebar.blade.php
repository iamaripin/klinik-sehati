<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="/dashboard" class="b-brand text-primary">
                <!-- ========   Change your logo from here   ============ -->
                <img src="../assets/images/logo-dark.svg" class="img-fluid logo-lg" alt="logo">
            </a>
        </div>
        <div class="navbar-content">
           <ul class="pc-navbar">
            <li class="pc-item">
                <a href="/dashboard" class="pc-link">
                    <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
                    <span class="pc-mtext">Dashboard</span>
                </a>
            </li>
            <li class="pc-item">
                <a href="/patient/medical-record" class="pc-link">
                    <span class="pc-micon"><i class="ti ti-box-padding"></i></span>
                    <span class="pc-mtext">Rekam Medis</span>
                </a>
            </li>
            
            <li class="pc-item pc-caption">
                <label>Master Data</label>
                <i class="ti ti-dashboard"></i>
            </li>
            <li class="pc-item pc-hasmenu">
                <a href="#!" class="pc-link"><span class="pc-micon"><i class="ti ti-leaf"></i></span><span class="pc-mtext">Master
                        Data</span><span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
                <ul class="pc-submenu">
                    <li class="pc-item"><a class="pc-link" href="/master/patient">Data Pasien</a></li>
                    <li class="pc-item"><a class="pc-link" href="/master/doctor">Data Dokter</a></li>
                    <li class="pc-item"><a class="pc-link" href="/master/users">Data User</a></li>
                </ul>
            </li>
                        
            @if(auth()->user()->role == 'pharmacy'|| auth()->user()->role == 'dev')
                    <li class="pc-item pc-hasmenu">
                <a href="#!" class="pc-link"><span class="pc-micon"><i class="ti ti-bandage"></i></span><span
                        class="pc-mtext">Stock & Inventory</span><span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
                <ul class="pc-submenu">
                    <li class="pc-item"><a class="pc-link" href="/master/inventory">Data Inventory</a></li>
                    <li class="pc-item"><a class="pc-link" href="#!">Pemakaian</a></li>
                    <li class="pc-item"><a class="pc-link" href="/master/inventory-category">Kategori Obat </a></li>
                    <li class="pc-item"><a class="pc-link" href="/master/suppliers">Data Supplier</a></li>
                    <li class="pc-item"><a class="pc-link" href="#!">Stock Opname</a></li>
                </ul>
            </li>
            @endif

            @if(auth()->user()->role == 'admin' || auth()->user()->role == 'dev')
           <li class="pc-item pc-caption">
                <label>Admission</label>
                <i class="ti ti-dashboard"></i>
            </li>
            <li class="pc-item">
                <a href="/registration" class="pc-link">
                    <span class="pc-micon"><i class="ti ti-ad-2"></i></span>
                    <span class="pc-mtext">Registrasi</span>
                </a>
            </li> 
            <li class="pc-item">
                <a href="/payment" class="pc-link">
                    <span class="pc-micon"><i class="ti ti-zoom-money"></i></span>
                    <span class="pc-mtext">Pembayaran</span>
                </a>
            </li>
            <li class="pc-item">
                <a href="/display-antrian" class="pc-link" target="_blank">
                    <span class="pc-micon"><i class="ti ti-box-multiple-1"></i></span>
                    <span class="pc-mtext">Display Antrian</span>
                </a>
            </li>
{{--             
            <li class="pc-item pc-hasmenu">
                <a href="#!" class="pc-link"><span class="pc-micon"><i class="ti ti-ad-2"></i></span><span
                        class="pc-mtext">Registrasi</span><span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
                <ul class="pc-submenu">
                    <li class="pc-item"><a class="pc-link" href="/registration">Rawat Jalan</a></li>
                    <li class="pc-item"><a class="pc-link" href="#">Rawat Inap</a></li>
                </ul>
            </li> --}}
        
            {{-- <li class="pc-item pc-hasmenu">
                <a href="#!" class="pc-link"><span class="pc-micon"><i class="ti ti-calendar-off"></i></span><span
                        class="pc-mtext">Pasien Pulang</span><span class="pc-arrow"><i
                            data-feather="chevron-right"></i></span></a>
                <ul class="pc-submenu">
                    <li class="pc-item"><a class="pc-link" href="#!">Rawat Jalan</a></li>
                    <li class="pc-item"><a class="pc-link" href="#!">Rawat Inap</a></li>
                    <li class="pc-item"><a class="pc-link" href="#!">Pembayaran</a></li>
                </ul>
            </li> --}}
            @endif 
        
            @if(auth()->user()->role == 'doctor'|| auth()->user()->role == 'dev')
            <li class="pc-item pc-caption">
                <label>Dokter</label>
                <i class="ti ti-dashboard"></i>
            </li>
             <li class="pc-item">
                <a href="/diagnosa" class="pc-link">
                    <span class="pc-micon"><i class="ti ti-report-medical"></i></span>
                    <span class="pc-mtext">Diagnosa & Tindakan</span>
                </a>
            </li> 
            @endif

            @if(auth()->user()->role == 'nurse'|| auth()->user()->role == 'dev')
            <li class="pc-item pc-caption">
                <label>Perawat</label>
                <i class="ti ti-dashboard"></i>
            </li>
            <li class="pc-item">
                <a href="/anamnesa" class="pc-link">
                    <span class="pc-micon"><i class="ti ti-report-medical"></i></span>
                    <span class="pc-mtext">Anamnesa Perawat</span>
                </a>
            </li> 
            @endif
 
        </ul>
        </div>
    </div>
</nav>