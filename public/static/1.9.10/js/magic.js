/*
 phpipam 1.9.10 2017-03-02-10:56:28 
*/
$(document).ready(function() {
    function showSpinner() {
        $("div.loading").show();
    }
    function hideSpinner() {
        $("div.loading").fadeOut("fast");
    }
    function safe_string(str) {
        return str.replace(/[^a-z0-9_]|[ \s\t\n\r]/g, function(char) {
            switch (char) {
              case " ":
              case "-":
              case "s":
              case "\t":
              case "\n":
              case "\r":
                return "_";

              default:
                return "";
            }
        });
    }
    function open_popup(popup_class, target_script, post_data, secondary) {
        return secondary = "undefined" != typeof secondary && secondary, showSpinner(), 
        $.post(target_script, post_data, function(data) {
            showPopup("popup_w" + popup_class, data, secondary), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }
    function submit_popup_data(result_div, target_script, post_data, reload) {
        return showSpinner(), reload = "undefined" == typeof reload || reload, $.post(target_script, post_data, function(data) {
            $("div" + result_div).html(data).slideDown("fast"), reload && data.search("alert-danger") == -1 && data.search("error") == -1 && data.search("alert-warning") == -1 ? setTimeout(function() {
                window.location.reload();
            }, 1500) : hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }
    function reload_window(data) {
        data.search("alert-danger") == -1 && data.search("error") == -1 && data.search("alert-warning") == -1 ? setTimeout(function() {
            window.location.reload();
        }, 1e3) : hideSpinner();
    }
    function showError(errorText) {
        $("div.jqueryError").fadeIn("fast"), errorText.length > 0 && $(".jqueryErrorText").html(errorText).show(), 
        hideSpinner();
    }
    function hideError() {
        $(".jqueryErrorText").html(), $("div.jqueryError").fadeOut("fast");
    }
    function hideTooltips() {
        $(".tooltip").hide();
    }
    function showPopup(pClass, data, secondary) {
        if (showSpinner(), secondary === !0) var oclass = "#popupOverlay2"; else var oclass = "#popupOverlay";
        $(oclass).fadeIn("fast"), data !== !1 && "undefined" != typeof data && $(oclass + " ." + pClass).html(data), 
        1 != secondary && $("#popupOverlay2 > div").empty(), $(oclass + " ." + pClass).fadeIn("fast"), 
        $("body").addClass("stop-scrolling");
    }
    function hidePopup(pClass, secondary) {
        if (secondary === !0) var oclass = "#popupOverlay2"; else var oclass = "#popupOverlay";
        $(oclass + " ." + pClass).fadeOut("fast"), $(oclass + " > div").empty(), $("body").removeClass("stop-scrolling");
    }
    function hidePopups() {
        $("#popupOverlay").fadeOut("fast"), $("#popupOverlay2").fadeOut("fast"), $("#popupOverlay > div").empty(), 
        $("#popupOverlay2 > div").empty(), $(".popup").fadeOut("fast"), $("body").removeClass("stop-scrolling"), 
        hideSpinner();
    }
    function hidePopup2() {
        $("#popupOverlay2").fadeOut("fast"), $("#popupOverlay2 .popup").fadeOut("fast"), 
        $("#popupOverlay2 > div").empty(), hideSpinner(), $("body").removeClass("stop-scrolling");
    }
    function hidePopupMasks() {
        $(".popup_wmasks").fadeOut("fast"), hideSpinner();
    }
    function randomPass() {
        var chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890", pass = "", x, i;
        for (x = 0; x < 10; x++) i = Math.floor(62 * Math.random()), pass += chars.charAt(i);
        return pass;
    }
    function createCookie(name, value, days) {
        var date, expires;
        if ("undefined" == typeof days) date = new Date(), date.setTime(date.getTime() + 24 * days * 60 * 60 * 1e3), 
        expires = "; expires=" + date.toGMTString(); else var expires = "";
        document.cookie = name + "=" + value + expires + "; path=/";
    }
    function readCookie(name) {
        for (var nameEQ = name + "=", ca = document.cookie.split(";"), i = 0; i < ca.length; i++) {
            for (var c = ca[i]; " " == c.charAt(0); ) c = c.substring(1, c.length);
            if (0 === c.indexOf(nameEQ)) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }
    function update_subnet_structure_cookie(action, cid) {
        var s_cookie = readCookie("sstr");
        if ("undefined" != typeof s_cookie && null != s_cookie && 0 !== s_cookie.length || (s_cookie = "|"), 
        "add" == action) {
            for (var arr = s_cookie.split("|"), exists = !1, i = 0; i < arr.length; i++) arr[i] == cid && (exists = !0);
            0 == exists && (s_cookie += cid + "|");
        } else "remove" == action && (s_cookie = s_cookie.replace("|" + cid + "|", "|"));
        createCookie("sstr", s_cookie, 365);
    }
    function search_execute(loc) {
        if (showSpinner(), "topmenu" == loc) var ip = $(".searchInput").val(), form_name = "searchSelect"; else var ip = $("form#search .search").val(), form_name = "search";
        ip = ip.replace(/\//g, "%252F");
        var addresses = $("#" + form_name + " input[name=addresses]").is(":checked") ? "on" : "off", subnets = $("#" + form_name + " input[name=subnets]").is(":checked") ? "on" : "off", vlans = $("#" + form_name + " input[name=vlans]").is(":checked") ? "on" : "off", vrf = $("#" + form_name + " input[name=vrf]").is(":checked") ? "on" : "off", pstn = $("#" + form_name + " input[name=pstn]").is(":checked") ? "on" : "off";
        createCookie("search_parameters", '{"addresses":"' + addresses + '","subnets":"' + subnets + '","vlans":"' + vlans + '","vrf":"' + vrf + '","pstn":"' + pstn + '"}', 365);
        var prettyLinks = $("#prettyLinks").html();
        send_to("Yes" == prettyLinks ? "tools/search/" + ip : "?page=tools&section=search&ip=" + ip);
    }
    function list_search_execute(page, section) {
        var prettyLinks = $("meta[name=application-name]").attr("data-prettylinks"), base = $("meta[name=application-name]").attr("data-base"), srch = $("input#list_search_term").val();
        if (!page) var page = $("meta[name=application-name]").attr("data-page");
        if (!section) var section = $("meta[name=application-name]").attr("data-section");
        srch || (srch = "");
        var loc = base + "?page=" + page + "&section=" + section + "&search=" + srch, ploc = base + page + "/" + section + "/search/" + srch;
        "Yes" == prettyLinks ? (console.log(ploc), send_to(ploc)) : (console.log(loc), send_to(loc));
    }
    function table_page_size(page, section, count, pagenum) {
        createCookie("table-page-size", count, 365);
        var prettyLinks = $("#prettyLinks").html(), srch = $("input#list_search_term").val(), base = $("meta[name=application-name]").attr("data-base");
        if (!page) var page = $("meta[name=application-name]").attr("data-page");
        if (!section) var section = $("meta[name=application-name]").attr("data-section");
        pagenum || (pagenum = 1), srch || (srch = "");
        var loc = base + "?page=" + page + "&section=" + section + "&p=" + pagenum + "&search=" + srch, ploc = base + page + "/" + section + "/search/" + pagenum + "/" + srch;
        send_to("Yes" == prettyLinks ? ploc : loc);
    }
    function send_to(loc) {
        var ua = window.navigator.userAgent, msie = ua.indexOf("MSIE ");
        if (msie > 0 || navigator.userAgent.match(/Trident.*rv\:11\./)) var base = $(".iebase").html(); else var base = "";
        window.location = base + loc;
    }
    return $(document).keydown(function(e) {
        27 === e.keyCode && hidePopups();
    }), $(document).on("submit", ".searchFormClass", function() {
        return !1;
    }), $(".show_popover").popover(), $(document).on("click", ".select2-container", function(event) {
        var distance_to_bottom = $(window).height() - ($(this).get(0).getBoundingClientRect().top + 30), ddh = $(".select2-dropdown").height();
        if (ddh > distance_to_bottom) {
            var h = distance_to_bottom - 40;
            newh = ddh < h ? ddh : h, $(".select2-results").css("height", newh);
        }
        var cwidth = 0;
        $(".s2cust-container").each(function() {
            var mycwidth = 0;
            $(this).children().each(function() {
                $(this).is("span") ? mycwidth += $(this).width() : $(this).is("div") && $(this).width() > mycwidth && (mycwidth = $(this).width());
            }), mycwidth > cwidth && (cwidth = Math.round(mycwidth));
        }), $(this).width() < cwidth && $(".select2-dropdown").css("width", cwidth + 30);
    }), $("div.jqueryError").hide(), $(document).on("click", "#hideError", function() {
        return hideError(), !1;
    }), $(".disabled a").click(function() {
        return !1;
    }), $(document).on("click", ".hidePopups", function() {
        hidePopups();
    }), $(document).on("click", ".hidePopup2", function() {
        hidePopup2();
    }), $(document).on("click", ".hidePopupMasks", function() {
        hidePopupMasks();
    }), $(document).on("click", ".hidePopupsReload", function() {
        window.location.reload();
    }), $("a.disabled, button.disabled").click(function() {
        return !1;
    }), $("body").on("touchstart.dropdown", ".dropdown-menu", function(e) {
        e.stopPropagation();
    }), $(document).on("click", ".selfDestruct", function() {
        $(this).parent("div").fadeOut("fast");
    }), $(function() {
        $(".popup").draggable({
            handle: ".pHeader"
        });
    }), null == readCookie("table-page-size") ? current_table_page_size = 50 : current_table_page_size = readCookie("table-page-size"), 
    $("#dashboard").length > 0 && $('div[id^="w-"]').each(function() {
        var w = $(this).attr("id");
        w = w.replace("w-", ""), $.post("/ajx/dashboard/widgets/" + w, function(data) {
            $("#w-" + w + " .hContent").html(data);
        }).fail(function(xhr, textStatus, errorThrown) {
            $("#w-" + w + " .hContent").html('<blockquote style="margin-top:20px;margin-left:20px;">File not found!</blockquote>');
        });
    }), $(document).on("click", ".add-new-widget", function() {
        return showSpinner(), $.post("/ajx/dashboard/widget-popup", function(data) {
            $("#popupOverlay div.popup_w700").html(data), showPopup("popup_w700"), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", "i.remove-widget", function() {
        $(this).parent().parent().fadeOut("fast").remove();
    }), $(document).on("click", "#sortablePopup li a.widget-add", function() {
        var wid = $(this).attr("id"), wsize = $(this).attr("data-size"), wtitle = $(this).attr("data-htitle"), data = '<div class="row-fluid"><div class="span' + wsize + ' widget-dash" id="' + wid + '"><div class="inner movable"><h4>' + wtitle + '</h4><div class="hContent"></div></div></div></div>';
        return $("#dashboard").append(data), w = wid.replace("w-", ""), $.post("/ajx/dashboard/widgets/" + w, function(data) {
            $("#" + wid + " .hContent").html(data);
        }).fail(function(xhr, textStatus, errorThrown) {
            $("#" + wid + " .hContent").html('<blockquote style="margin-top:20px;margin-left:20px;">File not found!</blockquote>');
        }), $(this).parent().fadeOut("fast"), !1;
    }), $("ul.submenu.submenu-close").hide(), $(".icon-folder-close,.icon-folder-show, .icon-search").tooltip({
        delay: {
            show: 2e3,
            hide: 0
        },
        placement: "bottom"
    }), $("ul#subnets").on("click", ".fa-folder-close-o", function() {
        $(this).removeClass("fa-folder-close-o").addClass("fa-folder-open-o"), $(this).nextAll(".submenu").slideDown("fast"), 
        update_subnet_structure_cookie("add", $(this).attr("data-str_id"));
    }), $("ul#subnets").on("click", ".fa-folder", function() {
        $(this).removeClass("fa-folder").addClass("fa-folder-open"), $(this).nextAll(".submenu").slideDown("fast"), 
        update_subnet_structure_cookie("add", $(this).attr("data-str_id"));
    }), $("ul#subnets").on("click", ".fa-folder-open-o", function() {
        $(this).removeClass("fa-folder-open-o").addClass("fa-folder-close-o"), $(this).nextAll(".submenu").slideUp("fast"), 
        update_subnet_structure_cookie("remove", $(this).attr("data-str_id"));
    }), $("ul#subnets").on("click", ".fa-folder-open", function() {
        $(this).removeClass("fa-folder-open").addClass("fa-folder"), $(this).nextAll(".submenu").slideUp("fast"), 
        update_subnet_structure_cookie("remove", $(this).attr("data-str_id"));
    }), $("#expandfolders").click(function() {
        var action = $(this).attr("data-action");
        "close" == action ? ($(".subnets ul#subnets li.folder > i").removeClass("fa-folder-close-o").addClass("fa-folder-open-o"), 
        $(".subnets ul#subnets li.folderF > i").removeClass("fa-folder").addClass("fa-folder-open"), 
        $(".subnets ul#subnets ul.submenu").removeClass("submenu-close").addClass("submenu-open").slideDown("fast"), 
        $(this).attr("data-action", "open"), createCookie("expandfolders", "1", "365"), 
        $(this).removeClass("fa-expand").addClass("fa-compress")) : ($(".subnets ul#subnets li.folder > i").addClass("fa-folder-close-o").removeClass("fa-folder-open-o"), 
        $(".subnets ul#subnets li.folderF > i").addClass("fa-folder").removeClass("fa-folder-open"), 
        $(".subnets ul#subnets ul.submenu").addClass("submenu-close").removeClass("submenu-open").slideUp("fast"), 
        $(this).attr("data-action", "close"), createCookie("expandfolders", "0", "365"), 
        $(this).removeClass("fa-compress").addClass("fa-expand"));
    }), $(document).on("click", ".modIPaddr", function() {
        showSpinner();
        var postdata = {
            action: $(this).attr("data-action"),
            id: $(this).attr("data-id"),
            subnetId: $(this).attr("data-subnetId"),
            stopIP: $(this).attr("data-stopIP")
        };
        return open_popup("600", "ajx/subnets/addresses/address-modify", postdata), !1;
    }), $(document).on("click", "a.moveIPaddr", function() {
        showSpinner();
        var action = $(this).attr("data-action"), id = $(this).attr("data-id"), subnetId = $(this).attr("data-subnetId"), postdata = "action=" + action + "&id=" + id + "&subnetId=" + subnetId;
        return $.post("/ajx/subnets/addresses/move-address", postdata, function(data) {
            $("#popupOverlay div.popup_w400").html(data), showPopup("popup_w400"), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", "#refreshHostname", function() {
        showSpinner();
        var ipaddress = $("input.ip_addr").val(), subnetId = $(this).attr("data-subnetId");
        $.post("/ajx/subnets/addresses/address-resolve", {
            ipaddress: ipaddress,
            subnetId: subnetId
        }, function(data) {
            0 !== data.length && $("input[name=dns_name]").val(data), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        });
    }), $(document).on("click", "button#editIPAddressSubmit, .editIPSubmitDelete", function() {
        showSpinner();
        var postdata = $("form.editipaddress").serialize();
        return "editIPSubmitDelete" == $(this).attr("id") && (postdata += "&deleteconfirm=yes&action=delete"), 
        "all-delete" == $(this).attr("data-action") && (postdata += "&action-visual=delete"), 
        $.post("/ajx/subnets/addresses/address-modify-submit", postdata, function(data) {
            $("div.addnew_check").html(data), $("div.addnew_check").slideDown("fast"), reload_window(data);
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", ".ping_ipaddress", function() {
        showSpinner();
        var id = $(this).attr("data-id"), subnetId = $(this).attr("data-subnetId");
        return $(this).hasClass("ping_ipaddress_new") && (id = $("input[name=ip_addr]").val()), 
        $.post("/ajx/subnets/addresses/ping-address", {
            id: id,
            subnetId: subnetId
        }, function(data) {
            $("#popupOverlay2 div.popup_w400").html(data), showPopup("popup_w400", !1, !0), 
            hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("change", "select#ip-device-select", function() {
        var device_mac = $("select#ip-device-select option:selected").data("mac_addr");
        $("form#editipaddress input[name=mac]").val(device_mac);
    }), $(document).on("change", "select#ip-device-select", function() {
        var device_mac = $("select#ip-device-select option:selected").data("mac_addr");
        $("form#editipaddress input[name=mac]").val(device_mac);
    }), $(document).on("click", "a.mail_ipaddress", function() {
        var IPid = $(this).attr("data-id");
        return $.post("/ajx/subnets/addresses/mail-notify", {
            id: IPid
        }, function(data) {
            $("#popupOverlay div.popup_w700").html(data), showPopup("popup_w700"), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", "#mailIPAddressSubmit", function() {
        showSpinner();
        var mailData = $("form#mailNotify").serialize();
        return $.post("/ajx/subnets/addresses/mail-notify-check", mailData, function(data) {
            $("div.sendmail_check").html(data).slideDown("fast"), reload_window(data);
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", "a.mail_subnet", function() {
        var id = $(this).attr("data-id");
        return $.post("/ajx/subnets/mail-notify-subnet", {
            id: id
        }, function(data) {
            $("#popupOverlay div.popup_w700").html(data), showPopup("popup_w700"), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", "#mailSubnetSubmit", function() {
        showSpinner();
        var mailData = $("form#mailNotifySubnet").serialize();
        return $.post("/ajx/subnets/mail-notify-subnet-check", mailData, function(data) {
            $("div.sendmail_check").html(data).slideDown("fast"), reload_window(data);
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $("table.ipaddresses th a").click(function() {
        return !1;
    }), $("a.scan_subnet").click(function() {
        showSpinner();
        var subnetId = $(this).attr("data-subnetId");
        return $.post("/ajx/subnets/scan/subnet-scan", {
            subnetId: subnetId
        }, function(data) {
            $("#popupOverlay div.popup_wmasks").html(data), showPopup("popup_wmasks"), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("change", "table.table-scan select#type", function() {
        var pingType = $("select[name=type]").find(":selected").val();
        "scan-telnet" == pingType ? $("tbody#telnetPorts").show() : $("tbody#telnetPorts").hide();
    }), $(document).on("change", "table.table-scan select#type", function() {
        var sel = $(this).find(":selected").val();
        createCookie("scantype", sel, 32);
    }), $(document).on("click", "#subnetScanSubmit", function() {
        showSpinner(), $("#subnetScanResult").slideUp("fast");
        var subnetId = $(this).attr("data-subnetId"), type = $("select[name=type]").find(":selected").val();
        if ($("input[name=debug]").is(":checked")) var debug = 1; else var debug = 0;
        var port = $("input[name=telnetports]").val();
        return $("#alert-scan").slideUp("fast"), $.post("/ajx/subnets/scan/subnet-scan-execute", {
            subnetId: subnetId,
            type: type,
            debug: debug,
            port: port
        }, function(data) {
            $("#subnetScanResult").html(data).slideDown("fast"), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", ".resultRemove", function() {
        $(this).hasClass("resultRemoveMac") && ($(this).parent().parent().find("span.ip-address").hasClass("hidden") || $(this).parent().parent().next().find("span.ip-address").removeClass("hidden"));
        var target = $(this).attr("data-target");
        return $("tr." + target).remove(), !1;
    }), $(document).on("click", "a#saveScanResults", function() {
        showSpinner();
        var script = $(this).attr("data-script"), subnetId = $(this).attr("data-subnetId"), postData = $("form." + script + "-form").serialize(), postData = postData + "&subnetId=" + subnetId;
        return $.post("/ajx/subnets/scan/subnet-" + script + "-result", postData, function(data) {
            $("#subnetScanAddResult").html(data), reload_window(data);
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $("a.csvImport").click(function() {
        showSpinner();
        var subnetId = $(this).attr("data-subnetId");
        return $.post("/ajx/subnets/import-subnet/index", {
            subnetId: subnetId
        }, function(data) {
            $("div.popup_max").html(data), showPopup("popup_max"), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", "input#csvimportcheck", function() {
        showSpinner();
        var filetype = $("span.fname").html(), xlsSubnetId = $("a.csvImport").attr("data-subnetId");
        $.post("/ajx/subnets/import-subnet/print-file", {
            filetype: filetype,
            subnetId: xlsSubnetId
        }, function(data) {
            $("div.csvimportverify").html(data).slideDown("fast"), hideSpinner(), $(".importFooter").removeClass("hidePopups").addClass("hidePopupsReload");
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        });
    }), $(document).on("click", "input#csvImportNo", function() {
        $("div.csvimportverify").hide("fast");
    }), $(document).on("click", "input#csvImportYes", function() {
        showSpinner();
        var filetype = $("span.fname").html();
        if ($("input[name=ignoreErrors]").is(":checked")) var ignoreError = "1"; else var ignoreError = "0";
        var xlsSubnetId = $("a.csvImport").attr("data-subnetId"), postData = "subnetId=" + xlsSubnetId + "&filetype=" + filetype + "&ignoreError=" + ignoreError;
        $.post("/ajx/subnets/import-subnet/import-file", postData, function(data) {
            $("div.csvImportResult").html(data).slideDown("fast"), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        });
    }), $(document).on("click", "#csvtemplate", function() {
        return $("div.dl").remove(), $("div.exportDIV").append("<div style='display:none' class='dl'><iframe src='/ajx/subnets/import-subnet/import-template.php'></iframe></div>"), 
        !1;
    }), $(document).on("click", "#vrftemplate", function() {
        return $("div.dl").remove(), $("div.exportDIV").append("<div style='display:none' class='dl'><iframe src='/ajx/admin/import-export/import-template.php?type=vrf'></iframe></div>"), 
        !1;
    }), $(document).on("click", "#vlanstemplate", function() {
        return $("div.dl").remove(), $("div.exportDIV").append("<div style='display:none' class='dl'><iframe src='/ajx/admin/import-export/import-template.php?type=vlans'></iframe></div>"), 
        !1;
    }), $(document).on("click", "#vlandomaintemplate", function() {
        return $("div.dl").remove(), $("div.exportDIV").append('<div style="display:none" class="dl"><iframe src="/ajx/admin/import-export/import-template/?type=' + tpl + "&csrf_cookie=" + csrf + '"></iframe></div>'), 
        !1;
    }), $(document).on("click", "#subnetstemplate", function() {
        return $("div.dl").remove(), $("div.exportDIV").append("<div style='display:none' class='dl'><iframe src='/ajx/admin/import-export/import-template.php?type=subnets'></iframe></div>"), 
        !1;
    }), $(document).on("click", "#ipaddrtemplate", function() {
        return $("div.dl").remove(), $("div.exportDIV").append("<div style='display:none' class='dl'><iframe src='/ajx/admin/import-export/import-template.php?type=ipaddr'></iframe></div>"), 
        !1;
    }), $(document).on("click", "#devicestemplate", function() {
        return $("div.dl").remove(), $("div.exportDIV").append("<div style='display:none' class='dl'><iframe src='/ajx/admin/import-export/import-template.php?type=devices'></iframe></div>"), 
        !1;
    }), $(document).on("click", "#devicetypestemplate", function() {
        return $("div.dl").remove(), $("div.exportDIV").append("<div style='display:none' class='dl'><iframe src='/ajx/admin/import-export/import-template.php?type=devicetypes'></iframe></div>"), 
        !1;
    }), $("a.csvExport").click(function() {
        showSpinner();
        var subnetId = $(this).attr("data-subnetId");
        return $.post("/ajx/subnets/addresses/export-field-select", {
            subnetId: subnetId
        }, function(data) {
            $("#popupOverlay div.popup_w400").html(data), showPopup("popup_w400"), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", "button#exportSubnet", function() {
        var subnetId = $("a.csvExport").attr("data-subnetId"), exportFields = $("form#selectExportFields").serialize();
        return $("div.dl").remove(), $("div.exportDIV").append("<div style='display:none' class='dl'><iframe src='/ajx/subnets/addresses/export-subnet/?subnetId=" + subnetId + "&" + exportFields + "'></iframe></div>"), 
        !1;
    }), $(document).on("click", "a.editFavourite", function() {
        var subnetId = $(this).attr("data-subnetId"), action = $(this).attr("data-action"), from = $(this).attr("data-from"), item = $(this);
        return $.post("/ajx/tools/favourites/favourite-edit", {
            subnetId: subnetId,
            action: action,
            from: from
        }, function(data) {
            "success" == data && "widget" == from ? ($("tr.favSubnet-" + subnetId).addClass("error"), 
            $("tr.favSubnet-" + subnetId).delay(200).fadeOut()) : "success" == data ? ($(this).toggleClass("btn-info"), 
            $("a.favourite-" + subnetId + " i").toggleClass("fa-star-o"), $(item).toggleClass("btn-info"), 
            "remove" == action ? ($("a.favourite-" + subnetId).attr("data-original-title", "Click to add to favourites"), 
            $(item).attr("data-action", "add")) : ($("a.favourite-" + subnetId).attr("data-original-title", "Click to remove from favourites"), 
            $(item).attr("data-action", "remove"))) : ($("#popupOverlay div.popup_w500").html(data), 
            showPopup("popup_w500"), hideSpinner());
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $("a.request_ipaddress").click(function() {
        showSpinner();
        var subnetId = $(this).attr("data-subnetId");
        return $.post("/ajx/tools/request-ip/index", {
            subnetId: subnetId
        }, function(data) {
            $("#popupOverlay div.popup_w500").html(data), showPopup("popup_w500"), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", "button#requestIP_widget", function() {
        showSpinner();
        var subnetId = $("select#subnetId option:selected").attr("value"), ip_addr = document.getElementById("ip_addr_widget").value;
        return $.post("/ajx/tools/request-ip/index", {
            subnetId: subnetId,
            ip_addr: ip_addr
        }, function(data) {
            $("div.popup_w500").html(data), showPopup("popup_w500"), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("change", "select#subnetId", function() {
        showSpinner();
        var subnetId = $("select#subnetId option:selected").attr("value");
        $.post("/ajx/login/request_ip_first_free", {
            subnetId: subnetId
        }, function(data) {
            $("input.ip_addr").val(data), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        });
    }), $(document).on("click", "button#requestIPAddressSubmit", function() {
        showSpinner();
        var request = $("form#requestIP").serialize();
        return $.post("/ajx/login/request_ip_result", request, function(data) {
            $("div#requestIPresult").html(data).slideDown("fast"), reload_window(data);
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $("form#ipCalc").submit(function() {
        showSpinner();
        var ipCalcData = $(this).serialize();
        return $.post("/ajx/tools/ip-calculator/result", ipCalcData, function(data) {
            $("div.ipCalcResult").html(data).fadeIn("fast"), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $("form#ipCalc input.reset").click(function() {
        $('form#ipCalc input[type="text"]').val(""), $("div.ipCalcResult").fadeOut("fast");
    }), $(".searchSubmit").click(function() {
        return search_execute("topmenu"), !1;
    }), $("form#userMenuSearch").submit(function() {
        return search_execute("topmenu"), !1;
    }), $("form#search").submit(function() {
        return search_execute("search"), !1;
    }), $("a.search_ipaddress").click(function() {
        createCookie("search_parameters", '{"addresses":"on","subnets":"off","vlans":"off","vrf":"off"}', 365);
    }), $("button#listSearchSubmit").click(function() {
        return list_search_execute(), !1;
    }), $("input#list_search_term").keypress(function(event) {
        if (13 == event.keyCode) return list_search_execute(), !1;
    }), $("select#table_page_size").change(function() {
        table_page_size($("meta[name=application-name]").attr("data-page"), $("meta[name=application-name]").attr("data-section"), $("select#table_page_size").val());
    }), $(document).on("mouseenter", "#userMenuSearch", function(event) {
        var object1 = $("#searchSelect");
        object1.slideDown("fast");
    }), $(document).on("mouseleave", "#user_menu", function(event) {
        $(this).stop();
        var object1 = $("#searchSelect");
        object1.slideUp();
    }), $(document).on("click", "#exportSearch", function(event) {
        var searchTerm = $(this).attr("data-post");
        return $("div.dl").remove(), $("div.exportDIVSearch").append("<div style='display:none' class='dl'><iframe src='/ajx/tools/search/search-results-export/?ip=" + searchTerm + "'></iframe></div>"), 
        !1;
    }), $("#hosts").submit(function() {
        showSpinner();
        var hostname = $("input.hostsFilter").val(), prettyLinks = $("#prettyLinks").html();
        return "Yes" == prettyLinks ? window.location = base + "tools/hosts/" + hostname : window.location = base + "?page=tools&section=hosts&ip=" + hostname, 
        !1;
    }), $("form#userModSelf").submit(function() {
        var selfdata = $(this).serialize();
        return $("div.userModSelfResult").hide(), $.post("/ajx/tools/user-menu/user-edit", selfdata, function(data) {
            $("div.userModSelfResult").html(data).fadeIn("fast").delay(2e3).fadeOut("slow");
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", "#randomPassSelf", function() {
        var password = randomPass();
        return $("input.userPass").val(password), $("#userRandomPass").html(password), !1;
    }), $("form#cform").submit(function() {
        showSpinner();
        var limit = $("form#cform .climit").val(), filter = $("form#cform .cfilter").val(), prettyLinks = $("#prettyLinks").html();
        return "Yes" == prettyLinks ? window.location = "tools/changelog/" + filter + "/" + limit + "/" : window.location = "?page=tools&section=changelog&subnetId=" + filter + "&sPage=" + limit, 
        !1;
    }), $("form#changePassRequiredForm").submit(function() {
        showSpinner();
        var ipampassword1 = $("#ipampassword1", this).val(), ipampassword2 = $("#ipampassword2", this).val(), postData = "ipampassword1=" + ipampassword1 + "&ipampassword2=" + ipampassword2;
        return $.post("/ajx/tools/pass-change/result", postData, function(data) {
            $("div#changePassRequiredResult").html(data).fadeIn("fast"), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", ".show-masks", function() {
        return open_popup("masks", "ajx/tools/subnet-masks/popup", {
            closeClass: $(this).attr("data-closeClass")
        }, !0), !1;
    }), $("#settings").submit(function() {
        showSpinner();
        var settings = $(this).serialize();
        return $.post("/ajx/admin/settings/settings-save", settings, function(data) {
            $("div.settingsEdit").html(data).slideDown("fast"), reload_window(data);
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $("#upload-logo").click(function() {
        return csrf_cookie = $("form#settings input[name=csrf_cookie]").val(), open_popup("700", "/ajx/admin/settings/logo/logo-uploader", {
            csrf_cookie: csrf_cookie
        }, !1), !1;
    }), $(document).on("click", ".logo-clear", function() {
        $.post("/ajx/admin/settings/logo/logo-clear", "", function(data) {
            $("div.logo-current").html(data).slideDown("fast"), reload_window(data);
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        });
    }), $("#mailsettings").submit(function() {
        showSpinner();
        var settings = $(this).serialize();
        return $.post("/ajx/admin/mail/edit", settings, function(data) {
            $("div.settingsMailEdit").html(data).slideDown("fast"), reload_window(data);
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $("select#mtype").change(function() {
        var type = $(this).find(":selected").val();
        "localhost" === type ? $("#mailsettingstbl tbody#smtp").hide() : $("#mailsettingstbl tbody#smtp").show();
    }), $(".sendTestMail").click(function() {
        return showSpinner(), $.post("/ajx/admin/mail/test-mail", $("form#mailsettings").serialize(), function(data) {
            $("div.settingsMailEdit").html(data).slideDown("fast"), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(".editUser").click(function() {
        showSpinner();
        var id = $(this).attr("data-userid"), action = $(this).attr("data-action");
        return $.post("/ajx/admin/users/edit", {
            id: id,
            action: action
        }, function(data) {
            $("#popupOverlay div.popup_w700").html(data), showPopup("popup_w700"), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", "#editUserSubmit", function() {
        showSpinner();
        var loginData = $("form#usersEdit").serialize();
        return $.post("/ajx/admin/users/edit-result", loginData, function(data) {
            $("div.usersEditResult").html(data).show(), reload_window(data);
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("change", "form#usersEdit select[name=authMethod]", function() {
        var type = $("select[name=authMethod]").find(":selected").val();
        "1" == type ? $("tbody#user_password").show() : $("tbody#user_password").hide();
    }), $(document).on("change", "form#usersEdit select[name=role]", function() {
        var type = $("form#usersEdit select[name=role]").find(":selected").val();
        "Administrator" == type ? $("tbody#user_notifications").show() : $("tbody#user_notifications").hide();
    }), $(document).on("click", "a#randomPass", function() {
        var password = randomPass();
        return $("input.userPass").val(password), $(this).html(password), !1;
    }), $(document).on("click", ".adsearchuser", function() {
        $("#popupOverlay2 .popup_w500").load("/ajx/admin/users/ad-search-form"), showPopup("popup_w500", !1, !0), 
        hideSpinner();
    }), $(document).on("click", "#adsearchusersubmit", function() {
        showSpinner();
        var dname = $("#dusername").val(), server = $("#adserver").find(":selected").val();
        $.post("/ajx/admin/users/ad-search-result", {
            dname: dname,
            server: server
        }, function(data) {
            $("div#adsearchuserresult").html(data), hideSpinner();
        });
    }), $(document).on("click", ".userselect", function() {
        var uname = $(this).attr("data-uname"), username = $(this).attr("data-username"), email = $(this).attr("data-email"), server = $(this).attr("data-server"), server_type = $(this).attr("data-server-type");
        return $("form#usersEdit input[name=real_name]").val(uname), $("form#usersEdit input[name=username]").val(username), 
        $("form#usersEdit input[name=email]").val(email), $("form#usersEdit select[name=authMethod]").val(server), 
        $("tbody#user_password").hide(), "AD" != server_type && "LDAP" != server_type || $.post("/ajx/admin/users/ad-search-result-groups-membership", {
            server: server,
            username: username
        }, function(data) {
            if (data.length > 0) {
                var groups = data.replace(/\s/g, "");
                for (groups = groups.split(";"), m = 0; m < groups.length; ++m) $("input[name='group" + groups[m] + "']").attr("checked", "checked");
            }
        }), hidePopup2(), !1;
    }), $(document).on("click", ".adLookup", function() {
        $("#popupOverlay div.popup_w700").load("/ajx/admin/groups/ad-search-group-form"), 
        showPopup("popup_w700"), hideSpinner();
    }), $(document).on("click", "#adsearchgroupsubmit", function() {
        showSpinner();
        var dfilter = $("#dfilter").val(), server = $("#adserver").find(":selected").val();
        $.post("/ajx/admin/groups/ad-search-group-result", {
            dfilter: dfilter,
            server: server
        }, function(data) {
            $("div#adsearchgroupresult").html(data), hideSpinner();
        });
    }), $(document).on("click", ".groupselect", function() {
        showSpinner();
        var gname = $(this).attr("data-gname"), gdescription = $(this).attr("data-gdescription"), gmembers = $(this).attr("data-members"), gid = $(this).attr("data-gid"), csrf_cookie = $(this).attr("data-csrf_cookie");
        return $.post("/ajx/admin/groups/edit-group-result", {
            action: "add",
            g_name: gname,
            g_desc: gdescription,
            gmembers: gmembers,
            csrf_cookie: csrf_cookie
        }, function(data) {
            $("div.adgroup-" + gid).html(data), hideSpinner();
        }), !1;
    }), $(".editGroup").click(function() {
        showSpinner();
        var id = $(this).attr("data-groupid"), action = $(this).attr("data-action");
        return $.post("/ajx/admin/groups/edit-group", {
            id: id,
            action: action
        }, function(data) {
            $("#popupOverlay div.popup_w700").html(data), showPopup("popup_w700"), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", "#editGroupSubmit", function() {
        showSpinner();
        var loginData = $("form#groupEdit").serialize();
        return $.post("/ajx/admin/groups/edit-group-result", loginData, function(data) {
            $("div.groupEditResult").html(data).show(), reload_window(data);
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(".addToGroup").click(function() {
        showSpinner();
        var g_id = $(this).attr("data-groupid");
        return $.post("/ajx/admin/groups/add-users", {
            g_id: g_id
        }, function(data) {
            $("#popupOverlay div.popup_w700").html(data), showPopup("popup_w700"), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", "#groupAddUsersSubmit", function() {
        showSpinner();
        var users = $("#groupAddUsers").serialize();
        return $.post("/ajx/admin/groups/add-users-result", users, function(data) {
            $("div.groupAddUsersResult").html(data).show(), reload_window(data);
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(".removeFromGroup").click(function() {
        showSpinner();
        var g_id = $(this).attr("data-groupid");
        return $.post("/ajx/admin/groups/remove-users", {
            g_id: g_id
        }, function(data) {
            $("#popupOverlay div.popup_w700").html(data), showPopup("popup_w700"), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", "#groupRemoveUsersSubmit", function() {
        showSpinner();
        var users = $("#groupRemoveUsers").serialize();
        return $.post("/ajx/admin/groups/remove-users-result", users, function(data) {
            $("div.groupRemoveUsersResult").html(data).show(), reload_window(data);
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(".editAuthMethod").click(function() {
        showSpinner();
        var id = $(this).attr("data-id"), action = $(this).attr("data-action"), type = $(this).attr("data-type");
        return $.post("/ajx/admin/authentication-methods/edit", {
            id: id,
            action: action,
            type: type
        }, function(data) {
            $("#popupOverlay div.popup_w700").html(data), showPopup("popup_w700"), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", "#editAuthMethodSubmit", function() {
        showSpinner();
        var loginData = $("form#editAuthMethod").serialize();
        return $.post("/ajx/admin/authentication-methods/edit-result", loginData, function(data) {
            $("div.editAuthMethodResult").html(data).show(), reload_window(data);
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(".checkAuthMethod").click(function() {
        showSpinner();
        var id = $(this).attr("data-id");
        return $.post("/ajx/admin/authentication-methods/check-connection", {
            id: id
        }, function(data) {
            $("#popupOverlay div.popup_w500").html(data), showPopup("popup_w500"), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $("#instructionsForm").submit(function() {
        var csrf_cookie = $("#instructionsForm input[name=csrf_cookie]").val(), id = $("#instructionsForm input[name=id]").val(), instructions = CKEDITOR.instances.instructions.getData();
        return $("div.instructionsPreview").hide("fast"), showSpinner(), $.post("/ajx/admin/instructions/edit-result", {
            instructions: instructions,
            csrf_cookie: csrf_cookie,
            id: id
        }, function(data) {
            $("div.instructionsResult").html(data).fadeIn("fast"), data.search("alert-danger") == -1 && data.search("error") == -1 ? ($("div.instructionsResult").delay(2e3).fadeOut("slow"), 
            hideSpinner()) : hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $("#preview").click(function() {
        showSpinner();
        var instructions = CKEDITOR.instances.instructions.getData();
        return $.post("/ajx/admin/instructions/preview", {
            instructions: instructions
        }, function(data) {
            $("div.instructionsPreview").html(data).fadeIn("fast"), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $("form#logs").change(function() {
        showSpinner();
        var logSelection = $("form#logs").serialize();
        $.post("/ajx/tools/logs/show-logs", logSelection, function(data) {
            $("div.logs").html(data), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        });
    }), $(document).on("click", "a.openLogDetail", function() {
        var id = $(this).attr("data-logid");
        return $.post("/ajx/tools/logs/detail-popup", {
            id: id
        }, function(data) {
            $("#popupOverlay div.popup_w500").html(data), showPopup("popup_w500"), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $("#logDirection button").click(function() {
        showSpinner();
        var logSelection = $("form#logs").serialize(), direction = $(this).attr("data-direction"), lastId;
        lastId = "next" == direction ? $("table#logs tr:last").attr("id") : $("table#logs tr:nth-child(2)").attr("id");
        var postData = logSelection + "&direction=" + direction + "&lastId=" + lastId;
        return $.post("/ajx/tools/logs/show-logs", postData, function(data1) {
            $("div.logs").html(data1), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $("#downloadLogs").click(function() {
        return showSpinner(), $("div.dl").remove(), $("div.exportDIV").append("<div style='display:none' class='dl'><iframe src='/ajx/admin/logs/export'></iframe></div>"), 
        hideSpinner(), $("div.logs").prepend("<div class='alert alert-info' id='logsInfo'><i class='icon-remove icon-gray selfDestruct'></i> Preparing download... </div>"), 
        !1;
    }), $("#clearLogs").click(function() {
        return showSpinner(), $.post("/ajx/tools/logs/clear-logs", function(data) {
            $("div.logs").html(data), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(".log-tabs li a").click(function() {
        return $(".log-tabs li").removeClass("active"), $(this).parent("li").addClass("active"), 
        $("div.log-print").hide(), $("div." + $(this).attr("data-target")).show(), !1;
    }), $(document).on("click", ".openChangelogDetail", function() {
        open_popup("700", "/ajx/tools/changelog/show-popup.php", {
            cid: $(this).attr("data-cid")
        });
    }), $("button.editSection").click(function() {
        showSpinner();
        var sectionId = $(this).attr("data-sectionid"), action = $(this).attr("data-action");
        $.post("ajx/admin/sections/edit", {
            sectionId: sectionId,
            action: action
        }, function(data) {
            $("#popupOverlay div.popup_w700").html(data), showPopup("popup_w700"), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        });
    }), $(document).on("click", "#editSectionSubmit, .editSectionSubmitDelete", function() {
        showSpinner();
        var sectionData = $("form#sectionEdit").serialize();
        return "editSectionSubmitDelete" == $(this).attr("id") && (sectionData += "&deleteconfirm=yes"), 
        $.post("/ajx/admin/sections/edit-result", sectionData, function(data) {
            $("div.sectionEditResult").html(data).slideDown("fast"), reload_window(data);
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $("button.sectionOrder").click(function() {
        return showSpinner(), $.post("ajx/admin/sections/edit-order", function(data) {
            $("#popupOverlay div.popup_w500").html(data), showPopup("popup_w500"), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", "#sectionOrderSubmit", function() {
        showSpinner();
        var m = 0, lis = $("#sortableSec li").map(function(i, n) {
            var pindex = $(this).index() + 1;
            return $(n).attr("id") + ":" + pindex;
        }).get().join(";");
        return $.post("/ajx/admin/sections/edit-order-result", {
            position: lis
        }, function(data) {
            $(".sectionOrderResult").html(data).fadeIn("fast"), reload_window(data);
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $("#pdns-settings").submit(function() {
        showSpinner();
        var settings = $(this).serialize();
        return $.post("/ajx/admin/powerDNS/settings-save", settings, function(data) {
            $("div.settingsEdit").html(data).slideDown("fast"), reload_window(data);
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $("#pdns-defaults").submit(function() {
        showSpinner();
        var settings = $(this).serialize();
        return $.post("/ajx/admin/powerDNS/defaults-save", settings, function(data) {
            $("div.settingsEdit").html(data).slideDown("fast"), reload_window(data);
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", ".editDomain", function() {
        $(this).hasClass("editDomain2") ? open_popup("700", "ajx/admin/powerDNS/domain-edit", {
            id: $(this).attr("data-id"),
            action: $(this).attr("data-action"),
            secondary: !0
        }, !0) : open_popup("700", "ajx/admin/powerDNS/domain-edit", {
            id: $(this).attr("data-id"),
            action: $(this).attr("data-action")
        });
    }), $(document).on("click", ".hideDefaults", function() {
        $(this).is(":checked") ? $("tbody.defaults").hide() : $("tbody.defaults").show();
    }), $(document).on("click", "#editDomainSubmit", function() {
        return $(this).hasClass("editDomainSubmit2") ? (showSpinner(), $.post("ajx/admin/powerDNS/domain-edit-result", $("form#domainEdit").serialize(), function(data) {
            $("#popupOverlay2 div.domain-edit-result").html(data).slideDown("fast"), data.search("alert-danger") == -1 && data.search("error") == -1 && data.search("alert-warning") == -1 ? ($.post("ajx/admin/powerDNS/record-edit", {
                id: $("#popupOverlay .pContent .ip_dns_addr").html(),
                domain_id: $("#popupOverlay .pContent strong").html(),
                action: "add"
            }, function(data2) {
                $("#popupOverlay .popup_w700").html(data2);
            }), setTimeout(function() {
                $("#popupOverlay2").fadeOut("fast");
            }, 1500), setTimeout(function() {
                hideSpinner();
            }, 1500)) : hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1) : void submit_popup_data(".domain-edit-result", "ajx/admin/powerDNS/domain-edit-result", $("form#domainEdit").serialize());
    }), $(".refreshPTRsubnet").click(function() {
        return open_popup("700", "ajx/admin/powerDNS/refresh-ptr-records", {
            subnetId: $(this).attr("data-subnetId")
        }), !1;
    }), $(document).on("click", ".refreshPTRsubnetSubmit", function() {
        return submit_popup_data(".refreshPTRsubnetResult", "ajx/admin/powerDNS/refresh-ptr-records-submit", {
            subnetId: $(this).attr("data-subnetId")
        }), !1;
    }), $(".editRecord").click(function() {
        return open_popup("700", "ajx/admin/powerDNS/record-edit", {
            id: $(this).attr("data-id"),
            domain_id: $(this).attr("data-domain_id"),
            action: $(this).attr("data-action")
        }), !1;
    }), $(document).on("click", "#editRecordSubmit", function() {
        submit_popup_data(".record-edit-result", "ajx/admin/powerDNS/record-edit-result", $("form#recordEdit").serialize());
    }), $(document).on("click", "#editRecordSubmitDelete", function() {
        var formData = $("form#recordEdit").serialize();
        formData = formData.replace("action=edit", "action=delete"), submit_popup_data(".record-edit-result", "ajx/admin/powerDNS/record-edit-result", formData);
    }), $("#firewallZoneSettings").submit(function() {
        showSpinner();
        var settings = $(this).serialize();
        return $.post("/ajx/admin/firewall-zones/settings-save", settings, function(data) {
            $("div.settingsEdit").html(data).slideDown("fast"), reload_window(data);
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", ".editFirewallZone", function() {
        open_popup("700", "ajx/admin/firewall-zones/zones-edit", {
            id: $(this).attr("data-id"),
            action: $(this).attr("data-action")
        });
    }), $(document).on("click", "#editZoneSubmit", function() {
        submit_popup_data(".zones-edit-result", "ajx/admin/firewall-zones/zones-edit-result", $("form#zoneEdit").serialize());
    }), $(document).on("click", ".subnet_to_zone", function() {
        showSpinner();
        var subnetId = $(this).attr("data-subnetId"), operation = $(this).attr("data-operation"), postdata = "operation=" + operation + "&subnetId=" + subnetId;
        return $.post("/ajx/admin/firewall-zones/subnet-to-zone", postdata, function(data) {
            $("#popupOverlay div.popup_w500").html(data), showPopup("popup_w500"), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", "#subnet-to-zone-submit", function() {
        submit_popup_data(".subnet-to-zone-result", "/ajx/admin/firewall-zones/subnet-to-zone-save", $("form#subnet-to-zone-edit").serialize());
    }), $(document).on("change", ".checkMapping", function() {
        showSpinner();
        var pData = $(this).serializeArray();
        return pData.push({
            name: "operation",
            value: "checkMapping"
        }), $.post("/ajx/admin/firewall-zones/ajax", pData, function(data) {
            $("div.mappingAdd").html(data).slideDown("fast");
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), hideSpinner(), !1;
    }), $(document).on("click", ".editNetwork", function() {
        showSpinner();
        var pData = $("form#zoneEdit").serializeArray();
        pData.push({
            name: "action",
            value: $(this).attr("data-action")
        }), pData.push({
            name: "subnetId",
            value: $(this).attr("data-subnetId")
        }), $("#popupOverlay2 .popup_w500").load("/ajx/admin/firewall-zones/zones-edit-network", pData), 
        showPopup("popup_w500", !1, !0), hideSpinner();
    }), $(document).on("click", ".deleteTempNetwork", function() {
        showSpinner();
        var filterName = "network[" + $(this).attr("data-subnetArrayKey") + "]", pData = $('form#zoneEdit :input[name != "' + filterName + '"][name *= "network["]').serializeArray();
        return pData.push({
            name: "noZone",
            value: 1
        }), $.post("ajx/admin/firewall-zones/ajax", pData, function(data) {
            $("div.zoneNetwork").html(data).slideDown("fast");
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), setTimeout(function() {
            hideSpinner();
        }, 500), !1;
    }), $(document).on("click", "#editNetworkSubmit", function() {
        return showSpinner(), reload = "undefined" == typeof reload || reload, $.post("ajx/admin/firewall-zones/zones-edit-network-result", $('form#networkEdit :input[name != "sectionId"]').serialize(), function(data) {
            $("div.zones-edit-network-result").html(data).slideDown("fast"), reload && data.search("alert-danger") == -1 && data.search("error") == -1 && data.search("alert-warning") == -1 ? ($.post("ajx/admin/firewall-zones/ajax", $('form#networkEdit :input[name != "sectionId"]').serialize(), function(data) {
                $("div.zoneNetwork").html(data).slideDown("fast");
            }).fail(function(jqxhr, textStatus, errorThrown) {
                showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
            }), setTimeout(function() {
                hideSpinner(), hidePopup2();
            }, 500)) : hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("change", "#fw-zone-section-select", function() {
        showSpinner();
        var pData = $(this).serializeArray();
        return pData.push({
            name: "operation",
            value: "fetchSectionSubnets"
        }), $.post("/ajx/admin/firewall-zones/ajax", pData, function(data) {
            $("div.sectionSubnets").html(data).slideDown("fast"), $("#master-select").select2({
                theme: "bootstrap",
                width: "",
                minimumResultsForSearch: 15,
                templateResult: $(this).s2oneLine
            });
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), hideSpinner(), !1;
    }), $(document).on("click", ".editMapping", function() {
        return open_popup("700", "ajx/admin/firewall-zones/mapping-edit", {
            id: $(this).attr("data-id"),
            action: $(this).attr("data-action")
        }), !1;
    }), $(document).on("click", "#editMappingSubmit", function() {
        submit_popup_data(".mapping-edit-result", "ajx/admin/firewall-zones/mapping-edit-result", $("form#mappingEdit").serialize());
    }), $(document).on("change", ".mappingZoneInformation", function() {
        showSpinner();
        var pData = $(this).serializeArray();
        return pData.push({
            name: "operation",
            value: "deliverZoneDetail"
        }), $.post("/ajx/admin/firewall-zones/ajax", pData, function(data) {
            $("div.zoneInformation").html(data).slideDown("fast");
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), hideSpinner(), !1;
    }), $(document).on("click", "a.fw_autogen", function() {
        var subnetId = $(this).attr("data-subnetid"), IPId = $(this).attr("data-ipid"), dnsName = $(this).attr("data-dnsname"), action = $(this).attr("data-action"), operation = "autogen";
        return showSpinner(), $.post("/ajx/admin/firewall-zones/ajax", {
            subnetId: subnetId,
            IPId: IPId,
            dnsName: dnsName,
            action: action,
            operation: operation
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), setTimeout(function() {
            hideSpinner(), window.location.reload();
        }, 500), !1;
    }), $('table#manageSubnets button[id^="subnet-"]').click(function() {
        showSpinner();
        var swid = $(this).attr("id");
        $("#content-" + swid).is(":visible") ? $(this).children("i").removeClass("fa-angle-down").addClass("fa-angle-right") : $(this).children("i").removeClass("fa-angle-right").addClass("fa-angle-down"), 
        $("table#manageSubnets tbody#content-" + swid).slideToggle("fast"), hideSpinner();
    }), $("#toggleAllSwitches").click(function() {
        showSpinner(), $(this).children().hasClass("fa-compress") ? ($(this).children().removeClass("fa-compress").addClass("fa-expand"), 
        $("table#manageSubnets i.fa-angle-down").removeClass("fa-angle-down").addClass("fa-angle-right"), 
        $('table#manageSubnets tbody[id^="content-subnet-"]').hide(), createCookie("showSubnets", 0, 30)) : ($(this).children().removeClass("fa-expand").addClass("fa-compress"), 
        $('table#manageSubnets tbody[id^="content-subnet-"]').show(), $("table#manageSubnets i.fa-angle-right").removeClass("fa-angle-right").addClass("fa-angle-down"), 
        createCookie("showSubnets", 1, 30)), hideSpinner();
    }), $("button.editSubnet").click(function() {
        showSpinner();
        var sectionId = $(this).attr("data-sectionid"), subnetId = $(this).attr("data-subnetid"), action = $(this).attr("data-action"), postdata = "sectionId=" + sectionId + "&subnetId=" + subnetId + "&action=" + action;
        $.post("ajx/admin/subnets/edit", postdata, function(data) {
            $("#popupOverlay div.popup_w700").html(data), showPopup("popup_w700"), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        });
    }), $(document).on("click", "#resize, #split, #truncate", function() {
        showSpinner();
        var action = $(this).attr("id"), subnetId = $(this).attr("data-subnetId");
        return $.post("ajx/admin/subnets/" + action, {
            action: action,
            subnetId: subnetId
        }, function(data) {
            showPopup("popup_w500", data, !0), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", "button#subnetResizeSubmit", function() {
        showSpinner();
        var resize = $("form#subnetResize").serialize();
        return $.post("ajx/admin/subnets/resize-save", resize, function(data) {
            $("div.subnetResizeResult").html(data), reload_window(data);
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", "button#subnetSplitSubmit", function() {
        showSpinner();
        var split = $("form#subnetSplit").serialize();
        return $.post("ajx/admin/subnets/split-save", split, function(data) {
            $("div.subnetSplitResult").html(data), reload_window(data);
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", "button#subnetTruncateSubmit", function() {
        showSpinner();
        var subnetId = $(this).attr("data-subnetId"), csrf_cookie = $(this).attr("data-csrf_cookie");
        return $.post("ajx/admin/subnets/truncate-save", {
            subnetId: subnetId,
            csrf_cookie: csrf_cookie
        }, function(data) {
            $("div.subnetTruncateResult").html(data), reload_window(data);
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("submit", "#editSubnetDetails", function() {
        return !1;
    }), $(document).on("click", ".editSubnetSubmit, .editSubnetSubmitDelete", function() {
        showSpinner();
        var subnetData = $("form#editSubnetDetails").serialize();
        return $(this).hasClass("editSubnetSubmitDelete") && (subnetData = subnetData.replace("action=edit", "action=delete")), 
        "editSubnetSubmitDelete" == $(this).attr("id") && (subnetData += "&deleteconfirm=yes"), 
        $.post("ajx/admin/subnets/edit-result", subnetData, function(data) {
            if ($("div.manageSubnetEditResult").html(data).slideDown("fast"), data.search("alert-danger") == -1 && data.search("error") == -1) {
                showSpinner();
                var sectionId, subnetId, parameter;
                if (subnetData.search("IPaddresses") != -1) if (sectionId = $("form#editSubnetDetails input[name=sectionId]").val(), 
                subnetId = $("form#editSubnetDetails input[name=subnetId]").val(), "undefined" !== $(".subnet_id_new").html()) {
                    var subnet_id_new = $(".subnet_id_new").html();
                    if (subnet_id_new % 1 === 0) {
                        var section_id_new = $(".section_id_new").html(), ua = window.navigator.userAgent, msie = ua.indexOf("MSIE ");
                        if (msie > 0 || navigator.userAgent.match(/Trident.*rv\:11\./)) var base = $(".iebase").html(); else var base = "";
                        var prettyLinks = $("#prettyLinks").html();
                        "Yes" == prettyLinks ? setTimeout(function() {
                            window.location = base + "subnets/" + section_id_new + "/" + subnet_id_new + "/";
                        }, 1500) : setTimeout(function() {
                            window.location = base + "?page=subnets&section=" + section_id_new + "&subnetId=" + subnet_id_new;
                        }, 1500);
                    } else setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                } else setTimeout(function() {
                    window.location.reload();
                }, 1500); else subnetData.search("freespace") != -1 ? setTimeout(function() {
                    window.location.reload();
                }, 1500) : subnetData.search("ipcalc") != -1 || setTimeout(function() {
                    window.location.reload();
                }, 1500);
            } else hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", "#get-ripe", function() {
        showSpinner();
        var subnet = $("form#editSubnetDetails input[name=subnet]").val();
        return $.post("ajx/admin/subnets/ripe-query", {
            subnet: subnet
        }, function(data) {
            showPopup("popup_w500", data, !0), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", "#ripeMatchSubmit", function() {
        var cfields_temp = $("form#ripe-fields").serialize(), cfields = cfields_temp.split("&");
        for (index = 0; index < cfields.length; ++index) if (cfields[index].indexOf("=0") > -1) ; else {
            console.log(cfields[index]);
            var cdata = cfields[index].split("=");
            $("form#editSubnetDetails input[name=" + cdata[1] + "]").val(cdata[0].replace(/___/g, " "));
        }
        hidePopup2();
    }), $(".showSubnetPerm").click(function() {
        showSpinner();
        var subnetId = $(this).attr("data-subnetId"), sectionId = $(this).attr("data-sectionId");
        return $.post("ajx/admin/subnets/permissions-show", {
            subnetId: subnetId,
            sectionId: sectionId,
            action: "read"
        }, function(data) {
            $("#popupOverlay div.popup_w500").html(data), showPopup("popup_w500"), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", ".editSubnetPermissionsSubmit", function() {
        showSpinner();
        var perms = $("form#editSubnetPermissions").serialize();
        return $.post("/ajx/admin/subnets/permissions-submit", perms, function(data) {
            $(".editSubnetPermissionsResult").html(data), reload_window(data);
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", ".dropdown-subnets li a", function() {
        var subnet = $(this).attr("data-cidr"), inputfield = $("form#editSubnetDetails input[name=subnet]");
        return $(inputfield).val(subnet), $(".dropdown-subnets").parent().removeClass("open"), 
        !1;
    }), $(".editSubnetLink").click(function() {
        return showSpinner(), $.post("ajx/admin/subnets/linked-subnet", {
            subnetId: $(this).attr("data-subnetId")
        }, function(data) {
            $("#popupOverlay div.popup_w500").html(data), showPopup("popup_w500"), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", ".linkSubnetSave", function() {
        return showSpinner(), $.post("/ajx/admin/subnets/linked-subnet-submit", $("form#editLinkedSubnet").serialize(), function(data) {
            $(".linkSubnetSaveResult").html(data), reload_window(data);
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", "#createSubnetFromCalc", function() {
        $("tr#selectSection").show();
    }), $(document).on("change", "select#selectSectionfromIPCalc", function() {
        var sectionId = $(this).val(), subnet = $("table.ipCalcResult td#sub2").html(), bitmask = $("table.ipCalcResult td#sub4").html();
        if ("IPv6" == $("table.ipCalcResult td#sub0").html()) var postdata = "sectionId=" + sectionId + "&subnet=" + $("table.ipCalcResult td#sub3").html() + "&bitmask=&action=add&location=ipcalc"; else var postdata = "sectionId=" + sectionId + "&subnet=" + subnet + "&bitmask=" + bitmask + "&action=add&location=ipcalc";
        $("table.newSections ul#sections li#" + sectionId).addClass("active"), $.post("/ajx/admin/subnets/edit", postdata, function(data) {
            $("#popupOverlay div.popup_w700").html(data), showPopup("popup_w700"), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        });
    }), $(document).on("click", ".createfromfree", function() {
        var sectionId = $(this).attr("data-sectionId"), cidr = $(this).attr("data-cidr"), freespaceMSISD = $(this).attr("data-masterSubnetId"), cidrArr = cidr.split("/"), subnet = cidrArr[0], bitmask = cidrArr[1], postdata = "sectionId=" + sectionId + "&subnet=" + subnet + "&bitmask=" + bitmask + "&freespaceMSID=" + freespaceMSISD + "&action=add&location=ipcalc";
        return $.post("/ajx/admin/subnets/edit", postdata, function(data) {
            $("#popupOverlay div.popup_w700").html(data), showPopup("popup_w700"), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", ".edit_subnet, button.edit_subnet, button#add_subnet", function() {
        var subnetId = $(this).attr("data-subnetId"), sectionId = $(this).attr("data-sectionId"), action = $(this).attr("data-action"), postdata = "sectionId=" + sectionId + "&subnetId=" + subnetId + "&action=" + action + "&location=IPaddresses";
        return $.post("/ajx/admin/subnets/edit", postdata, function(data) {
            $("#popupOverlay div.popup_w700").html(data), showPopup("popup_w700"), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("change", "select[name=vlanId]", function() {
        var domain = $("select[name=vlanId] option:selected").attr("data-domain");
        return "Add" == $(this).val() && (showSpinner(), $.post("/ajx/admin/vlans/edit", {
            action: "add",
            fromSubnet: "true",
            domain: domain
        }, function(data) {
            showPopup("popup_w400", data, !0), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        })), !1;
    }), $(document).on("click", ".vlanManagementEditFromSubnetButton", function() {
        showSpinner();
        var postData = $("form#vlanManagementEditFromSubnet").serialize();
        return $.post("/ajx/admin/vlans/edit-result", postData, function(data) {
            if ($("div.vlanManagementEditFromSubnetResult").html(data).show(), data.search("alert-danger") == -1 && data.search("error") == -1) {
                var vlanId = $("#vlanidforonthefly").html(), sectionId = $("#editSubnetDetails input[name=sectionId]").val();
                $.post("/ajx/admin/subnets/edit-vlan-dropdown", {
                    vlanId: vlanId,
                    sectionId: sectionId
                }, function(data) {
                    $(".editSubnetDetails td#vlanDropdown").html(data), hideSpinner();
                }).fail(function(jqxhr, textStatus, errorThrown) {
                    showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
                }), setTimeout(function() {
                    hidePopup("popup_w400", !0), hidePopup2(), parameter = null;
                }, 1e3);
            } else hideSpinner();
        }), !1;
    }), $(".vlansearchsubmit").click(function() {
        showSpinner();
        var search = $("input.vlanfilter").val(), location = $("input.vlanfilter").attr("data-location"), prettyLinks = $("#prettyLinks").html();
        "Yes" == prettyLinks ? setTimeout(function() {
            window.location = location + search + "/";
        }, 500) : setTimeout(function() {
            window.location = location + "&sPage=" + search;
        }, 500);
        var prettyLinks = $("#prettyLinks").html();
        return "Yes" == prettyLinks ? setTimeout(function() {
            window.location = base + "subnets/" + section_id_new + "/" + subnet_id_new + "/";
        }, 1500) : setTimeout(function() {
            window.location = base + "?page=subnets&section=" + section_id_new + "&subnetId=" + subnet_id_new;
        }, 1500), !1;
    }), $("#add_folder, .add_folder").click(function() {
        showSpinner();
        var subnetId = $(this).attr("data-subnetId"), sectionId = $(this).attr("data-sectionId"), action = $(this).attr("data-action"), postdata = "sectionId=" + sectionId + "&subnetId=" + subnetId + "&action=" + action + "&location=IPaddresses";
        return $.post("/ajx/admin/subnets/edit-folder", postdata, function(data) {
            $("#popupOverlay div.popup_w700").html(data), showPopup("popup_w700"), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", ".editFolderSubmit", function() {
        showSpinner();
        var postData = $("form#editFolderDetails").serialize();
        return $.post("/ajx/admin/subnets/edit-folder-result", postData, function(data) {
            $(".manageFolderEditResult").html("").html(data), reload_window(data);
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", ".editFolderSubmitDelete", function() {
        showSpinner();
        var subnetId = $(this).attr("data-subnetId"), description = $("form#editFolderDetails #field-description").val(), csrf_cookie = $("form#editFolderDetails input[name=csrf_cookie]").val(), postData = "subnetId=" + subnetId + "&description=" + description + "&action=delete&csrf_cookie=" + csrf_cookie;
        return "editFolderSubmitDelete" == $(this).attr("id") && (postData += "&deleteconfirm=yes"), 
        $.post("/ajx/admin/subnets/edit-folder-result", postData, function(data) {
            $(".manageFolderEditResult").html(data), reload_window(data);
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", ".editSwitch", function() {
        open_popup("600", "ajx/admin/devices/edit", {
            switchId: $(this).attr("data-switchid"),
            action: $(this).attr("data-action")
        });
    }), $(document).on("click", "#editSwitchsubmit", function() {
        submit_popup_data(".switchManagementEditResult", "ajx/admin/devices/edit-result", $("form#switchManagementEdit").serialize());
    }), $(document).on("click", ".editSwitchSNMP", function() {
        open_popup("400", "ajx/admin/devices/edit-snmp", {
            switchId: $(this).attr("data-switchid"),
            action: $(this).attr("data-action")
        });
    }), $(document).on("click", "#editSwitchSNMPsubmit", function() {
        submit_popup_data(".switchSNMPManagementEditResult", "ajx/admin/devices/edit-snmp-result", $("form#switchSNMPManagementEdit").serialize());
    }), $(document).on("click", "#test-snmp", function() {
        return open_popup("700", "ajx/admin/devices/edit-snmp-test", $("form#switchSNMPManagementEdit").serialize(), !0), 
        !1;
    }), $(document).on("click", "#snmp-routing", function() {
        return open_popup("700", "ajx/subnets/scan/subnet-scan-snmp-route", "", !0), !1;
    }), $(document).on("click", "#snmp-vlan", function() {
        return open_popup("700", "ajx/admin/vlans/vlans-scan", {
            domainId: $(this).attr("data-domainid")
        }, !0), !1;
    }), $(document).on("click", ".show-vlan-scan-result", function() {
        return submit_popup_data(".vlan-scan-result", "ajx/admin/vlans/vlans-scan-execute", $("form#select-devices-vlan-scan").serialize(), !0), 
        !1;
    }), $(document).on("click", "#saveVlanScanResults", function() {
        return submit_popup_data("#vlanScanAddResult", "ajx/admin/vlans/vlans-scan-result", $("form#scan-snmp-vlan-form").serialize()), 
        !1;
    }), $(document).on("click", "#snmp-vrf", function() {
        return open_popup("700", "ajx/admin/vrfs/vrf-scan", !0), !1;
    }), $(document).on("click", ".show-vrf-scan-result", function() {
        return submit_popup_data(".vrf-scan-result", "ajx/admin/vrfs/vrf-scan-execute", $("form#select-devices-vrf-scan").serialize(), !0), 
        !1;
    }), $(document).on("click", "#saveVrfScanResults", function() {
        return submit_popup_data("#vrfScanAddResult", "ajx/admin/vrfs/vrf-scan-result", $("form#scan-snmp-vrf-form").serialize()), 
        !1;
    }), $(document).on("click", ".select-snmp-subnet", function() {
        return $("form#editSubnetDetails input[name=subnet]").val($(this).attr("data-subnet") + "/" + $(this).attr("data-mask")), 
        hidePopup2(), !1;
    }), $(document).on("click", "#snmp-routing-section", function() {
        return open_popup("masks", "ajx/subnets/scan/subnet-scan-snmp-route-all", {
            sectionId: $(this).attr("data-sectionId"),
            subnetId: $(this).attr("data-subnetId")
        }), !1;
    }), $(document).on("click", ".remove-snmp-results", function() {
        $("tbody#" + $(this).attr("data-target")).remove(), $(this).parent().remove();
    }), $(document).on("click", ".remove-snmp-subnet", function() {
        return $("#editSubnetDetailsSNMPallTable tr#tr-" + $(this).attr("data-target-subnet")).remove(), 
        !1;
    }), $(document).on("click", "#add-subnets-to-section-snmp", function() {
        return submit_popup_data(".add-subnets-to-section-snmp-result", "ajx/subnets/scan/subnet-scan-snmp-route-all-result", $("form#editSubnetDetailsSNMPall").serialize()), 
        !1;
    }), $(document).on("click", ".editDevType", function() {
        open_popup("400", "ajx/admin/device-types/edit", {
            id: $(this).attr("data-id"),
            action: $(this).attr("data-action")
        });
    }), $(document).on("click", "#editDevTypeSubmit", function() {
        submit_popup_data(".devTypeEditResult", "ajx/admin/device-types/edit-result", $("form#devTypeEdit").serialize());
    }), $(document).on("click", ".editRack", function() {
        return open_popup("400", "ajx/admin/racks/edit", {
            rackid: $(this).attr("data-rackid"),
            action: $(this).attr("data-action")
        }), !1;
    }), $(document).on("click", "#editRacksubmit", function() {
        submit_popup_data(".rackManagementEditResult", "ajx/admin/racks/edit-result", $("form#rackManagementEdit").serialize());
    }), $(document).on("click", ".editRackDevice", function() {
        return open_popup("400", "ajx/admin/racks/edit-rack-devices", {
            rackid: $(this).attr("data-rackid"),
            deviceid: $(this).attr("data-deviceid"),
            action: $(this).attr("data-action"),
            csrf_cookie: $(this).attr("data-csrf")
        }), !1;
    }), $(document).on("click", "#editRackDevicesubmit", function() {
        submit_popup_data(".rackDeviceManagementEditResult", "ajx/admin/racks/edit-rack-devices-result", $("form#rackDeviceManagementEdit").serialize());
    }), $(document).on("click", ".showRackPopup", function() {
        return open_popup("400", "ajx/tools/racks/show-rack-popup", {
            rackid: $(this).attr("data-rackid"),
            deviceid: $(this).attr("data-deviceid")
        }, !0), !1;
    }), $(document).on("click", ".editLocation", function() {
        return open_popup("700", "ajx/admin/locations/edit", {
            id: $(this).attr("data-id"),
            action: $(this).attr("data-action")
        }), !1;
    }), $(document).on("click", "#editLocationSubmit", function() {
        return submit_popup_data(".editLocationResult", "ajx/admin/locations/edit-result", $("form#editLocation").serialize()), 
        !1;
    }), $(document).on("click", ".editPSTN", function() {
        return open_popup("700", "ajx/tools/pstn-prefixes/edit", {
            id: $(this).attr("data-id"),
            action: $(this).attr("data-action")
        }), !1;
    }), $(document).on("click", "#editPSTNSubmit", function() {
        return submit_popup_data(".editPSTNResult", "ajx/tools/pstn-prefixes/edit-result", $("form#editPSTN").serialize()), 
        !1;
    }), $(document).on("click", ".editPSTNnumber", function() {
        return open_popup("700", "ajx/tools/pstn-prefixes/edit-number", {
            id: $(this).attr("data-id"),
            action: $(this).attr("data-action")
        }), !1;
    }), $(document).on("click", "#editPSTNnumberSubmit", function() {
        return submit_popup_data(".editPSTNnumberResult", "ajx/tools/pstn-prefixes/edit-number-result", $("form#editPSTNnumber").serialize()), 
        !1;
    }), $(document).on("click", ".editNat", function() {
        return open_popup("700", "ajx/admin/nat/edit", {
            id: $(this).attr("data-id"),
            action: $(this).attr("data-action")
        }), !1;
    }), $(document).on("click", ".mapNat", function() {
        return open_popup("700", "ajx/admin/nat/edit-map", {
            id: $(this).attr("data-id"),
            object_type: $(this).attr("data-object-type"),
            object_id: $(this).attr("data-object-id")
        }), !1;
    }), $(document).on("click", "#editNatSubmit", function() {
        var action = $("form#editNat input[name=action]").val();
        return "add" === action ? ($.post("ajx/admin/nat/edit-result", $("form#editNat").serialize(), function(data) {
            $(".editNatResult").html(data), data.search("alert-danger") == -1 && data.search("error") == -1 ? setTimeout(function() {
                open_popup("700", "ajx/admin/nat/edit", {
                    id: $("div.new_nat_id").html(),
                    action: "edit"
                }), hidePopup2(), parameter = null;
            }, 1e3) : hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1) : void submit_popup_data(".editNatResult", "ajx/admin/nat/edit-result", $("form#editNat").serialize());
    }), $(document).on("click", ".removeNatItem", function() {
        var id = $(this).attr("data-id");
        return showSpinner(), $.post("ajx/admin/nat/item-remove", {
            id: $(this).attr("data-id"),
            type: $(this).attr("data-type"),
            item_id: $(this).attr("data-item-id"),
            csrf_cookie: $("form#editNat input[name=csrf_cookie]").val()
        }, function(data) {
            $("#popupOverlay2 div.popup_w500").html(data), showPopup("popup_w700", data, !0), 
            data.search("alert-danger") == -1 && data.search("error") == -1 ? setTimeout(function() {
                open_popup("700", "ajx/admin/nat/edit", {
                    id: id,
                    action: "edit"
                }), hidePopup2(), parameter = null;
            }, 1e3) : hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", ".addNatItem", function() {
        return open_popup("700", "ajx/admin/nat/item-add", {
            id: $(this).attr("data-id"),
            type: $(this).attr("data-type"),
            object_type: $(this).attr("data-object-type"),
            object_id: $(this).attr("data-object-id")
        }, !0), !1;
    }), $(document).on("submit", "form#search_nats", function() {
        return showSpinner(), $.post("ajx/admin/nat/item-add-search", $(this).serialize(), function(data) {
            $("#nat_search_results").html(data), hideSpinner();
        }), !1;
    }), $(document).on("click", "a.addNatObjectFromSearch", function() {
        var id = $(this).attr("data-id"), reload = $(this).attr("data-reload");
        return showSpinner(), $.post("ajx/admin/nat/item-add-submit", {
            id: $(this).attr("data-id"),
            type: $(this).attr("data-type"),
            object_type: $(this).attr("data-object-type"),
            object_id: $(this).attr("data-object-id")
        }, function(data) {
            $("#nat_search_results_commit").html(data), data.search("alert-danger") == -1 && data.search("error") == -1 ? "true" == reload ? reload_window(data) : setTimeout(function() {
                open_popup("700", "ajx/admin/nat/edit", {
                    id: id,
                    action: "edit"
                }), hidePopup2(), parameter = null;
            }, 1e3) : hideSpinner();
        }), !1;
    }), $(".editType").click(function() {
        open_popup("400", "ajx/admin/tags/edit", {
            id: $(this).attr("data-id"),
            action: $(this).attr("data-action")
        });
    }), $(document).on("click", "#editTypesubmit", function() {
        submit_popup_data(".editTypeResult", "ajx/admin/tags/edit-result", $("form#editType").serialize());
    }), $(document).on("click", ".editVLAN", function() {
        vlanNum = $(this).attr("data-number") ? $(this).attr("data-number") : "", open_popup("400", "ajx/admin/vlans/edit", {
            vlanId: $(this).attr("data-vlanid"),
            action: $(this).attr("data-action"),
            vlanNum: vlanNum,
            domain: $(this).attr("data-domain")
        });
    }), $(document).on("click", "#editVLANsubmit", function() {
        submit_popup_data(".vlanManagementEditResult", "ajx/admin/vlans/edit-result", $("form#vlanManagementEdit").serialize());
    }), $(".moveVLAN").click(function() {
        open_popup("400", "ajx/admin/vlans/move-vlan", {
            vlanId: $(this).attr("data-vlanid")
        });
    }), $(document).on("click", "#moveVLANsubmit", function() {
        submit_popup_data(".moveVLANSubmitResult", "ajx/admin/vlans/move-vlan-result", $("form#moveVLAN").serialize());
    }), $(".editVLANdomain").click(function() {
        open_popup("500", "ajx/admin/vlans/edit-domain", {
            id: $(this).attr("data-domainid"),
            action: $(this).attr("data-action")
        });
    }), $(document).on("click", "#editVLANdomainsubmit", function() {
        submit_popup_data(".domainEditResult", "ajx/admin/vlans/edit-domain-result", $("form#editVLANdomain").serialize());
    }), $(".vrfManagement").click(function() {
        open_popup("500", "ajx/admin/vrfs/edit", {
            vrfId: $(this).attr("data-vrfid"),
            action: $(this).attr("data-action")
        });
    }), $(document).on("click", "#editVRF", function() {
        submit_popup_data(".vrfManagementEditResult", "ajx/admin/vrfs/edit-result", $("form#vrfManagementEdit").serialize());
    }), $(".nameserverManagement").click(function() {
        open_popup("700", "ajx/admin/nameservers/edit", {
            nameserverId: $(this).attr("data-nameserverid"),
            action: $(this).attr("data-action")
        });
    }), $(document).on("click", "#add_nameserver", function() {
        showSpinner();
        var num = $(this).attr("data-id");
        return $("table#nameserverManagementEdit2 tbody#nameservers").append("<tr id='namesrv-" + num + "'><td>Nameserver " + num + "</td><td><input type='text' class='rd form-control input-sm' name='namesrv-" + num + "'></input><td><button class='btn btn-sm btn-default' id='remove_nameserver' data-id='namesrv-" + num + "'><i class='fa fa-trash-o'></i></buttom></td></td></tr>"), 
        num++, $(this).attr("data-id", num), hideSpinner(), !1;
    }), $(document).on("click", "#remove_nameserver", function() {
        showSpinner();
        var id = $(this).attr("data-id"), el = document.getElementById(id);
        return el.parentNode.removeChild(el), hideSpinner(), !1;
    }), $(document).on("click", "#editNameservers", function() {
        submit_popup_data(".nameserverManagementEditResult", "ajx/admin/nameservers/edit-result", $("form#nameserverManagementEdit").serialize());
    }), $("table#requestedIPaddresses button").click(function() {
        open_popup("700", "ajx/admin/requests/edit", {
            requestId: $(this).attr("data-requestid")
        });
    }), $(document).on("click", "button.manageRequest", function() {
        var postValues = $("form.manageRequestEdit").serialize(), action = $(this).attr("data-action"), postData = postValues + "&action=" + action;
        submit_popup_data(".manageRequestResult", "ajx/admin/requests/edit-result", postData);
    }), $(".shareTemp").click(function() {
        return open_popup("700", "ajx/tools/temp-shares/edit", {
            type: $(this).attr("data-type"),
            id: $(this).attr("data-id")
        }), !1;
    }), $(document).on("click", "#shareTempSubmit", function() {
        submit_popup_data(".shareTempSubmitResult", "ajx/tools/temp-shares/edit-result", $("form#shareTempEdit").serialize());
    }), $(".removeSharedTemp").click(function() {
        showPopup("popup_w400"), submit_popup_data("#popupOverlay .popup_w400", "ajx/tools/temp-shares/delete-result", {
            code: $(this).attr("data-code")
        }), hideSpinner();
    }), $("form#ripeImport").submit(function() {
        showSpinner();
        var as = $(this).serialize();
        return $.post("/ajx/admin/ripe-import/ripe-telnet", as, function(data) {
            $("div.ripeImportTelnet").html(data).fadeIn("fast"), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", "table.asImport .removeSubnet", function() {
        $(this).parent("tr").remove(), hideTooltips();
    }), $(document).on("submit", "form#asImport", function() {
        showSpinner();
        var importData = $(this).serialize();
        return $.post("/ajx/admin/ripe-import/import-subnets", importData, function(data) {
            $("div.ripeImportResult").html(data).slideDown("fast"), data.search("alert-danger") == -1 && data.search("error") == -1 ? ($("table.asImport").delay(1e3).fadeOut("fast"), 
            hideSpinner()) : hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $("button#filterIPSave").click(function() {
        showSpinner();
        var addata = $("form#filterIP").serialize();
        return $.post("/ajx/admin/filter-fields/filter-result", addata, function(data) {
            $("div.filterIPResult").html(data).slideDown("fast"), data.search("alert-danger") == -1 && data.search("error") == -1 ? ($("div.filterIPResult").delay(2e3).fadeOut("slow"), 
            hideSpinner()) : hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", ".edit-cf", function() {
        showSpinner();
        var action = $(this).attr("data-action"), id = $(this).attr("data-id"), table = $(this).attr("data-table");
        return $.post("/ajx/admin/custom-fields/edit", {
            action: action,
            id: id,
            table: table
        }, function(data) {
            $("#popupOverlay div.popup_w500").html(data), showPopup("popup_w500"), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", "#editcustomSubmit", function() {
        showSpinner();
        var field = $("form#editCustomFields").serialize();
        return $.post("/ajx/admin/custom-fields/edit-result", field, function(data) {
            $("div.customEditResult").html(data).slideDown("fast"), reload_window(data);
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $("table.customIP button.down").click(function() {
        showSpinner();
        var current = $(this).attr("data-fieldname"), next = $(this).attr("data-nextfieldname"), table = $(this).attr("data-table");
        return $.post("/ajx/admin/custom-fields/order", {
            current: current,
            next: next,
            table: table
        }, function(data) {
            $("div." + table + "-order-result").html(data).slideDown("fast"), reload_window(data);
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(".edit-custom-filter").click(function() {
        showSpinner();
        var table = $(this).attr("data-table");
        return $.post("/ajx/admin/custom-fields/filter", {
            table: table
        }, function(data) {
            $("#popupOverlay div.popup_w500").html(data), showPopup("popup_w500"), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", "#editcustomFilterSubmit", function() {
        showSpinner();
        var field = $("form#editCustomFieldsFilter").serialize();
        return $.post("/ajx/admin/custom-fields/filter-result", field, function(data) {
            $("div.customEditFilterResult").html(data).slideDown("fast"), reload_window(data);
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("focus", "input#cf-display-name", function() {
        $(this).keyup(function(e) {
            ($(this).val().length < 25 && e.which >= 48 && e.which <= 90 || e.which >= 188 || 46 == e.which || 8 == e.which) && $("input#cf-name").val(safe_string($(this).val().toLowerCase()));
        });
    }), $(document).on("switchChange.bootstrapSwitch", "input#cf-required", function(event, state) {
        $(this).bootstrapSwitch("state") ? ($("input#cf-null").bootstrapSwitch("state", !1), 
        $("input#cf-null").bootstrapSwitch("readonly", !0)) : $("input#cf-null").bootstrapSwitch("disabled", !1);
    }), $(document).on("change", "select#cf-type", function() {
        var selectedType = $(this).find(":selected").val(), showDateWarning = $("input#date-must-allow-null").val();
        if ("enum" == selectedType || "set" == selectedType) $("input#cf-limit").attr("disabled", !0), 
        $("tr#set-values-row").css("display", ""), $("input#cf-limit").val(""); else if ($.inArray(selectedType, [ "date", "datetime", "time", "timestamp" ]) != -1) {
            $("input#cf-limit").val(""), $("input#cf-limit").attr("disabled", !0), $("input#cf-null").bootstrapSwitch("state", !0);
            var dateWarning = "<div class=\"alert alert-warning\"><strong>Warning:</strong><hr /> Your database server is running with sql mode including NO_ZERO_IN_DATE and/or NO_ZERO_DATE. You MUST either set Nulls as 'yes' or modify your server config to allow 0 values in date fields. If not, the date column  will fill existing rows with 0 values, which will cause errors on all later DB changes.  See: <br />https://dev.mysql.com/doc/refman/5.7/en/sql-mode.html#sqlmode_no_zero_date</div>";
            1 == showDateWarning && $("div.customEditResult").html(dateWarning), "timestamp" == selectedType && $("input#cf-default").val("CURRENT_TIMESTAMP");
        } else $("input#cf-limit").attr("disabled", !1), $("input#cf-limit").attr("readonly", !1), 
        $("tr#set-values-row").css("display", "none"), "boolean" == selectedType && ($("input#cf-limit").val(1), 
        $("input#cf-limit").attr("readonly", !0)), "text" == selectedType && $("input#cf-limit").val(65535), 
        "string" == selectedType && $("input#cf-limit").val(64), "integer" == selectedType && $("input#cf-limit").val(11), 
        "float" == selectedType && $("input#cf-limit").val("12,4"), "char" == selectedType && $("input#cf-limit").val(8);
    }), $("button.lang").click(function() {
        showSpinner();
        var langid = $(this).attr("data-langid"), action = $(this).attr("data-action");
        return $.post("/ajx/admin/languages/edit", {
            langid: langid,
            action: action
        }, function(data) {
            $("#popupOverlay div.popup_w400").html(data), showPopup("popup_w400"), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", "#langEditSubmit", function() {
        showSpinner();
        var ldata = $("form#langEdit").serialize();
        return $.post("/ajx/admin/languages/edit-result", ldata, function(data) {
            $("div.langEditResult").html(data).slideDown("fast"), reload_window(data);
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $("button.wedit").click(function() {
        showSpinner();
        var wid = $(this).attr("data-wid"), action = $(this).attr("data-action");
        return $.post("/ajx/admin/widgets/edit", {
            wid: wid,
            action: action
        }, function(data) {
            $("#popupOverlay div.popup_w500").html(data), showPopup("popup_w500"), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", "#widgetEditSubmit", function() {
        showSpinner();
        var ldata = $("form#widgetEdit").serialize();
        return $.post("/ajx/admin/widgets/edit-result", ldata, function(data) {
            $("div.widgetEditResult").html(data).slideDown("fast"), reload_window(data);
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $("button.editAPI").click(function() {
        showSpinner();
        var appid = $(this).attr("data-appid"), action = $(this).attr("data-action");
        return $.post("/ajx/admin/api/edit", {
            appid: appid,
            action: action
        }, function(data) {
            $("#popupOverlay div.popup_w700").html(data), showPopup("popup_w700"), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", "#apiEditSubmit", function() {
        showSpinner();
        var apidata = $("form#apiEdit").serialize();
        return $.post("/ajx/admin/api/edit-result", apidata, function(data) {
            $("div.apiEditResult").html(data).slideDown("fast"), reload_window(data);
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", "#genMD5String", function() {
        var seconds = new Date().getTime(), md5 = $.md5(seconds);
        return $("input#md5string").val(md5), !1;
    }), $(".editAgent").click(function() {
        open_popup("700", "ajx/admin/scan-agents/edit", {
            id: $(this).attr("data-id"),
            action: $(this).attr("data-action")
        });
    }), $(document).on("click", "#agentEditSubmit", function() {
        submit_popup_data(".agentEditResult", "ajx/admin/scan-agents/edit-result", $("form#agentEdit").serialize());
    }), $("button#searchReplaceSave").click(function() {
        showSpinner();
        var searchData = $("form#searchReplace").serialize();
        return $.post("/ajx/admin/replace-fields/result", searchData, function(data) {
            $("div.searchReplaceResult").html(data), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $("button#XLSdump").click(function() {
        showSpinner();
        var csrf = $(this).attr("data-csrf"), type = $(this).attr("data-type");
        $("div.dl").remove(), $("div.exportDIV").append("<div style='display:none' class='dl'><iframe src='/ajx/admin/import-export/generate-xls.php'></iframe></div>"), 
        hideSpinner();
    }), $("button#MySQLdump").click(function() {
        showSpinner(), $("div.dl").remove(), $("div.exportDIV").append("<div style='display:none' class='dl'><iframe src='/ajx/admin/import-export/generate-mysql.php'></iframe></div>"), 
        hideSpinner();
    }), $("button#hostfileDump").click(function() {
        showSpinner(), $("div.dl").remove(), $("div.exportDIV").append("<div style='display:none' class='dl'><iframe src='/ajx/admin/import-export/generate-hosts.php'></iframe></div>"), 
        hideSpinner();
    }), $("button.dataExport").click(function() {
        var implemented = [ "vrf", "vlan", "subnets", "ipaddr" ], popsize = {};
        popsize.subnets = "w700", popsize.ipaddr = "w700";
        var dataType = $("select[name=dataType]").find(":selected").val();
        return hidePopups(), implemented.indexOf(dataType) > -1 ? (showSpinner(), $.post("/ajx/admin/import-export/export-" + dataType + "-field-select", function(data) {
            void 0 !== popsize[dataType] ? ($("div.popup_" + popsize[dataType]).html(data), 
            showPopup("popup_" + popsize[dataType])) : ($("#popupOverlay div.popup_w400").html(data), 
            showPopup("popup_w400")), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        })) : $.post("/ajx/admin/import-export/not-implemented", function(data) {
            $("#popupOverlay div.popup_w400").html(data), showPopup("popup_w400");
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", "button#dataExportSubmit", function() {
        var dataType = $(this).attr("data-type"), exportFields = $("form#selectExportFields").serialize();
        switch (dataType) {
          case "vrf":
            $("div.dl").remove(), $("div.exportDIV").append("<div style='display:none' class='dl'><iframe src='/ajx/admin/import-export/export-vrf/?" + exportFields + "'></iframe></div>"), 
            setTimeout(function() {
                hidePopups();
            }, 1500);
            break;

          case "vlan":
            var exportDomains = $("form#selectExportDomains").serialize();
            $("div.dl").remove(), $("div.exportDIV").append("<div style='display:none' class='dl'><iframe src='/ajx/admin/import-export/export-vlan/?" + exportDomains + "&" + exportFields + "'></iframe></div>"), 
            setTimeout(function() {
                hidePopups();
            }, 1500);
            break;

          case "subnets":
            var exportSections = $("form#selectExportSections").serialize();
            $("div.dl").remove(), $("div.exportDIV").append("<div style='display:none' class='dl'><iframe src='/ajx/admin/import-export/export-subnets/?" + exportSections + "&" + exportFields + "'></iframe></div>"), 
            setTimeout(function() {
                hidePopups();
            }, 1500);
            break;

          case "ipaddr":
            var exportSections = $("form#selectExportSections").serialize();
            $("div.dl").remove(), $("div.exportDIV").append("<div style='display:none' class='dl'><iframe src='/ajx/admin/import-export/export-ipaddr/?" + exportSections + "&" + exportFields + "'></iframe></div>"), 
            setTimeout(function() {
                hidePopups();
            }, 1500);
        }
        return !1;
    }), $(document).on("click", "input#exportSelectAll", function() {
        this.checked ? $("input#exportCheck").each(function() {
            this.checked = !0;
        }) : $("input#exportCheck").each(function() {
            this.checked = !1;
        });
    }), $(document).on("click", "input#recomputeSectionSelectAll", function() {
        this.checked ? $("input#recomputeSectionCheck").each(function() {
            this.checked = !0;
        }) : $("input#recomputeSectionCheck").each(function() {
            this.checked = !1;
        });
    }), $(document).on("click", "input#recomputeIPv4SelectAll", function() {
        this.checked ? $("input#recomputeIPv4Check").each(function() {
            this.checked = !0;
        }) : $("input#recomputeIPv4Check").each(function() {
            this.checked = !1;
        });
    }), $(document).on("click", "input#recomputeIPv6SelectAll", function() {
        this.checked ? $("input#recomputeIPv6Check").each(function() {
            this.checked = !0;
        }) : $("input#recomputeIPv6Check").each(function() {
            this.checked = !1;
        });
    }), $(document).on("click", "input#recomputeCVRFSelectAll", function() {
        this.checked ? $("input#recomputeCVRFCheck").each(function() {
            this.checked = !0;
        }) : $("input#recomputeCVRFCheck").each(function() {
            this.checked = !1;
        });
    }), $("button.dataImport").click(function() {
        var implemented = [ "vrf", "vlan", "subnets", "recompute", "ipaddr" ], popsize = {};
        popsize.subnets = "max", popsize.ipaddr = "max";
        var dataType = $("select[name=dataType]").find(":selected").val();
        return hidePopups(), implemented.indexOf(dataType) > -1 ? (showSpinner(), $.post("/ajx/admin/import-export/import-" + dataType + "-select", function(data) {
            void 0 !== popsize[dataType] ? ($("div.popup_" + popsize[dataType]).html(data), 
            showPopup("popup_" + popsize[dataType])) : ($("#popupOverlay div.popup_w700").html(data), 
            showPopup("popup_w700")), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        })) : $.post("/ajx/admin/import-export/not-implemented", function(data) {
            $("#popupOverlay div.popup_w400").html(data), showPopup("popup_w400");
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", "button#dataImportPreview", function() {
        var implemented = [ "vrf", "vlan", "subnets", "recompute", "ipaddr" ], popsize = {};
        popsize.subnets = "max", popsize.recompute = "max", popsize.ipaddr = "max";
        var dataType = $(this).attr("data-type"), importFields = $("form#selectImportFields").serialize();
        return hidePopups(), implemented.indexOf(dataType) > -1 ? (showSpinner(), $.post("/ajx/admin/import-export/import-" + dataType + "-preview/", importFields, function(data) {
            void 0 !== popsize[dataType] ? ($("div.popup_" + popsize[dataType]).html(data), 
            showPopup("popup_" + popsize[dataType])) : ($("#popupOverlay div.popup_w700").html(data), 
            showPopup("popup_w700")), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        })) : $.post("/ajx/admin/import-export/not-implemented", function(data) {
            $("#popupOverlay div.popup_w400").html(data), showPopup("popup_w400");
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", "button#dataImportSubmit", function() {
        var implemented = [ "vrf", "vlan", "subnets", "recompute", "ipaddr" ], popsize = {};
        popsize.subnets = "max", popsize.recompute = "max", popsize.ipaddr = "max";
        var dataType = $(this).attr("data-type"), importFields = $("form#selectImportFields").serialize();
        return hidePopups(), implemented.indexOf(dataType) > -1 ? (showSpinner(), $.post("/ajx/admin/import-export/import-" + dataType + "/", importFields, function(data) {
            void 0 !== popsize[dataType] ? ($("div.popup_" + popsize[dataType]).html(data), 
            showPopup("popup_" + popsize[dataType])) : ($("#popupOverlay div.popup_w700").html(data), 
            showPopup("popup_w700")), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        })) : $.post("/ajx/admin/import-export/not-implemented", function(data) {
            $("#popupOverlay div.popup_w400").html(data), showPopup("popup_w400");
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $("button.dataRecompute").click(function() {
        return showSpinner(), $.post("/ajx/admin/import-export/import-recompute-select", function(data) {
            $("#popupOverlay div.popup_w700").html(data), showPopup("popup_w700"), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), $(document).on("click", ".btn-tablefix", function() {
        var tableid = $(this).attr("data-tableid"), fieldid = $(this).attr("data-fieldid"), type = $(this).attr("data-type");
        return $.post("/ajx/admin/verify-database/fix", {
            tableid: tableid,
            fieldid: fieldid,
            type: type
        }, function(data) {
            $("div#fix-result-" + tableid + fieldid).html(data).fadeIn("fast"), hideSpinner();
        }).fail(function(jqxhr, textStatus, errorThrown) {
            showError(jqxhr.statusText + "<br>Status: " + textStatus + "<br>Error: " + errorThrown);
        }), !1;
    }), !1;
});