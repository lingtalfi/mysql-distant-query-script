Mysql Distant Query Script
=================================
2015-10-02

    
What is it?
------------------

Mysql Distant Query Script is a technique that one can use to query a distant database from 
the local machine's terminal and via ssh.

It's a technique for php websites only.
    
    
    
        
What is it useful for?
---------------------------

In my case, I need to do some moderator stuff on distant machine: things like activating a pending item on a dynamic website,
or see the last 10 pending items, change status=0 to status=1, ...
 
Rather than doing it via a web based interface, I do it via ssh (with this script), and so I feel more secure.



    
Features
------------

The technique I describe here is relatively secure: your database passwords won't be caught (unless your server is already doomed of course).

                
                
    
How to use (installation guide)
-----------------------------------

    So you have three php websites A, B and C on a distant server, and you want to query their database from your local machine.
    
    We need to do a little bit of setup here.
    You have to repeat the following recipe for each of your application.
    
    
    
### Recipe for web application A on distant server
    
    
    
    
    1. Go to your web application, and create the pawn.
    
    The pawn is the script that our local machine communicates with when we execute mysql queries (from our local terminal).
    The code of the pawn is [here]( TODO: pawn ).
    
    The pawn shouldn't be directly accessible by the webserver (don't put it in your www folder), and should not be executable
    by users other than you.
    This is an example structure:
    
    - /path/to/A/ 
    ----- app/
    --------- init.php
    --------- www/
    ------------- index.php
    ------------- ... all your website stuff
    ----- utils/
    --------- mydb_pawn.php   ( chmod 700 )
    
    
    
    2. Next, you need to configure the pawn so that it works with your application
    
    Basically, the pawn needs to know your mysql credentials, and it takes them from your application.
    
    In the example pawn, I assume that the init.php file contains the mysql credentials as php constants (MYSQL_DBNAME, PDOCONF_USER, PDOCONF_PASS).
    Open the pawn and replace those values by yours (you might want to remove/replace the require_once too):
    
        require_once __DIR__ . "/../app/init.php";
        $dbName = MYSQL_DBNAME;
        $user = PDOCONF_USER;
        $pass = PDOCONF_PASS;        
    
    
    3. Now that the pawn is deployed and configured on the distant server, we can use it.
     
        This is the basic command I use:
    
        ssh komin 'php -f /path/to/A/utils/mydb_pawn.php "select id, committer_id, the_name, publish_date, active from ideas limit 0, 5;"'
        
        Your might be more verbose:
        
        ssh -P 2424 myuser@12.34.56.78 'php -f /path/to/A/utils/mydb_pawn.php "select id, committer_id, the_name, publish_date, active from ideas limit 0, 5;"'
        
        The only thing that changes is the way you call ssh.
        In my case, to make things less verbose, I created a ~/.ssh/config file on my local machine that contains this:
        
        Host komin
            HostName 12.34.56.78
            User myuser
            IdentityFile /home/myuser/.ssh/id_dsa_komin    
            IdentitiesOnly yes
            Port 2424
            
            
        Note: you can create as much hosts as you need.            
        
        
        
    
    Alternatively, create a tmp.txt file with the following content inside:
    
        php -f /home/ling/websites/idees-de-sketch/util/sketch_sql.php "select id, committer_id, the_name, publish_date, active  from ideas limit 0, 5;"

    Then from your local terminal, type:
        
        ssh komin 'bash -s' < tmp.txt
            
    If you have long lines: you can break them with backslashes:            
        
        tmp.txt:
        php -f /home/ling/websites/idees-de-sketch/util/sketch_sql.php "select id, \
        committer_id, the_name, publish_date, active  from ideas limit 0, 5;"                    
            
            
            
            
Notes
------------

So that script is the first step of an automated workflow.
For now nothing is automated, but I wanted it raw, because implementations of automation can diverge,
but this technique should remain the same.

Once you have this basic technique in place, (and if you like it), it might be a good time to 
create your own automated systems.











            