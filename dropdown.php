<!-- Select2 -->
<link rel="stylesheet" href="assets/adminlte/plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="assets/adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

class="form-control select2"

 <!-- Select2 -->
 <script src="assets/adminlte/plugins/select2/js/select2.full.min.js"></script>

 // Select2 Dropdown
$(document).on('select2:open', () => {
            document.querySelector('.select2-search__field').focus();
        });

        $(document).ready(function() {
            //Initialize Select2 Elements
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%',
                containerCssClass: 'height-40px',
            });
        });
