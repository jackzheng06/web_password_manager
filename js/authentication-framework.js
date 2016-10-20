function AuthenticationFramework(){
    this.authUname = null;
    this.authToken = null;
    this.aesKey = null;
    this.pwdListTemp = null;

   /**
    * Initialize authentication framework
    * Please call this upon page load finishes
    */
    this.initialize = function(){
        this.authUname = this.get_cookie("wpm_uname");
        this.authToken = this.get_cookie("wpm_token");
        this.aesKey = this.get_cookie("wpm_key");
    }

   /**
    * Revoke current authentication info
    */
    this.revoke_token = function(){
        this.set_cookie("wpm_uname", "", 1);
        this.set_cookie("wpm_token", "", 1);
        this.set_cookie("wpm_key", "", 1);
    }

   /**
    * Reload current authentication status
    * Callback user info object if authentication is still valid, otherwise false.
    * @param {Function} callback
    */
    this.reload_auth_status = function(callback){
        var callback = callback;
        $.ajax({
            url:"api/",
            type:"POST",
            data:{
                action:"check_token",
                uname:this.authUname,
                token:this.authToken
            },
            dataType:"json",
            success:function(data){
                if(data.status == "success"){
                    callback(data.message);
                } else {
                    callback(false);
                }
            },
            error:function(e){
                callback(false);
            }
        })
    }

   /**
    * Delete a password by its ID 
    * Callback True if password is deleted, False otherwise.
    * @param {Integer} id
    * @param {Function} callback
    */
    this.delete_password = function(id, callback){
        var self = this;
        var callback = callback;
        $.ajax({
            url:"api/",
            type:"POST",
            data:{
                action:"delete_password",
                uname:this.authUname,
                token:this.authToken,
                id:id
            },
            dataType:"json",
            success:function(data){
                if(data.status == "success"){
                    callback(true);
                } else {
                    callback(false);
                }
            },
            error:function(e){
                console.log("Internal Error, delete_password \n" + e);
                callback(false);
            }
        });
    }

   /**
    * Get all passwords associated with current auth information
    * Callback an array of passwords, False if not authenticated.
    * Upon success, this will also set pwdListTemp property of this object.
    * @param {Function} callback
    */
    this.get_all_password = function(callback){
        var self = this;
        var callback = callback;
        $.ajax({
            url:"api/",
            type:"POST",
            data:{
                action:"get_all_password",
                uname:this.authUname,
                token:this.authToken
            },
            dataType:"json",
            success:function(data){
                if(data.status == "success"){
                    self.pwdListTemp = data.message;
                    callback(data.message);
                } else {
                    callback(false);
                }
            },
            error:function(e){
                console.log(e);
                callback(false);
            }
        })
    }

   /**
    * Update a password by its ID
    * Callback true if successful, false otherwise
    * @param {Integer} id
    * @param {String} name
    * @param {String} password
    * @param {String} description
    * @param {Function} callback
    */
    this.update_password = function(id, name, password, description, callback){
        var self = this;
        var callback = callback;
        $.ajax({
            url:"api/",
            type:"POST",
            data:{
                action:"update_password",
                uname:this.authUname,
                token:this.authToken,
                id:id,
                name:name,
                password:password,
                description:description
            },
            dataType:"json",
            success:function(data){
                if(data.status == "success"){
                    callback(true);
                } else {
                    callback(false);
                }
            },
            error:function(e){
                console.log("Internal Error, update_password \n" + e);
                callback(false);
            }
        })
    }

   /**
    * Request a new login token from server, callback true or false,
    * If authentication is successful, this will overwrite the token.
    * @param {String} username
    * @param {String} password, unhashed format
    * @param {Function} callback
    */
    this.request_token = function(username, password, callback){
        var callback = callback;
        var self = this;
        // First, hash the password
        password = this.hash_password(password);
        $.ajax({
            url:"api/",
            type:"POST",
            data:{
                action:"create_token",
                username:username,
                password:password
            },
            dataType:"json",
            success:function(data){
                if(data.status == "success"){
                    self.authToken = data.message;
                    self.authUname = MD5(username);
                    self.aesKey = self.hash_password(password);
                    self.set_cookie("wpm_uname", MD5(username), 1);
                    self.set_cookie("wpm_token", data.message, 1);
                    self.set_cookie("wpm_key", self.aesKey, 1);
                    callback(true);
                } else {
                    callback(false);
                }
            },
            error:function(e){
                console.log("Internal Error, request_token");
                console.log(e);
                callback(false);
            }
        });
    }

   /**
    * Set a new cookie, or overwrite a cookie
    * @param {String} cookie_name
    * @param {String} cookie_value
    * @param {String} expire_days
    */
    this.set_cookie = function(cookie_name, cookie_value, expire_days){
        var date = new Date();
        date.setTime(date.getTime() + (expire_days*24*60*60*1000));
        var expires = "expires="+ date.toUTCString();
        document.cookie = cookie_name + "=" + cookie_value + "; " + expires;
    }

   /**
    * Get a cookie 
    * @param {String} cookie_name
    * @return {String} cookie value
    */
    this.get_cookie = function(cookie_name){
        // This block of code sucks, I'm not going to bother commenting
        // From W3Schools
        var name = cookie_name + "=";
        var ca = document.cookie.split(';');
        for(var i = 0; i <ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length,c.length);
            }
        }
        return "";
    }

   /**
    * Hash a password without using salt
    * Used for password transmission between client and server
    * @param {String} password
    * @return {String} hashed password
    */
    this.hash_password = function(password){
        for(i = 0; i < 20000; i++){
            password = MD5(password);
        }
        return password;
    }
}