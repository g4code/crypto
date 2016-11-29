
package 'python-software-properties'

bash 'apt_ppa' do
  code <<-EOH
    sudo add-apt-repository ppa:ondrej/php -y
    sudo apt-get update
    sudo apt-get install php7.1 -y --force-yes
    sudo apt-get install php7.1-dev -y --force-yes
    sudo apt-get install php7.1-mcrypt -y --force-yes
    sudo apt-get install php7.1-xml -y --force-yes
    EOH
end

package 'apache2' do
  action :remove
end

bash 'install_composer' do
  code <<-EOH
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
    EOH
  not_if { ::File.exists?("/usr/bin/composer") }
end