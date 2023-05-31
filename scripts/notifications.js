$(document).ready(function () {
    $(".form-check-notification").change(function () {
        if ($(this).is(":checked")) {
            $(this).parent().parent().addClass("select-notifications")
        } else {
            $(this).parent().parent().removeClass("select-notifications")
        }
    })
    $(".trash-notifications").click(function () {
        if ($(this).parent().parent().parent().hasClass("tr-notification-unread")) {
            return modale(
                "Vous ne pouvez pas supprimer",
                "Vous ne pouvez pas supprimer les notifications que vous n'avez pas encore lus, lisez d'abord le notification, puis vous pourrez le supprimer.",
                "modal-danger"
            )
        }
        $(this).parent().parent().parent().fadeOut()
    })
    $("#select-input").change(select_notifications)
    $("#select-select").change(select_notifications)
    function select_notifications() {
        if ($("#select-select").val() == "all") {
            if ($("#select-input").is(":checked")) {
                $(".form-check-notification").prop('checked', true)
                $(".form-check-notification").parent().parent().addClass("select-notifications")
            } else {
                $(".form-check-notification").prop('checked', false)
                $(".form-check-notification").parent().parent().removeClass("select-notifications")
            }
        } else if ($("#select-select").val() == "unread") {
            if ($("#select-input").is(":checked")) {
                $(".tr-notification").each(function () {
                    if ($(this).hasClass('tr-notification-unread')) {
                        $(this).children(":first").children(":first").prop('checked', true)
                        $(this).addClass("select-notifications")
                    } else {
                        $(this).children(":first").children(":first").prop('checked', false)
                        $(this).removeClass("select-notifications")
                    }
                })
            } else {
                $(".form-check-notification").prop('checked', false)
                $(".form-check-notification").parent().parent().removeClass("select-notifications")
            }
        } else if ($("#select-select").val() == "read") {
            if ($("#select-input").is(":checked")) {
                $(".tr-notification").each(function () {
                    if ($(this).hasClass('tr-notification-read')) {
                        $(this).children(":first").children(":first").prop('checked', true)
                        $(this).addClass("select-notifications")
                    } else {
                        $(this).children(":first").children(":first").prop('checked', false)
                        $(this).removeClass("select-notifications")
                    }
                })
            } else {
                $(".form-check-notification").prop('checked', false)
                $(".form-check-notification").parent().parent().removeClass("select-notifications")
            }
        }
    }
    $("#del-select").click(() => {
        var error = 0;
        $(".form-check-notification").each(function () {
            if ($(this).is(':checked')) {
                if ($(this).parent().parent().hasClass("tr-notification-unread")) {
                    return error = 1
                }
            }
        })
        if (error) {
            modale(
                "Vous ne pouvez pas supprimer",
                "Vous ne pouvez pas supprimer les notifications que vous n'avez pas encore lus, lisez d'abord le notification, puis vous pourrez le supprimer.",
                "modal-danger"
            )

        } else {
            $(".form-check-notification").each(function () {
                if ($(this).is(':checked')) {
                    $(this).parent().parent().fadeOut()
                }
            })
        }

    })
    $("#read-select").click(() => {
        $(".form-check-notification").each(function () {
            if ($(this).is(':checked')) {
                $(this).parent().parent().removeClass("tr-notification-unread")
                $(this).parent().parent().addClass("tr-notification-read")
                // m2
                $(this).parent().parent().removeClass("select-notifications")
                $(this).prop('checked', false)
                // end m2
            }
        })
    })
    
    


    $(".td-notif-input").click((e) => {
        e.stopPropagation()
    })
    $(".notification-short-del-read").click((e) => {
        e.stopPropagation()
    })
    // modale error

    // ------------
})