Vagrant.configure(2) do |config|
  config.vm.provider "virtualbox" do |v|
    v.memory = 1024
    v.cpus = 2
  end
  config.ssh.insert_key = false
  config.vm.box = "scotch/box"
  config.vm.network "private_network", ip: "192.168.33.10"
  config.vm.hostname = "scotchbox"
  config.vm.synced_folder ".", "/var/www/ipam", :mount_options => ["dmode=777", "fmode=666"]
  config.vm.provision "shell", path: "vagrant/setup.sh", keep_color: true
end


