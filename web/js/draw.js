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
        templates: { text: null, img: null, page: null },
        selectedclass: "btn-success"
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

    var selected = null,
        aConnect = {},
        select = function(ob) {
            if( selected !== null ) {
                selected.select(false);
            }

            if( selected != ob ) {
                var settings = ob.val("editor").data('drawpage').settings,
                    panel = settings.panel,
                    open = panel.find(".panel-control:visible"),
                    oControl = panel.find("."+ob.val("type")+"-control");

                if( open.length != 0 ) {
                    open.hide();
                    aConnect = {};
                }

                var newPanel = ob.val("panel");
                if( newPanel !== null ) {
                    var panControls = newPanel.find(".panel-control");
                    panControls.append(oControl);
                    oControl.show();
                    aConnect = connectFields(ob, panControls);
                    initControls(ob, aConnect, settings);
    //                panControls.show();
                    panControls.slideDown("fast");
                }
            }
            selected = ob;
        },
        connectFields = function(ob, oDom) {
            var oFld = ob.getData(),
                aConn = {},
                findEl = function(dom){
                    dom.children().each(function(index, element){
                        var o = jQuery(this), v = o.data('field');
//                        console.log(o, v);
                        if( v ) {
                            for( var i in oFld ) {
                                if( i == v ) {
                                    if( !(i in aConn) ) {
                                        aConn[i] = [];
                                    }
                                    aConn[i].push(o);
                                    break;
                                }
                            }
                        }
                        findEl(o);
                    });
                };
            findEl(oDom);
//            console.log(aConn);
            return aConn;
        },
        setForm = function(ob, settings, name, control){
            if( control.length < 1 ) {
                return;
            }
            var sTag = control[0].get(0).tagName.toLowerCase(),
                atr = control[0].attr("type"),
                type = (atr === undefined ? "": atr.toLowerCase()),
                formel = control[0],
                v = ob.val(name);
            if( control.length == 1 ) {
                if( (sTag == "a") || (sTag == "button") ) {
                    if( v ) {
                        formel.addClass(settings.selectedclass);
                    }
                    else {
                        formel.removeClass(settings.selectedclass);
                    }
                    formel
                        .off("click")
                        .on(
                        "click",
                        function(event){
                            var th = jQuery(this);
                            event.preventDefault();
                            th.toggleClass(settings.selectedclass);
                            ob.val(name, th.hasClass(settings.selectedclass));
                            ob.draw(ob.val("editor"), settings.scale);
                            return false;
                        }
                    );
                }
                else if( (sTag == "input") || (sTag == "select") ) {
                    if( type == "checkbox" ) {
                        formel.prop('checked', v);
                        formel
                            .off("change")
                            .on(
                            "change",
                            function(event){
                                var th = jQuery(this);
                                event.preventDefault();
                                ob.val(name, th.prop('checked'));
                                ob.draw(ob.val("editor"), settings.scale);
                                return false;
                            }
                        );
                    }
                    else if( (type == "text") || (sTag == "select") || (type == "radio") ) {
                        var evnt = (type == "text") ? "keyup" : "change";
                        formel.val(v);
                        formel
                            .off(evnt)
                            .on(
                            evnt,
                            function(event){
                                var th = jQuery(this);
                                event.preventDefault();
                                ob.val(name, th.val());
                                ob.draw(ob.val("editor"), settings.scale);
                                return false;
                            }
                        );
                    }
                }
            }
            else if( control.length > 1 ) {
                if( (sTag == "a") || (sTag == "button") ) {
                    var sAttr = (sTag == "a") ? "data-value" : "value";
                    for(var i = 0; i < control.length; i++) {
                        formel = control[i];
                        if( v == formel.attr(sAttr) ) {
                            formel.addClass(settings.selectedclass);
                        }
                        else {
                            formel.removeClass(settings.selectedclass);
                        }
                        formel
                            .off("click")
                            .on(
                            "click",
                            function(event){
                                var th = jQuery(this);
                                event.preventDefault();
                                for(var i = 0; i < control.length; i++) {
                                    control[i].removeClass(settings.selectedclass);
                                }
                                th.addClass(settings.selectedclass);
                                ob.val(name, th.attr(sAttr));
                                ob.draw(ob.val("editor"), settings.scale);
                                return false;
                            }
                        );
                    }
                }
                else if( sTag == "input" ) {
                    if( type == "radio" ) {
                        for(var i = 0; i < control.length; i++) {
                            formel = control[i];
                            if( v == formel.val() ) {
                                formel.prop('checked', true);
                            }
                            else {
                                formel.prop('checked', false);
                            }
                            formel
                                .off("click")
                                .on(
                                "click",
                                function(event){
                                    var th = jQuery(this);
//                                    event.preventDefault();
                                    for(var i = 0; i < control.length; i++) {
                                        control[i].prop('checked', false);
                                    }
                                    th.prop('checked', true);
                                    ob.val(name, th.val());
                                    ob.draw(ob.val("editor"), settings.scale);
//                                    return false;
                                }
                            );
                        }
                    }
                }
            }

        },
        initControls = function(ob, aConnect, settings){
            for(var i in aConnect) {
                var el = aConnect[i];
                setForm(ob, settings, i, el);
            }
        };

    var addToPanel = function(panel, ob) {
        if( panel === null ) {
            return;
        }
        var data = ob.getData(),
            el = jQuery('<div class="selectregion col-md-12"><a href="#" class="btn btn-default btn-block">'+data.title+'</a><div class="panel-control"></div></div>'),
            dom = ob.getElement();

        panel.append(el);
        ob.val("panel", el);

        dom.on(
            "click",
            function(event) {
                event.preventDefault();
                ob.select(true);
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
        if( jQuery(".text-control").length == 0 ) {
            var o = jQuery(
                '<div class="text-control">'
                + '<a href="#" title="Bold" class="button-bold btn btn-default" data-field="bold">B</a>'
//            + '<a href="#" title="Italic" class="button-italic btn btn-default" data-field="italic">I</a>'
                + '<input type="checkbox" id="cb-italic" data-field="italic"> <label for="cb-italic">Italic</label>'
                + '<a href="#" title="Underline" class="button-underline btn btn-default" data-field="underline">U</a>'
                + '<br />'
//            + '<a href="#" title="Left" class="button-align-left btn btn-default" data-field="align" data-value="left">Left</a>'
//            + '<a href="#" title="Center" class="button-align-center btn btn-default" data-field="align" data-value="center">Center</a>'
//            + '<a href="#" title="Right" class="button-align-right btn btn-default" data-field="align" data-value="right">Right</a>'
                + '<select data-field="align"><option value="left">Left</option><option value="right">Right</option><option value="center">Center</option></select>'

//            + '<input type="radio" name="group2" value="left" data-field="align"> Left '
//            + '<input type="radio" name="group2" value="center" data-field="align"> Center'
//            + '<input type="radio" name="group2" value="right"  data-field="align"> Rigth'
                + '<br />'
                + '<input type="text" data-field="text" />'
                + '</div>'
            );

            panel.append(o);
        }
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
                r = $editor.data('drawpage').regions,
                a = [];
            for(var i= 0; i < r.length; i++) {
                a.push(r[i].getData());
            }
            return a;
        },
        append: function(conf) {
            var $editor = $(this),
                data = $editor.data('drawpage'),
                r = data.regions,
                settings = data.settings,
                page = r.reduce(
                    function(res, item, index, arr){
                        return (item.getData().type == "page") ? item : res;
                    },
                    null
                );
            oTmp = addElement(conf, $editor); // new Text(regions[i]);
            if( oTmp !== null ) {
                oTmp.draw(page.getElement(), settings.scale);
                addToPanel(settings.panel, oTmp);
                data.regions.push(oTmp);
            }

//            data.regions.push(addElement(conf));
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
                selected: false,
                panel: null
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
                fontfamily: 'Arial',
                left: null,   // mm
                right: null,  // mm
                top: null,    // mm
                bottom: null, // mm
                width: null,  // mm
                height: null, // mm
                id: "text-" + n,
                editor: null,
                element: null,
                selected: false,
                panel: null
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
                        "font-family": val("fontfamily"),
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
                    if( (i == "id") || (i == "editor") || (i == "element") || (i == "panel") ) { //  || (i == "selected")
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
