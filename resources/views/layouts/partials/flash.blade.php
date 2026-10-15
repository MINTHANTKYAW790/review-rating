@if (session()->has('success'))
<script type="module">
    $(document).ready(function() {
        toastr.success(@json(session('success')));
    });
</script>
@endif

@if (session()->has('error'))
<script type="module">
    $(document).ready(function() {
        toastr.options.closeButton = true;
        toastr.options.timeOut = 10000;
        toastr.error(@json(session('error')));
    });
</script>
@endif

@if (session()->has('swalSuccess'))
<script type="module">
    Swal.fire({
        title: "Good job!",
        text: @json(session('swalSuccess')),
        icon: "success",
        iconColor: "#6868AC",
        confirmButtonColor: "#6868AC",
    });
</script>
@endif

<script type="module">
    document.addEventListener('DOMContentLoaded', function() {
        window.addEventListener('show-toast', event => {
            const type = event.detail.type;
            const message = event.detail.message;
            if (type === 'success') {
                toastr.success(message);
            } else if (type === 'error') {
                toastr.error(message);
            }
        });
    });
</script>
