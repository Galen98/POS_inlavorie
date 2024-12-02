$(document).ready(function(){
    $("#logo").hide()
    //account
    $("#edit-account").on('click', function(){
        $(this).hide()
        $("#div-submit-account").append('<button type="button" id="btn-action-primer" class="btn btn-light rounded-pill mt-4"><i class="bi bi-box-arrow-down"></i> Simpan</button>');
        $(".editacc").attr('readonly', false)
        $(".editacc").removeClass("text-muted")
    })

    $("#div-submit-account").on('click', '#btn-action-primer', function(){
        $(this).remove()
        $("#edit-account").show()
        $(".editacc").attr('readonly', true)
        $(".editacc").addClass("text-muted")
        $("#loading-spinner").show()
        var name = $("#name").val()
        var noHp = $("#phone").val()
        var csrf_token = $("#csrf_token").val()
        $.ajax({
            url:'/api/update-account',
            method:'POST',
            data: {
                name: name,
                noHp: noHp,
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

    //profile
    $("#edit-profile").on('click', function(){
        $(this).hide()
        $("#logo").show()
        $("#div-submit-profile").append('<button type="submit" id="btn-action-primer" class="btn btn-light rounded-pill mt-4"><i class="bi bi-box-arrow-down"></i> Simpan</button>');
        $(".editprof").attr('readonly', false)
        $(".editprof").removeClass("text-muted")
    })

    // $("#div-submit-profile").on('click', '#btn-action-primer', function(){
    //     $(this).remove()
    //     $("#edit-profile").show()
    //     $(".editprof").attr('readonly', true)
    //     $(".editprof").addClass("text-muted")
    //     $("#profile_pic").hide()
    //     $alamat_lengkap = $("#alamat_lengkap").val()
    //     $kode_pos = $("#kode_pos").val()
    // })
});