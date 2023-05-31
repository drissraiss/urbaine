function modale(header_msg, body_msg, modale_type) {
    $("#myModal #modal-header").text(header_msg)
    $("#myModal #modal-body").text(body_msg)
    $("#myModal").removeClass()
    $("#myModal").addClass(modale_type)


    $("#myModal").fadeIn();
    $("#close").click(function () {
        $("#myModal").hide();
    })

    window.onclick = function (event) {
        if (event.target == document.getElementById('myModal')) {
            $('#myModal').css('display', 'none');
        }
    }
}