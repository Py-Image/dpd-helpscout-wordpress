#!/bin/bash

echo "Updating system ..."
sudo apt-get update

echo "Creating folder to place Selenium in ..."
sudo mkdir ~/selenium
cd ~/selenium

echo "Installing Selenium and headless Java runtime ..."
sudo wget http://selenium-release.storage.googleapis.com/3.12/selenium-server-standalone-3.12.0.jar
cd ../
sudo apt-get install openjdk-8-jre-headless -y

echo "Installing Chrome and Chrome Driver..."

CHROME_DRIVER_VERSION=`curl -sS chromedriver.storage.googleapis.com/LATEST_RELEASE`

sudo curl -sS -o - https://dl-ssl.google.com/linux/linux_signing_key.pub | apt-key add
sudo echo "deb http://dl.google.com/linux/chrome/deb/ stable main" >> /etc/apt/sources.list.d/google-chrome.list
sudo apt-get -y update
sudo apt-get -y install google-chrome-stable

# Install ChromeDriver.
wget -N http://chromedriver.storage.googleapis.com/$CHROME_DRIVER_VERSION/chromedriver_linux64.zip -P ~/
unzip ~/chromedriver_linux64.zip -d ~/
rm ~/chromedriver_linux64.zip
sudo mv -f ~/chromedriver /usr/local/bin/chromedriver
sudo chown root:root /usr/local/bin/chromedriver
sudo chmod 0755 /usr/local/bin/chromedriver

# Install headless GUI for firefox.  'Xvfb is a display server that performs graphical operations in memory'
#
echo "Installing XVFB (headless GUI for Firefox) ..."
sudo apt-get install xvfb -y