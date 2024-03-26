$(document).ready(function() {
    "use strict";
    
    var $appAdminWrap = $(".app-admin-wrap");
    var $html = $("html");
    var $body = $("body");
    var $customizer = $(".customizer");
    var $sidebarColor = $(".sidebar-colors a.color");
    var direction = localStorage['dir'];
    var dark = localStorage['dark'];

    if(direction == "rtl"){
        $html.attr("dir", "rtl");
        $("#rtl-checkbox").attr('checked', true);
    }else{
        $html.attr("dir", "ltr");
        $("#rtl-checkbox").attr('checked', false);
    }

    if(dark == "yes"){
        $body.addClass("dark-theme");
        $("#dark-checkbox").attr('checked', true);
    }else{
        $body.removeClass("dark-theme");
        $("#dark-checkbox").attr('checked', false);
    }
    
    // Change sidebar color
    $sidebarColor.on("click", function(e) {
        e.preventDefault();
        $appAdminWrap.removeClass(function(index, className) {
            return (className.match(/(^|\s)sidebar-\S+/g) || []).join(" ");
        });
        $appAdminWrap.addClass($(this).data("sidebar-class"));
        $sidebarColor.removeClass("active");
        $(this).addClass("active");
    });

    // Change Direction RTL/LTR
    $("#rtl-checkbox").change(function() {
        if (this.checked) {
            localStorage['dir'] = "rtl";
            $html.attr("dir", "rtl");
        } else {
            localStorage['dir'] = "ltr";
            $html.attr("dir", "ltr");
        }
    });

    // Dark version
    $("#dark-checkbox").change(function() {
        if (this.checked) {
            localStorage['dark'] = "yes";
            $body.addClass("dark-theme");
        } else {
            localStorage['dark'] = "no";
            $body.removeClass("dark-theme");
        }
    });

    let $themeLink = $("#gull-theme");
  
    $(".bootstrap-colors .color").on("click", function(e) {
        e.preventDefault();
        let color = $(this).attr("title");
        console.log(color);
        let fileUrl = "assets/styles/css/themes/" + color + ".min.css";
        if (localStorage) {
            gullUtils.changeCssLink("gull-theme", fileUrl);
        } else {
            $themeLink.attr("href", fileUrl);
        }
    });

    // Toggle customizer
    $(".handle").on("click", function(e) {
        $customizer.toggleClass("open");
    });
});
