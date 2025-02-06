function dragList(event) {
    const list = event.target.closest('.list-container');
    if (!list) return;
    
    event.dataTransfer.setData('text/plain', JSON.stringify({
        type: 'list',
        listId: list.dataset.listId
    }));
    list.classList.add('dragging');
}

function dragTask(event) {
    event.stopPropagation();
    const task = event.target.closest('.draggable');
    if (!task) return;
    
    event.dataTransfer.setData('text/plain', JSON.stringify({
        type: 'task',
        taskId: task.dataset.taskId,
        listId: task.dataset.listId
    }));
    task.classList.add('dragging');
}

function allowDrop(event) {
    event.preventDefault();
    const draggingElement = document.querySelector('.dragging');
    if (!draggingElement) return;
    
    if (draggingElement.classList.contains('list-container')) {
        const targetList = event.target.closest('.list-container');
        if (targetList && targetList !== draggingElement) {
            targetList.classList.add('list-drop-target');
        }
    } else {
        const container = event.target.closest('.task-container');
        if (container) {
            container.classList.add('drop-target');
        }
    }
}

function drop(event) {
    event.preventDefault();
    
    try {
        const data = JSON.parse(event.dataTransfer.getData('text/plain'));
        
        if (data.type === 'list') {
            handleListDrop(event, data.listId);
        } else if (data.type === 'task') {
            handleTaskDrop(event, data);
        }
    } catch (error) {
        console.error('Drop error:', error);
    } finally {
        // Cleanup
        document.querySelectorAll('.dragging').forEach(el => el.classList.remove('dragging'));
        document.querySelectorAll('.drop-target').forEach(el => el.classList.remove('drop-target'));
        document.querySelectorAll('.list-drop-target').forEach(el => el.classList.remove('list-drop-target'));
    }
}

function handleListDrop(event, draggedId) {
    const draggedList = document.querySelector(`[data-list-id="${draggedId}"]`);
    const targetList = event.target.closest('.list-container');
    
    if (!targetList || !draggedList || draggedList === targetList) return;
    
    const container = targetList.parentElement;
    const lists = [...container.children];
    const draggedIndex = lists.indexOf(draggedList);
    const targetIndex = lists.indexOf(targetList);
    
    if (draggedIndex < targetIndex) {
        targetList.parentNode.insertBefore(draggedList, targetList.nextSibling);
    } else {
        targetList.parentNode.insertBefore(draggedList, targetList);
    }
    
    updateListPositions();
}

function handleTaskDrop(event, data) {
    const taskElement = document.querySelector(`[data-task-id="${data.taskId}"]`);
    const targetContainer = event.target.closest('.task-container');
    
    if (!taskElement || !targetContainer) return;
    
    const newListId = targetContainer.dataset.listId;
    
    // Voeg de taak toe aan de nieuwe container
    targetContainer.appendChild(taskElement);
    
    // Update alleen de database als de lijst is veranderd
    if (newListId !== data.listId) {
        taskElement.dataset.listId = newListId;
        updateTaskList(data.taskId, newListId);
    }
}

function updateTaskPosition(taskId, status, position) {
    fetch('update_task_position.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            taskId: taskId,
            status: status,
            position: position
        })
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            console.error('Error updating task position:', data.error);
            // Optionally refresh the page or show an error message
        }
    });
}

function openAddTaskModal(listId) {
    document.getElementById('taskStatus').value = listId;
    document.getElementById('addTaskModal').classList.add('modal-open');
}

function closeAddTaskModal() {
    document.getElementById('addTaskModal').classList.remove('modal-open');
}

function openEditModal(id, task) {
    document.getElementById('editId').value = id;
    document.getElementById('editTask').value = task;
    document.getElementById('editDescription').value = ''; // Reset description
    document.getElementById('editModal').classList.add('modal-open');
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('modal-open');
}

function openEditMenu(id) {
    const taskTitle = document.getElementById(`task-title-${id}`);
    const currentTitle = taskTitle.innerText;
    const input = document.createElement('input');
    input.type = 'text';
    input.value = currentTitle;
    input.onblur = function() {
        taskTitle.innerText = input.value;
        // Hier kun je een AJAX-aanroep doen om de wijziging op te slaan
    };
    taskTitle.innerText = '';
    taskTitle.appendChild(input);
    input.focus();
}

let currentTaskId = null;

function openOverview(id, task) {
    currentTaskId = id;
    
    fetch(`get_task_details.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('overviewTaskName').value = data.task;
            document.getElementById('overviewDescription').value = data.description;
            document.getElementById('overviewStatus').value = data.status;
            document.getElementById('overviewPriority').value = data.priority || 'medium';
            
            // Populate assigned users
            const usersContainer = document.getElementById('assignedUsers');
            usersContainer.innerHTML = '';
            data.assigned_users.forEach(user => {
                usersContainer.innerHTML += `
                    <div class="flex justify-between items-center bg-base-300 p-2 rounded">
                        <span>${user.username}</span>
                        <button onclick="removeUserFromTask(${user.id})" class="btn btn-ghost btn-sm text-error">
                            <i class="fas fa-user-minus"></i>
                        </button>
                    </div>
                `;
            });
        });

    document.getElementById('overviewModal').classList.add('modal-open');
}

function closeOverviewModal() {
    document.getElementById('overviewModal').classList.remove('modal-open');
    currentTaskId = null;
}

function saveTaskChanges() {
    const data = {
        id: currentTaskId,
        task: document.getElementById('overviewTaskName').value,
        description: document.getElementById('overviewDescription').value,
        status: document.getElementById('overviewStatus').value,
        priority: document.getElementById('overviewPriority').value
    };

    fetch('update_task.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Vind het huidige taak element
            const taskElement = document.querySelector(`[data-task-id="${currentTaskId}"]`);
            if (taskElement) {
                // Update de taaknaam en prioriteit
                const titleElement = taskElement.querySelector('.card-title');
                titleElement.innerHTML = `
                    ${data.task}
                    <span class="priority-badge badge ml-2 badge-${data.priority}">
                        ${data.priority === 'high' ? 'High' : 
                          data.priority === 'medium' ? 'Normal' : 'Low'}
                    </span>
                `;

                // Verplaats de taak naar de juiste lijst als de status is gewijzigd
                const newStatusContainer = document.querySelector(`[data-status="${data.status}"] .task-container`);
                if (newStatusContainer) {
                    // Verwijder het element uit de huidige lijst
                    taskElement.remove();
                    // Voeg het toe aan de nieuwe lijst
                    newStatusContainer.appendChild(taskElement);
                }
            }
            closeOverviewModal();
            // Optioneel: toon een succesbericht
            alert('Task successfully updated');
        } else {
            alert('An error occurred while updating the task');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the task');
    });
}

function deleteTaskFromOverview() {
    if (confirm('Are you sure you want to delete this task?')) {
        fetch('delete_task.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: currentTaskId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Verwijder de taak uit de UI
                const taskElement = document.querySelector(`[data-task-id="${currentTaskId}"]`);
                if (taskElement) {
                    taskElement.remove();
                    closeOverviewModal();
                    // Optioneel: toon een succesbericht
                    alert('Task successfully deleted');
                }
            } else {
                // Toon een foutmelding als er iets mis ging
                alert('An error occurred while deleting the task');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the task');
        });
    }
}

function addUserToTask() {
    const userId = document.getElementById('userToAdd').value;
    if (!userId) return;

    fetch('add_user_to_task.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            taskId: currentTaskId,
            userId: userId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Refresh the overview to show the new user
            openOverview(currentTaskId);
        }
    });
}

function removeUserFromTask(userId) {
    fetch('remove_user_from_task.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            taskId: currentTaskId,
            userId: userId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Refresh the overview to show the updated user list
            openOverview(currentTaskId);
        }
    });
}

function toggleNotificationOptions(checkbox) {
    const options = document.getElementById('notificationOptions');
    options.classList.toggle('hidden', !checkbox.checked);
}

function openNewListModal() {
    document.getElementById('newListModal').classList.add('modal-open');
}

function closeNewListModal() {
    document.getElementById('newListModal').classList.remove('modal-open');
}

function createNewList(event) {
    event.preventDefault();
    const name = document.getElementById('newListName').value;
    const color = document.getElementById('newListColor').value;

    fetch('create_list.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ name, color })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Ververs de pagina om de nieuwe lijst te tonen
        } else {
            alert('An error occurred while creating the list');
        }
    });
}

function deleteList(listId) {
    if (confirm('Are you sure you want to delete this list? All tasks in this list will also be deleted.')) {
        console.log('Attempting to delete list with ID:', listId);
        
        fetch('delete_list.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: listId })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Delete response:', data);
            if (data.success) {
                // Zoek het lijst element met beide selectors voor het geval dat
                let listElement = document.querySelector(`[data-list-id="${listId}"]`);
                console.log('Found list element by list-id:', listElement);
                
                if (!listElement) {
                    listElement = document.querySelector(`[data-status="${listId}"]`);
                    console.log('Found list element by status:', listElement);
                }
                
                if (listElement) {
                    console.log('Removing list element from UI');
                    listElement.remove();
                    location.reload(); // Forceer een refresh
                } else {
                    console.error('Could not find list element to remove');
                }
                alert('List and its tasks successfully deleted');
            } else {
                throw new Error('Failed to delete list: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred: ' + error.message);
        });
    }
}

function updateListPositions() {
    const lists = [...document.querySelectorAll('[data-list-id]')];
    const positions = lists.map((list, index) => ({
        id: list.dataset.listId,
        position: index + 1
    }));

    fetch('update_list_positions.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ positions })
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            console.error('Error updating list positions:', data.error);
        }
    });
}

function updateTaskList(taskId, newListId) {
    fetch('update_task_list.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `task_id=${taskId}&list_id=${newListId}`
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            console.error('Error updating task list:', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Voeg deze event listener toe voor cleanup bij het beëindigen van slepen
document.addEventListener('dragend', function(event) {
    document.querySelectorAll('.dragging').forEach(el => el.classList.remove('dragging'));
    document.querySelectorAll('.drop-target').forEach(el => el.classList.remove('drop-target'));
    document.querySelectorAll('.list-drop-target').forEach(el => el.classList.remove('list-drop-target'));
});