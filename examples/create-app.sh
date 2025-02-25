

#!/bin/bash

# This script creates and empty Hubleto app and adds a sample model.

php hubleto init # init the project
php hubleto app create "HubletoApp\Custom\HelloWorldApp"
php hubleto app install "HubletoApp\Custom\HelloWorldApp"
php hubleto create model "HubletoApp\Custom\HelloWorldApp" "TodoItem"

