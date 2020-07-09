$(function () {
    let q = false;
    let u = false;
    let n = false;

    $("#startpage_top").css("display", "flex");
    $("#userpage_top").css("display", "flex");

    $("#register_button").on("click", function () {
        $("#startpage_top").css("display", "none");
        $("#startpage_register").css("display", "flex");
    });

    $("#login_button").on("click", function () {
        $("#startpage_top").css("display", "none");
        $("#startpage_login").css("display", "flex");
    });

    $("html").keydown(function (e) {
        if (e.keyCode === 81) {
            q = true;
        }
        if (e.keyCode === 85) {
            u = true;
        }
        if (e.keyCode === 78) {
            n = true;
        }
        if (q == true && u == true && n == true) {
            $("#hidden").css("display", "flex");
        }
    });

    $("html").keyup(function (e) {
        if (e.keyCode === 81) {
            q = false;
        }
        if (e.keyCode === 85) {
            u = false;
        }
        if (e.keyCode === 78) {
            n = false;
        }
    });

    $("#newgame_button").on("click", function () {
        $("#userpage_top").css("display", "none");
        $("#userpage_newgame").css("display", "flex");
    });

    $("#load_button").on("click", function () {
        $("#userpage_top").css("display", "none");
        $("#userpage_load").css("display", "flex");
    });

    $("#compete_button").on("click", function () {
        $("#userpage_top").css("display", "none");
        $("#userpage_compete").css("display", "flex");
    });

    $(".returns").on("click", function () {
        $(".button_boxes").css("display", "none")
        $("#userpage_top").css("display", "flex");
    });


});