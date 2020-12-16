<?php
require 'models.php';
$database = new Database();
$database->load_data();
$books_table_array = json_decode($database->queryBooksTable());
if(isset($_POST['author'])) {
	print_r($database->searchAuthor($_POST['author']));
	exit;
}
?>

<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
		<title>Books</title>
	</head>
	<body>
		<div class="container">
			<div class="row p-3">
				<div class="col-md-6">
					<table id="books_table" class="table">
					  <thead>
					    <tr>
					      <th scope="col">Title</th>
					      <th scope="col">Author</th>
					    </tr>
					  </thead>
					    <?php
					    	foreach($books_table_array as $book) { ?>
    							<tr>
    								<td>
    									<?php echo $book->title; ?>
    								</td>
    								<td>
    									<?php echo $book->author; ?>
    								</td>
    							</tr>
								<?php }?>
					  <tbody>
					  </tbody>
					</table>
				</div>
				<div class="col-md-6">
					<input id="search" class="form-control mr-sm-2" type="text" placeholder="Search" aria-label="Search">
  				<button style="width: 100%;" onclick="searchAuthor();" class="btn btn-info btn-rounded btn-sm my-0" type="submit">Search</button>
					<table id="authors_table" class="table">
					  <tbody>
					  </tbody>
					</table>
				</div>
			</div> <!-- ./row  -->
		</div>


		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

		<script>
      function searchAuthor() {
      	 var author = $( "#search" ).val();
      	 // remove emty space if there is some before and after the name
      	 author = $.trim(author); 
	      	$.ajax({
						type: 'POST',
						data: {author: author},
		        success: function(data) {
		        	var table = document.getElementById("authors_table");
		        	table.innerHTML = "";
		        	var data = data.replace(author,'');
	        		try {
	        			var json_data = JSON.parse(data);
	        			for (var key in json_data) {
	        				//loop the responded data and show the result below the search
		        				if (json_data.hasOwnProperty('title')) {
		        				}
	        					var row = table.insertRow(0);
	        					var cell1 = row.insertCell(0);
	        					cell1.innerHTML = json_data[key]['title'];
	        			}

	        		} catch(e) {
	        			console.log(e);
	        		}
		        		
		        },
		        error: function(data) {
		        	console.log(data);
		        }
	      	});
      }
		</script>
	</body>
</html>
