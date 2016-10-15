# Password Manager
密码管理器，练习AES和用户系统设计做的小项目。有时候自己也会用一用。

## 已知问题
* 在用户验证阶段服务器有可能获取解密密匙
* MD5可能不够安全

## 安装
使用以下命令设置数据库
```sql
CREATE TABLE passwords
(
    id INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
    pwd_name VARCHAR(256),
    uid INT(11),
    encrypted_data LONGTEXT,
    description LONGTEXT
);
CREATE TABLE tokens
(
    id INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
    uid INT(11),
    token_body LONGTEXT,
    valid_until INT(11)
);
CREATE TABLE users
(
    id INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
    username VARCHAR(128),
    password LONGTEXT,
    salt VARCHAR(512)
);
```
在`config.php`中输入数据库验证信息。

完成！
