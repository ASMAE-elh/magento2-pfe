.PHONY: all

SHELL := /bin/zsh
HOME_DIR := $(HOME)
USER_VOLUMES := .ssh .composer

all: add-url-to-hosts

add-url-to-hosts:
	sudo -- sh -c "echo '127.0.0.1 chaymae.magento' >> /etc/hosts"