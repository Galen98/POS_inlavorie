$(document).ready(function(){
    //size image validation
    $('#logo').on('change', function() {    
        const size = 
           (this.files[0].size / 1024 / 1024).toFixed(2);
        if (size > 2) {
            Swal.fire({
                title: 'Error',
                text: 'File tidak boleh melebihi 2MB',
                icon: 'error',
                confirmButtonText: 'Ok'
            });
            $("#logo").val(null)
        }
    });
})