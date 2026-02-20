<!DOCTYPE html>
<html lang="en">
<!-- [Head] start -->

<head>
    <title>{{ $title ?? 'Klinik Qita' }}</title>
    <!-- [Meta] -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description"
        content="Mantis is made using Bootstrap 5 design framework. Download the free admin template & use it for your project.">
    <meta name="keywords"
        content="Mantis, Dashboard UI Kit, Bootstrap 5, Admin Template, Admin Dashboard, CRM, CMS, Bootstrap Admin Template">
    <meta name="author" content="CodedThemes">
<meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- [Favicon] icon -->
    <link rel="icon" href="../assets/images/favicon.svg" type="image/x-icon"> <!-- [Google Font] Family -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap"
        id="main-font-link">
    <!-- [Tabler Icons] https://tablericons.com -->
    <link rel="stylesheet" href="../assets/fonts/tabler-icons.min.css">
    <!-- [Feather Icons] https://feathericons.com -->
    <link rel="stylesheet" href="../assets/fonts/feather.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
    <!-- data tables css -->
    <link rel="stylesheet" href="../assets/css/plugins/dataTables.bootstrap5.min.css">
    <!-- [Font Awesome Icons] https://fontawesome.com/icons -->
    <link rel="stylesheet" href="../assets/fonts/fontawesome.css">
    <!-- [Material Icons] https://fonts.google.com/icons -->
    <link rel="stylesheet" href="../assets/fonts/material.css">
    <!-- [Template CSS Files] -->
    <link rel="stylesheet" href="../assets/css/style.css" id="main-style-link">
    <link rel="stylesheet" href="../assets/css/style-preset.css">
    <!-- [Toastr CSS] -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"> 

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    {{-- modal custom css --}}
    <link rel="stylesheet" href="../assets/wm.css">
    <script src="../assets/wm.js"></script>
 
    @livewireStyles
</head>
<!-- [Head] end -->
<!-- [Body] Start -->

<body data-pc-preset="preset-1" data-pc-direction="ltr" data-pc-theme="light">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- [ Pre-loader ] End -->
    <!-- [ Sidebar Menu ] start -->
   @include('layouts.partials.sidebar')
    <!-- [ Sidebar Menu ] end -->
    <!-- [ Header Topbar ] start -->
    @include('layouts.partials.header')
    <!-- [ Header ] end -->



    <!-- [ Main Content ] start -->
    <div class="pc-container">
        <div class="pc-content">
            <!-- [ breadcrumb ] start -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">{{ $title }}</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/dashboard">Home</a></li> 
                                <li class="breadcrumb-item" aria-current="page">{{ $title }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->

            <!-- [ Main Content ] start -->
            {{ $slot }}
            <!-- [ Main Content ] end -->
        </div>
    </div>
    <!-- [ Main Content ] end -->
    @include('layouts.partials.footer')
    <!-- datatable Js --> 
    <script src="../assets/js/plugins/jquery.dataTables.min.js"></script>
    <script src="../assets/js/plugins/dataTables.bootstrap5.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- toastr Js --> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        window.CWModalDOM = (function(){
        
        let zIndex = 2000;
        
        function open(id){
        const win = document.getElementById(id);
        if(!win) return;
        
        win.style.display = "flex";
        
        setPosition(win); // 🔥 penting
        
        activate(win);
        init(win);
        }
        
        function close(id){
        const win = document.getElementById(id);
        if(!win) return;
        win.style.display = "none";
        }
        
        function init(win){
        
        win.style.zIndex = ++zIndex;
        
        win.addEventListener("mousedown", () => activate(win));
        
        makeDraggable(win);
        makeResizable(win);
        
        win.querySelector(".cw-close").onclick = () => win.style.display = "none";
        
        win.querySelector(".cw-min").onclick = () => {
        win.classList.toggle("cw-minimized");
        };
        
        win.querySelector(".cw-max").onclick = () => {
        if (win.classList.contains("cw-maximized")) {
        win.classList.remove("cw-maximized");
        } else {
        win.classList.remove("cw-minimized");
        win.classList.add("cw-maximized");
        }
        };
        }
        
        function activate(win){
        document.querySelectorAll(".cw-window")
        .forEach(w => w.classList.remove("cw-active"));
        
        win.classList.add("cw-active");
        win.style.zIndex = ++zIndex;
        }
        
        function makeDraggable(win){
        
        const header = win.querySelector(".cw-header");
        let offsetX, offsetY, dragging = false;
        
        header.onmousedown = (e)=>{
        if (win.classList.contains("cw-maximized")) return;
        
        dragging = true;
        offsetX = e.clientX - win.offsetLeft;
        offsetY = e.clientY - win.offsetTop;
        
        document.onmousemove = (e)=>{
        if(!dragging) return;
        
        win.style.left = (e.clientX - offsetX) + "px";
        win.style.top = (e.clientY - offsetY) + "px";
        };
        
        document.onmouseup = ()=>{
        dragging = false;
        document.onmousemove = null;
        };
        };
        }
        
        function makeResizable(win){
        
        const resizer = win.querySelector(".cw-resizer");
        let resizing = false;
        
        resizer.onmousedown = ()=>{
        resizing = true;
        
        document.onmousemove = (e)=>{
        if(!resizing) return;
        
        win.style.width = (e.clientX - win.offsetLeft) + "px";
        win.style.height = (e.clientY - win.offsetTop) + "px";
        };
        
        document.onmouseup = ()=>{
        resizing = false;
        document.onmousemove = null;
        };
        };
        }

        function setPosition(win){
        
        const pos = win.dataset.position;
        const margin = 20;
        
        const w = win.offsetWidth;
        const h = win.offsetHeight;
        
        switch(pos){
        
        case "top-left":
        win.style.top = margin + "px";
        win.style.left = margin + "px";
        break;
        
        case "top-right":
        win.style.top = margin + "px";
        win.style.left = (window.innerWidth - w - margin) + "px";
        break;
        
        case "bottom-left":
        win.style.top = (window.innerHeight - h - margin) + "px";
        win.style.left = margin + "px";
        break;
        
        case "bottom-right":
        win.style.top = (window.innerHeight - h - margin) + "px";
        win.style.left = (window.innerWidth - w - margin) + "px";
        break;
        
        default: // center
        win.style.top = (window.innerHeight / 2 - h / 2) + "px";
        win.style.left = (window.innerWidth / 2 - w / 2) + "px";
        }
        }
        
        return { open, close };
        
        })();

        window.addEventListener('open-cw', e => {
            CWModalDOM.open(e.detail);
        });

        

        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        };
        
        @if(session('success'))
            toastr.success("{{ session('success') }}");
        @endif
    
        @if(session('error'))
            toastr.error("{{ session('error') }}");
        @endif
    
        @if(session('warning'))
            toastr.warning("{{ session('warning') }}");
        @endif
    
        @if(session('info'))
            toastr.info("{{ session('info') }}");
        @endif
    </script>

     <!-- Required Js -->
    <script src="../assets/js/plugins/popper.min.js"></script>
    <script src="../assets/js/plugins/simplebar.min.js"></script>
    <script src="../assets/js/plugins/bootstrap.min.js"></script>
    <script src="../assets/js/fonts/custom-font.js"></script>
    <script src="../assets/js/pcoded.js"></script>
    <script src="../assets/js/plugins/feather.min.js"></script>

    <script>
        layout_change('light');
    </script>

    <script>
        change_box_container('false');
    </script>

    <script>
        layout_rtl_change('false');
    </script>

    <script>
        preset_change("preset-1");
    </script>

    <script>
        font_change("Public-Sans");
    </script>
   @livewireScripts 

</body>
<!-- [Body] end -->

</html>