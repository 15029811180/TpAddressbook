���ݿ⽨����䣺

 CREATE DATABASE txl;
�û���:
 CREATE TABLE user(
 id INT AUTO_INCREMENT PRIMARY KEY,
 username VARCHAR(18) NOT NULL,
 pwd CHAR(32) NOT NULL)ENGINE=MYISAM DEFAULT CHARSET=UTF8;
���ݱ�:
CREATE TABLE user_info(
 id INT AUTO_INCREMENT PRIMARY KEY,
 username VARCHAR(18) NOT NULL,
 sex TINYINT UNSIGNED NOT NULL DEFAULT 0,
 addtime INT UNSIGNED NOT NULL,
 phone CHAR(11) NOT NULL,
 address VARCHAR(255) NOT NULL)ENGINE=MYISAM DEFAULT CHARSET=UTF8;


�����û�������Ȩ
GRANT Ȩ�� ON ���ݿ�.[���ݱ�] TO �û���@��¼������ַ IDENTIFIED BY �����롯
���磺����һ��wangyun�û� ������jkxy��gz���в�ѯȨ��
GRANT SELECT on jkxy.gz TO wangyun@localhost IDENTIFIED BY ��123��

CREATE TABLE user_info(
 id INT AUTO_INCREMENT PRIMARY KEY,
 username VARCHAR(18) NOT NULL,
 sex TINYINT UNSIGNED NOT NULL DEFAULT 0,
 age TINYINT UNSIGNED NOT NULL,
 phone CHAR(11) NOT NULL,
 address VARCHAR(255) NOT NULL,
 addtime INT UNSIGNED NOT NULL)ENGINE=MYISAM DEFAULT CHARSET=UTF8;
GRANT SELECT,INSERT,UPDATE,DELETE ON *.*  TO �û���@��¼���� IDENTIFIED BY �����롯
�����û� Ҫ������еĿ��Լ����еı�������ɾ���ġ���Ȩ��
*.* ��ʾ ���п��е����б�