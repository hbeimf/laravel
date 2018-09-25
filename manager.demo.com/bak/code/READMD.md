安装依赖库

```
	composer install
```

nginx.conf

```
server {
    listen           80;
    server_name      la.demo.com;
    root             "/cpsite/client_api/public";
    index            index.php index.html index.htm;
    try_files        $uri $uri/ @rewrite;

    location @rewrite {
        rewrite ^/(.*)$ /index.php?_url=/$1;
    }

    location ~ \.php {
        fastcgi_pass                  127.0.0.1:9000;
        fastcgi_index                 /index.php;
        fastcgi_split_path_info       ^(.+\.php)(/.+)$;
        fastcgi_param PATH_INFO       $fastcgi_path_info;
        fastcgi_param PATH_TRANSLATED $document_root$fastcgi_path_info;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include                       fastcgi_params;
    }

    location ~* ^/(css|img|js|flv|swf|download)/(.+)$ {
    }

    location ~ /\.ht {
         deny all;
    }
	error_log /usr/local/nginx/logs/la_demo_com_error.log;
}	

```

mysql:

```
	oauth2.0生成的表在soft-docs 软件文档目录下的laravel.sql
```

目前配置文件采用的默认 .env


```
    hosts 配置：
    
        192.168.1.145 api.22dutech.com

        接口文档地址：

        http://api.22dutech.com/api/documentation

```

