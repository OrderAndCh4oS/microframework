﻿var CkEditorImageBrowser = {
    folders: [], images: {}, ckFunctionNum: null, $folderSwitcher: null, $imagesContainer: null, init: function () {
        CkEditorImageBrowser.$folderSwitcher = $("#js-folder-switcher");
        CkEditorImageBrowser.$imagesContainer = $("#js-images-container");
        var a = CkEditorImageBrowser.getQueryStringParam("baseHref");
        if (a) {
            var c = document.head || document.getElementsByTagName("head")[0];
            c.getElementsByTagName("link")[0].href = location.href.replace(/\/[^\/]*$/, "/browser.css");
            c.getElementsByTagName("base")[0].href =
                a
        }
        CkEditorImageBrowser.ckFunctionNum = CkEditorImageBrowser.getQueryStringParam("CKEditorFuncNum");
        CkEditorImageBrowser.initEventHandlers();
        CkEditorImageBrowser.loadData(CkEditorImageBrowser.getQueryStringParam("listUrl"), function () {
            CkEditorImageBrowser.initFolderSwitcher()
        })
    }, loadData: function (a, c) {
        CkEditorImageBrowser.folders = [];
        CkEditorImageBrowser.images = {};
        $.getJSON(a, function (a) {
            $.each(a, function (a, b) {
                "undefined" === typeof b.folder && (b.folder = "Images");
                "undefined" === typeof b.thumb && (b.thumb = b.image);
                CkEditorImageBrowser.addImage(b.folder, b.image, b.thumb)
            });
            c()
        }).error(function (c, d, b) {
            CkEditorImageBrowser.$imagesContainer.html(200 > c.status || 400 <= c.status ? "HTTP Status: " + c.status + "/" + c.statusText + ': "<strong style="color: red;">' + a + '</strong>"' : "parsererror" === d ? d + ': invalid JSON file: "<strong style="color: red;">' + a + '</strong>": ' + b.message : d + " / " + c.statusText + " / " + b.message)
        })
    }, addImage: function (a, c, e) {
        "undefined" === typeof CkEditorImageBrowser.images[a] && (CkEditorImageBrowser.folders.push(a),
            CkEditorImageBrowser.images[a] = []);
        CkEditorImageBrowser.images[a].push({imageUrl: c, thumbUrl: e})
    }, initFolderSwitcher: function () {
        var a = CkEditorImageBrowser.$folderSwitcher;
        a.find("li").remove();
        $.each(CkEditorImageBrowser.folders, function (c, e) {
            $("<li></li>").data("idx", c).text(e).appendTo(a)
        });
        0 === CkEditorImageBrowser.folders.length ? (a.remove(), CkEditorImageBrowser.$imagesContainer.text("No images.")) : (1 === CkEditorImageBrowser.folders.length && a.hide(), a.find("li:first").click())
    }, renderImagesForFolder: function (a) {
        var a =
            CkEditorImageBrowser.images[a], c = $("#js-template-image").html();
        CkEditorImageBrowser.$imagesContainer.html("");
        $.each(a, function (a, d) {
            var b = c, b = b.replace("%imageUrl%", d.imageUrl), b = b.replace("%thumbUrl%", d.thumbUrl), b = $($.parseHTML(b));
            CkEditorImageBrowser.$imagesContainer.append(b)
        })
    }, initEventHandlers: function () {
        $(document).on("click", "#js-folder-switcher li", function () {
            var a = parseInt($(this).data("idx"), 10), a = CkEditorImageBrowser.folders[a];
            $(this).siblings("li").removeClass("active");
            $(this).addClass("active");
            CkEditorImageBrowser.renderImagesForFolder(a)
        });
        $(document).on("click", ".js-image-link", function () {
            window.opener.CKEDITOR.tools.callFunction(CkEditorImageBrowser.ckFunctionNum, $(this).data("url"));
            window.close()
        })
    }, getQueryStringParam: function (a) {
        return (a = window.location.search.match(RegExp("[?&]" + a + "=([^&]*)"))) && 1 < a.length ? decodeURIComponent(a[1]) : null
    }
};