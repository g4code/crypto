TITLE = [crypto]

unit-tests:
	@/bin/echo -e "${TITLE} unit test suite started..." \
	&& ./vendor/bin/phpunit -c tests/unit/phpunit.xml --coverage-html tests/unit/coverage

integration-tests:
	@/bin/echo -e "${TITLE} starting virtual machine ..." \
	&& vagrant up \
	&& /bin/echo -e "${TITLE} importing clear database ..." \
	&& vagrant provision --provision-with test \
	&& /bin/echo -e "${TITLE} starting integration test suite ..." \
	&& ./vendor/bin/phpunit -c tests/integration/phpunit.xml --coverage-html tests/integration/coverage \
	&& /bin/echo -e "${TITLE} stopping virtual machine ..." \
	&& vagrant halt

.PHONY: unit-tests integration-testsp