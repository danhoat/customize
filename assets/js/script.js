(function($, wp) {
    const { __, _x, _n, _nx } = wp.i18n;
    const isEmpty = function(data) {
        if (typeof(data) === 'object'){
            if (JSON.stringify(data) === '{}' || JSON.stringify(data) === '[]') {
                return true;
            } else if(!data) {
                return true;
            }
            return false;
        } else if(typeof(data) === 'string') {
            if (!data.trim()) {
                return true;
            }
            return false;
        } else if(typeof(data) === 'undefined') {
            return true;
        } else {
            return false;
        }
    };
    const normalizeSlideHeights = function() {
        $('#carousel-example-generic').each(function(i, element){
            var items = $(element).find('.item');
            items.css('min-height', 0);
            var maxHeight = Math.max.apply(null, items.map(function(){
                return $(this).outerHeight()
            }).get() );
            items.css('min-height', maxHeight + 'px');
        });
    };

    $(window).on('load resize orientationchange', normalizeSlideHeights);

    var player; 
    $(document).ready(function() {
        function addhttp(url) {
            if (!/^(?:f|ht)tps?\:\/\//.test(url)) {
                url = "http://" + url;
            }
            return url;
        }

        function setCookie(name,value,days = 7) {
            var expires = "";
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days*24*60*60*1000));
                expires = "; expires=" + date.toUTCString();
            }
            document.cookie = name + "=" + (value || "")  + expires + "; path=/";
        }
        function getCookie(name) {
            var nameEQ = name + "=";
            var ca = document.cookie.split(';');
            for(var i=0;i < ca.length;i++) {
                var c = ca[i];
                while (c.charAt(0)==' ') c = c.substring(1,c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
            }
            return null;
        }
        function eraseCookie(name) {   
            document.cookie = name+'=; Max-Age=-99999999;';  
        }

        var sel = $(".audio-field .audio-input input[type='radio']:checked").val();
        if ( sel == "cupload" ) {
            $(".audio-input").find(".audfiled.cexternal").hide();
            $(".audio-input").find(".audfiled.cupload").show();
        } else if ( sel == "cexternal" ) {
            $(".audio-input").find(".audfiled.cupload").hide();
            $(".audio-input").find(".audfiled.cexternal").show();
        }
        $(document).on("change", ".audio-field .audio-input input[type='radio']", function(e) {
            e.preventDefault();
            var $this = $(this);
            var $parent = $this.closest(".audio-input");
            if ( $this.val() == "cupload" ) {
                $parent.find(".audfiled.cexternal").hide();
                $parent.find(".audfiled.cupload").show();
            } else if ( $this.val() == "cexternal" ) {
                $parent.find(".audfiled.cupload").hide();
                $parent.find(".audfiled.cexternal").show();
            }
        });

        $(document).on("click", ".audio-placeholer .remove-items:not(:disabled)", function(e) {
            var $this = $(this);
            var parent = $this.closest(".audio-placeholer");
            var attcid = parent.find("input[name^='audio_clip_'][type='hidden']").val();
            var clipsitem = $this.closest(".audio-field").find(".aud-wrapper > .audio-placeholer").length;
            var has_edit = $this.closest(".audio-field").find("input[name='is_edit']").length;
            var is_edit = (has_edit > 0) ? true : false;
            
            var decode_m = decodeURIComponent(parent.find("input[name^='audio_clip_'][type='hidden']").val());
            try {
                var mdata = JSON.parse(decode_m);
                if (mdata && mdata.constructor === Object && "src" in mdata && "host" in mdata) {
                    if ( mdata.host == "host" ) {
                        $.ajax({
                            type: "POST",
                            url: microjob.ajax_url,
                            data: {
                                action: 'microjob_delete_audio',
                                id: mdata.src
                            },
                            beforeSend: function(){
                                $("button.btn-submit").attr("disabled", true);
                                parent.css({"opacity": "0.4", "pointer-events": "none", "transition": "all 0.1s ease-in"});
                                $this.closest(".aud-wrapper").find(".remove-items").attr("disabled", true);
                            },
                            success: function (response) {
                                $("button.btn-submit").attr("disabled", false);
                                $this.closest(".aud-wrapper").find(".remove-items").attr("disabled", false);
                                $this.closest(".audio-field").find(".new_audio").show();
                                setCookie("audio_0", 0);
                                setCookie("audio_1", 0);
                                setCookie("audio_2", 0);
                                var html = '';
                                var i=0;
                                var ind = $this.closest(".audio-field").find(".aud-wrapper > .audio-placeholer").index($this.closest(".audio-placeholer"));
                                $this.closest(".audio-field").find(".aud-wrapper > .audio-placeholer:not(:eq("+ind+"))").each(function(index, element) {
                                    var inval = $(element).find("input[name^='audio_clip_'][type='hidden']").val();
                                    var decode_d = decodeURIComponent(inval);
                                    try {
                                        var ddata = JSON.parse(decode_d);
                                        if (ddata && ddata.constructor === Object && "src" in ddata && "host" in ddata) {
                                            var name = $(element).find("p.audio-text span").first().text();
                                            var size = $(element).find("p.audio-text span").last().text();

                                            html+= '<div class="audio-placeholer" id="up_'+index+'">';
                                            if ( ddata.host == "host" ) {
                                                html+= '<p class="audio-text"><span>'+name+'</span> <span>'+size+'</span></p>';
                                                html+= '<div class="audio-progess"><div class="audio-progess-bar" style="width: 100%;">100%</div></div>';
                                            } else {
                                                html+= '<p class="audio-text"><span>'+ddata.src+'</span> <span></span></p>';
                                            }
                                            html+= '<input type="hidden" class="input-item" name="audio_clip_'+index+'" value="'+inval+'"><a href="#" class="remove-items aud-remove-extra-item"><i class="fa fa-times"></i></a>';
                                            html+= '</div>';

                                            if ( !is_edit ) {
                                                setCookie("audio_"+i, name+","+size+","+ddata.src+","+ddata.host);
                                            }
                                            i++;
                                        }
                                    } catch (e) {}
                                });
                                if ( i < 3 && is_edit ) {
                                    var end = (i==0) ? 2 : 3 ;
                                    for (var j=i; j<end; j++) {
                                        html+= '<input type="hidden" class="input-item" name="audio_clip_'+j+'" value="">';
                                    }
                                }
                                $this.closest(".aud-wrapper").html(html);
                            }
                        });
                    } else {
                        setCookie("audio_0", 0);
                        setCookie("audio_1", 0);
                        setCookie("audio_2", 0);
                        
                        $("button.btn-submit").attr("disabled", false);
                        $this.closest(".audio-field").find(".new_audio").show();
                        var html = '';
                        var i=0;
                        var ind = $this.closest(".audio-field").find(".aud-wrapper > .audio-placeholer").index($this.closest(".audio-placeholer"));
                        $this.closest(".audio-field").find(".aud-wrapper > .audio-placeholer:not(:eq("+ind+"))").each(function(index, element) {
                            var inval = $(element).find("input[name^='audio_clip_'][type='hidden']").val();
                            var decode_d = decodeURIComponent(inval);
                            try {
                                var ddata = JSON.parse(decode_d);
                                if (ddata && ddata.constructor === Object && "src" in ddata && "host" in ddata) {
                                    var name = $(element).find("p.audio-text span").first().text();
                                    var size = $(element).find("p.audio-text span").last().text();

                                    html+= '<div class="audio-placeholer" id="up_'+index+'">';
                                    if ( ddata.host == "host" ) {
                                        html+= '<p class="audio-text"><span>'+name+'</span> <span>'+size+'</span></p>';
                                        html+= '<div class="audio-progess"><div class="audio-progess-bar" style="width: 100%;">100%</div></div>';
                                    } else {
                                        html+= '<p class="audio-text"><span>'+ddata.src+'</span> <span></span></p>';
                                    }
                                    html+= '<input type="hidden" class="input-item" name="audio_clip_'+index+'" value="'+inval+'"><a href="#" class="remove-items aud-remove-extra-item"><i class="fa fa-times"></i></a>';
                                    html+= '</div>';

                                    if ( !is_edit ) {
                                        setCookie("audio_"+i, name+","+size+","+ddata.src+","+ddata.host);
                                    }
                                    i++;
                                }
                            } catch (e) {}
                        });
                        if ( i < 3 && is_edit ) {
                            var end = (i==0) ? 2 : 3 ;
                            for (var j=i; j<end; j++) {
                                html+= '<input type="hidden" class="input-item" name="audio_clip_'+j+'" value="">';
                            }
                        }
                        $this.closest(".aud-wrapper").html(html);
                    }
                }
            }
            catch (e) {
                toastr.error(__("Invalid action", "enginethemes-child"), '', {closeButton: true});
            }
            e.preventDefault();
        });        

        $(document).on("click", ".audio-field .new_audio:not('.disabled')", function(e) {
            e.preventDefault();
            var $this = $(this);
            var $parent = $this.closest(".audio-field");
            var html = '<div class="audio-input">' 
                + '<input type="radio" class="hidden" name="uptype" value="cupload" id="cupload" checked="checked">' 
                + '<label for="cupload">'+__('Upload', 'enginethemes-child')+'</label>' 
                + '<input type="radio" class="hidden" name="uptype" value="cexternal" id="cexternal">' 
                + '<label for="cexternal">'+__('Link', 'enginethemes-child')+'</label>' 
                + '<div class="audfiled cupload">' 
                + '<input id="audio_upload" class="hidden" type="file" accept=".mp3,.wav,.aac">' 
                + '<label for="audio_upload">'+__('Select audio clip', 'enginethemes-child')+'</label>' 
                + '</div>' 
                + '<div class="audfiled cexternal" style="display: none;">' 
                + '<input type="text" name="audiolink" value="" placeholder="'+__('Enter Audio link', 'enginethemes-child')+'">' 
                + '<button class="setaudio">'+__('Add', 'enginethemes-child')+'</button>' 
                + '</div>' 
                + '</div>';
            if ( $parent.find(".aud-wrapper > .audio-placeholer").length <= 2 && $parent.find(".aud-grp .audio-input").length <= 0 ) {
                $parent.find(".aud-grp").html(html);
            } else {
                $parent.find(".aud-grp").html("");
            }
        });

        $(document).on("click", ".aud-grp .audio-input .setaudio", function(e) {
            e.preventDefault();
            var $this = $(this);
            var $parent = $this.closest(".audio-field");
            var input = $this.closest(".audfiled.cexternal").find("input[type='text']").val();
            var has_edit = $parent.find("input[name='is_edit']").length;
            var is_edit = (has_edit > 0) ? true : false;
            var clipsitem = $parent.find(".aud-wrapper > .audio-placeholer").length;
        
            if ( clipsitem <= 2 && !isEmpty(input) ) {
                input = addhttp(input);
                var clipid = clipsitem;
                if ( $parent.find(".aud-wrapper #up_0").length > 0 ) {
                    if ( $parent.find(".aud-wrapper #up_1").length < 1 ) {
                        clipid = 1;
                    } else if ( $parent.find(".aud-wrapper #up_2").length < 1 ) {
                        clipid = 2;
                    }
                } else if ( $parent.find(".aud-wrapper #up_1").length > 0 ) {
                    if ( $parent.find(".aud-wrapper #up_0").length < 1 ) {
                        clipid = 0;
                    } else if ( $parent.find(".aud-wrapper #up_2").length < 1 ) {
                        clipid = 2;
                    }
                } else if ( $parent.find(".aud-wrapper #up_2").length > 0 ) {
                    if ( $parent.find(".aud-wrapper #up_0").length < 1 ) {
                        clipid = 0;
                    } else if ( $parent.find(".aud-wrapper #up_1").length < 1 ) {
                        clipid = 1;
                    }
                }
                $.ajax({
                    type: "POST",
                    url: microjob.ajax_url,
                    data: {
                        action: 'microjob_check_link',
                        url: input
                    },
                    beforeSend: function(){
                        $("button.btn-submit").attr("disabled", true);
                        $parent.find(".new_audio").addClass("disabled");
                        $parent.find(".aud-grp .audio-input input[type='radio']").prop("disabled", true);
                        $this.closest(".audfiled.cexternal").find("input[type='text']").prop("disabled", true);
                        $this.prop("disabled", true).text(__('Loading...', 'enginethemes-child'));
                    },
                    success: function (response) {
                        $("button.btn-submit").attr("disabled", false);
                        if ( response != "error" ) {
                            if ( !is_edit ) {
                                setCookie("audio_"+clipid, input+",,"+input+","+response);
                            }
                            if ( $parent.find(".aud-wrapper > input[name='audio_clip_"+clipid+"']").length > 0 ) {
                                $parent.find(".aud-wrapper > input[name='audio_clip_"+clipid+"']").remove();
                            }
                            var html = '<div class="audio-placeholer" id="up_'+clipid+'">'
                            +'<p class="audio-text"><span>'+input+'</span><span></span></p>'
                            +'<a href="#" class="remove-items aud-remove-extra-item"><i class="fa fa-times"></i></a>'
                            +'<input type="hidden" class="input-item" name="audio_clip_'+clipid+'" value="'+encodeURIComponent(JSON.stringify({'src':input,'host':response}))+'">'
                            +'</div>';
                            $parent.find(".aud-wrapper").append(html);
                            $parent.find(".aud-grp").html("").show();
                            $parent.find(".new_audio").removeClass("disabled").show();
                            if ( clipsitem >= 2 ) {
                                $parent.find(".new_audio").hide();
                            }
                        } else {
                            toastr.error(__("Invalid or unsupported audio link", "enginethemes-child"), '', {closeButton: true});
                            $parent.find(".aud-grp").show();
                            $parent.find(".new_audio").removeClass("disabled").show();
                            $parent.find(".aud-grp .audio-input input[type='radio']").prop("disabled", false);
                            $this.closest(".audfiled.cexternal").find("input[type='text']").prop("disabled", false);
                            $this.prop("disabled", false).text(__('Add', 'enginethemes-child'));
                        }
                    }
                });
            } else {
                toastr.error(__("You can add maximum 3 audio clip", "enginethemes-child"), '', {closeButton: true});
            }
        });

        $(document).on("change", ".aud-grp .audio-input .audfiled.cupload input[type='file']#audio_upload", function(e) {
            e.preventDefault();
            var $this = $(this);
            var $parent = $this.closest(".audio-field");
            var has_edit = $parent.find("input[name='is_edit']").length;
            var is_edit = (has_edit > 0) ? true : false;
            var clipsitem = $parent.find(".aud-wrapper > .audio-placeholer").length;

            if ( clipsitem <= 2 ) {
                var clipid = clipsitem;
                if ( $parent.find(".aud-wrapper #up_0").length > 0 ) {
                    if ( $parent.find(".aud-wrapper #up_1").length < 1 ) {
                        clipid = 1;
                    } else if ( $parent.find(".aud-wrapper #up_2").length < 1 ) {
                        clipid = 2;
                    }
                } else if ( $parent.find(".aud-wrapper #up_1").length > 0 ) {
                    if ( $parent.find(".aud-wrapper #up_0").length < 1 ) {
                        clipid = 0;
                    } else if ( $parent.find(".aud-wrapper #up_2").length < 1 ) {
                        clipid = 2;
                    }
                } else if ( $parent.find(".aud-wrapper #up_2").length > 0 ) {
                    if ( $parent.find(".aud-wrapper #up_0").length < 1 ) {
                        clipid = 0;
                    } else if ( $parent.find(".aud-wrapper #up_1").length < 1 ) {
                        clipid = 1;
                    }
                }
                var filename = $this.val();
                var extension = filename.split('.').pop().toLowerCase();
                if (filename.substring(3,11) == 'fakepath') {
                    filename = filename.substring(12);
                }
                var validFile = ['mp3', 'wav', 'acc'];
                if ($.inArray(extension, validFile) == -1) {
                    toastr.error(__("Only mp3, wav, acc files are allowed", "enginethemes-child"), '', {closeButton: true});
                } else {
                    var file_s = 0; var fileSize = 0;
                    if( window.navigator.userAgent.indexOf("MSIE ") > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./) ) {
                        var objFSO = new ActiveXObject("Scripting.FileSystemObject");
                        var filePath = $this[0].value;
                        var objFile = objFSO.getFile(filePath);
                        var file_s = objFile.size;
                        var fileSize = file_s / 1048576;
                    } else {
                        file_s = $this[0].files[0].size;
                        fileSize = file_s / 1048576;
                    }
                    if ( file_s > 20971520 ) {
                        toastr.error(__("You can upload maximum 20 MB audio file", "enginethemes-child"), '', {closeButton: true});
                    } else {
                        fileSize = parseFloat(fileSize).toFixed(2);
                        if (window.File && window.FileList && window.FileReader) {
                            var formdata = new FormData();
                            formdata.append("action", "microjob_upload_audio");
                            formdata.append("audio_clip", $this[0].files[0]); 
                            formdata.append("name", filename); 
                            formdata.append("filesize", file_s); 
                            $.ajax({
                                type: "POST",
                                url: microjob.ajax_url,
                                data: formdata,
                                cache: false,
                                processData: false,
                                contentType: false,
                                beforeSend: function(){
                                    $parent.find(".aud-grp").hide();
                                    $parent.find(".new_audio").addClass("disabled");
                                    $("button.btn-submit").attr("disabled", true);
                                    $this.attr("disabled", true);
                                    var html = '<div class="audio-placeholer" id="up_'+clipid+'">'
                                        +'<p class="audio-text"><span>'+filename+'</span> <span>('+fileSize+' MB)</span></p>'
                                        +'<div class="audio-progess">'
                                        +'<div class="audio-progess-bar" style="width: 0%;"></div>'
                                        +'</div>'
                                        +'<input type="hidden" class="input-item" name="audio_clip_'+clipid+'" value="">'
                                        +'</div>';
                                    $parent.find(".aud-wrapper").append(html);
                                },
                                xhr: function(){
                                    var xhr = $.ajaxSettings.xhr() ;
                                    if ( xhr.upload ) {
                                        xhr.upload.addEventListener( 'progress', function(evt) {
                                            if ( evt.lengthComputable ) {
                                                var perc = ( evt.loaded / evt.total ) * 100;
                                                perc = perc.toFixed(2);
                                                $parent.find(".aud-wrapper #up_"+clipid+" .audio-progess-bar").width(perc + '%');
                                                $parent.find(".aud-wrapper #up_"+clipid+" .audio-progess-bar").html(perc+'%');
                                            }
                                        }, false );
                                    }
                                    return xhr ;
                                },
                                success: function (response) {
                                    $parent.find(".new_audio").removeClass("disabled");
                                    $("button.btn-submit").attr("disabled", false);
                                    $this.attr("disabled", false);
                                    if ( /^\d+$/.test(response) && response > 0 ) {
                                        $parent.find(".aud-grp").html("").show();
                                        if ( !is_edit ) {
                                            setCookie("audio_"+clipid, filename+",("+fileSize+" MB),"+response+",host");
                                        }
                                        if ( $parent.find(".aud-wrapper > input[name='audio_clip_"+clipid+"']").length > 0 ) {
                                            $parent.find(".aud-wrapper > input[name='audio_clip_"+clipid+"']").remove();
                                        }
                                        $parent.find(".aud-wrapper #up_"+clipid+" input[type='hidden']").val(encodeURIComponent(JSON.stringify({'src':response,'host':'host'})));
                                        $parent.find(".aud-wrapper #up_"+clipid).append('<a href="#" class="remove-items aud-remove-extra-item"><i class="fa fa-times"></i></a>');
                                        if ( clipsitem >= 2 ) {
                                            $parent.find(".new_audio").hide();
                                        }
                                    } else {
                                        toastr.error(__("Failed to upload audio clip", "enginethemes-child"), '', {closeButton: true});
                                        $parent.find(".aud-grp").show();
                                        $parent.find(".new_audio").show().removeClass("disabled");
                                        $parent.find(".aud-wrapper #up_"+clipid).remove();
                                        $this.closest(".add-audio").show();
                                    }
                                    $parent.find(".add-audio #audio_hidden").val(filename);
                                }
                            });
                        } else {
                            toastr.error(__("Your browser doesn't support to File API", "enginethemes-child"), '', {closeButton: true});
                        }
                    }
                }
            } else {
                toastr.error(__("You can add maximum 3 audio clip", "enginethemes-child"), '', {closeButton: true});
            }
        });

        var settings = {
            instanceName:"player1",
            cssUrl:microjob.childdir+'/assets/css/brona.css',
            sourcePath:"",
            activePlaylist:"#playlist-mixed",
            activeItem:0,
            volume:0.5,
            autoPlay:false,
            preload:true,
            randomPlay:false,
            loopState:'playlist',
            soundCloudAppId:"r4wruADPCq7iqJomagvYpdehvILa2bgE",
            gDriveAppId:"AIzaSyB0Rw9B0WgjWQUYoxRi_rwpwr5E0ZxXuXs",
            usePlaylistScroll:true,
            playlistScrollOrientation:"vertical",
            playlistScrollTheme:"dark-thin",
            useKeyboardNavigationForPlayback:true,
            facebookAppId:"644413448983338",
            useNumbersInPlaylist: true,
            playlistItemContent:"title,thumb,duration",
            searchDescriptionInPlaylist: false,
            playlistOpened:true
        };

        if ( $("#audio_player > #hap-wrapper").length > 0 && $.isFunction($.fn.hap) ) {
            player = $("#audio_player > #hap-wrapper").hap(settings);
        }

        $('#carousel-example-generic.carousel').carousel({
            interval: 15000, // 15 sec
            pause: "hover"
        });
    });
})(jQuery, window.wp);