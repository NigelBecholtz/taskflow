<!DOCTYPE html>
<html lang="en" data-theme="business">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@latest/dist/full.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="../assets/images/tasks-solid.svg" type="image/x-icon" />
    <style>
        img[src$="tasks-solid.svg"] {
            filter: invert(47%) sepia(98%) saturate(1502%) hue-rotate(190deg) brightness(100%) contrast(119%);
        }
        .draggable {
            cursor: move; /* Cursor changes to 'move' on hover */
        }
        .badge-high {
            background-color: #ef4444;
            color: white;
        }
        .badge-medium {
            background-color: #f59e0b;
            color: white;
        }
        .badge-low {
            background-color: #10b981;
            color: white;
        }
        .list-drop-target {
            border: 2px dashed #666;
            margin: -2px;
        }
        [data-list-id] {
            cursor: move;
            transition: opacity 0.2s;
        }
        .draggable {
            cursor: move;
            transition: opacity 0.2s, transform 0.2s;
        }
        .dragging {
            opacity: 0.5;
            transform: scale(1.02);
        }
        .drop-target {
            border: 2px dashed #666;
            margin: -2px;
        }
        .space-y-3.drop-target {
            min-height: 100px;
        }
        .task-container {
            min-height: 50px;
        }
        .task-container.drop-target {
            background-color: rgba(100, 100, 100, 0.1);
            border-radius: 0.5rem;
        }
    </style>
</head>
<body class="bg-base-200 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <div class="flex items-center">
                <img src="../assets/img/tasks-solid.svg" alt="TaskFlow Logo" class="w-8 h-8 mr-3 text-primary">
                <h1 class="text-3xl font-bold text-primary">TaskFlow</h1>
            </div>
            <div class="flex gap-2">
                <button onclick="openNewListModal()" class="btn btn-primary">
                    <i class="fas fa-plus mr-2"></i>New List
                </button>
                <a href="index.php" class="btn btn-danger">Logout</a>
            </div>
        </div>

        <div class="flex space-x-4 overflow-x-auto pb-4">
            <?php
            // Debug informatie
            $user_id = $_SESSION['user_id'] ?? null;
            error_log("Current user_id: " . $user_id);

            // Haal alle taken op
            $todos_stmt = $pdo->prepare("
                SELECT t.*, l.name as list_name 
                FROM todos t 
                JOIN lists l ON t.list_id = l.id 
                WHERE t.user_id = ?
            ");
            $todos_stmt->execute([$_SESSION['user_id']]);
            $todos = $todos_stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Found " . count($todos) . " total tasks");

            // Haal alle lijsten op
            $stmt = $pdo->prepare("
                SELECT * FROM lists 
                WHERE user_id = ? 
                ORDER BY position ASC
            ");
            $stmt->execute([$_SESSION['user_id']]);
            $lists = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Loop door de lijsten
            foreach ($lists as $list):
                $list_tasks = array_filter($todos, function($todo) use ($list) {
                    return $todo['list_id'] == $list['id'];
                });
            ?>
            <div class="w-72 bg-base-100 rounded-lg shadow-md list-container" 
                 data-status="<?php echo htmlspecialchars($list['id']); ?>" 
                 data-list-id="<?php echo htmlspecialchars($list['id']); ?>"
                 draggable="true"
                 ondragstart="dragList(event)">
                
                <!-- Lijst header -->
                <div class="bg-<?php echo htmlspecialchars($list['color']); ?> text-<?php echo htmlspecialchars($list['color']); ?>-content p-3 rounded-t-lg">
                    <div class="flex justify-between items-center">
                        <h2 class="font-semibold"><?php echo htmlspecialchars($list['name']); ?></h2>
                        <div class="dropdown dropdown-end relative z-50">
                            <button class="btn btn-ghost btn-sm">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-52">
                                <li>
                                    <button onclick="openAddTaskModal('<?php echo htmlspecialchars($list['id']); ?>')" class="text-left">
                                        <i class="fas fa-plus"></i> Add Task
                                    </button>
                                </li>
                                <li>
                                    <button onclick="deleteList('<?php echo htmlspecialchars($list['id']); ?>')" class="text-left text-error">
                                        <i class="fas fa-trash"></i> Delete List
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Taken container -->
                <div class="p-3 space-y-3 task-container" 
                     data-list-id="<?php echo htmlspecialchars($list['id']); ?>"
                     ondragover="allowDrop(event)"
                     ondrop="drop(event)">
                    <?php foreach ($list_tasks as $todo): ?>
                        <div class="card bg-base-200 shadow-sm draggable" 
                             draggable="true"
                             ondragstart="dragTask(event)"
                             data-task-id="<?php echo htmlspecialchars($todo['id']); ?>"
                             data-list-id="<?php echo htmlspecialchars($list['id']); ?>">
                            <div class="card-body p-3">
                                <h3 class="card-title text-sm">
                                    <?php echo htmlspecialchars($todo['task']); ?>
                                    <span class="priority-badge badge ml-2 badge-<?php echo htmlspecialchars($todo['priority'] ?? 'medium'); ?>">
                                        <?php 
                                        $priority_text = [
                                            'high' => 'High',
                                            'medium' => 'Normal',
                                            'low' => 'Low'
                                        ];
                                        echo $priority_text[$todo['priority'] ?? 'medium']; 
                                        ?>
                                    </span>
                                </h3>
                                <p class="text-sm"><?php echo htmlspecialchars($todo['description']); ?></p>
                                <div class="flex justify-end mt-2">
                                    <button onclick="openOverview(<?php echo $todo['id']; ?>, '<?php echo htmlspecialchars($todo['task']); ?>')" 
                                            class="btn btn-primary btn-xs">
                                        <i class="fas fa-eye mr-1"></i> Overview
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Add Task Modal -->
    <div id="addTaskModal" class="modal">
        <div class="modal-box max-w-2xl">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-primary">Add New Task</h2>
                <button onclick="closeAddTaskModal()" class="btn btn-ghost btn-sm">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="addTaskForm" method="POST" class="space-y-6">
                <input type="hidden" name="status" id="taskStatus">
                
                <!-- Basic Information -->
                <div class="bg-base-200 p-4 rounded-lg">
                    <h3 class="font-bold mb-4 text-lg">Basic Information</h3>
                    <div class="space-y-4">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Task Name</span>
                                <span class="label-text-alt text-error">*</span>
                            </label>
                            <input type="text" name="task" id="task" 
                                   class="input input-bordered w-full" 
                                   placeholder="Enter task name"
                                   required>
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Description</span>
                            </label>
                            <textarea name="description" id="description" 
                                      class="textarea textarea-bordered h-24" 
                                      placeholder="Enter task description"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Details -->
                <div class="bg-base-200 p-4 rounded-lg">
                    <h3 class="font-bold mb-4 text-lg">Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Priority</span>
                            </label>
                            <select name="priority" class="select select-bordered w-full">
                                <option value="low">Low</option>
                                <option value="medium" selected>Normal</option>
                                <option value="high">High</option>
                            </select>
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Due Date</span>
                            </label>
                            <input type="date" name="due_date" id="due_date" 
                                   class="input input-bordered">
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Category</span>
                            </label>
                            <select name="category" class="select select-bordered w-full">
                                <option value="">No category</option>
                                <option value="frontend">Frontend</option>
                                <option value="backend">Backend</option>
                                <option value="database">Database</option>
                                <option value="testing">Testing</option>
                                <option value="bugfix">Bug Fixes</option>
                                <option value="feature">New Features</option>
                                <option value="refactor">Refactoring</option>
                                <option value="docs">Documentation</option>
                                <option value="devops">DevOps</option>
                                <option value="security">Security</option>
                            </select>
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Tags</span>
                            </label>
                            <input type="text" name="tags" 
                                   class="input input-bordered" 
                                   placeholder="Separated by commas">
                        </div>
                    </div>
                </div>

                <!-- Notifications -->
                <div class="bg-base-200 p-4 rounded-lg">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-lg">Notifications</h3>
                        <label class="label cursor-pointer">
                            <span class="label-text mr-2">Enable</span> 
                            <input type="checkbox" name="enable_notifications" 
                                   class="toggle toggle-primary" 
                                   onchange="toggleNotificationOptions(this)">
                        </label>
                    </div>
                    <div id="notificationOptions" class="space-y-4 hidden">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Reminder</span>
                            </label>
                            <select name="reminder_time" class="select select-bordered w-full">
                                <option value="0">On due date</option>
                                <option value="1">1 day before</option>
                                <option value="2">2 days before</option>
                                <option value="7">1 week before</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="modal-action flex justify-between">
                    <button type="button" onclick="closeAddTaskModal()" 
                            class="btn btn-ghost">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </button>
                    <div class="space-x-2">
                        <button type="submit" name="add_task" class="btn btn-primary">
                            <i class="fas fa-plus mr-2"></i>
                            Add Task
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-box">
            <h2 class="font-bold text-lg">Edit Task</h2>
            <form id="editForm" method="POST">
                <input type="hidden" name="id" id="editId">
                <div class="form-group">
                    <label for="editTask">Task</label>
                    <input type="text" name="task" id="editTask" class="input input-bordered" required>
                </div>
                <div class="form-group">
                    <label for="editDescription">Description</label>
                    <textarea name="description" id="editDescription" class="input input-bordered" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label for="editStatus">Status</label>
                    <select name="new_status" id="editStatus" class="input input-bordered">
                        <option value="todo">To Do</option>
                        <option value="in_progress">In Progress</option>
                        <option value="done">Done</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="editPriority">Priority</label>
                    <select name="priority" id="editPriority" class="input input-bordered">
                        <option value="high">High</option>
                        <option value="medium">Normal</option>
                        <option value="low">Low</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="editDueDate">Due Date</label>
                    <input type="date" name="due_date" id="editDueDate" class="input input-bordered">
                </div>
                <button type="submit" name="edit_task" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
            </form>
        </div>
    </div>

    <!-- Overview Modal -->
    <div id="overviewModal" class="modal">
        <div class="modal-box max-w-2xl">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold text-primary">Task Overview</h2>
                <button onclick="closeOverviewModal()" class="btn btn-ghost btn-sm">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="grid grid-cols-1 gap-4">
                <!-- Task Details Section -->
                <div class="bg-base-200 p-4 rounded-lg">
                    <h3 class="font-bold mb-4 text-lg">Task Details</h3>
                    <div class="space-y-4">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Name</span>
                            </label>
                            <input type="text" id="overviewTaskName" class="input input-bordered" />
                        </div>
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Description</span>
                            </label>
                            <textarea id="overviewDescription" class="textarea textarea-bordered" rows="3"></textarea>
                        </div>
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Status</span>
                            </label>
                            <select id="overviewStatus" class="select select-bordered">
                                <option value="todo">To Do</option>
                                <option value="in_progress">In Progress</option>
                                <option value="done">Done</option>
                            </select>
                        </div>
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Priority</span>
                            </label>
                            <select id="overviewPriority" class="select select-bordered">
                                <option value="low">Low</option>
                                <option value="medium">Normal</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Category</span>
                            </label>
                            <select id="overviewCategory" class="select select-bordered">
                                <option value="">No category</option>
                                <option value="frontend">Frontend</option>
                                <option value="backend">Backend</option>
                                <option value="database">Database</option>
                                <option value="testing">Testing</option>
                                <option value="bugfix">Bug Fixes</option>
                                <option value="feature">New Features</option>
                                <option value="refactor">Refactoring</option>
                                <option value="docs">Documentation</option>
                                <option value="devops">DevOps</option>
                                <option value="security">Security</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- User Management Section -->
                <div class="bg-base-200 p-4 rounded-lg">
                    <h3 class="font-bold mb-4 text-lg">User Management</h3>
                    <div class="flex gap-4 mb-4">
                        <select id="userToAdd" class="select select-bordered flex-1">
                            <option value="">Select user...</option>
                            <!-- PHP will populate this -->
                        </select>
                        <button onclick="addUserToTask()" class="btn btn-primary">
                            <i class="fas fa-user-plus mr-2"></i> Add
                        </button>
                    </div>
                    <div id="assignedUsers" class="space-y-2">
                        <!-- Assigned users will be populated here -->
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-between mt-4">
                    <div class="space-x-2">
                        <button onclick="saveTaskChanges()" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i> Save
                        </button>
                        <button onclick="closeOverviewModal()" class="btn btn-ghost">
                            <i class="fas fa-times mr-2"></i> Cancel
                        </button>
                    </div>
                    <button onclick="deleteTaskFromOverview()" class="btn btn-error">
                        <i class="fas fa-trash mr-2"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- New List Modal -->
    <div id="newListModal" class="modal">
        <div class="modal-box">
            <h2 class="font-bold text-lg mb-4">Add New List</h2>
            <form onsubmit="createNewList(event)">
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text">List Name</span>
                    </label>
                    <input type="text" id="newListName" class="input input-bordered" required>
                </div>
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text">Color</span>
                    </label>
                    <select id="newListColor" class="select select-bordered">
                        <option value="neutral">Neutral</option>
                        <option value="primary">Primary</option>
                        <option value="secondary">Secondary</option>
                        <option value="accent">Accent</option>
                        <option value="info">Info</option>
                        <option value="success">Success</option>
                        <option value="warning">Warning</option>
                        <option value="error">Error</option>
                    </select>
                </div>
                <div class="modal-action">
                    <button type="submit" class="btn btn-primary">Add</button>
                    <button type="button" onclick="closeNewListModal()" class="btn btn-ghost">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    <script src="../assets/js/plan.js"></script>
</body>
</html>
