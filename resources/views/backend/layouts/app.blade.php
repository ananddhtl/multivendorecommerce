<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    @include('backend.includes.favicon')
    @include('backend.includes.plugins')
    @yield('plugins')
    @include('backend.includes.css')
    @yield('css')
    <title>@yield('PageTitle')</title>
</head>
@include('sweetalert::alert')

<body>
<!--wrapper-->
<div class="wrapper">
    <!--sidebar wrapper -->
    @include('backend.includes.sidebar')
    <!--end sidebar wrapper -->
    <!--start header -->
    @include('backend.includes.header')
    <!--end header -->
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
           @yield('content')
        </div>
    </div>
    <!--end page wrapper -->
    <!--start overlay-->
    <div class="overlay toggle-icon"></div>
    <!--end overlay-->
    <!--Start Back To Top Button--> <a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
    <!--End Back To Top Button-->
    @include('backend.includes.footer')
</div>
<!--end wrapper-->
<!--start switcher-->
<div class="switcher-wrapper">
    <div class="switcher-btn"> <i class='bx bx-cog bx-spin'></i>
    </div>
   
</div>
<!--end switcher-->
@yield('AjaxScript')

{{--notifications-script --}}
<script type="text/javascript">

        $(document).ready(function() {
            $('#mark-as-read').click(function() {
                $('.alert-count').remove();

                $.ajax({
                    url: "{{route('read-all-notifications')}}",
                    method: 'GET',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success : function(response)
                    {
                    },
                    error: function(response) {
                    }
                });
            });
        });
    </script>

@include('backend.includes.js')
@yield('js')
</body>

</html>
