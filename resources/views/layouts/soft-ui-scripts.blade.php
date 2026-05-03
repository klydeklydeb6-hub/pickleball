<script src="{{ asset('soft-ui-dashboard-main/assets/js/core/popper.min.js') }}"></script>
<script src="{{ asset('soft-ui-dashboard-main/assets/js/core/bootstrap.min.js') }}"></script>
<script src="{{ asset('soft-ui-dashboard-main/assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
<script src="{{ asset('soft-ui-dashboard-main/assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
<script>
    const softUiPlatformIsWindows = navigator.platform.indexOf('Win') > -1;

    if (softUiPlatformIsWindows && document.querySelector('#sidenav-scrollbar') && window.Scrollbar) {
        Scrollbar.init(document.querySelector('#sidenav-scrollbar'), {
            damping: '0.5',
        });
    }
</script>
<script src="{{ asset('soft-ui-dashboard-main/assets/js/soft-ui-dashboard.min.js?v=1.1.0') }}"></script>
@stack('scripts')
