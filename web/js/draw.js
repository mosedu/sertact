(function ($) {
    $.fn.drawpage = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.drawpage');
            return false;
        }
    };

    var defaults = {
        //
        scale: 2.0, // px/mm
        panel: null, // jQuery object for controls
        templates: { text: null, img: null, page: null }
        // шаблоны панелей для типов данных
        //
    };

    var textAttributes = {
        //
        text: "text",
        bold: false,
        italic: false,
        underline: true
    };

    var imgAttributes = {
        //
        src: ""
    };

    var addElement = function(ob, editor) {
        var o = null;
        if( ob.type == "page" ) {
            o = new Page(ob.width, ob.height);
            o.val("editor", editor);
        }
        else if( ob.type == "text" ) {
            o = new Text(ob);
            o.val("editor", editor);
        }
        return o;
    };

    var selected = null;
    var select = function(ob) {
        if( selected !== null ) {
            selected.select(false);
        }
//        ob.select(true);
        selected = ob;
    };

    var addToPanel = function(panel, ob) {
        if( panel === null ) {
            return;
        }
        var data = ob.getData(),
            el = jQuery('<div class="selectregion"><a href="#" class="btn btn-default">'+data.title+'</a><div class="panel-control"></div></div>'),
            dom = ob.getElement();

        panel.append(el);

        dom.on(
            "click",
            function(event) {
                var oControl = panel.find("."+data.type+"-control");
                event.preventDefault();
                ob.select(true);
//                select(ob);

                /*
                                dom.parent().find(".draw-border").remove();
                                dom.append(jQuery('<div class="draw-border"></div>'));
                                panel.find(".panel-control").slideUp("slow", function(){
                                    ob.setControls(oControl);
                                    el.find("div").append(oControl).slideDown("slow");
                                });
                */
                return false;
            }
        );
        el.find("a").on(
            "click",
            function(event) {
                event.preventDefault();
                dom.trigger("click");
                return false;
            }
        );

    };

    var preparePanels = function(panel) {
        if( panel === null ) {
            return;
        }
        var o = jQuery(
            '<div class="text-control">'
            + '<a href="#" title="Bold" class="button-bold btn btn-default">B</a>'
            + '<a href="#" title="Italic" class="button-italic btn btn-default">I</a>'
            + '<a href="#" title="Underline" class="button-underline btn btn-default">U</a>'
            + '<br />'
            + '<a href="#" title="Left" class="button-align-left btn btn-default">Left</a>'
            + '<a href="#" title="Center" class="button-align-center btn btn-default">Center</a>'
            + '<a href="#" title="Right" class="button-align-right btn btn-default">Right</a>'
            + '</div>'
        );

        panel.append(o);
    };

    var methods = {
        init: function(options, regions) {
            var $editor = $(this);
            if ($editor.data('drawpage')) {
                return;
            }

            var settings = $.extend({}, defaults, options || {});

            regions = regions || [];
            var ob = [],
                page;
            for(var i = 0; i < regions.length; i++) {
                var oTmp = null;
                if( regions[i].type == "page" ) {
                    page = addElement(regions[i], $editor); // new Page(regions[i].width, regions[i].height);
                    oTmp = page;
                    page.draw($editor, settings.scale)
                }
                else if( regions[i].type == "text" ) {
                    oTmp = addElement(regions[i], $editor); // new Text(regions[i]);
                    if( page !== null ) {
                        oTmp.draw(page.getElement($editor), settings.scale);
                        addToPanel(settings.panel, oTmp);
                    }
                }

                if( oTmp !== null ) {
                    ob.push(oTmp);
                }
            }

            if( ob.length == 0 ) {
                page = addElement({type: "page", width: 297, height: 210}); // new Page(297, 210);
                ob.unshift(page);
                page.draw($editor, settings.scale);
            }

            preparePanels(settings.panel);

            $editor.data('drawpage', {
                settings: settings,
                regions: ob,
                saved: false
            });
        },
        regions: function() {
            var $editor = $(this),
                r = $editor.data('drawpage').regions
                a = [];
            for(var i= 0; i < r.length; i++) {
                a.push(r[i].getData());
            }
            return a;
        },
        append: function(conf) {
            var $editor = $(this),
                data = $editor.data('drawpage');
            data.regions.push(addElement(conf));
        },
        select: function(ob) {
            select(ob);
        },
        redraw: function() {
            var $editor = $(this),
                data = $editor.data('drawpage'),
                page = null;
            for(var i = 0; i < data.regions.length; i++) {
                if( data.regions[i].type == "page" ) {
                    page = data.regions[i].getElement($editor);
                    data.regions[i].draw($editor, data.settings.scale);
                }
                else if( page !== null ) {
                    data.regions[i].draw(page, data.settings.scale);
                }

            }
        }
    };

    /**************************************************************************************************/

    var Page = function(w, h){
        var data = {
                type: "page",
                width: w,
                height: h,
                title: "page",
                editor: null,
                element: null,
                selected: false
            },
            val = function(name, val) {
                if( (typeof name != "string") || !(name in data) ) {
                    throw new Error('Invalid property '+ name + ' in Page');
                }

                if( typeof val === "undefined") {
                    return data[name];
                }
                data[name] = val;
            };

        return {
            type: "page",
            val: val,
            select: function(b) {},
            size: function(w, h) {
                if( typeof w === "undefined") {
                    return [val("width"), val("height")];
                }
                val("width", w);
                val("height", h);
            },
            draw: function(el, scale) {
                var page = el.find(".draw-page");
                if( page.length == 0 ) {
                    page = jQuery('<div />').addClass('draw-page');
                    el.append(page);
                }
                page.width(val("width") * scale);
                page.height(val("height") * scale);
                val("element", page);
            },
            getElement: function() {
                return val("element");
            },
            getData: function() {
                return {
                    type: val("type"),
                    title: val("title"),
                    width: val("width"),
                    height: val("height")
                };
            }
        };
    };

    /**************************************************************************************************/

    var Text = function(conf){
        var n = jQuery(".draw-text").length,
            data = {
                type: "text",
                text: "text" in conf ? conf.text : "text",
                title: "text " + n,
                bold: false,
                italic: false,
                underline: false,
                align: "left",
                fontsize: 10, // mm
                left: null,   // mm
                right: null,  // mm
                top: null,    // mm
                bottom: null, // mm
                width: null,  // mm
                height: null, // mm
                id: "text-" + n,
                editor: null,
                element: null,
                selected: false
            },
            val = function(name, val) {
                if( (typeof name != "string") || !(name in data) ) {
                    throw new Error('Invalid property '+ name + ' in Page');
                }

                if( typeof val === "undefined") {
                    return data[name];
                }
                data[name] = val;
            };

            var
                styleToVar = function(conf){
                    for(var i in conf) {
                        val(i, conf[i]);
                    }
                },
            stylize = function(ob, scale) {
                var oSt = {
                        "font-size": val("fontsize") * scale + "px",
                        "text-align": val("align"),
                        "font-weight": val("bold") ? "bold" : "normal",
                        "font-style": val("italic") ? "italic" : "normal",
                        "text-decoration": val("underline") ? "underline" : "none"
                    },
                    aKeys = ["left", "right", "top", "bottom", "width", "height"];
                for(var i = 0; i < aKeys.length; i++) {
                    var k = aKeys[i], v = val(k);
                    if( v !== null ) {
                        oSt[k] = v * scale + "px";
                    }
                }
                ob.css(oSt);
            };

        styleToVar(conf);

        var oRet = {
            type: "text",
            val: val,
            select: function(b) {
                var ed = val("editor"),
                    data = ed.data('drawpage');
                b = (typeof b == "undefined") ? true : b;
                if( b ) {
                    ed.drawpage("select", oRet);
                }
                val("selected", b);
                oRet.draw(ed, data.settings.scale);
            },
            text: function(txt) {
                return val("text", txt);
            },
            draw: function(el, scale) {
                var ob = el.find("." + val("id"));
                if( ob.length == 0 ) {
                    ob = jQuery('<div />')
                        .addClass('draw-text')
                        .addClass(val("id"));
                    el.append(ob);
                    val("element", ob);
                }

                try {
                    ob.resizable("destroy");
//                        ob.resizable("disable");
                }
                catch (err) {}

                try {
                    ob.draggable("destroy");
                }
                catch (err) {}

                ob.text(val("text"));

                stylize(ob, scale);

                if( val("selected") ) {
                    var border = ob.parent().find(".draw-border");
                    if( border.length == 0 ) {
                        border = jQuery('<div class="draw-border"></div>');
                    }
                    ob.append(border);
                    ob.resizable({
                        stop: function( event, ui ){
//                            if( val("width") !== null ) {
                                val("width", ob.width() / scale);
//                            }
//                            if( val("height") !== null ) {
                                val("height", ob.height() / scale);
//                            }
                        }
                    });
                }
/*
                panel.find(".panel-control").slideUp("slow", function(){
                    ob.setControls(oControl);
                    el.find("div").append(oControl).slideDown("slow");
                });
*/
                ob.draggable({
                    containment: "parent",
                    start: function() {
                        setTimeout(function(){ oRet.select(true); }, 20);
                    },
                    stop: function() {
                        var pos = ob.position();
                        if( val("left") !== null ) {
                            val("left", pos.left / scale);
                        }
                        if( val("right") !== null ) {
                            val("right", (ob.parent().width() - pos.left - ob.width()) / scale);
                        }
                        if( val("top") !== null ) {
                            val("top", pos.top / scale);
                        }
                        if( val("bottom") !== null ) {
                            val("bottom", (ob.parent().height() - pos.top - ob.height()) / scale);
                        }
                    }
                });
            },
            setStyle: function(style) {
                styleToVar(style);
            },
            getElement: function() {
                return val("element");
            },
            getData: function() {
                var ob = {};
                for(var i in data) {
                    if( (i == "id") || (i == "editor") || (i == "element") ) { //  || (i == "selected")
                        continue;
                    }
                    var v = data[i];
                    if( v !== null ) {
                        ob[i] = v;
                    }
                }
                return ob;
            }
        };
        return oRet;
    };

})(window.jQuery);

/**************************************************************************************************/
/*
 setControls: function(oControl) {
 var button;
 button = oControl.find(".button-bold");
 if( bold ) {
 button.addClass("btn-success");
 }
 else {
 button.removeClass("btn-success");
 }
 button.off("click").on("click", function(event){
 event.preventDefault();
 var b = jQuery(this);
 b.toggleClass("btn-success");
 bold = b.hasClass("btn-success");
 editor.drawpage('redraw');
 return false;
 });

 button = oControl.find(".button-italic");
 if( italic ) {
 button.addClass("btn-success");
 }
 else {
 button.removeClass("btn-success");
 }

 button = oControl.find(".button-underline");
 if( underline ) {
 button.addClass("btn-success");
 }
 else {
 button.removeClass("btn-success");
 }


 oControl.find(".button-align-left, .button-align-center, .button-align-right").removeClass("btn-success");
 oControl.find(".button-align-" + align).addClass("btn-success");

 },

*/
/*
jQuery(document).ready(function () {
    var pPaint = jQuery("#paint-region"),
        pTool = jQuery("#tool-region"),
        pImgControl = jQuery("#img-control"),
        pTextControl = jQuery("#text-control"),

        scale = 2 // точек/мм
        ;

    pImgControl.hide();
    pTextControl.hide();

    jQuery("#button-addtext")
        .on(
            "click",
            function(event){
                event.preventDefault();
                pPaint.drawpage('append', {type: "text", text: "New text", fontsize: 20});
                pPaint.drawpage('redraw');
                return false;
            }
        );

    pPaint.drawpage(
        {},
        [
            {type: "page", width: 210, height: 297},
            {type: "text", text: "some text", bold: true, align: "center", left: 50, top: 100, width: 100}
        ]
    );
    console.log(pPaint.drawpage('regions'));
});
*/
