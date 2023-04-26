
 


{{ $js_scripts ?? '' }}

<script type="text/javascript">
    $(document).ready(function(){
        
        
        $('.data-table').dataTable({
            dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>tp",
            responsive: true,
            // "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
            buttons: [
                {extend: 'copy',className: 'btn-sm'},
                {extend: 'csv',title: 'Download', className: 'btn-sm'},
                /*{extend: 'pdf', title: 'Download', className: 'btn-sm'},
                {extend: 'print',className: 'btn-sm'}*/
            ]
        });

        {{ $slot }}

    });

</script>
