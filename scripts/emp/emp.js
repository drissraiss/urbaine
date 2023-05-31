$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();
    $("#menu ul li, .nav-phone li").each(function (i) {
        $(this).click(() => {
            other($(this))
        })
    })

    function other(li) {
        $("#menu ul li, .nav-phone li").each(function () {
            $(this).removeClass("active-now")
        })
        li.addClass("active-now")
    }

    
    
    // --------------------------------------------------------------------------
    $.ajax({
        url: "../php/choices/employes_ch.php?choice=notifications",
        //url: "../php/choices/employes_ch.php?choice=notifications",
        success: function (result) {
            $("#section-content").html(result);
        }
    });
    $('.btn-notifications').click(() => {
        $.ajax({
            url: "../php/choices/employes_ch.php?choice=notifications",
            success: function (result) {
                $("#section-content").html(result);
            }
        });
    })
    $('.btn-messages').click(() => {
        $.ajax({
            url: "../php/choices/employes_ch.php?choice=messages",
            success: function (result) {
                $("#section-content").html(result);
            }
        });
    })
    $('.btn-profile').click(() => {
        $.ajax({
            url: "../php/choices/employes_ch.php?choice=profile",
            success: function (result) {
                $("#section-content").html(result);
            }
        });
    })
    $('.btn-demandes').click(() => {
        $.ajax({
            url: "../php/choices/employes_ch.php?choice=demandes",
            success: function (result) {
                $("#section-content").html(result);
            }
        });
    })
    $('.btn-plaines').click(() => {
        $.ajax({
            url: "../php/choices/employes_ch.php?choice=plaines",
            success: function (result) {
                $("#section-content").html(result);
            }
        });
    })
    // -------------------------------------------------------
})