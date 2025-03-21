rem # Hubleto Business Application Hub
rem # PHP-based opensource CRM and ERP

rem       ###         
rem      ###        ##
rem     #####      ###
rem    ###  ####  ### 
rem   ###      #####  
rem   ##        ###   
rem            ###    

rem This script creates an empty Hubleto app and adds a sample model.

php hubleto init # init the project
php hubleto app create "HubletoApp\Custom\HelloWorldApp"
php hubleto app install "HubletoApp\Custom\HelloWorldApp"
php hubleto create model "HubletoApp\Custom\HelloWorldApp" "TodoItem"

