function EncryptionFramework(){

   /**
    * Encrypt a string with AES CCM Mode
    * @param {String} data
    * @param {String} password
    * @return {String} encrypted string
    */
    this.encryptAes = function(data, password){
        try{
            encrypted = sjcl.encrypt(password, data, {mode : "ccm"});
        } catch (e){
            encrypted = false;
        }

        return encrypted;
    }

   /**
    * Decrypt a string with AES
    * @param {String} data
    * @param {String} password
    * @return {String} decrypted string
    */
    this.decryptAes = function(data, password){
        try{
            decrypted = sjcl.decrypt(password, data);
        } catch(e){
            decrypted = false;
        }
        return decrypted;
    }

    /**
     * Encrypt a string with default encryption mode
     * @param {String} data
     * @param {String} password
     * @return {String} encrypted string, in json format
     */
    this.encrypt = function(data, password){
        return this.encryptAes(data, password);
    }
}