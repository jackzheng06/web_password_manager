# Password Manager
密码管理器，练习AES和用户系统设计做的小项目。
## 工作流程
服务器不保存密码的解密密匙
#### 用户登录
* 用户输入用户名和密码
* 前端计算密码MD5散列两万次
* 发送用户名和MD5散列到服务器
* 服务器从数据库中读取用户信息
* 服务器计算MD5散列 + 密码盐的SHA512散列十万次
* 如果输入的密码和数据库中的密码匹配，创建新的验证令牌

#### 创建新的密码（用户已经登录）
* 用户输入密码信息
* 使用用户登录密码的MD5散列进行AES加密
* 发送至服务器
* 服务器保存数据

#### 获取密码（用户已经登录）
* 服务器返回加密的密码
* 使用用户登录密码的MD5散列进行AES解密

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
