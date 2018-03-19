<?php

session_start();

if (empty($_SESSION['user'])) {
	header('Location: index.php');
}

if (!$_POST['exit']) {
	
} else {
    session_destroy();
    header('Location: index.php');
}

$connect = mysqli_connect("localhost", "aevlahina", "neto1705", "aevlahina");

$users = mysqli_query($connect, "select * from user");
$users_id = [];
$users_login = [];
while ($data = mysqli_fetch_array($users)) {
	$users_id[] = $data['id'];
	$users_login[] = $data['login'];
}

$user_id = mysqli_query($connect, "select * from user where id = ".$_SESSION['user']);
$user = mysqli_fetch_array($user_id);

$sql = "select * from task left join user on user.id=task.assigned_user_id where task.user_id = ".$_SESSION['user'];
$sql_rep = "select * from task left join user on user.id=task.user_id where task.assigned_user_id = ".$_SESSION['user']." and task.user_id <> ".$_SESSION['user'];

if (!empty($_POST)) {
	if ($_POST['add'] && $_POST['adding'] !== '') {
		mysqli_query($connect, "insert into `task`(`user_id`, `assigned_user_id`, `description`) values ('".$user['id']."', '".$user['id']."', '".$_POST['adding']."')");
		header('Location: tasks.php');
	}

	foreach ($_POST as $key => $value) {
		if ($key[0] === 'c' && $value != '') {
			$i = substr($key, 1);
			mysqli_query($connect, "update task set is_done = 1 where id = ".$i);
			header('Location: tasks.php');
		}

		if ($key[0] === 'd' && $value != '') {
			$i = substr($key, 1);
			mysqli_query($connect, "delete from task where id = ".$i);
			header('Location: tasks.php');
		}

		if ($key[0] === 'r' && $value != '') {
			$i = substr($key, 1);
			for ($j = 0; $j < count($users_login); $j++) {
				if ($_POST['user_rep'] === $users_login[$j]) {
					$new_user = $j;
				}
			}
			mysqli_query($connect, "update task set assigned_user_id = ".$users_id[$new_user]." where id = ".$i);
			header('Location: tasks.php');
		}
	}
}

$res = mysqli_query($connect, $sql);

$res_rep = mysqli_query($connect, $sql_rep);

?>

<!doctype>
<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="utf-8">
	<title>Работа с БД (3-е занятие)</title>
	<style>
		table {
			margin-top: 5px;
			border-collapse: collapse;
		}
		th, td {
			padding: 5px;
			border: 1px solid grey;
		}
	</style>
</head>
<body>
	<h1>Здравствуйте <?php echo $user['login']; ?>. Список дел на сегодня</h1>
	<form action="" method="post">
		<table>
			<input type="text" name="adding"><input type="submit" name="add" value="Добавить">			
			<tr style="background-color: #eeeeee">
				<td>Автор</td>
				<td>Ответственный</td>
				<td>Закрепить задачу за пользователем</td>
				<td>Описание задачи</td>
				<td>Статус</td>
				<td>Дата добавления</td>
			</tr>
			<?php while ($data = mysqli_fetch_array($res)) { ?>
			
			<tr>
				<td><?php echo $user['login']; ?></td>
				<td><?php echo $data['login']; ?></td>
				<td>
					<select name="user_rep">
						<option></option>
						<?php for ($i = 0; $i < count($users_login); $i++) { ?>
						<option><?php echo $users_login[$i]; ?></option>
						<?php } ?>
					</select>
					<br>
					<input type="submit" name="<?= 'r'.$data['user_id']; ?>" value="Перенаправить задачу">
				</td>
				<td><?php echo $data['description']; ?></td>
				<td><?php echo $data['is_done']; ?></td>
				<td><?php echo $data['date_added']; ?></td>
				<td style="border: none"><input type="submit" name="<?= 'c'.$data[0]; ?>" value="Выполнить"></td>
				<td style="border: none"><input type="submit" name="<?= 'd'.$data[0]; ?>" value="Удалить"></td>
			</tr>
			<?php } ?>
		</table>
	</form>

	<p>Так же посмотрите, что от Вас требуют другие люди</p>
	<form action="" method="post">
		<table>
			<tr style="background-color: #eeeeee">
				<td>Автор</td>
				<td>Ответственный</td>
				<td>Описание задачи</td>
				<td>Статус</td>
				<td>Дата добавления</td>
			</tr>
			<?php while ($data = mysqli_fetch_array($res_rep)) { ?>
			<tr>
				<td><?php echo $data['login']; ?></td>
				<td><?php echo $user['login']; ?></td>
				<td><?php echo $data['description']; ?></td>
				<td><?php echo $data['is_done']; ?></td>
				<td><?php echo $data['date_added']; ?></td>
				<td style="border: none"><input type="submit" name="<?= 'c'.$data[0]; ?>" value="Выполнить"></td>
				<td style="border: none"><input type="submit" name="<?= 'd'.$data[0]; ?>" value="Удалить"></td>
			</tr>
			<?php } ?>
		</table>
	</form>

	<br>
	<form action="" method="post">
		<input type="submit" name="exit" value="Выход">
	</form>
</body>
</html>
