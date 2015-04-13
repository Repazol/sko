function MakeHTML(res) {
                newhtml = '';
                res = res.replace("http://namba.kz/#!/video/", "http://namba.kz/video_player.php?id=");
                res = res.replace("https://www.youtube.com/watch?v=", "//www.youtube.com/embed/");
                res = res.replace("http://kset.kz/video/view/", "http://kset.kz/video_frame.php?id=");
                res = res.replace("http://kset.kz/cinema/view/", "http://kset.kz/cinema/frame?id=");
                res = res.replace("http://kset.kz/v.php?id=", "http://kset.kz/video_frame.php?id=");
                res = res.replace("http://kivvi.kz/watch/", "http://v.kiwi.kz/v2/");
                res = res.replace("http://www.mill.kz/videos/", "http://www.mill.kz/embed/");
                res = res.replace("http://kaztube.kz/kz/video/", "http://kaztube.kz/ru/video/embed/");
                res = res.replace("http://kaztube.kz/ru/video/", "http://kaztube.kz/ru/video/embed/");
                
                var str = res.slice(22,31);

                    if (str == "/flashvar" || str == "flashvars") {
                        newhtml = '<div class="video-player" class="align-center"><object width="100%" height="380" data="http://video.namba.kz/swf/player/3.2.11/flowplayer-3.2.11.swf" type="application/x-shockwave-flash"><param name="allowfullscreen" value="true"><param name="wmode" value="opaque"><param name="allowscriptaccess" value="always"><param value="http://video.namba.kz/swf/player/3.2.11/flowplayer-3.2.11.swf" name="src"><param name="flashvars" value="config='+res+'"></object></div>';
                    }
                        else { 
                        		str = res.slice(-4);
                        	if (str == ".mp4"){
								newhtml='<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="100%" height="385" id="Player-12f2aa00ca963050fec85b3bb0a2bcf8"><param name="movie" value="http://tv1.kz.kz/engine/classes/flashplayer/media_player_v3.swf?stageW=640&amp;stageH=380&amp;contentType=video&amp;videoUrl='+res+'&amp;videoHDUrl=&amp;showWatermark=false&amp;showPreviewImage=true&amp;previewImageUrl=&amp;isYouTube=false&amp;rollOverAlpha=0.5&amp;contentBgAlpha=0.8&amp;progressBarColor=0xFFFFFF&amp;defaultVolume=1&amp;fullSizeView=2&amp;showRewind=false&amp;showInfo=false&amp;showFullscreen=true&amp;showScale=true&amp;showSound=true&amp;showTime=true&amp;showCenterPlay=true&amp;autoHideNav=false&amp;videoLoop=false&amp;defaultBuffer=3" /><param name="allowFullScreen" value="true" /><param name="scale" value="noscale" /><param name="quality" value="high" /><param name="bgcolor" value="#000000" /><param name="wmode" value="opaque" /><embed src="http://tv1.kz/engine/classes/flashplayer/media_player_v3.swf?stageW=638&amp;stageH=385&amp;contentType=video&amp;videoUrl='+res+'&amp;videoHDUrl=&amp;showWatermark=false&amp;showPreviewImage=true&amp;previewImageUrl=&amp;isYouTube=false&amp;rollOverAlpha=0.5&amp;contentBgAlpha=0.8&amp;progressBarColor=0xFFFFFF&amp;defaultVolume=1&amp;fullSizeView=2&amp;showRewind=false&amp;showInfo=false&amp;showFullscreen=true&amp;showScale=true&amp;showSound=true&amp;showTime=true&amp;showCenterPlay=true&amp;autoHideNav=false&amp;videoLoop=false&amp;defaultBuffer=3" quality="high" bgcolor="#000000" wmode="opaque" allowFullScreen="true" width="640" height="380" align="middle" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"></embed></object>';
		                        }else {
		                            newhtml = '<div class="video-player" class="align-center"><iframe title="tv1.kz" width="613" height="380" src="'+ res +'" frameborder="0" allowfullscreen></iframe></div>';
		                        }
                        }
                    // проверка принудительного отключения плеера
                    str= res.slice(0,2);
                    if (str == "-1") {
                    	newhtml = "";
                    }
                    if (str == "-2") {
                        newhtml = '<div class="align-center"><b><span style="color:#CC0000">К великому сожалению видео было удалено по просьбе правообладателей :( <br>Загляните к нам попозже, возможно все уладится. </span></b></div>';
                    }
                
                    return newhtml;

    }
	
$(document).ready(function(){

        $("div#video").each(function(i,n) { 
            var u=$(this).html();
			$(this).html(MakeHTML(u));
			
       });
        	
});