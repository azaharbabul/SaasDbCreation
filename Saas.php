<?php

/******************************
 * FUNCTION: DATABASE CONNECT *
 ******************************/
function db_open($host,$port,$user,$pwd,$db)
{
        // Connect to the database
        try
        {

              $db = new PDO("mysql:dbname=".$db.";host=".$host.";port=".$port,$user,$pwd, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
				//echo 'Connected successfully root_saas db';
                return $db;
        }
        catch (PDOException $e)
        {
                printf("A fatal error has occurred.  Please contact support.");
                //die("Database Connection Failed: " . $e->getMessage());
        }

        return null;
}

/*********************************
 * FUNCTION: DATABASE DISCONNECT *
 *********************************/
function db_close($db)
{
        // Close the DB connection
        $db = null;
}
/*==========================================*/
/*********************************
 * FUNCTION:Domain DATABASE CONNECT *
 *********************************/

function domain_db_open($domainhost,$domainuser,$domainpwd)
{
    $link = mysql_connect($domainhost, $domainuser, $domainpwd);
    if (!$link)
    {
        die("Could not connect: " . mysql_error()."\n");
    }
    else
    {
        echo "Connected successfully\n";
        return $link;

    }
}
/*********************************
 * FUNCTION:Domain DATABASE DISCONNECT *
 *********************************/
function domain_db_close($link)
{
    mysql_close($link);
    $link=null;
}
/*==========================================*/

/*********************************
 * FUNCTION:Domain DATABASE CREATE*
 *********************************/

function create_domain_db($domain_db)
{
    //$domain_db=trim($domain.'_riskdb');
    $host='localhost';
    $user='root';
    $pwd='mysql';
    $domain_dbconnect=domain_db_open($host,$user,$pwd);
    $create_domaindbsql = "CREATE Database ".$domain_db;
   // echo $sql;
    $db_create = mysql_query( $create_domaindbsql, $domain_dbconnect );
    if(! $db_create )
    {
        die("Could not create database: " . mysql_error()."\n");
        domain_db_close($domain_dbconnect);
        return false;
    }
    else
    {
        echo "Database " .$domain_db. " created successfully\n";
        domain_db_close($domain_dbconnect);
        return true;
    }

}
/*********************************
 * FUNCTION:Run Sql Script file*
 *********************************/
function runsql_script($domain_db,$SqlScriptFile)
{
    $host='localhost';
    $port='3306';
    $user='root';
    $pwd='mysql';
    $domain_dbconnect=db_open($host,$port,$user,$pwd,$domain_db);
    if (empty($domain_dbconnect))
    {
        //return 0;
        echo "Not Connected to ".$domain_db. " database\n";
        db_close($domain_dbconnect);
    }
    else
    {
        echo "hi";
        $sql=file_get_contents($SqlScriptFile);
        $qr = $domain_dbconnect->exec($sql);
        db_close($domain_dbconnect);
        echo "End";
    }

}
/*=================================*/

$host='localhost';
$port='3306';
$user='root';
$pwd='mysql';
$db='root_saas';
//$db=db_open('locahost','3306','root','mysql','root_saas');
$db=db_open($host,$port,$user,$pwd,$db);
if (empty($db))
{
    //return 0;
    echo "Not Connected \n";
}
else
{
    echo "Connected successfully root_saas db \n";
    // Query the DB for a matching user and hash
    $stmt = $db->prepare("SELECT domain FROM clients WHERE id = 2");
    //$stmt->bindParam(":username", $username, PDO::PARAM_STR, 20);
    $stmt->execute();
    // Store the list in the array
    $array = $stmt->fetchAll();
    $domain = $array[0]['domain'];
    // Close the database connection
    db_close($db);
    //create db for new domain
    $domain_db=trim($domain.'_riskdb');
    $db_create_result=create_domain_db($domain_db);
    if($db_create_result)
    {
        //Run the script.sql file (domaintable.sql)
        $SqlScriptFile='script.sql';
        runsql_script($domain_db,$SqlScriptFile);
    }
    else
    {
        echo "Domain db create error!!!!!\n";
    }
}

?>