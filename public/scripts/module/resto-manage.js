$(document).ready(function(){
//list page
$('input[name="status"]').on('change', function(){
    $('input[name="status"]').each(function() {
        $("#loading-spinner").show()
        var statusId = $(this).attr('data-id'); 
        var isChecked = $(this).prop('checked');
        var csrf_token = $("#csrf_token").val()
        let check = 0
        if($(this).is(":checked")){
            check = 1
        } else {
            check = 0
        }
        $.ajax({
            url:'/api/active-resto',
            method:'POST',
            data: {
                id: statusId,
                checked: check,
                csrf_token: csrf_token
            },
            success: function(r) {
                $("#loading-spinner").hide()
            },
            error: function(e) {
                console.log(e)
            }
        })
    });
})

//view page
$("#hapus-data-resto").on('click', function(){
    var id = $(this).attr('data-id'); 
    $('#confirmDeleteRestoButton').val(id); 
    $('#confirmDeleteRestoModal').modal('show');
})
});