/*! angular-spectrum-colorpicker v1.4.5 */
!function (a, b) {
    "use strict";
    var c = a.module("angularSpectrumColorpicker", []);
    !function (b) {
        c.directive("spectrumColorpicker", function () {
            return {
                restrict: "EA",
                require: "ngModel",
                scope: {
                    fallbackValue: "=",
                    disabled: "=?",
                    format: "=?",
                    options: "=?",
                    triggerId: "@?",
                    palette: "=?",
                    onChange: "&?",
                    onShow: "&?",
                    onHide: "&?",
                    onMove: "&?",
                    onBeforeShow: "&?",
                    onChangeOptions: "=?",
                    onShowOptions: "=?",
                    onHideOptions: "=?",
                    onMoveOptions: "=?"
                },
                replace: !0,
                templateUrl: "directive.html",
                link: function (b, c, d, e) {
                    function f(a) {
                        var c = a;
                        return c && (c = a.toString(b.format)), c
                    }

                    function g(c) {
                        a.isFunction(b.onChange) && b.onChange({color: c})
                    }

                    function h(c) {
                        var d = b.fallbackValue;
                        c ? d = f(c) : a.isUndefined(b.fallbackValue) && (d = c), e.$setViewValue(d), g(d)
                    }

                    var i = c.find("input"), j = function (a) {
                        b.$evalAsync(function () {
                            h(a)
                        })
                    }, k = function () {
                        return i.spectrum("toggle"), !1
                    }, l = {color: e.$viewValue}, m = {};
                    a.forEach({change: "onChange", move: "onMove", hide: "onHide", show: "onShow"}, function (c, d) {
                        var e = b[c + "Options"];
                        m[d] = function (d) {
                            return (!e || e.update) && j(d), "change" !== c && a.isFunction(b[c]) ? b[c]({color: f(d)}) : null
                        }
                    }), a.isFunction(b.onBeforeShow) && (m.beforeShow = function (a) {
                        return b.onBeforeShow({color: f(a)})
                    }), b.palette && (m.palette = b.palette);
                    var n = a.extend({}, l, b.options, m);
                    b.triggerId && a.element(document.body).on("click", "#" + b.triggerId, k), e.$render = function () {
                        i.spectrum("set", e.$viewValue || ""), g(e.$viewValue)
                    }, n.color && (i.spectrum("set", n.color || ""), h(n.color)), i.spectrum(n), b.$on("$destroy", function () {
                        b.triggerId && a.element(document.body).off("click", "#" + b.triggerId, k)
                    }), c.on("$destroy", function () {
                        i.spectrum("destroy")
                    }), a.isDefined(n.disabled) && (b.disabled = !!n.disabled), b.$watch("disabled", function (a) {
                        i.spectrum(a ? "disable" : "enable")
                    }), b.$watch("palette", function (a) {
                        i.spectrum("option", "palette", a)
                    }, !0)
                }
            }
        })
    }(), a.module("angularSpectrumColorpicker").run(["$templateCache", function (a) {
        a.put("directive.html", '<span><input class="input-small"></span>')
    }])
}(window.angular);