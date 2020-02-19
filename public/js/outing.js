$(document).ready(function () {
    $('#dtVerticalScrollExample').DataTable({
        "scrollY": "400px",
        "scrollCollapse": true,
    });
    $('.dataTables_length').addClass('bs-select');
});