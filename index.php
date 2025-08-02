<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}
$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Smart Daily Planner</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <script>
    function toggleTheme() {
      document.documentElement.classList.toggle('dark');
    }
    function showModal(id) {
      document.getElementById(id).classList.remove('hidden');
    }
    function closeModal(id) {
      document.getElementById(id).classList.add('hidden');
    }
  </script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white transition-all">

  <div class="max-w-5xl mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold">📝 Smart Planner + Tracker</h1>
      <div class="space-x-2">
        <button onclick="toggleTheme()" class="px-3 py-1 bg-gray-800 text-white rounded">🌓 Theme</button>
        <a href="logout.php" class="px-3 py-1 bg-red-600 text-white rounded">Logout</a>
      </div>
    </div>

    <!-- WEEKLY TASKS -->
    <section class="mb-10">
      <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold">📅 Weekly Tasks</h2>
        <button onclick="showModal('addTaskModal')" class="bg-blue-500 text-white px-3 py-1 rounded">+ Add Task</button>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-7 gap-4 mt-4">
        <?php
        $days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
        $today = date('Y-m-d');
        foreach ($days as $i => $day) {
          $date = date('Y-m-d', strtotime("Sunday +$i day"));
          $result = $conn->query("SELECT * FROM tasks WHERE user_id = $user_id AND DATE(created_at) = '$date' ORDER BY created_at DESC");
          echo "<div class='bg-white dark:bg-gray-800 p-3 rounded shadow'>";
          echo "<h3 class='font-bold mb-2'>$day</h3>";
          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              $checked = $row['completed'] ? 'line-through text-green-500' : '';
              echo "<div class='flex justify-between items-center mb-1'>
                      <span class='$checked'>{$row['title']}</span>";
              if (!$row['completed']) {
                echo "<form method='POST' action='update_task.php'>
                        <input type='hidden' name='id' value='{$row['id']}'>
                        <button class='text-green-600'>✔</button>
                      </form>";
              }
              echo "</div>";
            }
          } else {
            echo "<p class='text-sm text-gray-500'>No tasks</p>";
          }
          echo "</div>";
        }
        ?>
      </div>
    </section>

    <!-- HABITS -->
    <section>
      <h2 class="text-xl font-semibold mb-4">💪 Habit Tracker</h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
        <?php
        $habits = $conn->query("SELECT * FROM habits WHERE user_id = $user_id");
        while ($habit = $habits->fetch_assoc()) {
          echo "<div class='bg-white dark:bg-gray-800 p-4 rounded shadow'>
                  <h4 class='font-semibold mb-2'>{$habit['name']}</h4>
                  <p>🔥 Streak: <strong>{$habit['streak']}</strong> days</p>
                </div>";
        }
        ?>
      </div>
    </section>
  </div>

  <!-- MODAL: Add Task -->
  <div id="addTaskModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
    <form method="POST" action="add_task.php" class="bg-white dark:bg-gray-800 p-6 rounded shadow w-96">
      <h3 class="text-lg font-bold mb-4">Add Task</h3>
      <input type="text" name="title" placeholder="Task title..." required class="w-full px-3 py-2 mb-4 border rounded text-black">
      <div class="flex justify-end space-x-2">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Add</button>
        <button type="button" onclick="closeModal('addTaskModal')" class="bg-gray-400 text-white px-4 py-2 rounded">Cancel</button>
      </div>
    </form>
  </div>

</body>
</html>
