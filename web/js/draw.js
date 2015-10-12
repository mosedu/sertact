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
        scale: 2.0 // px/mm
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

    var addElement = function(ob) {
        var o = null;
        if( ob.type == "page" ) {
            o = new Page(ob.width, ob.height);
        }
        else if( ob.type == "text" ) {
            o = new Text(ob);
        }
        return o;
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
                    page = addElement(regions[i]); // new Page(regions[i].width, regions[i].height);
                    oTmp = page;
                    page.draw($editor, settings.scale)
                }
                else if( regions[i].type == "text" ) {
                    oTmp = addElement(regions[i]); // new Text(regions[i]);
                    if( page !== null ) {
                        oTmp.draw(page.getElement($editor), settings.scale);
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
/*
            for(var i = 0; i < ob.length; i++) {
                if( page === ob[i] ) {
                    ob[i].draw($editor, settings.scale);
                }
                else {
                    ob[i].draw(page.getElement($editor), settings.scale);
                }
            }
*/
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
        var width = w,
            height = h;
        return {
            type: "page",
            size: function(w, h) {
                if( typeof w === "undefined") {
                    return [width, height];
                }
                width = w;
                height = h;
            },
            draw: function(el, scale) {
                var page = el.find(".draw-page");
                if( page.length == 0 ) {
                    page = jQuery('<div />').addClass('draw-page');
                    el.append(page);
                }
                page.width(width * scale);
                page.height(height * scale);
            },
            getElement: function(el) {
                return el.find(".draw-page");
            },
            getData: function() {
                return {
                    type: "page",
                    width: width,
                    height: height
                };
            }
        };
    };

    /**************************************************************************************************/

    var Text = function(data){
        var text = "text" in data ? data.text : "text",
            bold = false,
            italic = false,
            underline = false,
            align = "left",
            fontsize = 10, // mm
            left = null, // mm
            right = null, // mm
            top = null, // mm
            bottom = null, // mm
            width = null, // mm
            height = null, // mm
            id = "text-" + jQuery(".draw-text").length,
            styleToVar = function(style) {
                if( "bold" in style ) {
                    bold = style.bold;
                }
                if( "italic" in style ) {
                    italic = style.italic;
                }
                if( "underline" in style ) {
                    underline = style.underline;
                }
                if( "align" in style ) {
                    align = style.align;
                }
                if( "fontsize" in style ) {
                    fontsize = style.fontsize;
                }
                if( "left" in style ) {
                    left = style.left;
                }
                if( "right" in style ) {
                    right = style.right;
                }
                if( "top" in style ) {
                    top = style.top;
                }
                if( "bottom" in style ) {
                    bottom = style.bottom;
                }
                if( "width" in style ) {
                    width = style.width;
                }
                if( "height" in style ) {
                    height = style.height;
                }
            },
            stylize = function(ob, scale) {
                var oSt = {
                    "font-size": fontsize * scale + "px",
                    "text-align": align,
                    "font-weight": bold ? "bold" : "normal",
                    "font-style": italic ? "italic" : "normal",
                    "text-decoration": underline ? "underline" : "none"
                };
                if( left !== null ) {
                    oSt.left = left * scale + "px";
                }
                if( right !== null ) {
                    oSt.right = right * scale + "px";
                }
                if( top !== null ) {
                    oSt.top = top * scale + "px";
                }
                if( bottom !== null ) {
                    oSt.bottom = bottom * scale + "px";
                }
                if( width !== null ) {
                    oSt.width = width * scale + "px";
                }
                if( height !== null ) {
                    oSt.height = height * scale + "px";
                }
                ob.css(oSt);
            };

        styleToVar(data);

        return {
            type: "text",
            text: function(txt) {
                if( typeof txt === "undefined") {
                    return text;
                }
                text = txt;
            },
            draw: function(el, scale) {
                var ob = el.find("#" + id);
                if( ob.length == 0 ) {
                    ob = jQuery('<div />').addClass('draw-text');
                    ob.attr("id", id);
                    el.append(ob);
                    ob.draggable({
                        containment: "parent" ,
                        stop: function() {
                            var pos = ob.position();
                            if( left !== null ) {
                                left = pos.left / scale;
                            }
                            if( right !== null ) {
                                right = (ob.parent().width() - pos.left - ob.width()) / scale;
                            }
                            if( top !== null ) {
                                top = pos.top / scale;
                            }
                            if( bottom !== null ) {
                                bottom = (ob.parent().height() - pos.top - ob.height()) / scale;
                            }
                            if( width !== null ) {
                                width = ob.width() / scale;
                            }
                            if( height !== null ) {
                                height = ob.height() / scale;
                            }
                        }
                    });
                }
                ob.text(text);
                stylize(ob, scale);
            },
            setStyle: function(style) {
                styleToVar(style);
            },
            getData: function() {
                var ob = {
                    type: "text",
                    text: text,
                    bold: bold,
                    italic: italic,
                    underline: underline,
                    align: align,
                    fontsize: fontsize
                };
                if( left != null ) {
                    ob.left = left;
                }
                if( right != null ) {
                    ob.right = right;
                }
                if( top != null ) {
                    ob.top = top;
                }
                if( bottom != null ) {
                    ob.bottom = bottom;
                }
                if( width != null ) {
                    ob.width = width;
                }
                if( height != null ) {
                    ob.height = height;
                }
                return ob;
            }
        };
    };

})(window.jQuery);

/**************************************************************************************************/
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
