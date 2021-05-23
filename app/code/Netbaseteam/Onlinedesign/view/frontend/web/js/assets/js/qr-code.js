/* QR Code Generator */
var qrcode = function () {
    var i = function (r, t) {
        var a = r, u = p[t], c = null, h = 0, n = null, f = [], g = {}, s = function (r, t) {
            c = function (r) {
                for (var t = new Array(r), e = 0; e < r; e += 1) {
                    t[e] = new Array(r);
                    for (var n = 0; n < r; n += 1) t[e][n] = null
                }
                return t
            }(h = 4 * a + 17), e(0, 0), e(h - 7, 0), e(0, h - 7), i(), o(), v(r, t), 7 <= a && l(r), null == n && (n = w(a, u, f)), d(n, t)
        }, e = function (r, t) {
            for (var e = -1; e <= 7; e += 1) if (!(r + e <= -1 || h <= r + e)) for (var n = -1; n <= 7; n += 1) t + n <= -1 || h <= t + n || (c[r + e][t + n] = 0 <= e && e <= 6 && (0 == n || 6 == n) || 0 <= n && n <= 6 && (0 == e || 6 == e) || 2 <= e && e <= 4 && 2 <= n && n <= 4)
        }, o = function () {
            for (var r = 8; r < h - 8; r += 1) null == c[r][6] && (c[r][6] = r % 2 == 0);
            for (var t = 8; t < h - 8; t += 1) null == c[6][t] && (c[6][t] = t % 2 == 0)
        }, i = function () {
            for (var r = y.getPatternPosition(a), t = 0; t < r.length; t += 1) for (var e = 0; e < r.length; e += 1) {
                var n = r[t], o = r[e];
                if (null == c[n][o]) for (var i = -2; i <= 2; i += 1) for (var u = -2; u <= 2; u += 1) c[n + i][o + u] = -2 == i || 2 == i || -2 == u || 2 == u || 0 == i && 0 == u
            }
        }, l = function (r) {
            for (var t = y.getBCHTypeNumber(a), e = 0; e < 18; e += 1) {
                var n = !r && 1 == (t >> e & 1);
                c[Math.floor(e / 3)][e % 3 + h - 8 - 3] = n
            }
            for (e = 0; e < 18; e += 1) {
                n = !r && 1 == (t >> e & 1);
                c[e % 3 + h - 8 - 3][Math.floor(e / 3)] = n
            }
        }, v = function (r, t) {
            for (var e = u << 3 | t, n = y.getBCHTypeInfo(e), o = 0; o < 15; o += 1) {
                var i = !r && 1 == (n >> o & 1);
                o < 6 ? c[o][8] = i : o < 8 ? c[o + 1][8] = i : c[h - 15 + o][8] = i
            }
            for (o = 0; o < 15; o += 1) {
                i = !r && 1 == (n >> o & 1);
                o < 8 ? c[8][h - o - 1] = i : o < 9 ? c[8][15 - o - 1 + 1] = i : c[8][15 - o - 1] = i
            }
            c[h - 8][8] = !r
        }, d = function (r, t) {
            for (var e = -1, n = h - 1, o = 7, i = 0, u = y.getMaskFunction(t), a = h - 1; 0 < a; a -= 2) for (6 == a && (a -= 1); ;) {
                for (var f = 0; f < 2; f += 1) if (null == c[n][a - f]) {
                    var g = !1;
                    i < r.length && (g = 1 == (r[i] >>> o & 1)), u(n, a - f) && (g = !g), c[n][a - f] = g, -1 == (o -= 1) && (i += 1, o = 7)
                }
                if ((n += e) < 0 || h <= n) {
                    n -= e, e = -e;
                    break
                }
            }
        }, w = function (r, t, e) {
            for (var n = k.getRSBlocks(r, t), o = m(), i = 0; i < e.length; i += 1) {
                var u = e[i];
                o.put(u.getMode(), 4), o.put(u.getLength(), y.getLengthInBits(u.getMode(), r)), u.write(o)
            }
            var a = 0;
            for (i = 0; i < n.length; i += 1) a += n[i].dataCount;
            if (o.getLengthInBits() > 8 * a) throw"code length overflow. (" + o.getLengthInBits() + ">" + 8 * a + ")";
            for (o.getLengthInBits() + 4 <= 8 * a && o.put(0, 4); o.getLengthInBits() % 8 != 0;) o.putBit(!1);
            for (; !(o.getLengthInBits() >= 8 * a || (o.put(236, 8), o.getLengthInBits() >= 8 * a));) o.put(17, 8);
            return function (r, t) {
                for (var e = 0, n = 0, o = 0, i = new Array(t.length), u = new Array(t.length), a = 0; a < t.length; a += 1) {
                    var f = t[a].dataCount, g = t[a].totalCount - f;
                    n = Math.max(n, f), o = Math.max(o, g), i[a] = new Array(f);
                    for (var c = 0; c < i[a].length; c += 1) i[a][c] = 255 & r.getBuffer()[c + e];
                    e += f;
                    var h = y.getErrorCorrectPolynomial(g), s = B(i[a], h.getLength() - 1).mod(h);
                    for (u[a] = new Array(h.getLength() - 1), c = 0; c < u[a].length; c += 1) {
                        var l = c + s.getLength() - u[a].length;
                        u[a][c] = 0 <= l ? s.getAt(l) : 0
                    }
                }
                var v = 0;
                for (c = 0; c < t.length; c += 1) v += t[c].totalCount;
                var d = new Array(v), w = 0;
                for (c = 0; c < n; c += 1) for (a = 0; a < t.length; a += 1) c < i[a].length && (d[w] = i[a][c], w += 1);
                for (c = 0; c < o; c += 1) for (a = 0; a < t.length; a += 1) c < u[a].length && (d[w] = u[a][c], w += 1);
                return d
            }(o, n)
        };
        return g.addData = function (r, t) {
            var e = null;
            switch (t = t || "Byte") {
                case"Numeric":
                    e = L(r);
                    break;
                case"Alphanumeric":
                    e = M(r);
                    break;
                case"Byte":
                    e = b(r);
                    break;
                case"Kanji":
                    e = x(r);
                    break;
                default:
                    throw"mode:" + t
            }
            f.push(e), n = null
        }, g.isDark = function (r, t) {
            if (r < 0 || h <= r || t < 0 || h <= t) throw r + "," + t;
            return c[r][t]
        }, g.getModuleCount = function () {
            return h
        }, g.make = function () {
            if (a < 1) {
                for (var r = 1; r < 40; r++) {
                    for (var t = k.getRSBlocks(r, u), e = m(), n = 0; n < f.length; n++) {
                        var o = f[n];
                        e.put(o.getMode(), 4), e.put(o.getLength(), y.getLengthInBits(o.getMode(), r)), o.write(e)
                    }
                    var i = 0;
                    for (n = 0; n < t.length; n++) i += t[n].dataCount;
                    if (e.getLengthInBits() <= 8 * i) break
                }
                a = r
            }
            s(!1, function () {
                for (var r = 0, t = 0, e = 0; e < 8; e += 1) {
                    s(!0, e);
                    var n = y.getLostPoint(g);
                    (0 == e || n < r) && (r = n, t = e)
                }
                return t
            }())
        }, g.createTableTag = function (r, t) {
            r = r || 2;
            var e = "";
            e += '<table style="', e += " border-width: 0px; border-style: none;", e += " border-collapse: collapse;", e += " padding: 0px; margin: " + (t = void 0 === t ? 4 * r : t) + "px;", e += '">', e += "<tbody>";
            for (var n = 0; n < g.getModuleCount(); n += 1) {
                e += "<tr>";
                for (var o = 0; o < g.getModuleCount(); o += 1) e += '<td style="', e += " border-width: 0px; border-style: none;", e += " border-collapse: collapse;", e += " padding: 0px; margin: 0px;", e += " width: " + r + "px;", e += " height: " + r + "px;", e += " background-color: ", e += g.isDark(n, o) ? "#000000" : "#ffffff", e += ";", e += '"/>';
                e += "</tr>"
            }
            return e += "</tbody>", e += "</table>"
        }, g.createSvgTag = function (r, t) {
            r = r || 4, t = void 0 === t ? 4 * r : t, t = 0;
            var e, n, o, i, u = g.getModuleCount() * r + 2 * t, a = "";
            for (i = "l" + r + ",0 0," + r + " -" + r + ",0 0,-" + r + "z ", a += '<svg version="1.1" xmlns="http://www.w3.org/2000/svg"', a += ' width="' + u + 'px"', a += ' height="' + u + 'px"', a += ' viewBox="0 0 ' + u + " " + u + '" ', a += ' preserveAspectRatio="xMinYMin meet">', a += '<path d="', n = 0; n < g.getModuleCount(); n += 1) for (o = n * r + t, e = 0; e < g.getModuleCount(); e += 1) g.isDark(n, e) && (a += "M" + (e * r + t) + "," + o + i);
            return a += '" stroke="transparent" fill="black"/>', a += "</svg>"
        }, g.createImgTag = function (o, r) {
            o = o || 2, r = void 0 === r ? 4 * o : r;
            var t = g.getModuleCount() * o + 2 * r, i = r, u = t - r;
            return I(t, t, function (r, t) {
                if (i <= r && r < u && i <= t && t < u) {
                    var e = Math.floor((r - i) / o), n = Math.floor((t - i) / o);
                    return g.isDark(n, e) ? 0 : 1
                }
                return 1
            })
        }, g
    };
    i.stringToBytes = (i.stringToBytesFuncs = {
        default: function (r) {
            for (var t = [], e = 0; e < r.length; e += 1) {
                var n = r.charCodeAt(e);
                t.push(255 & n)
            }
            return t
        }
    }).default, i.createStringToBytes = function (a, f) {
        var i = function () {
            for (var t = D(a), r = function () {
                var r = t.read();
                if (-1 == r) throw"eof";
                return r
            }, e = 0, n = {}; ;) {
                var o = t.read();
                if (-1 == o) break;
                var i = r(), u = r() << 8 | r();
                n[String.fromCharCode(o << 8 | i)] = u, e += 1
            }
            if (e != f) throw e + " != " + f;
            return n
        }(), u = "?".charCodeAt(0);
        return function (r) {
            for (var t = [], e = 0; e < r.length; e += 1) {
                var n = r.charCodeAt(e);
                if (n < 128) t.push(n); else {
                    var o = i[r.charAt(e)];
                    "number" == typeof o ? (255 & o) == o ? t.push(o) : (t.push(o >>> 8), t.push(255 & o)) : t.push(u)
                }
            }
            return t
        }
    };
    var t, r, e, u = 1, a = 2, o = 4, f = 8, p = {L: 1, M: 0, Q: 3, H: 2}, n = 0, g = 1, c = 2, h = 3, s = 4, l = 5,
        v = 6, d = 7,
        y = (t = [[], [6, 18], [6, 22], [6, 26], [6, 30], [6, 34], [6, 22, 38], [6, 24, 42], [6, 26, 46], [6, 28, 50], [6, 30, 54], [6, 32, 58], [6, 34, 62], [6, 26, 46, 66], [6, 26, 48, 70], [6, 26, 50, 74], [6, 30, 54, 78], [6, 30, 56, 82], [6, 30, 58, 86], [6, 34, 62, 90], [6, 28, 50, 72, 94], [6, 26, 50, 74, 98], [6, 30, 54, 78, 102], [6, 28, 54, 80, 106], [6, 32, 58, 84, 110], [6, 30, 58, 86, 114], [6, 34, 62, 90, 118], [6, 26, 50, 74, 98, 122], [6, 30, 54, 78, 102, 126], [6, 26, 52, 78, 104, 130], [6, 30, 56, 82, 108, 134], [6, 34, 60, 86, 112, 138], [6, 30, 58, 86, 114, 142], [6, 34, 62, 90, 118, 146], [6, 30, 54, 78, 102, 126, 150], [6, 24, 50, 76, 102, 128, 154], [6, 28, 54, 80, 106, 132, 158], [6, 32, 58, 84, 110, 136, 162], [6, 26, 54, 82, 110, 138, 166], [6, 30, 58, 86, 114, 142, 170]], e = function (r) {
            for (var t = 0; 0 != r;) t += 1, r >>>= 1;
            return t
        }, (r = {}).getBCHTypeInfo = function (r) {
            for (var t = r << 10; 0 <= e(t) - e(1335);) t ^= 1335 << e(t) - e(1335);
            return 21522 ^ (r << 10 | t)
        }, r.getBCHTypeNumber = function (r) {
            for (var t = r << 12; 0 <= e(t) - e(7973);) t ^= 7973 << e(t) - e(7973);
            return r << 12 | t
        }, r.getPatternPosition = function (r) {
            return t[r - 1]
        }, r.getMaskFunction = function (r) {
            switch (r) {
                case n:
                    return function (r, t) {
                        return (r + t) % 2 == 0
                    };
                case g:
                    return function (r, t) {
                        return r % 2 == 0
                    };
                case c:
                    return function (r, t) {
                        return t % 3 == 0
                    };
                case h:
                    return function (r, t) {
                        return (r + t) % 3 == 0
                    };
                case s:
                    return function (r, t) {
                        return (Math.floor(r / 2) + Math.floor(t / 3)) % 2 == 0
                    };
                case l:
                    return function (r, t) {
                        return r * t % 2 + r * t % 3 == 0
                    };
                case v:
                    return function (r, t) {
                        return (r * t % 2 + r * t % 3) % 2 == 0
                    };
                case d:
                    return function (r, t) {
                        return (r * t % 3 + (r + t) % 2) % 2 == 0
                    };
                default:
                    throw"bad maskPattern:" + r
            }
        }, r.getErrorCorrectPolynomial = function (r) {
            for (var t = B([1], 0), e = 0; e < r; e += 1) t = t.multiply(B([1, w.gexp(e)], 0));
            return t
        }, r.getLengthInBits = function (r, t) {
            if (1 <= t && t < 10) switch (r) {
                case u:
                    return 10;
                case a:
                    return 9;
                case o:
                case f:
                    return 8;
                default:
                    throw"mode:" + r
            } else if (t < 27) switch (r) {
                case u:
                    return 12;
                case a:
                    return 11;
                case o:
                    return 16;
                case f:
                    return 10;
                default:
                    throw"mode:" + r
            } else {
                if (!(t < 41)) throw"type:" + t;
                switch (r) {
                    case u:
                        return 14;
                    case a:
                        return 13;
                    case o:
                        return 16;
                    case f:
                        return 12;
                    default:
                        throw"mode:" + r
                }
            }
        }, r.getLostPoint = function (r) {
            for (var t = r.getModuleCount(), e = 0, n = 0; n < t; n += 1) for (var o = 0; o < t; o += 1) {
                for (var i = 0, u = r.isDark(n, o), a = -1; a <= 1; a += 1) if (!(n + a < 0 || t <= n + a)) for (var f = -1; f <= 1; f += 1) o + f < 0 || t <= o + f || 0 == a && 0 == f || u == r.isDark(n + a, o + f) && (i += 1);
                5 < i && (e += 3 + i - 5)
            }
            for (n = 0; n < t - 1; n += 1) for (o = 0; o < t - 1; o += 1) {
                var g = 0;
                r.isDark(n, o) && (g += 1), r.isDark(n + 1, o) && (g += 1), r.isDark(n, o + 1) && (g += 1), r.isDark(n + 1, o + 1) && (g += 1), 0 != g && 4 != g || (e += 3)
            }
            for (n = 0; n < t; n += 1) for (o = 0; o < t - 6; o += 1) r.isDark(n, o) && !r.isDark(n, o + 1) && r.isDark(n, o + 2) && r.isDark(n, o + 3) && r.isDark(n, o + 4) && !r.isDark(n, o + 5) && r.isDark(n, o + 6) && (e += 40);
            for (o = 0; o < t; o += 1) for (n = 0; n < t - 6; n += 1) r.isDark(n, o) && !r.isDark(n + 1, o) && r.isDark(n + 2, o) && r.isDark(n + 3, o) && r.isDark(n + 4, o) && !r.isDark(n + 5, o) && r.isDark(n + 6, o) && (e += 40);
            var c = 0;
            for (o = 0; o < t; o += 1) for (n = 0; n < t; n += 1) r.isDark(n, o) && (c += 1);
            return e += Math.abs(100 * c / t / t - 50) / 5 * 10
        }, r), w = function () {
            for (var t = new Array(256), e = new Array(256), r = 0; r < 8; r += 1) t[r] = 1 << r;
            for (r = 8; r < 256; r += 1) t[r] = t[r - 4] ^ t[r - 5] ^ t[r - 6] ^ t[r - 8];
            for (r = 0; r < 255; r += 1) e[t[r]] = r;
            var n = {
                glog: function (r) {
                    if (r < 1) throw"glog(" + r + ")";
                    return e[r]
                }, gexp: function (r) {
                    for (; r < 0;) r += 255;
                    for (; 256 <= r;) r -= 255;
                    return t[r]
                }
            };
            return n
        }();

    function B(n, o) {
        if (void 0 === n.length) throw n.length + "/" + o;
        var t = function () {
            for (var r = 0; r < n.length && 0 == n[r];) r += 1;
            for (var t = new Array(n.length - r + o), e = 0; e < n.length - r; e += 1) t[e] = n[e + r];
            return t
        }(), i = {
            getAt: function (r) {
                return t[r]
            }, getLength: function () {
                return t.length
            }, multiply: function (r) {
                for (var t = new Array(i.getLength() + r.getLength() - 1), e = 0; e < i.getLength(); e += 1) for (var n = 0; n < r.getLength(); n += 1) t[e + n] ^= w.gexp(w.glog(i.getAt(e)) + w.glog(r.getAt(n)));
                return B(t, 0)
            }, mod: function (r) {
                if (i.getLength() - r.getLength() < 0) return i;
                for (var t = w.glog(i.getAt(0)) - w.glog(r.getAt(0)), e = new Array(i.getLength()), n = 0; n < i.getLength(); n += 1) e[n] = i.getAt(n);
                for (n = 0; n < r.getLength(); n += 1) e[n] ^= w.gexp(w.glog(r.getAt(n)) + t);
                return B(e, 0).mod(r)
            }
        };
        return i
    }

    var C, A,
        k = (C = [[1, 26, 19], [1, 26, 16], [1, 26, 13], [1, 26, 9], [1, 44, 34], [1, 44, 28], [1, 44, 22], [1, 44, 16], [1, 70, 55], [1, 70, 44], [2, 35, 17], [2, 35, 13], [1, 100, 80], [2, 50, 32], [2, 50, 24], [4, 25, 9], [1, 134, 108], [2, 67, 43], [2, 33, 15, 2, 34, 16], [2, 33, 11, 2, 34, 12], [2, 86, 68], [4, 43, 27], [4, 43, 19], [4, 43, 15], [2, 98, 78], [4, 49, 31], [2, 32, 14, 4, 33, 15], [4, 39, 13, 1, 40, 14], [2, 121, 97], [2, 60, 38, 2, 61, 39], [4, 40, 18, 2, 41, 19], [4, 40, 14, 2, 41, 15], [2, 146, 116], [3, 58, 36, 2, 59, 37], [4, 36, 16, 4, 37, 17], [4, 36, 12, 4, 37, 13], [2, 86, 68, 2, 87, 69], [4, 69, 43, 1, 70, 44], [6, 43, 19, 2, 44, 20], [6, 43, 15, 2, 44, 16], [4, 101, 81], [1, 80, 50, 4, 81, 51], [4, 50, 22, 4, 51, 23], [3, 36, 12, 8, 37, 13], [2, 116, 92, 2, 117, 93], [6, 58, 36, 2, 59, 37], [4, 46, 20, 6, 47, 21], [7, 42, 14, 4, 43, 15], [4, 133, 107], [8, 59, 37, 1, 60, 38], [8, 44, 20, 4, 45, 21], [12, 33, 11, 4, 34, 12], [3, 145, 115, 1, 146, 116], [4, 64, 40, 5, 65, 41], [11, 36, 16, 5, 37, 17], [11, 36, 12, 5, 37, 13], [5, 109, 87, 1, 110, 88], [5, 65, 41, 5, 66, 42], [5, 54, 24, 7, 55, 25], [11, 36, 12, 7, 37, 13], [5, 122, 98, 1, 123, 99], [7, 73, 45, 3, 74, 46], [15, 43, 19, 2, 44, 20], [3, 45, 15, 13, 46, 16], [1, 135, 107, 5, 136, 108], [10, 74, 46, 1, 75, 47], [1, 50, 22, 15, 51, 23], [2, 42, 14, 17, 43, 15], [5, 150, 120, 1, 151, 121], [9, 69, 43, 4, 70, 44], [17, 50, 22, 1, 51, 23], [2, 42, 14, 19, 43, 15], [3, 141, 113, 4, 142, 114], [3, 70, 44, 11, 71, 45], [17, 47, 21, 4, 48, 22], [9, 39, 13, 16, 40, 14], [3, 135, 107, 5, 136, 108], [3, 67, 41, 13, 68, 42], [15, 54, 24, 5, 55, 25], [15, 43, 15, 10, 44, 16], [4, 144, 116, 4, 145, 117], [17, 68, 42], [17, 50, 22, 6, 51, 23], [19, 46, 16, 6, 47, 17], [2, 139, 111, 7, 140, 112], [17, 74, 46], [7, 54, 24, 16, 55, 25], [34, 37, 13], [4, 151, 121, 5, 152, 122], [4, 75, 47, 14, 76, 48], [11, 54, 24, 14, 55, 25], [16, 45, 15, 14, 46, 16], [6, 147, 117, 4, 148, 118], [6, 73, 45, 14, 74, 46], [11, 54, 24, 16, 55, 25], [30, 46, 16, 2, 47, 17], [8, 132, 106, 4, 133, 107], [8, 75, 47, 13, 76, 48], [7, 54, 24, 22, 55, 25], [22, 45, 15, 13, 46, 16], [10, 142, 114, 2, 143, 115], [19, 74, 46, 4, 75, 47], [28, 50, 22, 6, 51, 23], [33, 46, 16, 4, 47, 17], [8, 152, 122, 4, 153, 123], [22, 73, 45, 3, 74, 46], [8, 53, 23, 26, 54, 24], [12, 45, 15, 28, 46, 16], [3, 147, 117, 10, 148, 118], [3, 73, 45, 23, 74, 46], [4, 54, 24, 31, 55, 25], [11, 45, 15, 31, 46, 16], [7, 146, 116, 7, 147, 117], [21, 73, 45, 7, 74, 46], [1, 53, 23, 37, 54, 24], [19, 45, 15, 26, 46, 16], [5, 145, 115, 10, 146, 116], [19, 75, 47, 10, 76, 48], [15, 54, 24, 25, 55, 25], [23, 45, 15, 25, 46, 16], [13, 145, 115, 3, 146, 116], [2, 74, 46, 29, 75, 47], [42, 54, 24, 1, 55, 25], [23, 45, 15, 28, 46, 16], [17, 145, 115], [10, 74, 46, 23, 75, 47], [10, 54, 24, 35, 55, 25], [19, 45, 15, 35, 46, 16], [17, 145, 115, 1, 146, 116], [14, 74, 46, 21, 75, 47], [29, 54, 24, 19, 55, 25], [11, 45, 15, 46, 46, 16], [13, 145, 115, 6, 146, 116], [14, 74, 46, 23, 75, 47], [44, 54, 24, 7, 55, 25], [59, 46, 16, 1, 47, 17], [12, 151, 121, 7, 152, 122], [12, 75, 47, 26, 76, 48], [39, 54, 24, 14, 55, 25], [22, 45, 15, 41, 46, 16], [6, 151, 121, 14, 152, 122], [6, 75, 47, 34, 76, 48], [46, 54, 24, 10, 55, 25], [2, 45, 15, 64, 46, 16], [17, 152, 122, 4, 153, 123], [29, 74, 46, 14, 75, 47], [49, 54, 24, 10, 55, 25], [24, 45, 15, 46, 46, 16], [4, 152, 122, 18, 153, 123], [13, 74, 46, 32, 75, 47], [48, 54, 24, 14, 55, 25], [42, 45, 15, 32, 46, 16], [20, 147, 117, 4, 148, 118], [40, 75, 47, 7, 76, 48], [43, 54, 24, 22, 55, 25], [10, 45, 15, 67, 46, 16], [19, 148, 118, 6, 149, 119], [18, 75, 47, 31, 76, 48], [34, 54, 24, 34, 55, 25], [20, 45, 15, 61, 46, 16]], (A = {}).getRSBlocks = function (r, t) {
            var e = function (r, t) {
                switch (t) {
                    case p.L:
                        return C[4 * (r - 1) + 0];
                    case p.M:
                        return C[4 * (r - 1) + 1];
                    case p.Q:
                        return C[4 * (r - 1) + 2];
                    case p.H:
                        return C[4 * (r - 1) + 3];
                    default:
                        return
                }
            }(r, t);
            if (void 0 === e) throw"bad rs block @ typeNumber:" + r + "/errorCorrectionLevel:" + t;
            for (var n, o, i = e.length / 3, u = [], a = 0; a < i; a += 1) for (var f = e[3 * a + 0], g = e[3 * a + 1], c = e[3 * a + 2], h = 0; h < f; h += 1) u.push((n = c, o = void 0, (o = {}).totalCount = g, o.dataCount = n, o));
            return u
        }, A), m = function () {
            var e = [], n = 0, o = {
                getBuffer: function () {
                    return e
                }, getAt: function (r) {
                    var t = Math.floor(r / 8);
                    return 1 == (e[t] >>> 7 - r % 8 & 1)
                }, put: function (r, t) {
                    for (var e = 0; e < t; e += 1) o.putBit(1 == (r >>> t - e - 1 & 1))
                }, getLengthInBits: function () {
                    return n
                }, putBit: function (r) {
                    var t = Math.floor(n / 8);
                    e.length <= t && e.push(0), r && (e[t] |= 128 >>> n % 8), n += 1
                }
            };
            return o
        }, L = function (r) {
            var t = u, n = r, e = {
                getMode: function () {
                    return t
                }, getLength: function (r) {
                    return n.length
                }, write: function (r) {
                    for (var t = n, e = 0; e + 2 < t.length;) r.put(o(t.substring(e, e + 3)), 10), e += 3;
                    e < t.length && (t.length - e == 1 ? r.put(o(t.substring(e, e + 1)), 4) : t.length - e == 2 && r.put(o(t.substring(e, e + 2)), 7))
                }
            }, o = function (r) {
                for (var t = 0, e = 0; e < r.length; e += 1) t = 10 * t + i(r.charAt(e));
                return t
            }, i = function (r) {
                if ("0" <= r && r <= "9") return r.charCodeAt(0) - "0".charCodeAt(0);
                throw"illegal char :" + r
            };
            return e
        }, M = function (r) {
            var t = a, n = r, e = {
                getMode: function () {
                    return t
                }, getLength: function (r) {
                    return n.length
                }, write: function (r) {
                    for (var t = n, e = 0; e + 1 < t.length;) r.put(45 * o(t.charAt(e)) + o(t.charAt(e + 1)), 11), e += 2;
                    e < t.length && r.put(o(t.charAt(e)), 6)
                }
            }, o = function (r) {
                if ("0" <= r && r <= "9") return r.charCodeAt(0) - "0".charCodeAt(0);
                if ("A" <= r && r <= "Z") return r.charCodeAt(0) - "A".charCodeAt(0) + 10;
                switch (r) {
                    case" ":
                        return 36;
                    case"$":
                        return 37;
                    case"%":
                        return 38;
                    case"*":
                        return 39;
                    case"+":
                        return 40;
                    case"-":
                        return 41;
                    case".":
                        return 42;
                    case"/":
                        return 43;
                    case":":
                        return 44;
                    default:
                        throw"illegal char :" + r
                }
            };
            return e
        }, b = function (r) {
            var t = o, e = i.stringToBytes(r), n = {
                getMode: function () {
                    return t
                }, getLength: function (r) {
                    return e.length
                }, write: function (r) {
                    for (var t = 0; t < e.length; t += 1) r.put(e[t], 8)
                }
            };
            return n
        }, x = function (r) {
            var t = f, n = i.stringToBytesFuncs.SJIS;
            if (!n) throw"sjis not supported.";
            !function (r, t) {
                var e = n("友");
                if (2 != e.length || 38726 != (e[0] << 8 | e[1])) throw"sjis not supported."
            }();
            var o = n(r), e = {
                getMode: function () {
                    return t
                }, getLength: function (r) {
                    return ~~(o.length / 2)
                }, write: function (r) {
                    for (var t = o, e = 0; e + 1 < t.length;) {
                        var n = (255 & t[e]) << 8 | 255 & t[e + 1];
                        if (33088 <= n && n <= 40956) n -= 33088; else {
                            if (!(57408 <= n && n <= 60351)) throw"illegal char at " + (e + 1) + "/" + n;
                            n -= 49472
                        }
                        n = 192 * (n >>> 8 & 255) + (255 & n), r.put(n, 13), e += 2
                    }
                    if (e < t.length) throw"illegal char at " + (e + 1)
                }
            };
            return e
        }, S = function () {
            var e = [], o = {
                writeByte: function (r) {
                    e.push(255 & r)
                }, writeShort: function (r) {
                    o.writeByte(r), o.writeByte(r >>> 8)
                }, writeBytes: function (r, t, e) {
                    t = t || 0, e = e || r.length;
                    for (var n = 0; n < e; n += 1) o.writeByte(r[n + t])
                }, writeString: function (r) {
                    for (var t = 0; t < r.length; t += 1) o.writeByte(r.charCodeAt(t))
                }, toByteArray: function () {
                    return e
                }, toString: function () {
                    var r = "";
                    r += "[";
                    for (var t = 0; t < e.length; t += 1) 0 < t && (r += ","), r += e[t];
                    return r += "]"
                }
            };
            return o
        }, D = function (r) {
            var e = r, n = 0, o = 0, i = 0, t = {
                read: function () {
                    for (; i < 8;) {
                        if (n >= e.length) {
                            if (0 == i) return -1;
                            throw"unexpected end of file./" + i
                        }
                        var r = e.charAt(n);
                        if (n += 1, "=" == r) return i = 0, -1;
                        r.match(/^\s$/) || (o = o << 6 | u(r.charCodeAt(0)), i += 6)
                    }
                    var t = o >>> i - 8 & 255;
                    return i -= 8, t
                }
            }, u = function (r) {
                if (65 <= r && r <= 90) return r - 65;
                if (97 <= r && r <= 122) return r - 97 + 26;
                if (48 <= r && r <= 57) return r - 48 + 52;
                if (43 == r) return 62;
                if (47 == r) return 63;
                throw"c:" + r
            };
            return t
        }, I = function (r, t, e, n) {
            for (var o, i, u, a, v, f, g, d, c = (u = o = r, a = i = t, v = new Array(o * i), f = {
                setPixel: function (r, t, e) {
                    v[t * u + r] = e
                }, write: function (r) {
                    r.writeString("GIF87a"), r.writeShort(u), r.writeShort(a), r.writeByte(128), r.writeByte(0), r.writeByte(0), r.writeByte(0), r.writeByte(0), r.writeByte(0), r.writeByte(255), r.writeByte(255), r.writeByte(255), r.writeString(","), r.writeShort(0), r.writeShort(0), r.writeShort(u), r.writeShort(a), r.writeByte(0);
                    var t = g(2);
                    r.writeByte(2);
                    for (var e = 0; 255 < t.length - e;) r.writeByte(255), r.writeBytes(t, e, 255), e += 255;
                    r.writeByte(t.length - e), r.writeBytes(t, e, t.length - e), r.writeByte(0), r.writeString(";")
                }
            }, g = function (r) {
                for (var t = 1 << r, e = 1 + (1 << r), n = r + 1, o = d(), i = 0; i < t; i += 1) o.add(String.fromCharCode(i));
                o.add(String.fromCharCode(t)), o.add(String.fromCharCode(e));
                var u, a, f, g = S(), c = (u = g, f = a = 0, {
                    write: function (r, t) {
                        if (r >>> t != 0) throw"length over";
                        for (; 8 <= a + t;) u.writeByte(255 & (r << a | f)), t -= 8 - a, r >>>= 8 - a, a = f = 0;
                        f |= r << a, a += t
                    }, flush: function () {
                        0 < a && u.writeByte(f)
                    }
                });
                c.write(t, n);
                var h = 0, s = String.fromCharCode(v[h]);
                for (h += 1; h < v.length;) {
                    var l = String.fromCharCode(v[h]);
                    h += 1, o.contains(s + l) ? s += l : (c.write(o.indexOf(s), n), o.size() < 4095 && (o.size() == 1 << n && (n += 1), o.add(s + l)), s = l)
                }
                return c.write(o.indexOf(s), n), c.write(e, n), c.flush(), g.toByteArray()
            }, d = function () {
                var t = {}, e = 0, n = {
                    add: function (r) {
                        if (n.contains(r)) throw"dup key:" + r;
                        t[r] = e, e += 1
                    }, size: function () {
                        return e
                    }, indexOf: function (r) {
                        return t[r]
                    }, contains: function (r) {
                        return void 0 !== t[r]
                    }
                };
                return n
            }, f), h = 0; h < t; h += 1) for (var s = 0; s < r; s += 1) c.setPixel(s, h, e(s, h));
            var l = S();
            c.write(l);
            for (var w, p, y, B, C, A, k, m = (y = p = w = 0, B = "", A = function (r) {
                B += String.fromCharCode(k(63 & r))
            }, k = function (r) {
                if (r < 0) ; else {
                    if (r < 26) return 65 + r;
                    if (r < 52) return r - 26 + 97;
                    if (r < 62) return r - 52 + 48;
                    if (62 == r) return 43;
                    if (63 == r) return 47
                }
                throw"n:" + r
            }, (C = {}).writeByte = function (r) {
                for (w = w << 8 | 255 & r, p += 8, y += 1; 6 <= p;) A(w >>> p - 6), p -= 6
            }, C.flush = function () {
                if (0 < p && (A(w << 6 - p), p = w = 0), y % 3 != 0) for (var r = 3 - y % 3, t = 0; t < r; t += 1) B += "="
            }, C.toString = function () {
                return B
            }, C), L = l.toByteArray(), M = 0; M < L.length; M += 1) m.writeByte(L[M]);
            m.flush();
            var b = "";
            return b += "<img", b += ' src="', b += "data:image/gif;base64,", b += m, b += '"', b += ' width="', b += r, b += '"', b += ' height="', b += t, b += '"', n && (b += ' alt="', b += n, b += '"'), b += "/>"
        };
    return i
}();
qrcode.stringToBytesFuncs["UTF-8"] = function (r) {
    return function (r) {
        for (var t = [], e = 0; e < r.length; e++) {
            var n = r.charCodeAt(e);
            n < 128 ? t.push(n) : n < 2048 ? t.push(192 | n >> 6, 128 | 63 & n) : n < 55296 || 57344 <= n ? t.push(224 | n >> 12, 128 | n >> 6 & 63, 128 | 63 & n) : (e++, n = 65536 + ((1023 & n) << 10 | 1023 & r.charCodeAt(e)), t.push(240 | n >> 18, 128 | n >> 12 & 63, 128 | n >> 6 & 63, 128 | 63 & n))
        }
        return t
    }(r)
}, function (r) {
    "function" == typeof define && define.amd ? define([], r) : "object" == typeof exports && (module.exports = r())
}(function () {
    return qrcode
});