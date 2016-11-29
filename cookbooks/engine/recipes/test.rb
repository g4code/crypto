
bash 'integration_tests' do
  code <<-EOH
    cd /vagrant_data
    ./vendor/bin/phpunit -c tests/integration/phpunit.xml --coverage-html tests/integration/coverage
    EOH
end