# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|

    config.vm.box = "ubuntu/xenial64"
    config.vm.network "private_network", ip: "192.168.33.13"
    config.vm.hostname = "snovio.test"
    config.ssh.forward_agent = true
    config.ssh.insert_key = false

    config.vm.provider "virtualbox" do |v|
        v.memory = 2048
        v.cpus = 2
    end

    config.vm.synced_folder ".", "/var/www", :nfs => { :mount_options => ["dmode=777","fmode=777"] }

    config.vm.provision "shell", path: "snovio.sh"
end
