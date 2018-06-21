#!/bin/bash

# Following the guide found at this page
# http://programmingarehard.com/2014/03/17/behat-and-selenium-in-vagrant.html

echo "Updating system ..."
sudo apt-get update


# Create folder to place selenium in
#
echo "Creating folder to place selenium in ..."
sudo mkdir ~/selenium
cd ~/selenium


# Get Selenium and install headless Java runtime
#
echo "Installing Selenium and headless Java runtime ..."
sudo wget http://selenium-release.storage.googleapis.com/3.12/selenium-server-standalone-3.12.0.jar
cd ../
sudo apt-get install openjdk-8-jre-headless -y


# Install Firefox
#
echo "Installing Firefox ..."
sudo apt-get install firefox -y


# Install headless GUI for firefox.  'Xvfb is a display server that performs graphical operations in memory'
#
echo "Installing XVFB (headless GUI for Firefox) ..."
sudo apt-get install xvfb -y