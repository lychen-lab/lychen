# Contributing

Thanks for your interest in contributing! 💪  
This repository uses [Moonrepo](https://moonrepo.dev).  
Please take a moment to read the guidelines below before submitting your contribution.

## Install Proto

In order to install all you need you have to install [Proto](https://moonrepo.dev/proto)
, Moonrepo’s version manager, to ensure consistent toolchains across all environments. Follow the instruction at this link https://moonrepo.dev/docs/proto/install

Don't forget to setup your $PATH

```bash
export PROTO_HOME="$HOME/.proto"
export PATH="$PROTO_HOME/shims:$PROTO_HOME/bin:$PATH"
```

Then restart your terminal and verify the installation with:

```bash
proto --version
```

Proto automatically installs and manages the correct versions of node, yarn, moon, npm and other tools defined in .prototools. This ensures that everyone in the project uses the same setup, reducing build inconsistencies and environment issues.

## 🧩 Project setup

### 1. Fork and clone

Fork this repository and clone it locally:

```bash
git clone https://github.com/<your-username>/<repo-name>.git
cd <repo-name>
```

### 2. Install tools

Run

```bash
proto install
```

## 🌿 Branch and commit conventions

Branch naming

Use clear, descriptive branch names:

```cpp
<issue-id>-<short-description>
<scope>/<short-description>
```

## Working on project

Run

```bash
moon run <project-id>:dev
```
