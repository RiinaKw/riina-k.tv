// http://w3g.jp/blog/tools/js_browser_sniffing
var _ua = (function(){
return {
ltIE6:typeof window.addEventListener == "undefined" && typeof document.documentElement.style.maxHeight == "undefined",
ltIE7:typeof window.addEventListener == "undefined" && typeof document.querySelectorAll == "undefined",
ltIE8:typeof window.addEventListener == "undefined" && typeof document.getElementsByClassName == "undefined",
IE:document.uniqueID,
Firefox:window.sidebar,
Opera:window.opera,
Webkit:!document.uniqueID && !window.opera && !window.sidebar && window.localStorage && typeof window.orientation == "undefined",
Mobile:typeof window.orientation != "undefined"
}
})();