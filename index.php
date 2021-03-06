<?php
$server 	= "localhost";
$username 	= "root";
$password 	= "root";
$dbname 	= "sortable";

//Create Connection
$conn = new mysql($server, $username, $password, $dbname);

//Check Connection
if ($conn->connect_error){
	die("Connection Failed: ". $conn->connect_error);
}

$sql = "Select *, groups.title AS group_title, groups.order_id AS group_order_id 
		FROM groups LEFT JOIN items ON items.group_id = groups.id
		ORDER BY groups.order_id, items.order_id";

$result = $conn->query($sql);
while($row = $result->fetch_assoc()){
	print_r($row)
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Sortable Like Trello</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/css/materialize.min.css">
</head>
<style type="text/css">
	body{
		background: #ec407a;
	}
	.wrapper{
		width: 80%;
		margin:10px auto;
	}

</style>
<body>
	<div class="wrapper">
		<div id="items" class="row">
			<? 
				$prev_group_id = "";
				while ($row = $result->fetch_assoc()):
			?>
				<? if($prev_group_id != $row['group_id']): ?>
					<div id="group1" class="group col s4" data-id="<?= $row['group_id'] ?>">
						<h1><?= $row['group_id'] ?></h1>
						<div class="group-items collection">
							<li class="collection-item" data-id="<?= $row['group_id'] ?>"><?= $row['title'] ?></li>
					
				<? endif; ?>

				<? if($prev_group_id != $row['group_id']): ?>
						</div>
					</div>
				<? endif; ?>

			<? 
				$prev_group_id = $row['group_id'];
				endwhile; 
			?>
		</div>
	</div>
<script type="text/javascript" src="node_modules/jquery/dist/jquery.min.js"></script>
<script type="text/javascript" src="node_modules/sortablejs/Sortable.min.js"></script>
<script type="text/javascript">
	var container = document.getElementById('items');
	Sortable.create(container,{
		handle:'h1',
		draggable:'.group',
		onUpdate: function(evt){
			update_via_ajax(evt.from.children,'groups');
			
		}
	});

	var group = document.getElementsByClassName('group-items');
	for (var i = 0; i<group.length; i++) {
		Sortable.create(group.item(i),{
			group: 'item-list'.
			onUpdate:function(evt){
				update_via_ajax(evt.from.children,'items');
			},
			onAdd:function(evt){
				update_via_ajax(evt.item.parentElement.children,'items');

				data = {
					action 		: "on_add",
					type		: "items",
					items 		: parseInt(evt.item.getAttribute('data-id'));
					group_id 	: parseInt(evt.item.parentElement.parentElement.getAttribute('data-id'));
				};

				$.post('update.php',data).done(function(data){
					console.log(data)
				});

			}
		});
	}

	function update_via_ajax(evt,type){
			var items 		= [];
			for (var i = 0; i < children.length; i++) {
				items.push(parseInt(children[i].getAttribute('data-id')));
			}

			//request ajax
			data = {
				action 	: "on_update",
				type	: type,
				items 	: items
			};
			$.post('update.php',data).done(function(data){
				console.log(data)
			});
	}
	
</script>

</body>
</html>
