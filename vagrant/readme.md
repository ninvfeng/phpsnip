# 启用虚拟机环境步骤
1. 安装vitrualbox 局域网下载地址: http://lan.ninvfeng.com/vagrant/VirtualBox-5.2.8-121009-Win.exe
2. 安装vagrant    局域网下载地址: http://lan.ninvfeng.com/vagrant/vagrant_2.0.3_x86_64.msi
3. 下载 box 文件  局域网下载地址: http://lan.ninvfeng.com/vagrant/bgframework.box

## window7系统需要升级powershell到4.0以上
   * 先下载 .net4.5 并安装 http://lan.ninvfeng.com/vagrant/dotNetFx45_Full_setup.exe
   * 然后再下载 powershell4 并安装 http://lan.ninvfeng.com/vagrant/Windows6.1-KB2819745-x64-MultiPkg.msu

4. 导入box文件,在box文件所在目录执行 (仅需执行一次)
vagrant box add bgframework bgframework.box

5. 启动虚拟机 在 代码根目录/vagrant 执行 (每次开机执行)
vagrant up

6. hosts 文件中添加 192.168.33.10 local.bgframework.com

## everythink is ok! 浏览器打开local.bgframework.com即可 虚拟机内已预装好nginx php7.2 mysql redis swoole等环境,直接连接即可

## 其他:
* 虚拟机IP: 192.168.33.10
* 账号: root/root
* mysql: root/mysqlP@ssw0rd
* 登录虚拟机: vagrant ssh
* 关闭虚拟机: vagrant halt
* 重启虚拟机: vagrant reload