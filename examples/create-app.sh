
#!/bin/bash

# Hubleto Business Application Hub
# PHP-based opensource CRM and ERP

#       ###         
#      ###        ##
#     #####      ###
#    ###  ####  ### 
#   ###      #####  
#   ##        ###   
#            ###    

# This script creates an empty Hubleto app and adds a sample model.

php hubleto init # init the project
php hubleto app create "HubletoApp\Custom\HelloWorldApp"
php hubleto app install "HubletoApp\Custom\HelloWorldApp"
php hubleto create model "HubletoApp\Custom\HelloWorldApp" "TodoItem"

