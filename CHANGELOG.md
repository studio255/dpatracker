# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.3] - 2026-07-12

### Fixed

- App icon was invisible in the Nextcloud header (app.svg/app-dark.svg were swapped)
- PDF export print button script no longer deleted by frontend builds
- ESLint configuration now supports TypeScript in Vue single-file components
- OpenAPI CI check no longer fails on generated composer lock files

### Changed

- Updated dependencies: vue 3.5.39, @nextcloud/vite-config 2.5.4, @vue/tsconfig 0.9.1
- Added @nextcloud/auth, @nextcloud/l10n and @nextcloud/router as direct dependencies

## [1.0.2] - 2026-06-28

### Added

- First release
