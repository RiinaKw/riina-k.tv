
var currentUrl = "";

function naviScroll()
{
	var $nav = $("header nav ul");
	var navTopMargin = 80;
	var headerHeight = $("#container header").height();
	
	if ($(window).scrollTop() > headerHeight) {
		$nav.stop().animate(
			{ top: $(window).scrollTop() + navTopMargin - headerHeight }
		);
	} else {
		if ( $(window).width() >= 760 ) {
			$nav.stop().animate(
				{ top: $(window).scrollTop() }
			);
		} else {
			$nav.stop().animate(
				{ top: navTopMargin }
			);
		}
	}
}

// Convenience object to ease global animation queueing
$.globalQueue = {
	queue: function(anim) {
		$("html")
		.queue(function(dequeue) {
			anim()
			.queue(function(innerDequeue) {
				dequeue();
				innerDequeue();
			});
		});
		return this;
	}
};

function init(){
	// external link (a data-rel="external") to new window (a target="_blank")
	$("a[data-rel='external']").on("click", function(){
		window.open(this.href);
		return false;
	});
	
	// navigation effect (scroll)
	$(window).on("scroll", function() {
		naviScroll();
	});
	
	if ( $("#page-music").length ) {
		if ( ! $("#popup-background").length ) {
			$div = $("<div />").attr("id", "popup-background");
			$("#track-list").append($div);
		}
		$("#popup-background").hide();
		
		// music category to global navi
		$("#nav-music ol").remove();
		$("#nav-music").append( $("#index ol").clone().hide() );
		
		// music category index effect (scroll)
		var indexTopMargin = 30;
		var indexDefaultOffset = $("#index-wrapper").offset();
		var indexOffset = $("#index").offset();
		$(window).on("scroll", function() {
			if ( $(window).width() >= 760 ) {
				if ($(window).scrollTop() > indexDefaultOffset.top) {
					$("#index").stop().animate(
						{ marginTop: $(window).scrollTop() - indexDefaultOffset.top + indexTopMargin + "px" }
					);
				} else {
					$("#index").stop().animate(
						{ marginTop: 0 }
					);
				}
			} else {
				$("#index").css({ marginTop: 0 });
				resizeHandler();
			}
		});
		
		$("#popup-background").on("click", function(){
			closeTrack({
				track: $(".track.active")
			});
		}).css(
			{
				position: "absolute",
				height: $(document).height(),
				top: -$("#track-list").offset().top + $(document).scrollTop(),
				left: -$("#track-list").offset().left - $("#outer").offset().left,
				width: $(window).width()
			}
		);
		
		$(".track").on({
			// hover
			"mouseenter" : function(){
				if ( $(".active").length ) {
					return;
				}
				var $track = $(this);
				$("img", $track).fadeTo(100, 0.2, function(){
					$("article").removeClass("hover")
					$track.addClass("hover");
				});
				$(".track-container > header *", $track).not("a").not("img").stop().show().fadeTo(100, 1, function(){
					$(this).show();
				});
			},
			// blur
			"mouseleave" : function(){
				var $track = $(this);
				$track.removeClass("hover");
				$(".track-container img", $track).fadeTo(100, 1, function(){
				});
				$(".track-container > header *", $track).not("a").not("img").stop().fadeTo(100, 0, function(){
					$(this).hide();
				});
			}
		});
		$(".track").on("click", function(e){
			var $track = $(this);
			$track.trigger("mouseleave");
			openTrack({
				track: $track
			});
			return false;
		});
	} else {
		$("#nav-music ol").remove();
	}
	
	var urlHash = location.hash;
	//ハッシュ値があればページ内スクロール
	if (urlHash) {
		//スクロールを0に戻す
		$("body,html").stop().scrollTop(0);
		setTimeout(function () {
			// wait load
			scrollToAnchor(urlHash);
		}, 200);
	}
	// normal link
	$('a[href^="#"]').on("click", function() {
		var href = $(this).attr("href");
		var hash = (href == "#" || href == "" ? "html" : href);
		scrollToAnchor(hash);
		return false;
	});
	
	// navigation effect (item pop)
	$("header nav a").off("mouseenter").off("mouseleave");
	var $li = $("header nav > ul > li").not("[class = 'disabled']").not("[class = 'current']");
	//$("header nav > ul > li[class != 'disabled'][class != 'current'] > a").on({
	$("a", $li).on({
		// hover
		"mouseenter" : function(){
			if ( !$("body").hasClass("ajaxing") ) {
				$(this).stop().animate(
					{ top: -10 },
					{ duration: 200, easing: "easeOutBounce" }
				);
				$("nav").dequeue();
			}
		},
		// blur
		"mouseleave" : function(){
			if ( !$("body").hasClass("ajaxing") ) {
				$(this).stop().animate(
					{ top: 0 },
					{ duration: 200, easing: "easeOutBounce" }
				);
			}
		}
	});
	
	// current page navi
	$("header nav li").removeClass("current");
	var $curNavi = $("header nav > ul > li#nav-" + $("body").attr("id").split("-")[1] + " > a");
	$curNavi.parents("li").addClass("current");
	
	$("header nav > ul > li > a").off("click");
	$("header nav > ul > li.disabled > a").on("click", function(e){
		e.preventDefault();
	});
	$("header nav ul > li.current > a").on("click", function(e){
		var $parent = $(this).parents("li");
		if ( $(window).width() < 760 && $parent.attr("id") == "nav-music" && $("#page-music").length ) {
			//$("#nav-music ol").toggle(200);
			var $category = $("#nav-music ol");
			if ( $category.hasClass("open") ) {
				closeCategory($category);
			} else {
				openCategory($category);
			}
		}
		e.preventDefault();
		return false;
	});
	
	// internal link ajax
	$("a[data-rel='internal']").on("click", function(e){
		if ( $("body").hasClass("ajaxing") ) {
			return false;
		}
		var $nav = $(this).parents("li");
		if ( !$nav || !$nav.hasClass("current") ) {
			e.preventDefault();
			$("body,html").stop().animate({scrollTop:0}, 300);
			return loadPage( $(this).attr("href"), location.href, false );
		}
		return true;
	});
	
	
	if ( !$("body").hasClass("effected") ) {
		// load effect
		$("#container").css("visibility", "hidden");
		$("#wrapper").css("height", 0).animate(
			{ height: $(window).height() },
			{
				duration: 2000,
				easing: "easeInQuad",
				step: function(){
					$("html, body").scrollTop(0);
				},
				complete: function(){
					$(this).css("height", "auto");
					$("#container").hide().css("visibility", "visible").fadeIn("slow");
					$("body").addClass("effected");
					resizeHandler();
				}
			}
		);
		
		// background effect
		var bg = $("body").css("background-image");
		var bg2 = "../images/bg-blur.jpg";
		var bgHeight = 800;
		var backgroundPos = ( $(window).height() - bgHeight ) / 2;
		$("body").css({
			backgroundPosition: "center " + backgroundPos + "px"
		});
		$.get(bg2);
		setInterval(function(){
			$("body").css("background-image", "none").delay(50).queue(function(){
				$(this).css({
					backgroundImage: "url(" + bg2 + ")",
					backgroundPosition: "center " + (backgroundPos + 50) + "px"
				}).dequeue();
			}).delay(100).queue(function(){
				$(this).css({
					backgroundPosition: "center " + (backgroundPos - 50) + "px"
				}).dequeue();
			}).delay(100).queue(function(){
				$(this).css("background-image", "none").dequeue();
			}).delay(100).queue(function(){
				$(this).css({
					backgroundImage: bg,
					backgroundPosition: "center " + backgroundPos + "px"
				}).dequeue();
			});
		}, 10000);
		
		var title = location.pathname.split("/")[2];
		//if (location.hash) {
		if (title) {
			var hash = "#track-" + title;
			setTimeout(function () {
				// wait load
				scrollToAnchor(hash);
			}, 3000);
			openTrack({
				track: $(hash)
			});
		}
	}
	
	$(window).on("scroll", function(){
		if ( $("#popup-background").length ) {
			$("#popup-background").css(
				{
					position: "absolute",
					width: $(window).width(),
					height: $(window).height(),
					left: -$("#track-list").offset().left - $("#outer").offset().left,
					top: -$("#track-list").offset().top + $(document).scrollTop()
				}
			);
		}
		debug();
	});
	
	resizeHandler();
	
	$(window).on("load resize orientationchange", resizeHandler);
	$(window).on("orientationchange", function(){ debug("orientationchange") });
	currentUrl = location.href;
}; // function init()

function openCategory($category)
{
	$category.show(200).addClass("open");
}

function closeCategory($category)
{
	$category.hide(200).removeClass("open");
}

// anchor effect (scroll)
function scrollToAnchor(hash)
{
	var target = $(hash);
	if ( target && target.offset() ) {
		var position = target.offset().top;
		$("body,html").stop().animate(
			{
				scrollTop: position
			},
			500
		);
	}
}

function resizeHandler()
{
	var windowHeight = $(window).height();
	var headerHeight = $("#container > header").outerHeight(true);
	var contentHeight = $("#main").outerHeight(true);
	var footerHeight = $("footer").outerHeight(true);
	
	var scrollPos = $("html, body").scrollTop();
	
	if ( $("#page-music").length ) {
		// active track
		var $track = $("article.active");
		if ($track.length) {
			var trackWidth = $track.width();
			var trackPosition = $track.position().left;
			$("article.active .content").css( "left", -trackPosition );
		}
	}
	
	if (_ua.Webkit) {
		$("body").css("overflowY", "hidden");
	} else if (_ua.Opera) {
		$("html").css("overflowY", "hidden");
	}
	if ( windowHeight < headerHeight + contentHeight + footerHeight ) {
		// appear scroll bar
		$("#container").css("height", headerHeight + contentHeight + footerHeight + "px");
		
		if (_ua.Webkit) {
			$("body").css("overflowY", "auto");
		} else if (_ua.Opera) {
			$("html").css("overflowY", "auto");
		}
		$("footer").css({
			position: "relative"
		});
	} else {
		// expand container height
		$("html, body").css("overflowY", "hidden");
		$("#container").css("height", windowHeight + "px");
		$("footer").css({
			position: "absolute",
			bottom: "0"
		});
	}
	
	$("#popup-background").css(
		{
			cursor: "pointer",
		//	position: "absolute",
			top: $(window).scrollTop() - $("main").offset().top,
			height: $(window).height()
		}
	);
	
	if ( $(window).width() >= 760 ) {
		$("#nav-music ol").hide();
	}
	
	naviScroll();
	
	debug();
} // function resizeHandler()

/*****
	open track
	param:
		track : target article.track
		popstate: no add history
*****/
function openTrack(param)
{
	param = {
		track: param.track,
		popstate: param.popstate
	};
	var $article = null;
	if (param.track) {
		$article = param.track;
	}
	if ( !$article || $article.length == 0 ) {
		return;
	}
	if ( $("article.active").length ) {
		return;
	}
	
	closeCategory( $("#nav-music ol") );
	
	debug("open track");
	
	// expand category
	var $categories = $(".category-container");
	var categoriesLength = $categories.length;
	var $category = $article.parents(".category-container");
	if ( $categories.index($category) + 1 == categoriesLength ) {
		// last category
		var trackPerColumn = 3;
		var $category = $article.parents(".category-container");
		var trackHeight = $article.height();
		var trackCount = $(".track", $category).length;
		var trackColumnCount = Math.floor( ( trackCount-1 ) / 3) + 1;
		var currentColumn = Math.floor( $(".track", $category).index($article) / 3 ) + 1;
		var expandColumn = trackColumnCount + ( trackColumnCount == currentColumn ? 1 : 0 );
		if ( $(window).width() < 590 ) {
			expandColumn += 1;
		}
		$category.animate(
			{
				height: expandColumn * trackHeight
			},
			200,
			function(){
				resizeHandler();
			}
		);
	}
	
	$(".close", $article).remove();
	
	scrollToAnchor( "#" + $article.attr("id") );
	$article.addClass("animating");
	$(".content", $article).css("z-index", 10);
	
	$.globalQueue
	.queue(function(){
		// background fade in
		var $background = $("#popup-background");
		if ( $background.is(":hidden") ) {
			$background.stop(false, false).css(
				{
					height: $(window).height(),
					zIndex: 5,
					opacity: 0
				}
			).show().fadeTo(300, 0.8, function(){});
		}
		return $background;
	})
	.queue(function(){
		var $content = $(".content", $article);
		var $iconbox = $(".iconbox", $article);
		if ( $iconbox.length == 0 ) {
			$iconbox = $('<div class="iconbox" />').prependTo($content);
			if ( $("img", $iconbox).length == 0 ) {
				var width = 200;
				var height = 200;
				if ($(window).width() < 760) {
					width = "26.5vw";
					height = "26.5vw";
				}
				$iconbox.empty()
					.css({
						position: "absolute",
						left: 0,
						top: 0,
						width: width,
						height: height,
						opacity: 1,
						display: "block"
					})
					.prepend( $("img", $article).clone().show() );
			}
		}
		var $innerHeader = $(".content header", $article);
		if ( $innerHeader.length == 0 ) {
			var $orgHeader = $(".track-container > header", $article);
			$innerHeader = $('<header />').prependTo( $(".content-inner", $article) );
			$("> a > *", $orgHeader).not("a").not("img").each(function(){
				$innerHeader.append( $(this).clone().show() );
			});
		}
		$(".track-container > header *", $article).not("a").not("img").hide();
		
		// show iconbox
		var width = 600;
		if ($(window).width() < 760) {
			width = "79.5vw";
		}
		return $content.css({opacity:0}).show().animate(
			{
				width: width,
				left: 0,
				opacity: 1
			},
			{ duration: 300 }
		);
	})
	.queue(function(){
		var pos = $article.position();
		var duration = ( pos.left ? 600 : 0 );
		var containerOffset = $article.parents(".category-container").offset();
		var articleOffset = $article.offset();
		$(".iconbox", $article).show().css("opacity", 1);
		$(".content > *", $article).show();
		// cover slide in
		return $(".content", $article).show().animate(
			{ left: containerOffset.left - articleOffset.left },
			{ duration: duration }
		);
	})
	.queue(function(){
		return $(".animating *").fadeTo(500, 1);
	})
	.queue(function(){
		if ( $article.hasClass("animating") ) {
			$article.removeClass("animating");
			if ( !$article.hasClass("active") ) {
				//var url = location.href.split("#")[0] + "#" + $article.attr("id");
				var url = "/music/" + $article.attr("id").split("-")[1];
				if ( !param.popstate ) {
					window.history.pushState(null, null, url);
				}
				currentUrl = url;
				document.title = $("h4", $article).html() + " - " + $("h1 a").html();
				$article.addClass("active");
			}
			// close button
			$close = $(".close", $article);
			if ( !$close.length ) {
				$close = $("<div />").addClass("close").appendTo( $(".iconbox", $article) );
				$close.html("close").css("opacity", 0).fadeTo(200, 0.8);
				$close.on("click", function(){
					debug("close track");
					closeTrack({
						track: $(this).parents(".track")
					});
				});
			}
			// soundcloud fade in
			var iframeUrl = $("a.meta", $article).data("iframe");
			var previewUrl = $("a.meta", $article).data("preview");
			var $iframeWrapper = $(".iframe-wrapper", $article);
			if ( $iframeWrapper.length == 0 ) {
				$(".content", $article).append('<div class="iframe-wrapper" width="600" height="200" style="" />');
				$iframeWrapper = $(".iframe-wrapper", $article).show(400);
			}
			if (iframeUrl) {
				// stop mp3
				var audio = $("iframe.preview");
				for (idx in track) {
					var track = audio[idx];
					track.pause();
					track.currentTime = 0;
				}
				
				// soundcloud iframe
				var $iframe = $("iframe", $article);
				if ( $iframe.length == 0 ) {
					$iframeWrapper.append('<iframe />');
					$iframe = $("iframe", $article);
					$iframe.attr("src", iframeUrl);
					$iframe.on("load", function(){
						setTimeout(function(){
							var iframeElement = $(".track.active iframe").get(0);
							var widget = SC.Widget(iframeElement);
							widget.bind(SC.Widget.Events.PLAY, function() {
								$(".track.active iframe").addClass("playing");
							});
							widget.bind(SC.Widget.Events.PAUSE, function() {
								$("iframe.playing").removeClass("playing");
							});
							widget.bind(SC.Widget.Events.FINISH, function() {
								$("iframe.playing").removeClass("playing");
							});
						}, 1000);
					});
				}
			} else if (previewUrl) {
				//$iframeWrapper.empty().html('<p class="no-soundcloud">soundcloud not available. <a href="' + previewUrl + '" target="_blank">download here</a></p>');
				// stop soundcloud
				var iframeElement = $("iframe.playing").get(0);
				if (iframeElement) {
					widget = SC.Widget(iframeElement);
					widget.pause();
				}
				
				var src = previewUrl.replace("preview", "music_iframe");
				var $iframe = $("iframe", $article);
				if ( $iframe.length == 0 ) {
					$iframeWrapper.append('<iframe />');
					$iframe = $("iframe", $article);
					$iframe.addClass("preview").addClass("playing").attr("src", src);
				}
				//$iframeWrapper.empty().html('<p class="no-soundcloud"><audio controls="controls" preload="metadata"><source src="' + previewUrl + '" type="audio/mp3" /></audio></p>');
			} else {
				$iframeWrapper.empty().html('<p class="no-soundcloud">listening not available</p>');
			}
			debug("open track complete");
		}
		return $article;
		
	}); // $.globalQueue.queue()
	
} // function openTrack()

/*****
	close track
	param:
		track : target article.track
		popstate: no add history
		success: callback function()
*****/
function closeTrack(param)
{
	param = {
		track: param.track,
		popstate: param.popstate,
		success: param.success
	};
	var $article = null;
	if (param.track) {
		$article = param.track;
	}
	if ( !$article || $article.length == 0 ) {
		return;
	}
	
	debug("close track");
	
	var $iconbox = $(".iconbox", $article);
	
	// close button
	$(".close", $article).fadeTo(200, 0, function(){
		$(this).remove();
	});
	
	
	$.globalQueue
	.queue(function(){
		return $(".active .content > *").not(".iconbox").fadeTo(300, 0);
	})
	.queue(function(){
		$(".active .content > *").not(".iconbox").hide();
		// content fade out
		var pos = $article.position();
		return $(".active .content").animate(
			{ left: 0 },
			{ duration: ( pos.left ? 600 : 0 ) }
		);
	})
	.queue(function(){
		return $(".content", $article).fadeTo(100, 0);
	})
	.queue(function(){
		$content = $(".content", $article);
		$content.hide();
		$article.removeClass("active");
		$content.css("z-index", 0);
		
		// shrink category
		var $categories = $(".category-container");
		var categoriesLength = $categories.length;
		var $category = $article.parents(".category-container");
		if ( $categories.index($category) + 1 == categoriesLength ) {
			var $category = $article.parents(".category-container");
			var trackHeight = $article.height();
			var trackCount = $(".track", $category).length;
			var trackColumn = Math.floor( ( trackCount-1 ) / 3) + 1;
			$category.animate(
				{
					height: trackColumn * trackHeight
				},
				200,
				function(){
					resizeHandler();
				}
			);
		}
		$("iframe.preview", $article).remove();
		if (param.popstate) {
			if (param.success) {
				param.success();
			}
			debug("close track complete");
		} else {
			// background fade out
			$("#popup-background").css(
				{
					zIndex: 0,
					cursor: "default"
				}
			).fadeTo(100, 0, function(){
				$("#popup-background").hide();
				if (param.success) {
					param.success();
				}
				debug("close track complete");
			});
		}
		return $content;
	}); // $.globalQueue.queue()
} // function closeTrack()

function loadPage(nextUrl, curUrl, popstate)
{
	var reCurPage = currentUrl.match( /^(https?:\/\/.*)?\/([^\/]*)/ );
	var curPage = "/" + (reCurPage[2] ? reCurPage[2] : "");
	
	var reNextPage = nextUrl.match( /^(https?:\/\/.*)?\/([^\/]*)/ );
	var nextPage = "/" + (reNextPage[2] ? reNextPage[2] : "");
	
	if (curPage != nextPage) {
		debug("load page");
		$("body").addClass("ajaxing");
		$.ajax({
			url: nextUrl,
			dataType: "html"
		})
		.done(function(data, status, xhr){
			
			if ( $("body").hasClass("ajaxing") ) {
				$("header nav ul > li.current ol").hide(200);
				$("body,html").stop().animate(
					{scrollTop: 0}, 500, function(){
						$("#main").slideUp(1000, function(){
							var bodyId = data.match(/<\s*body\s[^>]*id="\s*(.*?)\s*"[^>]*>/)[1];
							$("body").attr("id", bodyId);
							$("#main").empty().append( $( "#main", $(data) ).html() ).slideDown(1000, function(){
								resizeHandler();
								currentUrl = curUrl;
								if ( !popstate && currentUrl != nextUrl ) {
									window.history.pushState(null, null, nextUrl);
								}
								var title = data.match(/<title>(.*)<\/title>/)[1];
								document.title = title;
								init();
								$("body").removeClass("ajaxing");
								$("header nav a").css("top", "");
								debug("load page complete");
							
								// get track title
								var title = location.pathname.split("/")[2];
								if ( $("#page-music").length && title ) {
									openTrack({
										track: $("#track-" + title),
										popstate: popstate
									});
									setTimeout(function(){
										scrollToAnchor("#track-" + title);
									}, 500);
								}
							});
						});
					}
				);
			}
			
		})
		.fail(function(xhr, status, message){
			alert("something wrong");
		});
	} else if ( $("#page-music").length ) {
		var hash = location.hash;
		if (hash) {
			if ( $("article.active").length ) {
				closeTrack({
					track: $("article.active"),
					popstate: popstate,
					success: function(){
						openTrack({
							track: $(hash),
							popstate: popstate
						});
					}
				});
			} else {
				openTrack({
					track: $(hash),
					popstate: popstate
				});
			}
			scrollToAnchor(hash);
		} else {
			closeTrack({
				track: $("article.active")
			});
		}
	}
	return false;
}

$(window).on("popstate", function(e) {
	var nextUrl = e.originalEvent.target.location.href;
	loadPage(nextUrl, currentUrl, true);
});

function debug(eventName)
{
	if ( location.protocol == "file:" || location.host == "test.riina-k.tv" ) {
		var $body = $("body");
		var $documentWidth = $(".document-width", $body);
		var $documentHeight = $(".document-height", $body);
		var $windowWidth = $(".window-width", $body);
		var $windowHeight = $(".window-height", $body);
		var $windowScrollTop = $(".scroll-top", $body);
		var $eventName = $(".event-name", $body);
		if ( !$documentWidth.length ) {
			$documentWidth = $("<span />").addClass("document-width").css({
				position: "fixed",
				bottom: 80,
				zIndex: 100,
				backgroundColor: "rgba(0, 0, 0, 0.7)"
			}).prependTo($body);
		}
		if ( !$documentHeight.length ) {
			$documentHeight = $("<span />").addClass("document-height").css({
				position: "fixed",
				bottom: 60,
				zIndex: 100,
				backgroundColor: "rgba(0, 0, 0, 0.7)"
			}).prependTo($body);
		}
		if ( !$windowWidth.length ) {
			$windowWidth = $("<span />").addClass("window-width").css({
				position: "fixed",
				bottom: 40,
				zIndex: 100,
				backgroundColor: "rgba(0, 0, 0, 0.7)"
			}).prependTo($body);
		}
		if ( !$windowHeight.length ) {
			$windowHeight = $("<span />").addClass("window-height").css({
				position: "fixed",
				bottom: 20,
				zIndex: 100,
				backgroundColor: "rgba(0, 0, 0, 0.7)"
			}).prependTo($body);
		}
		if ( !$windowScrollTop.length ) {
			$windowScrollTop = $("<span />").addClass("scroll-top").css({
				position: "fixed",
				bottom: 0,
				zIndex: 100,
				backgroundColor: "rgba(0, 0, 0, 0.7)"
			}).prependTo($body);
		}
		if ( !$eventName.length && eventName ) {
			$eventName = $("<span />").addClass("event-name").css({
				position: "fixed",
				opacity: 1,
				bottom: 100,
				zIndex: 100,
				backgroundColor: "rgba(0, 0, 0, 0.7)"
			}).prependTo($body);
		}
		$documentWidth.html( " document-width:" +  $("body").width() + " " );
		$documentHeight.html( " document-height:" + $("body").height() + " " );
		$windowWidth.html( " window-width:" +  $(window).width() + " " );
		$windowHeight.html( " window-height:" + $(window).height() + " " );
		$windowScrollTop.html( " scroll-top:" + $(window).scrollTop() + " " );
		if (eventName) {
			$eventName.show().css("opacity", 1).html( " " + eventName + " " ).stop().css("opacity", 1).fadeOut(500);
		}
	}
} // functino debug();
