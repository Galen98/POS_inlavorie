$(document).ready(function(){
    $(".btnnext").click(function(){
        $('html, body').animate({
            scrollTop: $('.first-hero').offset().top
        }, 1000);
    });
    $('#myModal').modal('show');

    $("#contact").click(function(){
        $("#contactModal").modal("show")
    })
})