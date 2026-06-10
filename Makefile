# SPDX-License-Identifier: AGPL-3.0-or-later
# Build target for packaging the app for the Nextcloud App Store.
# Produces build/artifacts/<app_name>.tar.gz

app_name=$(notdir $(CURDIR))
build_dir=$(CURDIR)/build
artifacts_dir=$(build_dir)/artifacts
appstore_dir=$(artifacts_dir)/$(app_name)
package=$(artifacts_dir)/$(app_name).tar.gz

.PHONY: all
all: appstore

.PHONY: clean
clean:
	rm -rf $(build_dir)

.PHONY: appstore
appstore: clean
	mkdir -p $(appstore_dir)
	rsync -a --exclude='.git' --exclude='.github' --exclude='.gitignore' --exclude='.nvmrc' --exclude='.eslintrc.cjs' --exclude='.php-cs-fixer.dist.php' --exclude='stylelint.config.cjs' --exclude='tsconfig.json' --exclude='vite.config.ts' --exclude='psalm.xml' --exclude='rector.php' --exclude='package.json' --exclude='package-lock.json' --exclude='openapi.json' --exclude='src' --exclude='tests' --exclude='vendor-bin' --exclude='node_modules' --exclude='build' --exclude='Makefile' --exclude='krankerl.toml' --exclude='CLAUDE.md' ./ $(appstore_dir)/
	tar -czf $(package) -C $(artifacts_dir) $(app_name)