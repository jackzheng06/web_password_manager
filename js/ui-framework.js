function UiFramework(){

    // Page configurations
    this.pages = {
        "":"page/main.html",
        "#":"page/main.html",
        "#main":"page/main.html",
        "#signup":"page/signup.html",
        "#login":"page/login.html",
        "#auth":"page/auth.html"
    }

    // First, bind onhashchange event
    var self = this;
    $(window).on('hashchange', function() {
        self.window_hashchange();
    });

   /**
    * window.hashchange event handler
    */
    this.window_hashchange = function(){
        if(self.pages[window.location.hash] != undefined){
            self.load_page($("#page-container"), self.pages[window.location.hash], function(status){
                console.log(status);
            });
        } else {
            window.location.hash = "#main";
        }
    }

   /**
    * Load a new page into dom object
    * @param {Dom} target_dom 
    * @param {String} page_path
    * @param {Function} callback
    */
    this.load_page = function(target_dom, page_path, callback){
        var target_dom = target_dom;
        var callback = callback;
        this.show_loading();
        var self = this;
        $.ajax({
            url:page_path,
            type:"GET",
            dataType:"text",
            success:function(data){
                $(target_dom).html(data);
                console.log(target_dom);
                callback(true);
                self.hide_loading();
            },
            error:function(e){
                callback(false);
                self.hide_loading();
            }
        });
    }

   /**
    * Display loading indicator
    */
    this.show_loading = function(){
        $("#loading-indicator").fadeIn();
    }

   /**
    * Hide loading indicator
    */
    this.hide_loading = function(){
        $("#loading-indicator").fadeOut();
    }

   /**
    * Display a new notification to user
    * noti_type can be "Alert", "Error", "Success" or "Message"
    * @param {String} noti_type
    * @param {String} message 
    * @param {Integer} timeout
    */
    this.new_notification = function(noti_type, message, timeout){
        $noti = $('<div></div>');
        $noti.addClass("notification");
        $noti.addClass(noti_type);
        $noti.html(message);
        $("body").append($noti);
        $noti.animate({'right':20});
        var $noti = $noti;
        setTimeout(function(){
            $noti.fadeOut();
        },timeout);
    }

}