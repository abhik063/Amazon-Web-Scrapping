<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Amazon Price Scrapping</title>
        <link href="https://unpkg.com/tailwindcss@^1.0/dist/tailwind.min.css" rel="stylesheet">
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    </head>
    <style>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #dddddd;
}
</style>
<body >
        <header class="text-gray-500 bg-gray-900 body-font">
        <div class="container mx-auto flex flex-wrap p-5 flex-col md:flex-row items-center">
          <nav class="flex lg:w-2/5 flex-wrap items-center text-base md:ml-auto">

          </nav>
          <a class="flex order-first lg:order-none lg:w-1/5 title-font font-medium items-center text-white lg:items-center lg:justify-center mb-4 md:mb-0">
            <img src="https://images-na.ssl-images-amazon.com/images/G/01/gc/designs/livepreview/amazon_dkblue_noto_email_v2016_us-main._CB468775337_.png" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1" class="w-20 h-20 text-white p-2 bg-orange-500 rounded-full" viewBox="0 0 24 24">
              <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
            </svg>
            <span class="ml-3 text-xl xl:block lg:hidden">Amazon Scrapper</span>
          </a>
          <div class="lg:w-2/5 inline-flex lg:justify-end ml-5 lg:ml-0"> 
               <form method="get">
                   <label style="color: white;">Enter the URL <input style="border-radius: 8px;color: black;"name="asin"></label>
               </form>
          </div>
        </div>
      </header>
      
       
        <?php
        $servername = "";
        $username = "root";
        $password = "root";
        $dbname = "amazon";

        // Create connection
        $conn = new mysqli( "localhost",$username, $password, $dbname);
        // Check connection        
            $asin=filter_input(INPUT_GET, 'asin');
            if(!empty($asin))
            {
                echo '<hr>';
                $baseUrl= $asin;
                $html=file_get_contents($baseUrl); 
                $price=0;             
                if(preg_match_all('|"priceblock_dealprice".*\₹(.*)<|', $html,$match) && isset($match[1]))
                 {
                     $price=$match[1];
                 }
                 else if(preg_match_all('|"a-size-medium a-color-price priceBlockBuyingPriceString".*\₹(.*)<|',$html, $subject) && isset($subject[1]))
                 {
                     $price=$subject[1];
                 }
                 else if(preg_match_all('|"a-size-medium a-color-price inlineBlock-display offer-price a-text-normal price3P".*\₹(.*)<|',$html, $subject) && isset($subject[1]))
                 {
                     $price=$subject[1];
                 }
                 else if(preg_match_all('|"priceblock_ourprice".*\₹(.*)<|', $html,$match) && isset($match[1]))
                 {
                     $price=$match[1];
                 }  
                 else if(preg_match_all('|"priceblock_saleprice".*\₹(.*)<|', $html,$match) && isset($match[1]))
                 {
                     $price=$match[1];
                 } 
                 else if(preg_match('|"a-size-medium a-color-price"(.*)<|', $html,$match)  && isset($match[1]))
                 {
                     
                        $price=array();
                        $price[0]= $match[1];
                        
                   
                 } 
                 if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                  }
                   $time=date("d/m/Y");
                   
                  $sql = "INSERT INTO Data VALUES ('$time','$asin','$price[0]');";
                   if ($conn->query($sql) === TRUE) {
                    echo "New record created successfully";
                  } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                  }
                 
                
            }
            $myquery="SELECT * FROM data ";
            $query= mysqli_query($conn, $myquery);
            echo '<table>';
            echo '<tr><td>Date</td> <td>URL</td> <td>Price</td></tr>';
            while ($record = mysqli_fetch_array($query)) {
                    echo '<tr> <td>' . $record['Date'] . '</td> <td>' . $record['URL'] . '</td> <td>' . $record['Price'] . '</td> </tr>';
            }
            echo '</table>';            
            $conn->close();
        ?>  
    <br>
    <div class="lex lex lg:w-1/2 flex-wrap items-center text-base md:ml-auto flex-wrap items-center text-base md:ml-auto">
        <form action="export.php" method="get">
            <input style="background-color: #59acff; border-radius: 8px; height: 50px; width: 150px" type="submit" value="Export to Excel">
          </form>
    </div>
    </body>
    
</html>
