<link rel="stylesheet" href="{{ asset('assets/back/css/components/loader.css') }}">

<script>
    window.addEventListener('load', function() {
        const loader = document.getElementById('loader-wrapper');
        if (loader) {
            setTimeout(() => {
                loader.style.opacity = '0';
                setTimeout(() => {
                    loader.style.display = 'none';
                }, 500);
            }, 300); // Small delay to enjoy the loader
        }
    });
</script>
