#Import all of the necessary libraries for the script to do its tasks
import os
import secrets
import getpass

#Check what action the user wants to perform
print("Welcome to the PCAPAnalyzer system installer")
print("You can choose to install the main [s]erver and frontend package, or install the [a]gent in a client.")
choice = input("Choose your task [s/a]: ")

#Check if the option was invalid
while choice not in ["s", "S", "a", "A"]:
    print("Option you specified is not a choice. You can choose to install the main [s]erver and frontend package, [u]pgrade the application from its last version to the current one, or install the [a]gent in a client.")
    choice = input("Choose your task [s/u/a]: ")

#Choosing the full server install
if choice in ["s", "S"]:
    print("Installing the full frontend and backend service!")
    print("Dependency install... ")
    #Install dependencies
    os.system("apt update")
    os.system("apt install sendmail mailutils python3-bcrypt tshark sed python3-psycopg2 php php-pgsql")
    import bcrypt
    import psycopg2
    print("Now we will do some data collection for your server install")
    #Get the machine ID for the server
    machineid = input("What machine id do you want this server to havee when data is inserted? it will show up as a textfield  in the database, for example 'server1'. A good choice is the machine hostname: ")
    #Check if the data exists
    while machineid == "":
        machineid = input("You need to input a valid machineID. What machine id do you want this server to havee when data is inserted? it will show up as a textfield  in the database, for example 'server1'. A good choice is the machine hostname: ")
    #Get the installation directory for the server
    basedir = input("Put in the root directory of this web install, ended with a slash. For example '/var/www/pcapanalyzer/' (It doesn't need to exist now, the script will create it): ")
    #Check if the data exists
    while basedir == "":
        basedir = input("Put in the root directory of this web install, ended with a slash. For example '/var/www/pcapanalyzer/': ")
    #Add a slash at the end in case it's not there on the string
    if basedir[-1] != "/":
        basedir = basedir + "/"
    #Get the database system host
    dbHost = input("Please write the database system host IP address where you will install PCAPAnalyzer: ")
    #Check if the data exists
    while dbHost == "":
        dbHost = input("Invalid value, please input a user to proceed. Please write the database host  IP address where you want to install PCAPAnalyzer: ")
    #Get the database system administrator account
    dbUser = input("Please write the database admin account with ability to create databases and users, as the requirements listed: ")
    #Check if the data exists
    while dbUser == "":
        dbUser = input("Invalid value, please input a user to proceed. Please write the database admin account with ability to create databases and users, as the requirements listed: ")
    #Get the database system administrator password
    dbPass = getpass.getpass(prompt="Please write the database admin password for the account you just entered (CAUTION: the password is not visible in this step!): ")
    #Get the web domain for the Apache2 VirtualHost
    domain = input("Please provide a domain for the apache VHost: ")
    #Check if the data exists
    while domain == "":
        domain = input("Invalid. Please provide a domain for the apache VHost: ")
    #Show system interfaces
    os.system("ip a")
    print("")
    #Provide the network interface to listen on
    interface = input("Please provide the interface you want your server to listen on from the above names: ")
    #Check if the data exists
    while interface == "":
        interface = input("Invalid. Please provide the interface you want your server to listen on from the above names: ")
    print("Relevant data stored! Proceeding to install...")
    print("Replacing relevant strings...")
    #Possible password characters string
    alphabet = "1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYXZ"
    #Generate packetstream user password
    packetstreampass = ""
    for i in range(64):
        packetstreampass = packetstreampass + secrets.choice(alphabet)
    #Generate pcapagent user password
    pcapagentpass = ""
    for i in range(64):
        pcapagentpass = pcapagentpass + secrets.choice(alphabet)
    #Generate queryrunner user password
    selectuserpass = ""
    for i in range(64):
        selectuserpass = selectuserpass + secrets.choice(alphabet)
    #Generate credchecking user password
    credcheckingpass = ""
    for i in range(64):
        credcheckingpass = credcheckingpass + secrets.choice(alphabet)
    #Replace the destination database host
    print("Replacing the destination database host")
    os.system("sed -i \"s/host=REPLACEONINSTALL/host=" + dbHost + "/g\" resources/server/backend/.my.cnf")
    #Replace the packetstream user password
    print("Replacing the packetstream user password")
    os.system("sed -i \"0,/password=REPLACEONINSTALL/s/password=REPLACEONINSTALL/password=" + packetstreampass + "/\" resources/server/backend/.my.cnf")
    #Replace the machine id for the server
    print("Replacing the server machine id")
    os.system("sed -i \"s/machineid=REPLACEONINSTALL/machineid=" + machineid + "/\" resources/server/backend/.my.cnf")
    #Replace the base directory
    print("Replacing the base directory")
    os.system("sed -i \"s|basedir=REPLACEONINSTALL|basedir=" + basedir + "backend/|\" resources/server/backend/.my.cnf")
    #Replace the listening interface
    print("Replacing the listening interface")
    os.system("sed -i \"s/interface=REPLACEONINSTALL/interface=" + interface + "/\" resources/server/backend/.my.cnf")
    #Replace the queryrunner user password
    print("Replacing the queryrunner user password")
    os.system("sed -i \"0,/selectpassword=REPLACEONINSTALL/s/selectpassword=REPLACEONINSTALL/selectpassword=" + selectuserpass + "/\" resources/server/backend/.my.cnf")
    #Replace the credchecking user password
    print("Replacing the credchecking user password")
    os.system("sed -i \"0,/password=REPLACEONINSTALL/s/password=REPLACEONINSTALL/password=" + credcheckingpass + "/\" resources/server/backend/.my.cnf")
    #Replace the pcapagent user password in SQL
    print("Replacing the SQL password for pcapagent")
    os.system("sed -i \"s/CREATE USER pcapagent LOGIN ENCRYPTED PASSWORD 'REPLACEONINSTALL';/CREATE USER pcapagent LOGIN ENCRYPTED PASSWORD '" + pcapagentpass + "';/\" resources/common/postgres.sql")
    #Replace the selectuser user password in SQL
    print("Replacing the SQL password for selectuser")
    os.system("sed -i \"s/CREATE USER selectuser LOGIN ENCRYPTED PASSWORD 'REPLACEONINSTALL';/CREATE USER selectuser LOGIN ENCRYPTED PASSWORD '" + selectuserpass + "';/\" resources/common/postgres.sql")
    #Replace the pcapanalyzer user password in SQL
    print("Replacing the SQL password for pcapanalyzer")
    os.system("sed -i \"s/CREATE USER pcapanalyzer LOGIN CREATEDB ENCRYPTED PASSWORD 'REPLACEONINSTALL';/CREATE USER pcapanalyzer LOGIN CREATEDB ENCRYPTED PASSWORD '" + packetstreampass + "';/\" resources/common/postgres.sql")
    #Replace the pcapuserchecker user password in SQL
    print("Replacing the SQL password for credchecking")
    os.system("sed -i \"s/CREATE USER pcapuserchecker LOGIN CREATEDB ENCRYPTED PASSWORD 'REPLACEONINSTALL';/CREATE USER pcapuserchecker LOGIN CREATEDB ENCRYPTED PASSWORD '" + credcheckingpass + "';/\" resources/common/postgres.sql")
    #Replace the system's document root
    print("Replacing apache documentroot")
    os.system("sed -i \'s|DocumentRoot \"REPLACEONINSTALL\"|DocumentRoot \"" + basedir + "frontend\"|' resources/common/pcapanalyzer.conf")
    #Replace the system's domain to listen on
    print("Replacing the domain on the virtualhost")
    os.system("sed -i  \"s/ServerName REPLACEONINSTALL/ServerName " + domain + "/\" resources/common/pcapanalyzer.conf")
    #Replace the system's directory for avoiding indexing
    print("Replacing the indexing directive for the frontend")
    os.system("sed -i  \"s|<Directory REPLACEONINSTALL>|<Directory " + basedir + "frontend>|\" resources/common/pcapanalyzer.conf")
    #Replace the backend's working directory
    print("Replacing the backend directory setting")
    os.system("sed -i \"s|WorkingDirectory=REPLACEONINSTALL|WorkingDirectory=" + basedir + "backend/packetStream|\" resources/common/packetstream.service")
    #Replace the IDS working directory
    print("Replacing the IDS service directory setting")
    os.system("sed -i \"s|WorkingDirectory=REPLACEONINSTALL|WorkingDirectory=" + basedir + "backend/intrusionDetection|\" resources/common/pcapanalyzer-probe.service")
    print("All data replaced! Creating database backend...")
    #Connect to database for first user setup
    connString = "host='" + dbHost + "' dbname='postgres' user='" + dbUser + "' password='" + dbPass + "'"
    conn = psycopg2.connect(connString)
    conn.set_session(autocommit=True)
    cursor = conn.cursor()
    #First base user setup
    print("Creating necessary users...")
    sqlpostgres = open("resources/common/postgres.sql", "r").read()
    cursor.execute(sqlpostgres)
    #Create first database for main data entry 
    cursor.execute("CREATE DATABASE packetstream;")
    cursor.close()
    conn.close()
    print("setting up main database...")
    #setup packetStream database tables
    connString = "host='" + dbHost + "' dbname='packetstream' user='pcapanalyzer' password='" + packetstreampass + "'"
    conn = psycopg2.connect(connString)
    conn.set_session(autocommit=True)
    cursor = conn.cursor()
    sqlpacketstream = open("resources/common/packetstream.sql", "r").read()
    cursor.execute(sqlpacketstream)
    cursor.close()
    conn.close()
    #Install the credentials database
    #
    connString = "host='" + dbHost + "' dbname='postgres' user='" + dbUser + "' password='" + dbPass + "'"
    conn = psycopg2.connect(connString)
    conn.set_session(autocommit=True)
    cursor = conn.cursor()
    #Create first database for main data entry 
    cursor.execute("CREATE DATABASE pcapanalyzer_creds;")
    cursor.execute("ALTER DATABASE pcapanalyzer_creds OWNER TO pcapuserchecker;")
    cursor.close()
    conn.close()
    print("setting up the credential checking database...")
    #setup credchecking database tables
    connString = "host='" + dbHost + "' dbname='pcapanalyzer_creds' user='pcapuserchecker' password='" + credcheckingpass + "'"
    conn = psycopg2.connect(connString)
    conn.set_session(autocommit=True)
    cursor = conn.cursor()
    sqlcredchecking = open("resources/common/pcapanalyzer_creds.sql", "r").read()
    cursor.execute(sqlcredchecking)
    cursor.close()
    conn.close()
    #Generate the web frontend user password
    print("Creating the default frontend user...")
    frontendpass = ""
    for i in range(12):
        frontendpass = frontendpass + secrets.choice(alphabet)
    frontendhash = bcrypt.hashpw(frontendpass.encode("utf-8"), bcrypt.gensalt(12))
    frontendhash = frontendhash.decode("ascii")
    #Insert admin user into the database
    connString = "host='" + dbHost + "' dbname='pcapanalyzer_creds' user='pcapuserchecker' password='" + credcheckingpass + "'"
    conn = psycopg2.connect(connString)
    conn.set_session(autocommit=True)
    cursor = conn.cursor()
    sqlinsertuser = "INSERT INTO users(username, password) VALUES ('admin', '" + frontendhash + "')"
    cursor.execute(sqlinsertuser)
    cursor.close()
    conn.close()
    #Move the files to target directory and do permission management
    print("Installed database backend! moving web panel and setting up permissions...")
    command = "mkdir -p " + basedir
    os.system(command)
    command = "mv resources/server/* " + basedir
    os.system(command)
    command = "chown root:www-data " + basedir
    os.system(command)
    command = "chmod 755 " + basedir
    os.system(command)
    command = "chown root:www-data -R " + basedir + "frontend/"
    os.system(command)
    command = "chmod 750 -R " + basedir + "frontend"
    os.system(command)
    command = "chmod 770 " + basedir + "frontend/queryrunner"
    os.system(command)
    command = "chown root:root " + basedir + "backend"
    os.system(command)
    command = "chmod 755 " + basedir + "backend"
    os.system(command)
    command = "chown root:www-data " + basedir + "backend/.my.cnf"
    os.system(command)
    command = "chmod 740 " + basedir + "backend/.my.cnf"
    os.system(command)
    command = "chown root:root -R " + basedir + "backend/packetStream"
    os.system(command)
    command = "chmod 700 -R " + basedir + "backend/packetStream"
    os.system(command)
    command = "chmod 755 " + basedir + "backend/packetStream"
    os.system(command)
    command = "chmod 755 " + basedir + "backend/packetStream/pgsql"
    os.system(command)
    command = "chmod 755 -R " + basedir + "backend/packetStream/pgsql/inputs"
    os.system(command)
    command = "chown www-data:www-data " + basedir + "backend/analysisGenerator"
    os.system(command)
    command = "chmod 755 " + basedir + "backend/analysisGenerator"
    os.system(command)
    command = "chown root:www-data -R " + basedir + "backend/analysisGenerator/*"
    os.system(command)
    command = "chmod 750 -R " + basedir + "backend/analysisGenerator/scripts"
    os.system(command)
    command = "chmod 755 " + basedir + "backend/analysisGenerator/files"
    os.system(command)
    command = "chmod 770 -R " + basedir + "backend/analysisGenerator/files/pcaps"
    os.system(command)
    command = "chmod 775 -R " + basedir + "backend/analysisGenerator/files/csvs"
    os.system(command)
    command = "chown root:root -R " + basedir + "backend/intrusionDetection/"
    os.system(command)
    command = "chmod 700 -R " + basedir + "backend/intrusionDetection/"
    os.system(command)
    print("Permissions have been set up! Now we will set up the apache2 virtualHost...")
    #Apache2 setup
    os.system("mv resources/common/pcapanalyzer.conf /etc/apache2/sites-available/")
    os.system("a2enmod headers")
    os.system("a2ensite pcapanalyzer")
    os.system("service apache2 reload")
    #Setup the service management files
    print("Apache has been set up. Configuring service files...")
    os.system("chown root:root resources/common/packetstream.service")
    os.system("chmod 644 resources/common/packetstream.service")
    os.system("mv resources/common/packetstream.service /etc/systemd/system/")
    os.system("chown root:root resources/common/pcapanalyzer-probe.service")
    os.system("chmod 644 resources/common/pcapanalyzer-probe.service")
    os.system("mv resources/common/pcapanalyzer-probe.service /etc/systemd/system/")
    print("The PCAPAnalyzer system has been set up! You can now access your web panel and enable logging on this server with 'service packetstream start' for the packetstream capture system, and the same for the pcapanalyzer-probe intrusion detection system. You can refer to documentation on this at https://ivanol55.github.io/pcapanalyzer-docs/.")
    print("The password for the agent user is '" + pcapagentpass + "' as generated in this script. MAKE SURE to write it down and save it for future agent installs, as it will not appear again. You will be asked this password when you want to install an agent instance on another machine.")
    print("The password for the frontend web login has been created with username 'admin' and password '" + frontendpass + "' as generated in this script. MAKE SURE to write it down, as it will not appear again. You can now login to the web frontend at your provided domain (if you set up the DNS entry for your case) and create a user and password pair of your liking.")
else:
    #Option choice for installing the agent on a machine
    print("Setting up the agent on this machine!")
    print("Dependency install... ")
    #Install the dependencies for the agent 
    os.system("apt update")
    os.system("apt install tshark sed python3-psycopg2")
    import psycopg2
    print("First, let's get some data for replacement...")
    #Get the database host where the postgres instance is installed
    host = input("input the network address or domain that hosts the PCAPAnalyzer server with the database: ")
    #Check if the data exists
    while host == "":
        host = input("You need to specify a host. Input the network address or domain that hosts the PCAPAnalyzer server with the database: ")
    #Get the passowrd for the PCAPAgent user generated on server install
    password = input("Please input the PCAPAgent password you got on the server install and got told to save: ")
    #Check if the data exists
    while password == "":
        password = input("You need to specify a host. Input the network address or domain that hosts the PCAPAnalyzer server with the database: ")
    #Set a machineid for the agent
    machineid =  input("input a unique machine id that will identify the logs from this machine. Use something recognizable, like the machine's hostname: ")
    #Check if the data exists
    while machineid  == "":
        machineid =  input("You need to specify a machine id. Input a unique machine id that will identify the logs from this machine. Use something recognizable, like the machine's hostname: ")
    #Set the install directory for the agent
    basedir = input("Specify the full address of the folder you want to store this agent in ended in a slash, for example '/opt/pcapagent/': ")
    #Check if the data exists
    while basedir == "":
        basedir = input("You need to choose a folder to put the agent in. Specify the full address of the folder you want to store this agent in ended in a slash, for example '/opt/pcapagent/': ")
    #Check if the last character is a /, add it if it's not present
    if basedir[-1] != "/":
        basedir = basedir + "/"
    #Show system interfaces
    os.system("ip a")
    #Select the interface you want the agent to listen on
    interface = input("from the listed above, please specify which one you want to monitor on this machine: ")
    print("All data collected! replacing relevant info...")
    #Data replacement
    print("Replacing the remote database host")
    os.system("sed -i \"s/host=REPLACEONINSTALL/host=" + host + "/\" resources/agent/.my.cnf")
    print("Replacing the agent login password")
    os.system("sed -i \"s/password=REPLACEONINSTALL/password=" + password + "/\" resources/agent/.my.cnf")
    print("Replacing the machine ID")
    os.system("sed -i \"s/machineid=REPLACEONINSTALL/machineid=" + machineid + "/\" resources/agent/.my.cnf")
    print("Replacing the agent directory")
    os.system("sed -i \"s|basedir=REPLACEONINSTALL|basedir=" + basedir + "agent/|\" resources/agent/.my.cnf")
    print("Replacing the listening interface")
    os.system("sed -i \"s/interface=REPLACEONINSTALL/interface=" + interface + "/\" resources/agent/.my.cnf")
    print("Replacing the service directory setting")
    os.system("sed -i \"s|WorkingDirectory=REPLACEONINSTALL|WorkingDirectory=" + basedir + "/agent/packetStream|\" resources/common/packetstream.service")
    #Move files into place
    print("Replacing done! Moving the files into place...")
    command = "mkdir -p " + basedir
    os.system(command)
    command = "mv resources/common/packetstream.service /etc/systemd/system/"
    os.system(command)
    command = "mv resources/agent/ " +  basedir
    os.system(command)
    command = "chmod 755 " +  basedir
    os.system(command)
    command = "chmod 755 " +  basedir + "agent/"
    os.system(command)
    command = "chmod 700 -R " +  basedir + "agent/packetStream"
    os.system(command)
    command = "chmod 755 " +  basedir + "agent/packetStream/pgsql/"
    os.system(command)
    command = "chmod 755 -R " +  basedir + "agent/packetStream/pcaps/outputs/"
    os.system(command)
    command = "chmod 755 -R " +  basedir + "agent/packetStream/csvs/inputs/"
    os.system(command)
    command = "chmod 755 -R " +  basedir + "agent/packetStream/csvs/outputs/"
    os.system(command)
    command = "chmod 755 -R " +  basedir + "agent/packetStream/pgsql/outputs/"
    os.system(command)
    print("The agent install is done! you can now enable the agent with 'service packetstream start' to begin sending data to the server.")
