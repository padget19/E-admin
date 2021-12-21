<div align="center">
    <img src="http://rocky-git.gitee.io/e-admin-sites/static/logo.50c3504.png" height="80"> 
</div>
<br>
<p align=""><code>E-admin</code>是一个基于<a href="https://www.laravel-admin.org/" target="_blank">Element Plus</a>开发而成后台系统构建工具，无需关注页面模板JavaScript，只用php代码即可快速构建出一个功能完善的后台系统。。</p>


- [中文文档](http://rocky-git.gitee.io/e-admin-sites/#/zh-CN/component/installation)
- [Demo / 在线演示](https://eadmin.togy.com.cn)
- [github](https://github.com/rocky-git/E-admin)
- [gitee(码云)](https://gitee.com/rocky-git/eadmin)



![](http://rocky-git.gitee.io/e-admin-sites/static/theme-index-bg1.fed340c.png)




### 功能特性

- [x] 后台组件面向对象编程，组件化开发
- [x] 自定义vue页面组件，无需重新编译打包
- [x] 注解权限BAC的权限系统,无限极菜单
- [x] 页面组件url复用
- [x] 数据表格构建工具，内置丰富的表格常用功能（如拖拽排序、数据导出、搜索、快捷创建、批量操作等）
- [x] 数据表单构建工具，分步表单构建工具，内置丰富的表单类型，表单watch，表单互动
- [x] 数据详情页构建工具
- [x] 支持自定义图表
- [x] 支持本地和七牛云、oss文件上传


### 环境
 - PHP >= 7.1.0
 - ThinkPhP 6.0
 - Fileinfo PHP Extension

### 安装


首先需要安装`ThinkPhP`框架，如已安装可以跳过此步骤。如果您是第一次使用`ThinkPhP`，请务必先阅读文档 [安装 《ThinkPhP中文文档》](https://www.kancloud.cn/manual/thinkphp6_0/1037481) ！
```bash
composer create-project topthink/think tp
# 或
composer create-project topthink/think=6.0.x-dev tp
```

安装完`ThinkPhP`之后需要修改`.env`文件，设置数据库连接设置正确

```dotenv
[DATABASE]
TYPE = mysql
DRIVER = mysql
HOSTNAME = 127.0.0.1
DATABASE = tp6
USERNAME = root
PASSWORD = root
HOSTPORT = 3306
CHARSET = utf8mb4
```

安装`e-admin`


```
cd {项目名称}

composer require rockys/e-admin
```

然后运行下面的命令来安装：

```
php think eadmin:install
```

强制重新安装：

```
php think admin:install -f
```

phpstudy的apache环境需要配置验证token，默认关闭
```dotenv
// 路径举例：D:\phpstudy_pro\Extensions\Apache2.4.39\conf
// 在httpd.conf搜索 IfModule dir_module 新增SefEnvIf这一行

<IfModule dir_module>
    DirectoryIndex index.php index.html
    SetEnvIf Authorization .+ HTTP_AUTHORIZATION=$0
</IfModule>
```

启动服务后，在浏览器打开 `http://localhost/admin`，使用用户名 `admin` 和密码 `admin`登陆。




### 鸣谢
`E-admin` 基于以下组件:

+ [ThinkPhP](http://www.thinkphp.cn/)
+ [Element Plus](https://element-plus.gitee.io/)
+ [Ant Design Vue](https://2x.antdv.com/)
+ [Vue3](https://cn.vuejs.org/)
+ [font-awesome](http://fontawesome.io)
+ [echarts](https://echarts.apache.org/)
+ [simple-uploader.js](https://github.com/simple-uploader/Uploader)
+ [tinymce](https://www.tiny.cloud/)
+ [sortablejs](http://www.sortablejs.com/)
+ [amap map](https://www.amap.com/)


### License
------------
`e-admin` is licensed under [The MIT License (MIT)](LICENSE).
