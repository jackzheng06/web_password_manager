function EncryptionFramework(){

   /**
    * Encrypt a string with AES CCM Mode
    * @param {String} data
    * @param {String} password
    * @return {String} encrypted string
    */
    this.encryptAes = function(data, password){
        return sjcl.encrypt(password, data, {mode : "ccm"});
    }

   /**
    * Decrypt a string with AES
    * @param {String} data
    * @param {String} password
    * @return {String} decrypted string
    */
    this.decryptAes = function(data, password){
        return sjcl.decrypt(password, data);
    }
}